export function useVariantGenerator(optionValues, form, props) {
    const generateVariants = () => {
        // Get all active option values grouped by option
        const activeOptionValues = Object.entries(optionValues.value)
            .map(([optionId, values]) => {
                const option = props.product.options.find(
                    (opt) => opt.id == optionId,
                );

                const activeValues = Object.entries(values)
                    .filter(([, isActive]) => isActive)
                    .map(([valueId]) => {
                        // Find the actual value object
                        const value = option?.values.find(
                            (v) => v.id == valueId,
                        );
                        return value
                            ? {
                                  ...value,
                                  optionId: option.id,
                                  optionLabel: option.name,
                              }
                            : null;
                    })
                    .filter(Boolean);

                return { optionId: option.id, values: activeValues };
            })
            .filter((item) => item.values.length > 0)
            .sort((a, b) => a.optionId - b.optionId); // Sort by option ID

        // Generate all combinations (cartesian product)
        if (activeOptionValues.length === 0) {
            form.value.variants = [];
            return;
        }

        form.value.option_ids = activeOptionValues.map((item) => item.optionId);

        const combinations = activeOptionValues.reduce((acc, item) => {
            const values = item.values;
            if (acc.length === 0) {
                return values.map((v) => [v]);
            }

            const result = [];
            acc.forEach((combo) => {
                values.forEach((value) => {
                    result.push([...combo, value]);
                });
            });
            return result;
        }, []);

        // Format variations
        form.value.variants = combinations.map((combo) => {
            // Check props.product.variants
            const productVariant = props.product.variants?.find((v) => {
                const variantValueIds = v.values
                    ?.map((val) => val.id)
                    .sort()
                    .join('-');
                const comboValueIds = combo
                    .map((val) => val.id)
                    .sort()
                    .join('-');
                return variantValueIds === comboValueIds;
            });

            return {
                id: productVariant?.id ?? null,
                product_id: props.product.id,
                media_id: productVariant?.media_id ?? null,
                unit_quantity: productVariant?.unit_quantity ?? 1,
                min_quantity: productVariant?.min_quantity ?? 1,
                quantity_increment: productVariant?.quantity_increment ?? 1,
                sku:
                    form.value.variant.sku +
                    '-' +
                    combo.map((v) => v.value).join('-'),
                enabled: productVariant?.enabled ?? true,
                length_value: productVariant?.length_value ?? '0.0000',
                length_unit: productVariant?.length_unit ?? 'mm',
                width_value: productVariant?.width_value ?? '0.0000',
                width_unit: productVariant?.width_unit ?? 'mm',
                height_value: productVariant?.height_value ?? '0.0000',
                height_unit: productVariant?.height_unit ?? 'mm',
                weight_value: productVariant?.weight_value ?? '0.0000',
                weight_unit: productVariant?.weight_unit ?? 'mm',
                volume_value: productVariant?.volume_value ?? '0.0000',
                volume_unit: productVariant?.volume_unit ?? 'mm',
                shippable: productVariant?.shippable ?? true,
                stock: productVariant?.stock ?? 0,
                backorder: productVariant?.backorder ?? 0,
                price: productVariant?.price?.amount ?? 0,
                purchasable: productVariant?.purchasable ?? true,
                barcode: productVariant?.barcode ?? null,
                values: combo,
            };
        });
    };

    return {
        generateVariants,
    };
}
