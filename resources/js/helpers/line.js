import axios from 'axios';
import { computed, ref, watch } from 'vue';

export function useQuoteVariants(form = null) {
    // Reactive state
    const products = ref([]);
    const variants = ref([]);
    const selectedVariants = ref([]);
    const variantQuantities = ref({});
    const variantPrices = ref({});
    const applyPriceAll = ref(true);
    const sharedPrice = ref(0);

    const currentVariantForNames = ref(null);
    const variantNames = ref({});
    const tempNames = ref([]);
    const showSetNamesOverlay = ref(false);

    /** Fetch products for a category */
    const fetchProducts = async (categoryId) => {
        if (!categoryId) return;
        const res = await axios.get(`/admin/category/${categoryId}/products`);
        products.value = res.data;
    };

    /** Fetch variants for a product */
    const fetchVariants = async (productId) => {
        if (!productId) return;
        const res = await axios.get(`/admin/product/${productId}/variants`);
        variants.value = res.data;
    };

    /** Computed selected variant objects */
    const selectedVariantObjects = computed(() =>
        variants.value.filter((v) => selectedVariants.value.includes(v.id)),
    );

    /** Watch selected variants to initialize quantity & price */
    watch(selectedVariants, (newVal) => {
        newVal.forEach((id) => {
            if (variantQuantities.value[id] == null)
                variantQuantities.value[id] = 1;
            if (variantPrices.value[id] == null)
                variantPrices.value[id] = sharedPrice.value;
        });
    });

    /** Apply shared price to all selected variants */
    const applyToAllPrice = () => {
        if (applyPriceAll.value) {
            selectedVariants.value.forEach((id) => {
                variantPrices.value[id] = sharedPrice.value;
            });
        }
    };

    /** Clear all selected variants */
    const clearAllVariants = () => (selectedVariants.value = []);

    /** Check if a variant needs "Set Names" button */
    const needsSetNames = (desc) => desc?.toUpperCase().includes('WITH NAME');

    /** Open names overlay */
    const openSetNamesOverlay = (variant) => {
        currentVariantForNames.value = variant;
        const qty = variantQuantities.value[variant.id] || 1;

        tempNames.value = variantNames.value[variant.id]?.length
            ? [...variantNames.value[variant.id]]
            : Array(qty).fill('');

        if (tempNames.value.length < qty)
            tempNames.value.push(
                ...Array(qty - tempNames.value.length).fill(''),
            );
        if (tempNames.value.length > qty)
            tempNames.value = tempNames.value.slice(0, qty);

        showSetNamesOverlay.value = true;
    };

    /** Close names overlay */
    const closeSetNamesOverlay = () => (showSetNamesOverlay.value = false);

    /** Save names from overlay */
    const saveNames = () => {
        const id = currentVariantForNames.value.id;
        variantNames.value[id] = [...tempNames.value];
        showSetNamesOverlay.value = false;
    };

    /** Computed price error for validation */
    const PriceError = computed(() => {
        if (!form) return null;
        const errors = Object.keys(form.errors)
            .filter((key) => key.includes('purchase_price'))
            .map((k) => form.errors[k]);
        return errors.length ? errors[0] : null;
    });

    return {
        products,
        variants,
        selectedVariants,
        variantQuantities,
        variantPrices,
        applyPriceAll,
        sharedPrice,
        selectedVariantObjects,
        fetchProducts,
        fetchVariants,
        applyToAllPrice,
        clearAllVariants,
        needsSetNames,
        currentVariantForNames,
        variantNames,
        tempNames,
        showSetNamesOverlay,
        openSetNamesOverlay,
        closeSetNamesOverlay,
        saveNames,
        PriceError,
    };
}
