<template>
    <Head :title="`${product.name} - ${brandPartner.name}`" />

    <div class="grocery-product-page">
        <!-- Breadcrumb at the top -->
        <div class="product-breadcrumb-wrapper px-15">
            <Breadcrumb :items="breadcrumbItems" />
        </div>

        <!-- Header with back arrow -->
        <div class="product-header px-15">
            <button type="button" class="back-arrow" @click="goBack">
                <i class="ri-arrow-left-s-line"></i>
            </button>
            <h6 class="header-title">Product Details</h6>
            <Link :href="route('store.brand-partner.cart')" class="cart-icon-link">
                <i class="ri-shopping-cart-2-line"></i>
                <span v-if="cartCount > 0" class="cart-badge">{{ cartCount }}</span>
            </Link>
        </div>

        <!-- Desktop Layout Wrapper -->
        <div class="desktop-product-layout">
            <!-- Main Product Section -->
            <section class="main-product-section">
                <div class="slider-box">
                    <div class="main-product-image" @click="selectedImage = product.image_url">
                        <img
                            :src="selectedImage || product.image_url || '/img/tshirt-placeholder.svg'"
                            :alt="product.name"
                        />
                        <span v-if="discountPercent > 0" class="discount-badge">
                            -{{ discountPercent }}%
                        </span>
                    </div>
                    <!-- Thumbnail Row -->
                    <div class="thumbnail-strip" v-if="product.images && product.images.length > 1">
                        <div
                            v-for="(image, index) in product.images"
                            :key="index"
                            class="thumb-item"
                            :class="{ active: selectedImage === image.url }"
                            @click="selectedImage = image.url"
                        >
                            <img :src="image.url" :alt="`${product.name} ${index + 1}`" />
                        </div>
                    </div>
                </div>

                <!-- Product Container -->
                <div class="product-container px-15">
                    <div class="product-tags" v-if="product.category || product.event">
                        <span class="ptag" v-if="product.category">{{ product.category.name }}</span>
                        <span class="ptag ptag-event" v-if="product.event">{{ product.event.name }}</span>
                    </div>

                    <h4 class="product-title">{{ product.name }}</h4>

                    <div class="product-price-row">
                        <span class="current-price">{{ formatCurrency(product.price) }}</span>
                        <del
                            v-if="product.compare_price && product.compare_price > product.price"
                            class="old-price"
                        >{{ formatCurrency(product.compare_price) }}</del>
                        <span v-if="discountPercent > 0" class="save-badge">Save {{ discountPercent }}%</span>
                    </div>

                    <div class="stock-indicator">
                        <span v-if="currentInStock" class="stock-in">
                            <i class="ri-checkbox-circle-fill"></i> In Stock
                        </span>
                        <span v-else class="stock-out">
                            <i class="ri-close-circle-fill"></i> Out of Stock
                        </span>
                    </div>

                    <p class="short-description" v-if="product.short_description">
                        {{ product.short_description }}
                    </p>

                    <div class="sku-info" v-if="product.sku">
                        <span>SKU: {{ product.sku }}</span>
                    </div>

                    <!-- Variations: Colors -->
                    <div v-if="hasColors" class="variation-section">
                        <div class="variation-label">
                            Color: <strong>{{ selectedColor || 'Select' }}</strong>
                        </div>
                        <div class="color-swatches">
                            <button
                                v-for="color in availableColors"
                                :key="color"
                                type="button"
                                class="color-swatch-btn"
                                :class="{ active: selectedColor === color }"
                                @click="selectColor(color)"
                            >{{ color }}</button>
                        </div>
                    </div>

                    <!-- Variations: Sizes -->
                    <div v-if="hasSizes" class="variation-section">
                        <div class="variation-label">Size:</div>
                        <div class="size-options">
                            <button
                                v-for="size in availableSizes"
                                :key="size"
                                type="button"
                                class="size-btn"
                                :class="{ active: selectedSize === size }"
                                @click="selectSize(size)"
                            >{{ size }}</button>
                        </div>
                    </div>

                    <p v-if="hasVariations && !variationReady" class="variation-hint">
                        Please select{{ hasColors ? ' a color' : '' }}{{ hasColors && hasSizes ? ' and' : '' }}{{ hasSizes ? ' a size' : '' }} to continue.
                    </p>

                    <!-- Desktop Add to Cart -->
                    <div class="desktop-add-section">
                        <div class="qty-box">
                            <button class="qty-btn" @click="decrementQuantity" :disabled="quantity <= 1">
                                <i class="ri-subtract-line"></i>
                            </button>
                            <input type="number" v-model.number="quantity" min="1" class="qty-input" />
                            <button class="qty-btn" @click="incrementQuantity">
                                <i class="ri-add-line"></i>
                            </button>
                        </div>
                        <button
                            class="add-cart-btn"
                            :disabled="!currentInStock || isAddingToCart || !variationReady"
                            @click="showConfirmModal"
                        >
                            <i class="ri-shopping-cart-2-line"></i>
                            <span v-if="isAddingToCart">Adding...</span>
                            <span v-else>Add to Cart</span>
                        </button>
                    </div>
                </div>
            </section>
        </div>

        <!-- Description Section -->
        <section class="section-t-space-3" v-if="product.description">
            <div class="description-box px-15">
                <h5 class="desc-title">Description</h5>
                <div class="accordion accordion-style-1" id="descriptionAccordion">
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="descHeading">
                            <button
                                class="accordion-button"
                                type="button"
                                data-bs-toggle="collapse"
                                data-bs-target="#descCollapse"
                                aria-expanded="true"
                                aria-controls="descCollapse"
                            >Product Details</button>
                        </h2>
                        <div
                            id="descCollapse"
                            class="accordion-collapse collapse show"
                            aria-labelledby="descHeading"
                            data-bs-parent="#descriptionAccordion"
                        >
                            <div class="accordion-body" v-html="product.description"></div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Similar Products Section -->
        <section class="section-t-space-4" v-if="relatedProducts.length > 0">
            <div class="title title-2 px-15">
                <h4>Similar Products</h4>
            </div>
            <div class="similar-grid px-15">
                <div v-for="related in relatedProducts" :key="related.id" class="product-box">
                    <Link :href="route('store.brand-partner.product', related.slug)" class="product-box-link">
                        <div class="product-box-img">
                            <img
                                :src="related.image_url || '/img/tshirt-placeholder.svg'"
                                :alt="related.name"
                            />
                        </div>
                        <div class="product-box-detail">
                            <h5 class="product-box-name">{{ related.name }}</h5>
                            <div class="product-box-price-row">
                                <span class="product-box-price">{{ formatCurrency(related.price) }}</span>
                            </div>
                        </div>
                    </Link>
                    <Link :href="route('store.brand-partner.product', related.slug)" class="add-cart-icon">
                        <i class="ri-add-line"></i>
                    </Link>
                </div>
            </div>
        </section>

        <!-- Bottom Cart Box (Mobile Only) -->
        <div class="product-cart-box">
            <div class="mobile-qty-control">
                <button class="qty-btn-mobile" @click="decrementQuantity" :disabled="quantity <= 1">
                    <i class="ri-subtract-line"></i>
                </button>
                <input type="number" v-model.number="quantity" min="1" class="qty-input-mobile" />
                <button class="qty-btn-mobile" @click="incrementQuantity">
                    <i class="ri-add-line"></i>
                </button>
            </div>
            <button
                class="add-cart-mobile-btn"
                :disabled="!currentInStock || isAddingToCart || !variationReady"
                @click="showConfirmModal"
            >
                <i class="ri-shopping-cart-2-line"></i>
                <span v-if="isAddingToCart">Adding...</span>
                <span v-else>Add to Cart | {{ formatCurrency(product.price * quantity) }}</span>
            </button>
        </div>

        <!-- Confirm Modal -->
        <div class="modal fade" id="addToCartConfirmModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content grocery-modal">
                    <div class="modal-header">
                        <h5 class="modal-title">Confirm Add to Cart</h5>
                        <button type="button" class="btn-close" @click="confirmModal?.hide()"></button>
                    </div>
                    <div class="modal-body">
                        <div class="confirm-product">
                            <img
                                :src="selectedImage || product.image_url || '/img/tshirt-placeholder.svg'"
                                :alt="product.name"
                                class="confirm-img"
                            />
                            <div class="confirm-info">
                                <h6>{{ product.name }}</h6>
                                <span class="confirm-price">{{ formatCurrency(product.price) }}</span>
                            </div>
                        </div>
                        <div v-if="selectedColor || selectedSize" class="confirm-variation">
                            <span v-if="selectedColor">Color: <strong>{{ selectedColor }}</strong></span>
                            <span v-if="selectedSize" class="ms-2">Size: <strong>{{ selectedSize }}</strong></span>
                        </div>
                        <div class="confirm-summary">
                            <div class="summary-row">
                                <span>Quantity</span>
                                <strong>{{ quantity }}</strong>
                            </div>
                            <div class="summary-row total">
                                <span>Total</span>
                                <strong>{{ formatCurrency(product.price * quantity) }}</strong>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn-cancel" @click="confirmModal?.hide()">Cancel</button>
                        <button type="button" class="btn-confirm" @click="addToCart" :disabled="isAddingToCart">
                            <span v-if="isAddingToCart">Adding...</span>
                            <span v-else>Confirm</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, computed, onMounted, onBeforeUnmount } from 'vue';
