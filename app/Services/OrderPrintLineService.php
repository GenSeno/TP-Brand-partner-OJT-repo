<?php

namespace App\Services;

use App\Models\Contract\OrderLinePrintable;
use App\Models\Contract\OrderPrintable;
use App\Models\OrderPrintLine;
use App\Models\ProductVariant;
use Illuminate\Support\Collection;

class OrderPrintLineService
{
    /**
     * Group order lines by product and create print lines.
     *
     * @param OrderPrintable $order
     * @param string $printableType
     * @param int $printableId
     * @return Collection<OrderPrintLine>
     */
    public function createPrintLinesFromOrder(OrderPrintable $order, string $printableType, int $printableId): Collection
    {
        $groupedLines = $this->groupOrderLinesByProduct($order);

        return $groupedLines->map(function ($group, $index) use ($printableType, $printableId) {
            return $this->createPrintLine($group, $printableType, $printableId, $index * 10);
        });
    }

    /**
     * Group order lines by product.
     *
     * @param OrderPrintable $order
     * @return Collection
     */
    protected function groupOrderLinesByProduct(OrderPrintable $order): Collection
    {
        return $order->productLines()
            ->with(['purchasable.product'])
            ->get()
            ->filter(function (OrderLinePrintable $line) {
                return $line->purchasable instanceof ProductVariant;
            })
            ->groupBy(function (OrderLinePrintable $line) {
                return $line->purchasable->product->id;
            })
            ->map(function (Collection $lines) {
                $firstLine = $lines->first();
                $purchasable = $firstLine->purchasable;

                return [
                    'product_id' => $purchasable->product->id,
                    'product_name' => $purchasable->product->name,
                    'sku' => $purchasable->sku,
                    'uom_code' => $purchasable->uom_code,
                    'unit_price' => $firstLine->unit_price->value,
                    'quantity' => $lines->sum('quantity'),
                    'total' => $lines->sum('sub_total.value'),
                    'options_payload' => $this->aggregateOptionsPayload($lines),
                    'lines' => $lines,
                ];
            });
    }

    /**
     * Aggregate options from multiple order lines.
     *
     * @param Collection $lines
     * @return array
     */
    protected function aggregateOptionsPayload(Collection $lines): array
{
    $grouped = [];

    foreach ($lines as $line) {
        // Determine if this line has names
        $hasNames = !empty($line->meta['names']) && is_array($line->meta['names']);
        $printingOption = $hasNames ? 'WITH Name' : 'NO Name';

        // Get size (custom dimension first, then meta options, then variant)
        $size = $this->extractSize($line);

        // Normalize size for grouping (avoids duplicates due to spacing/case)
        $sizeNormalized = trim(strtoupper($size));

        // Initialize group if not exists
        if (!isset($grouped[$printingOption])) {
            $grouped[$printingOption] = [];
        }

        // Find existing size entry
        $sizeKey = false;
        foreach ($grouped[$printingOption] as $key => $item) {
            if (trim(strtoupper($item['size'])) === $sizeNormalized) {
                $sizeKey = $key;
                break;
            }
        }

        if ($sizeKey === false) {
            // Create new size entry
            $item = [
                'order_line_id' => $line->id,
                'size' => $size,
                'quantity' => $line->quantity,
                'unit_price' => $line->unit_price->value,
                'unit_price_formatted' => $line->unit_price->formatted(),
            ];

            if ($hasNames) {
                $item['names'] = $line->meta['names'];
            }

            $grouped[$printingOption][] = $item;
        } else {
            // Update existing size entry
            $grouped[$printingOption][$sizeKey]['quantity'] += $line->quantity;

            if ($hasNames) {
                $grouped[$printingOption][$sizeKey]['names'] = array_merge(
                    $grouped[$printingOption][$sizeKey]['names'] ?? [],
                    $line->meta['names']
                );
            }
        }
    }

    // Convert grouped data to final format
    $result = [];
    foreach ($grouped as $printingOption => $items) {
        $result[] = [
            'printing_option' => $printingOption,
            'items' => array_values($items),
        ];
    }

    return $result;
}

