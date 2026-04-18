import { computed } from 'vue';

/* =======================
   GROUPING
======================= */
export function groupByNameTypeAndSize(lines = []) {
    const groups = { withName: {}, noName: {} };
    const counters = { withName: {}, noName: {} };

    lines.forEach((line) => {
        const description = line.purchasable?.description ?? '';

        const isWithName =
            description.startsWith('WITH-name') ||
            description.startsWith('WITH name');

        const groupKey = isWithName ? 'withName' : 'noName';

        // SIZE LOGIC (support custom_dimension)
        const rawSize = description.split('/')[1]?.trim() || 'Unknown';

        const size =
            rawSize?.toLowerCase() === 'custom' && line.meta?.custom_dimension
                ? `${rawSize} : ${line.meta.custom_dimension}`
                : rawSize;

        // ✅ Add flag to show custom dimension above names

        if (!groups[groupKey][size]) groups[groupKey][size] = [];
        if (!counters[groupKey][size]) counters[groupKey][size] = 1;

        // Build names with numbers
        line.namesWithNumbers = Array.from(
            { length: line.quantity },
            (_, idx) => ({
                number: counters[groupKey][size] + idx,
                name: line.meta?.names?.[idx] ?? null,
            }),
        );

        counters[groupKey][size] += line.quantity;

        groups[groupKey][size].push(line);
    });

    if (!Object.keys(groups.withName).length) delete groups.withName;
    if (!Object.keys(groups.noName).length) delete groups.noName;
    return groups;
}

export function groupByUOM(lines = []) {
    return lines.reduce((groups, line) => {
        const uom = line.purchasable?.uom_code || 'Unknown';
        if (!groups[uom]) groups[uom] = [];
        groups[uom].push(line);
        return groups;
    }, {});
}

/* =======================
   QUANTITY
======================= */
export function getTotalQuantity(lines = []) {
    return lines.reduce((sum, line) => sum + (line.quantity || 0), 0);
}

/* =======================
   PRICING
======================= */
export function getTotalPriceValue(lines = []) {
    return lines.reduce((sum, line) => {
        const price = line.purchase_price?.decimal ?? 0;
        return sum + price * (line.quantity ?? 0);
    }, 0);
}

export function getTotalPrice(lines = [], currency) {
    if (!lines.length) return '';
    return formatCurrency(getTotalPriceValue(lines), currency);
}

export function formatCurrency(value, currency) {
    if (value === null || value === undefined || value === '' || value == 0)
        return '—';

    const number = Number(value);
    if (Number.isNaN(number)) return '—';

    const formatted = number.toLocaleString('en-US', {
        minimumFractionDigits: currency?.decimal_places ?? 2,
        maximumFractionDigits: currency?.decimal_places ?? 2,
    });

    return `${currency?.symbol ?? ''} ${formatted}`;
}

export function getPriceDisplay(lines = [], currency) {
    if (!lines.length) return [];

    const withName = [];
    const noName = [];
    const pricesSet = new Set();

    lines.forEach((line) => {
        const description = line.purchasable?.description ?? '';
        const isWithName =
            description.startsWith('WITH-name') ||
            description.startsWith('WITH name');

        // Determine size or custom dimension
        const rawSize =
            line.meta?.size ?? description.split('/')[1]?.trim() ?? 'Unknown';
        const size =
            rawSize.toLowerCase() === 'custom' && line.meta?.custom_dimension
                ? line.meta.custom_dimension
                : rawSize;

        const priceValue = line.purchase_price?.decimal ?? 0;
        const price = line.purchase_price?.formatted ?? priceValue.toFixed(2);

        pricesSet.add(price);

        const formatted = `${size}: ${price}`;

        if (isWithName) withName.push(formatted);
        else noName.push(formatted);
    });

    // If all prices are the same, just return the single price
    if (pricesSet.size === 1) {
        return [...pricesSet][0]; // e.g., "P10.00"
    }

    // Otherwise, combine WITH NAME first, NO NAME second
    return [...withName, ...noName];
}

/* =======================
   TOTALS
======================= */

export function sumOfAllGetTotalPrice(linesRef) {
    const sumOfAllGetTotalPrice = computed(() => {
        let total = 0;
        let currency = null;

        const lines = linesRef?.decimal || {};

        Object.values(lines).forEach((productLines) => {
            if (!currency && productLines.length) {
                currency = productLines[0].purchase_price?.currency;
            }
            total += getTotalPriceValue(productLines);
        });

        return { total, currency };
    });

    return {
        sumOfAllGetTotalPrice,
    };
}

export function useQuoteTotals(linesRef) {
    const sumOfAllGetTotalPrice = computed(() => {
        let total = 0;
        let currency = null;

        const lines = linesRef?.value || {};

        Object.values(lines).forEach((productLines) => {
            if (!currency && productLines.length) {
                currency = productLines[0].purchase_price?.currency;
            }
            total += getTotalPriceValue(productLines);
        });

        return { total, currency };
    });

    return {
        sumOfAllGetTotalPrice,
    };
}