import { Modal } from 'bootstrap';
import Breadcrumb from '@/components/breadcrumb/layout-breadcrumb.vue';

const props = defineProps({
    brandPartner: Object,
    product: Object,
    relatedProducts: {
        type: Array,
        default: () => [],
    },
    cartCount: {
        type: Number,
        default: 0,
    },
});

const breadcrumbItems = computed(() => [
    { label: 'Shop', link: route('store.brand-partner.shop', props.brandPartner?.slug) },
    { label: props.product.name }
]);

const quantity = ref(1);
const isAddingToCart = ref(false);
const selectedImage = ref(props.product.image_url);

const selectedColor = ref(null);
const selectedSize = ref(null);

const availableColors = computed(() => props.product.colors_array ?? []);
const availableSizes = computed(() => props.product.sizes_array ?? []);
const hasColors = computed(() => availableColors.value.length > 0);
const hasSizes = computed(() => availableSizes.value.length > 0);
const hasVariations = computed(() => hasColors.value || hasSizes.value);

const variationReady = computed(() => {
    if (!hasVariations.value) return true;
    const colorOk = !hasColors.value || selectedColor.value !== null;
    const sizeOk = !hasSizes.value || selectedSize.value !== null;
    return colorOk && sizeOk;
});

const currentInStock = computed(() => props.product.in_stock);