    /**
     * Extract size from order line.
     *
     * @param OrderLinePrintable $line
     * @return string
     */
    protected function extractSize(OrderLinePrintable $line): string
    {
        // 1. Get size from order_lines.option (example: "WITH Name / XL")
        if (!empty($line->option)) {
            $parts = explode('/', $line->option);

            // Get second value after "/"
            $size = trim($parts[1] ?? $parts[0]);

            // 2. If CUSTOM size, return the custom dimension
            if (strcasecmp($size, 'custom') === 0) {
                return $line->meta['custom_dimension'] ?? 'Custom';
            }

            return $size;
        }

        // 3. Fallback to meta size
        if (!empty($line->meta['size'])) {
            return $line->meta['size'];
        }

        // 4. Fallback to variant values
        if ($line->purchasable instanceof ProductVariant) {
            foreach ($line->purchasable->values as $value) {
                $optionName = strtolower($value->option->name ?? '');
                if (in_array($optionName, ['size', 'sizes'])) {
                    return $value->value;
                }
            }
        }

        return 'N/A';
    }

    /**
     * Create an OrderPrintLine from grouped data.
     *
     * @param array $groupedData
     * @param string $printableType
     * @param int $printableId
     * @param int $sortOrder
     * @return OrderPrintLine
     */
    protected function createPrintLine(array $groupedData, string $printableType, int $printableId, int $sortOrder): OrderPrintLine
    {
        return OrderPrintLine::updateOrCreate(
            [
                'printable_type' => $printableType,
                'printable_id' => $printableId,
                'product_id' => $groupedData['product_id'],
            ],
            [
                'product_name' => $groupedData['product_name'],
                'sku' => $groupedData['sku'],
                'uom_code' => $groupedData['uom_code'],
                'unit_price' => $groupedData['unit_price'],
                'quantity' => $groupedData['quantity'],
                'total' => $groupedData['total'],
                'options_payload' => $groupedData['options_payload'],
                'sort_order' => $sortOrder,
            ]
        );
    }

    /**
     * Bulk create print lines from multiple orders.
     *
     * @param Collection $orders
     * @param string $printableType
     * @param int $printableId
     * @return Collection<OrderPrintLine>
     */
    public function createPrintLinesFromOrders(Collection $orders, string $printableType, int $printableId): Collection
    {
        $allGroupedLines = collect();
        $sortOrder = 1;

        foreach ($orders as $order) {
            $groupedLines = $this->groupOrderLinesByProduct($order);

            foreach ($groupedLines as $group) {
                $existingKey = $allGroupedLines->search(function ($existing) use ($group) {
                    return $existing['sku'] === $group['sku']
                        && $existing['options_payload'] === $group['options_payload'];
                });

                if ($existingKey !== false) {
                    // Merge quantities and totals
                    $allGroupedLines[$existingKey]['quantity'] += $group['quantity'];
                    $allGroupedLines[$existingKey]['total'] += $group['total'];
                } else {
                    $allGroupedLines->push($group);
                }
            }
        }

        return $allGroupedLines->map(function ($group) use ($printableType, $printableId, &$sortOrder) {
            return $this->createPrintLine($group, $printableType, $printableId, $sortOrder++);
        });
    }

    /**
     * Delete existing print lines for a printable.
     *
     * @param string $printableType
     * @param int $printableId
     * @return void
     */
    public function deletePrintLines(string $printableType, int $printableId): void
    {
        OrderPrintLine::where('printable_type', $printableType)
            ->where('printable_id', $printableId)
            ->delete();
    }

    /**
     * Recreate print lines for a printable.
     *
     * @param OrderPrintable $order
     * @param string $printableType
     * @param int $printableId
     * @return Collection<OrderPrintLine>
     */
    public function recreatePrintLines(OrderPrintable $order, string $printableType, int $printableId): Collection
    {
        $this->deletePrintLines($printableType, $printableId);
        return $this->createPrintLinesFromOrder($order, $printableType, $printableId);
    }
}