const selectColor = (color) => {
    selectedColor.value = color === selectedColor.value ? null : color;
};

const selectSize = (size) => {
    selectedSize.value = size === selectedSize.value ? null : size;
};

const discountPercent = computed(() => {
    if (!props.product.compare_price || props.product.compare_price <= props.product.price) return 0;
    return Math.round((1 - props.product.price / props.product.compare_price) * 100);
});

const formatCurrency = (amount) => {
    return new Intl.NumberFormat('en-PH', {
        style: 'currency',
        currency: 'PHP',
    }).format(amount / 100);
};

const incrementQuantity = () => quantity.value++;
const decrementQuantity = () => { if (quantity.value > 1) quantity.value--; };

let confirmModal = null;

onMounted(() => {
    const modalEl = document.getElementById('addToCartConfirmModal');
    if (modalEl) confirmModal = new Modal(modalEl);
});

onBeforeUnmount(() => {
    if (confirmModal) {
        confirmModal.dispose();
        confirmModal = null;
    }
});

const goBack = () => {
    if (window.history.length > 1) {
        window.history.back();
    } else {
        router.get(route('store.brand-partner.index'));
    }
};

const showConfirmModal = () => confirmModal?.show();

const addToCart = () => {
    isAddingToCart.value = true;
    router.post(
        route('store.brand-partner.cart.add'),
        {
            product_id: props.product.id,
            quantity: quantity.value,
            color: selectedColor.value,
            size: selectedSize.value,
        },
        {
            preserveScroll: true,
            onSuccess: () => confirmModal?.hide(),
            onFinish: () => { isAddingToCart.value = false; },
        },
    );
};
</script>

<style scoped>
/* ========================
   Theme Variables
   ======================== */
.grocery-product-page {
    font-family: 'Public Sans', sans-serif;
    background: #f7f7f7;
    min-height: 100vh;
    padding-bottom: 100px;
    --grocery-theme: 255, 149, 5, 1;
    --grocery-content: 143, 143, 178;
    --grocery-title: 27, 27, 62;
    --grocery-border: 232, 232, 232;
    --grocery-primary: 254, 175, 24;
    --grocery-light-bg: 247, 247, 247;
    --grocery-rating: 255, 191, 19;
}

/* ========================
   Header
   ======================== */
.product-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding-top: 16px;
    padding-bottom: 12px;
    background: #fff;
    position: sticky;
    top: 0;
    z-index: 100;
    border-bottom: 1px solid rgb(var(--grocery-border));
}

.px-15 {
    padding-left: 15px;
    padding-right: 15px;
}

.back-arrow {
    width: 38px;
    height: 38px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 12px;
    background: #f5f5f5;
    color: rgb(var(--grocery-title));
    text-decoration: none;
    font-size: 22px;
    transition: all 0.2s;
}

.back-arrow:hover {
    background: rgb(var(--grocery-theme));
    color: #fff;
}

.header-title {
    font-size: 16px;
    font-weight: 700;
    color: rgb(var(--grocery-title));
    margin: 0;
    text-transform: uppercase;
}

.cart-icon-link {
    position: relative;
    width: 38px;
    height: 38px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 12px;
    background: #f5f5f5;
    color: rgb(var(--grocery-title));
    text-decoration: none;
    font-size: 20px;
    transition: all 0.2s;
}

.cart-icon-link:hover {
    background: rgb(var(--grocery-theme));
    color: #fff;
}

.cart-badge {
    position: absolute;
    top: -4px;
    right: -4px;
    background: rgb(var(--grocery-theme));
    color: #fff;
    font-size: 10px;
    font-weight: 700;
    width: 18px;
    height: 18px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    line-height: 1;
}

/* Top Breadcrumb */
.product-breadcrumb-wrapper {
    background: #fff;
    padding-top: 12px;
    padding-bottom: 4px;
    border-bottom: 1px solid rgb(var(--grocery-border));
}

.product-breadcrumb-wrapper :deep(.breadcrumb) {
    padding: 0;
    margin: 0;
}

.product-breadcrumb-wrapper :deep(.breadcrumb-item) {
    font-size: 13px;
}

.product-breadcrumb-wrapper :deep(.breadcrumb-home) {
    color: #666;
}

.product-breadcrumb-wrapper :deep(.breadcrumb-home:hover) {
    color: rgb(var(--grocery-theme));
}

.product-breadcrumb-wrapper :deep(.breadcrumb-item a) {
    color: #666;
}

.product-breadcrumb-wrapper :deep(.breadcrumb-item a:hover) {
    color: rgb(var(--grocery-theme));
}

.product-breadcrumb-wrapper :deep(.breadcrumb-item.active) {
    color: rgb(var(--grocery-title));
    font-weight: 600;
}

/* Adjust header spacing */
.product-header {
    border-bottom: 1px solid rgb(var(--grocery-border));
}

/* Desktop adjustments */
@media (min-width: 768px) {
    .product-breadcrumb-wrapper {
        max-width: 1200px;
        margin: 0 auto;
        background: transparent;
        border-bottom: none;
        padding-top: 16px;
        padding-bottom: 8px;
    }
    
    .product-header {
        max-width: 1200px;
        margin: 0 auto;
        border-bottom: none;
        padding-top: 8px;
    }
}


/* ========================
   Main Product Section
   ======================== */
.main-product-section {
    background: #fff;
    margin-bottom: 10px;
}

.slider-box {
    padding: 15px;
    width: 100%;
}

.main-product-image {
    position: relative;
    border-radius: 16px;
    overflow: hidden;
    background: #f8f8f8;
    cursor: pointer;
    border: 1px solid rgb(var(--grocery-border));
    display: flex;
    align-items: center;
    justify-content: center;
    width: 100%;
    aspect-ratio: 1 / 1;
    max-height: 500px;
}

.main-product-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
}

.discount-badge {
    position: absolute;
    top: 12px;
    left: 12px;
    background: #ff4757;
    color: #fff;
    padding: 4px 12px;
    border-radius: 8px;
    font-size: 12px;
    font-weight: 700;
}

.thumbnail-strip {
    display: flex;
    gap: 10px;
    margin-top: 12px;
    overflow-x: auto;
    padding-bottom: 4px;
}

.thumb-item {
    width: 64px;
    height: 64px;
    min-width: 64px;
    border: 2px solid rgb(var(--grocery-border));
    border-radius: 12px;
    overflow: hidden;
    cursor: pointer;
    transition: border-color 0.2s;
}

.thumb-item.active,
.thumb-item:hover {
    border-color: rgb(var(--grocery-theme));
}

.thumb-item img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

/* ========================
   Product Info
   ======================== */
.product-container {
    padding-bottom: 20px;
}

.product-tags {
    display: flex;
    gap: 8px;
    margin-bottom: 10px;
    flex-wrap: wrap;
}

.ptag {
    display: inline-block;
    padding: 3px 12px;
    border-radius: 20px;
    background: #f0f0f0;
    font-size: 12px;
    font-weight: 600;
    color: #777;
}

.ptag-event {
    background: #e8f4f7;
    color: rgb(var(--grocery-theme));
}

.product-title {
    font-size: 20px;
    font-weight: 800;
    color: rgb(var(--grocery-title));
    margin: 0 0 10px;
    line-height: 1.3;
    text-transform: uppercase;
    font-family: 'Public Sans', sans-serif;
}

.product-price-row {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 10px;
    flex-wrap: wrap;
}

.current-price {
    font-size: 22px;
    font-weight: 800;
    color: #FF9505;
}

.old-price {
    font-size: 15px;
    color: #bbb;
}

.save-badge {
    background: rgba(var(--grocery-theme), 0.1);
    color: rgb(var(--grocery-theme));
    padding: 3px 10px;
    border-radius: 6px;
    font-size: 12px;
    font-weight: 700;
}

.stock-indicator {
    margin-bottom: 12px;
}

.stock-in {
    color: #27ae60;
    font-weight: 700;
    font-size: 14px;
    display: inline-flex;
    align-items: center;
    gap: 4px;
}

.stock-out {
    color: #e74c3c;
    font-weight: 700;
    font-size: 14px;
    display: inline-flex;
    align-items: center;
    gap: 4px;
}

.short-description {
    font-size: 14px;
    color: rgb(var(--grocery-content));
    line-height: 1.6;
    margin: 0 0 12px;
}

.sku-info {
    font-size: 13px;
    color: #aaa;
    margin-bottom: 16px;
}

/* ========================
   Variations
   ======================== */
.variation-section {
    margin: 12px 0;
}

.variation-label {
    font-size: 13px;
    color: rgb(var(--grocery-title));
    margin-bottom: 8px;
    font-weight: 600;
}

.color-swatches {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
}

.color-swatch-btn {
    padding: 6px 14px;
    border-radius: 8px;
    border: 1.5px solid rgb(var(--grocery-border));
    background: #fff;
    font-size: 13px;
    font-weight: 600;
    color: rgb(var(--grocery-title));
    cursor: pointer;
    transition: all 0.2s;
}

.color-swatch-btn.active,
.color-swatch-btn:hover {
    background: rgba(255, 149, 5, 1);
    color: #fff;
}

.size-options {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
}

.size-btn {
    padding: 6px 14px;
    border-radius: 8px;
    border: 1.5px solid rgb(var(--grocery-border));
    background: #fff;
    font-size: 13px;
    font-weight: 600;
    color: rgb(var(--grocery-title));
    cursor: pointer;
    transition: all 0.2s;
}

.size-btn.active,
.size-btn:hover {
    background: rgba(255, 149, 5, 1);
    color: #fff;
}

.variation-hint {
    font-size: 12px;
    color: #e74c3c;
    margin: 6px 0 0;
}

.confirm-variation {
    font-size: 13px;
    color: rgb(var(--grocery-content));
    margin-bottom: 10px;
}

/* Desktop Add to Cart - hidden on mobile */
.desktop-add-section {
    display: none;
}

/* ========================
   Description
   ======================== */
.section-t-space-3 {
    padding-top: 10px;
}

.description-box {
    background: #fff;
    border-radius: 16px;
    padding: 20px 15px;
    margin: 0 15px;
    border: 1px solid rgb(var(--grocery-border));
}

.desc-title {
    font-size: 16px;
    font-weight: 700;
    color: rgb(var(--grocery-title));
    margin: 0 0 14px;
}

.accordion-style-1 .accordion-item {
    border: none;
    border-radius: 12px;
    overflow: hidden;
    background: #fafafa;
}

.accordion-style-1 .accordion-button {
    font-size: 14px;
    font-weight: 700;
    color: #FF9505;
    background: #fafafa;
    padding: 14px 16px;
    box-shadow: none;
}

.accordion-style-1 .accordion-button:not(.collapsed) {
    color: #FF9505;
    background: rgba(var(--grocery-theme), 0.05);
}

.accordion-style-1 .accordion-body {
    padding: 0 16px 16px;
    font-size: 14px;
    color: #555;
    line-height: 1.7;
}

/* ========================
   Similar Products
   ======================== */
.section-t-space-4 {
    padding-top: 20px;
    padding-bottom: 10px;
}

.title-2 h4 {
    font-size: 18px;
    font-weight: 700;
    color: rgb(var(--grocery-title));
    margin: 0 0 14px;
}

.similar-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 12px;
}

.product-box {
    background: #fff;
    border-radius: 14px;
    overflow: hidden;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
    border: 1px solid rgb(var(--grocery-border));
    position: relative;
    transition: all 0.2s;
}

.product-box:hover {
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
    transform: translateY(-2px);
}

.product-box-link {
    text-decoration: none;
    display: block;
}

.product-box-img {
    aspect-ratio: 1;
    overflow: hidden;
    background: #f8f8f8;
}

.product-box-img img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s;
}

.product-box:hover .product-box-img img {
    transform: scale(1.05);
}

.product-box-detail {
    padding: 10px 12px;
}

.product-box-name {
    font-size: 13px;
    font-weight: 600;
    color: rgb(var(--grocery-title));
    margin: 0 0 4px;
    line-height: 1.3;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.product-box-price-row {
    display: flex;
    align-items: center;
    gap: 8px;
}

.product-box-price {
    font-size: 15px;
    font-weight: 800;
    color: rgb(var(--grocery-theme));
}

.add-cart-icon {
    position: absolute;
    bottom: 10px;
    right: 10px;
    width: 30px;
    height: 30px;
    background: rgb(var(--grocery-theme));
    color: #fff;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 16px;
    text-decoration: none;
    transition: background 0.2s;
}

.add-cart-icon:hover {
    background: rgba(var(--grocery-theme), 0.8);
    color: #fff;
}

/* ========================
   Mobile Bottom Cart
   ======================== */
.product-cart-box {
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    background: #fff;
    padding: 12px 15px;
    box-shadow: 0 -4px 16px rgba(0, 0, 0, 0.08);
    border-top: 1px solid rgb(var(--grocery-border));
    z-index: 999;
    display: flex;
    align-items: center;
    gap: 12px;
}

.mobile-qty-control {
    display: flex;
    align-items: center;
    background: #f5f5f5;
    border-radius: 12px;
    overflow: hidden;
    border: 1px solid rgb(var(--grocery-border));
}

.qty-btn-mobile {
    width: 40px;
    height: 40px;
    border: none;
    background: none;
    color: rgb(var(--grocery-theme));
    font-size: 18px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
}

.qty-btn-mobile:disabled {
    color: #ccc;
}

.qty-input-mobile {
    width: 40px;
    height: 40px;
    border: none;
    background: none;
    text-align: center;
    font-weight: 700;
    font-size: 15px;
    color: rgb(var(--grocery-title));
    outline: none;
    appearance: textfield;
    -moz-appearance: textfield;
}

.qty-input-mobile::-webkit-outer-spin-button,
.qty-input-mobile::-webkit-inner-spin-button {
    -webkit-appearance: none;
    margin: 0;
}

.add-cart-mobile-btn {
    flex: 1;
    height: 44px;
    background: rgb(var(--grocery-theme));
    color: #fff;
    border: none;
    border-radius: 12px;
    font-size: 14px;
    font-weight: 700;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    transition: background 0.2s;
}

.add-cart-mobile-btn:hover {
    background: rgba(var(--grocery-theme), 0.85);
}

.add-cart-mobile-btn:disabled {
    background: #ddd;
    cursor: not-allowed;
}

.add-cart-mobile-btn i {
    font-size: 18px;
}

/* ========================
   Confirm Modal
   ======================== */
/* ===== Modal - Grocery Styling ===== */
.grocery-modal,
.grocery-modal-content {
    border: none;
    border-radius: 22px;
    overflow: hidden;
    background: #fff;
    box-shadow: 0 20px 45px rgba(0, 0, 0, 0.12);
    font-family: 'Public Sans', sans-serif;
}

.grocery-modal .modal-header,
.grocery-modal-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 18px 22px;
    background: #fff;
    border-bottom: 1px solid #f1f1f1;
}

.grocery-modal .modal-title,
.grocery-modal-title {
    margin: 0;
    font-size: 18px;
    font-weight: 800;
    color: #111;
}

.grocery-modal .modal-body,
.grocery-modal-body {
    padding: 20px;
    background: #fff;
}

.confirm-product,
.modal-product-detail {
    display: flex;
    gap: 16px;
    align-items: flex-start;
    margin-bottom: 18px;
}

.confirm-img,
.modal-product-img {
    width: 90px;
    height: 90px;
    object-fit: cover;
    border-radius: 18px;
    border: 1px solid #f0f0f0;
    background: #f8f8f8;
}

.confirm-info p,
.modal-product-info p {
    margin: 0 0 10px;
    color: #5a5a5a;
    font-size: 13px;
    line-height: 1.5;
}

.confirm-price,
.modal-product-price {
    margin: 0;
    font-size: 18px;
    font-weight: 800;
    color: #e84b0f;
}

.old-price {
    display: block;
    margin-top: 8px;
    font-size: 13px;
    color: #999;
}

.confirm-variation {
    font-size: 13px;
    color: rgb(var(--grocery-content));
    margin-bottom: 10px;
}

.confirm-summary,
.qty-section-title {
    margin-top: 0;
    padding-top: 0;
}

.qty-section-title {
    margin-bottom: 10px;
    font-size: 14px;
    font-weight: 700;
    color: rgb(var(--grocery-title));
}

.summary-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 6px 0;
    font-size: 14px;
    color: rgb(var(--grocery-content));
}

.summary-row.total {
    border-top: 1px solid rgb(var(--grocery-border));
    padding-top: 12px;
    margin-top: 6px;
    color: #ec4e1f;
    font-size: 16px;
}

.summary-row.total strong {
    color: #ec4e1f;
}

.qty-selector,
.confirm-summary {
    padding-bottom: 10px;
}

.qty-box,
.input-group {
    display: flex;
    align-items: center;
}

.qty-box {
    background: #f7f7f7;
    border-radius: 18px;
    overflow: hidden;
    border: 1px solid #e4e4e4;
}

.qty-btn {
    width: 44px;
    height: 44px;
    border: none;
    background: #fff;
    color: #111;
    font-size: 18px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: background 0.2s;
}

.qty-btn:hover:not(:disabled) {
    background: #f0f0f0;
}

.qty-btn:disabled {
    color: #ccc;
}

.qty-input {
    width: 68px;
    border: none;
    text-align: center;
    background: transparent;
    font-size: 15px;
    font-weight: 700;
    color: #111;
    padding: 0 12px;
    outline: none;
}

.grocery-modal-footer,
.grocery-modal .modal-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 14px;
    padding: 18px 22px;
    background: #fafafa;
    border-top: 1px solid #f1f1f1;
}

.modal-footer-info h5,
.modal-footer-info h4,
.confirm-summary h5,
.confirm-summary h4 {
    margin: 0;
}

.modal-footer-info h5 {
    font-size: 12px;
    color: #5a5a5a;
    font-weight: 500;
}

.modal-footer-info h4 {
    font-size: 18px;
    font-weight: 800;
    color: #ff9505;
}

.cart-bar-btn,
.btn-confirm {
    min-width: 150px;
    height: 44px;
    border-radius: 16px;
    border: none;
    background-color: #ec4e1f;
    color: #fff;
    font-size: 13px;
    font-weight: 800;
    text-transform: uppercase;
    cursor: pointer;
}

.cart-bar-btn:hover:not(:disabled),
.btn-confirm:hover:not(:disabled) {
    background: rgba(var(--grocery-theme), 0.85);
}

.cart-bar-btn:disabled,
.btn-confirm:disabled {
    opacity: 0.75;
    cursor: not-allowed;
}

.btn-cancel {
    min-width: 150px;
    height: 44px;
    border-radius: 16px;
    border: 1.5px solid rgb(var(--grocery-border));
    background: #fff;
    color: rgb(var(--grocery-title));
    font-size: 14px;
    font-weight: 700;
    cursor: pointer;
}

.btn-cancel:hover {
    background: #f7f7f7;
}

/* ========================
   Desktop (768px+)
   ======================== */
@media (min-width: 768px) {
    .grocery-product-page {
        padding-bottom: 40px;
        padding-top: 120px;
    }

    .product-header {
        max-width: 1200px;
        margin: 0 auto;
        border-bottom: none;
        padding-left: 20px;
        padding-right: 20px;
    }

    .desktop-product-layout {
        max-width: 1200px;
        margin: 0 auto;
    }

    .main-product-section {
        display: flex;
        gap: 40px;
        padding: 32px;
        border-radius: 16px;
        margin: 0 20px;
        border: 1px solid rgb(var(--grocery-border));
    }

    .slider-box {
        flex: 0 0 50%;
        max-width: 50%;
        padding: 0;
    }

    .main-product-image img {
        max-height: none;
        height: 100%;
    }

    .product-container {
        flex: 1;
        padding: 0;
        display: flex;
        flex-direction: column;
    }

    .desktop-add-section {
        display: flex;
        gap: 12px;
        align-items: center;
        margin-top: auto;
        padding-top: 20px;
    }

    .qty-box {
        display: flex;
        align-items: center;
        background: #f5f5f5;
        border-radius: 12px;
        overflow: hidden;
        border: 1px solid rgb(var(--grocery-border));
    }

    .qty-btn {
        width: 44px;
        height: 44px;
        border: none;
        background: none;
        color: #005523;
        font-size: 18px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: background 0.2s;
    }

    .qty-btn:hover {
        background: rgba(var(--grocery-theme), 0.08);
    }

    .qty-btn:disabled {
        color: #ccc;
    }

    .qty-input {
        width: 46px;
        height: 44px;
        border: none;
        background: none;
        text-align: center;
        font-weight: 700;
        font-size: 15px;
        color: rgb(var(--grocery-title));
        outline: none;
        appearance: textfield;
        -moz-appearance: textfield;
    }

    .qty-input::-webkit-outer-spin-button,
    .qty-input::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }

    .add-cart-btn {
        flex: 1;
        height: 46px;
        background: #005523;
        color: #fff;
        border: none;
        border-radius: 2px;
        font-size: 15px;
        font-weight: 700;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        transition: background 0.2s;
        font-family: 'Public Sans', sans-serif;
    }

    .add-cart-btn:hover {
        background: rgba(255, 149, 5, 1);
    }

    .add-cart-btn:disabled {
        background: #ddd;
        cursor: not-allowed;
    }

    .add-cart-btn i {
        font-size: 18px;
    }

    .product-cart-box {
        display: none;
    }

    .section-t-space-3,
    .section-t-space-4 {
        max-width: 1200px;
        margin-left: auto;
        margin-right: auto;
        padding-left: 20px;
        padding-right: 20px;
    }

    .description-box {
        margin: 0;
        border-radius: 16px;
    }

    .similar-grid {
        grid-template-columns: repeat(4, 1fr);
        gap: 16px;
    }

    .product-title {
        font-size: 24px;
    }

    .current-price {
        font-size: 26px;
    }
}

/* ========================
   Mobile (max 767px)
   ======================== */
@media (max-width: 767px) {
    .grocery-product-page {
        padding-bottom: 80px;
    }

    .main-product-image img {
        max-height: none;
        height: 100%;
    }

    .thumb-item {
        width: 56px;
        height: 56px;
        min-width: 56px;
    }

    .product-title {
        font-size: 18px;
    }

    .current-price {
        font-size: 20px;
    }

    .description-box {
        margin: 0 10px;
        border-radius: 14px;
    }

    .similar-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 374px) {
    .add-cart-mobile-btn {
        font-size: 12px;
    }

    .qty-btn-mobile {
        width: 34px;
        height: 34px;
    }

    .qty-input-mobile {
        width: 34px;
        height: 34px;
    }
}
</style>