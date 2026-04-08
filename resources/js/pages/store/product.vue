<template>
    <Head :title="`${product.name} - ${brandPartner.name}`" />

    <div class="grocery-product-page">
        <!-- Header with back arrow -->
        <div class="product-header px-15">
            <Link :href="route('store.brand-partner.index')" class="back-arrow">
                <i class="ri-arrow-left-s-line"></i>
            </Link>
            <h6 class="header-title">Product Details</h6>
            <Link
                :href="route('store.brand-partner.cart')"
                class="cart-icon-link"
            >
                <i class="ri-shopping-cart-2-line"></i>
                <span v-if="cartCount > 0" class="cart-badge">{{
                    cartCount
                }}</span>
            </Link>
        </div>

        <!-- Desktop Layout Wrapper -->
        <div class="desktop-product-layout">
            <!-- Main Product Section -->
            <section class="main-product-section">
                <div class="slider-box">
                    <div
                        class="main-product-image"
                        @click="selectedImage = product.image_url"
                    >
                        <img
                            :src="
                                selectedImage ||
                                product.image_url ||
                                '/img/tshirt-placeholder.svg'
                            "
                            :alt="product.name"
                        />
                        <span v-if="discountPercent > 0" class="discount-badge">
                            -{{ discountPercent }}%
                        </span>
                    </div>
                    <!-- Thumbnail Row -->
                    <div
                        class="thumbnail-strip"
                        v-if="product.images && product.images.length > 1"
                    >
                        <div
                            v-for="(image, index) in product.images"
                            :key="index"
                            class="thumb-item"
                            :class="{ active: selectedImage === image.url }"
                            @click="selectedImage = image.url"
                        >
                            <img
                                :src="image.url"
                                :alt="`${product.name} ${index + 1}`"
                            />
                        </div>
                    </div>
                </div>

                <!-- Product Container -->
                <div class="product-container px-15">
                    <div
                        class="product-tags"
                        v-if="product.category || product.event"
                    >
                        <span class="ptag" v-if="product.category">{{
                            product.category.name
                        }}</span>
                        <span class="ptag ptag-event" v-if="product.event">{{
                            product.event.name
                        }}</span>
                    </div>

                    <h4 class="product-title">{{ product.name }}</h4>

                    <div class="product-price-row">
                        <span class="current-price">{{
                            formatCurrency(product.price)
                        }}</span>
                        <del
                            v-if="
                                product.compare_price &&
                                product.compare_price > product.price
                            "
                            class="old-price"
                        >
                            {{ formatCurrency(product.compare_price) }}
                        </del>
                        <span v-if="discountPercent > 0" class="save-badge"
                            >Save {{ discountPercent }}%</span
                        >
                    </div>

                    <div class="stock-indicator">
                        <span v-if="currentInStock" class="stock-in">
                            <i class="ri-checkbox-circle-fill"></i> In Stock
                        </span>
                        <span v-else class="stock-out">
                            <i class="ri-close-circle-fill"></i> Out of Stock
                        </span>
                    </div>

                    <p
                        class="short-description"
                        v-if="product.short_description"
                    >
                        {{ product.short_description }}
                    </p>

                    <div class="sku-info" v-if="product.sku">
                        <span>SKU: {{ product.sku }}</span>
                    </div>

                    <!-- Variations: Colors -->
                    <div v-if="hasColors" class="variation-section">
                        <div class="variation-label">
                            Color:
                            <strong>{{ selectedColor || 'Select' }}</strong>
                        </div>
                        <div class="color-swatches">
                            <button
                                v-for="color in availableColors"
                                :key="color"
                                type="button"
                                class="color-swatch-btn"
                                :class="{ active: selectedColor === color }"
                                @click="selectColor(color)"
                            >
                                {{ color }}
                            </button>
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
                            >
                                {{ size }}
                            </button>
                        </div>
                    </div>

                    <p
                        v-if="hasVariations && !variationReady"
                        class="variation-hint"
                    >
                        Please select{{ hasColors ? ' a color' : ''
                        }}{{ hasColors && hasSizes ? ' and' : ''
                        }}{{ hasSizes ? ' a size' : '' }} to continue.
                    </p>

                    <!-- Desktop Add to Cart (hidden on mobile) -->
                    <div class="desktop-add-section">
                        <div class="qty-box">
                            <button
                                class="qty-btn"
                                @click="decrementQuantity"
                                :disabled="quantity <= 1"
                            >
                                <i class="ri-subtract-line"></i>
                            </button>
                            <input
                                type="number"
                                v-model.number="quantity"
                                min="1"
                                class="qty-input"
                            />
                            <button class="qty-btn" @click="incrementQuantity">
                                <i class="ri-add-line"></i>
                            </button>
                        </div>
                        <button
                            class="add-cart-btn"
                            :disabled="
                                !currentInStock ||
                                isAddingToCart ||
                                !variationReady
                            "
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
                <div
                    class="accordion accordion-style-1"
                    id="descriptionAccordion"
                >
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="descHeading">
                            <button
                                class="accordion-button"
                                type="button"
                                data-bs-toggle="collapse"
                                data-bs-target="#descCollapse"
                                aria-expanded="true"
                                aria-controls="descCollapse"
                            >
                                Product Details
                            </button>
                        </h2>
                        <div
                            id="descCollapse"
                            class="accordion-collapse collapse show"
                            aria-labelledby="descHeading"
                            data-bs-parent="#descriptionAccordion"
                        >
                            <div
                                class="accordion-body"
                                v-html="product.description"
                            ></div>
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
                <div
                    v-for="related in relatedProducts"
                    :key="related.id"
                    class="product-box"
                >
                    <Link
                        :href="
                            route('store.brand-partner.product', related.slug)
                        "
                        class="product-box-link"
                    >
                        <div class="product-box-img">
                            <img
                                :src="
                                    related.image_url ||
                                    '/img/tshirt-placeholder.svg'
                                "
                                :alt="related.name"
                            />
                        </div>
                        <div class="product-box-detail">
                            <h5 class="product-box-name">{{ related.name }}</h5>
                            <div class="product-box-price-row">
                                <span class="product-box-price">{{
                                    formatCurrency(related.price)
                                }}</span>
                            </div>
                        </div>
                    </Link>
                    <Link
                        :href="
                            route('store.brand-partner.product', related.slug)
                        "
                        class="add-cart-icon"
                    >
                        <i class="ri-add-line"></i>
                    </Link>
                </div>
            </div>
        </section>

        <!-- Bottom Cart Box (Mobile Only) -->
        <div class="product-cart-box">
            <div class="mobile-qty-control">
                <button
                    class="qty-btn-mobile"
                    @click="decrementQuantity"
                    :disabled="quantity <= 1"
                >
                    <i class="ri-subtract-line"></i>
                </button>
                <input
                    type="number"
                    v-model.number="quantity"
                    min="1"
                    class="qty-input-mobile"
                />
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
                <span v-else
                    >Add to Cart |
                    {{ formatCurrency(product.price * quantity) }}</span
                >
            </button>
        </div>

        <!-- Confirm Modal -->
        <div
            class="modal fade"
            id="addToCartConfirmModal"
            tabindex="-1"
            aria-hidden="true"
        >
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content grocery-modal">
                    <div class="modal-header">
                        <h5 class="modal-title">Confirm Add to Cart</h5>
                        <button
                            type="button"
                            class="btn-close"
                            @click="confirmModal?.hide()"
                        ></button>
                    </div>
                    <div class="modal-body">
                        <div class="confirm-product">
                            <img
                                :src="
                                    selectedImage ||
                                    product.image_url ||
                                    '/img/tshirt-placeholder.svg'
                                "
                                :alt="product.name"
                                class="confirm-img"
                            />
                            <div class="confirm-info">
                                <h6>{{ product.name }}</h6>
                                <span class="confirm-price">{{
                                    formatCurrency(product.price)
                                }}</span>
                            </div>
                        </div>
                        <div
                            v-if="selectedColor || selectedSize"
                            class="confirm-variation"
                        >
                            <span v-if="selectedColor"
                                >Color:
                                <strong>{{ selectedColor }}</strong></span
                            >
                            <span v-if="selectedSize" class="ms-2"
                                >Size: <strong>{{ selectedSize }}</strong></span
                            >
                        </div>
                        <div class="confirm-summary">
                            <div class="summary-row">
                                <span>Quantity</span>
                                <strong>{{ quantity }}</strong>
                            </div>
                            <div class="summary-row total">
                                <span>Total</span>
                                <strong>{{
                                    formatCurrency(product.price * quantity)
                                }}</strong>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button
                            type="button"
                            class="btn-cancel"
                            @click="confirmModal?.hide()"
                        >
                            Cancel
                        </button>
                        <button
                            type="button"
                            class="btn-confirm"
                            @click="addToCart"
                            :disabled="isAddingToCart"
                        >
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

const quantity = ref(1);
const isAddingToCart = ref(false);
const selectedImage = ref(props.product.image_url);

// Variations
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
    if (
        !props.product.compare_price ||
        props.product.compare_price <= props.product.price
    )
        return 0;
    return Math.round(
        (1 - props.product.price / props.product.compare_price) * 100,
    );
});

const formatCurrency = (amount) => {
    return new Intl.NumberFormat('en-PH', {
        style: 'currency',
        currency: 'PHP',
    }).format(amount / 100);
};

const incrementQuantity = () => quantity.value++;
const decrementQuantity = () => {
    if (quantity.value > 1) quantity.value--;
};

let confirmModal = null;

onMounted(() => {
    const modalEl = document.getElementById('addToCartConfirmModal');
    if (modalEl) {
        confirmModal = new Modal(modalEl);
    }
});

onBeforeUnmount(() => {
    if (confirmModal) {
        confirmModal.dispose();
        confirmModal = null;
    }
});

const showConfirmModal = () => {
    confirmModal?.show();
};

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
            onSuccess: () => {
                confirmModal?.hide();
            },
            onFinish: () => {
                isAddingToCart.value = false;
            },
        },
    );
};
</script>

<style scoped>
/* ========================
   Variation Selectors
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
    border-color: rgb(var(--grocery-theme));
    background: rgb(var(--grocery-theme));
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
    border-color: rgb(var(--grocery-theme));
    background: rgb(var(--grocery-theme));
    color: #fff;
}
.variation-hint {
    font-size: 12px;
    color: #e57373;
    margin: 6px 0 0;
}
.confirm-variation {
    font-size: 13px;
    color: rgb(var(--grocery-content));
    margin-bottom: 10px;
}

/* ========================
   Grocery Product Page
   ======================== */
.grocery-product-page {
    font-family: 'Public Sans', sans-serif;
    background: #f9f9f9;
    min-height: 100vh;
    padding-bottom: 100px;
    /* Grocery Theme Color Variables */
    --grocery-theme: 60, 133, 153; /* Main teal/cyan color: rgb(60, 133, 153) */
    --grocery-content: 143, 143, 178; /* Light gray-blue content text */
    --grocery-title: 27, 27, 62; /* Dark blue-gray for titles */
    --grocery-border: 232, 232, 232; /* Light gray borders */
    --grocery-primary: 254, 175, 24; /* Yellow/orange accent */
    --grocery-light-bg: 247, 247, 247; /* Light gray background */
    --grocery-rating: 255, 191, 19; /* Gold/yellow for ratings */
}

/* Header */
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

.product-header .px-15 {
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

/* Utility */
.px-15 {
    padding-left: 15px;
    padding-right: 15px;
}

/* Main Product Section */
.main-product-section {
    background: #fff;
    margin-bottom: 10px;
}

.slider-box {
    padding: 15px;
}

.main-product-image {
    position: relative;
    border-radius: 16px;
    overflow: hidden;
    background: #f8f8f8;
    cursor: pointer;
}

.main-product-image img {
    width: 100%;
    max-height: 400px;
    object-fit: contain;
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
    border: 2px solid #eee;
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

/* Product Container */
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
    background: rgb(var(--grocery-border));
    font-size: 12px;
    font-weight: 600;
    color: #777;
}

.ptag-event {
    background: #fff5ec;
    color: rgb(var(--grocery-theme));
}

.product-title {
    font-size: 20px;
    font-weight: 800;
    color: rgb(var(--grocery-title));
    margin: 0 0 10px;
    line-height: 1.3;
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
    color: rgb(var(--grocery-theme));
}

.old-price {
    font-size: 15px;
    color: #bbb;
}

.save-badge {
    background: #fff0e6;
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
    color: #2ed573;
    font-weight: 700;
    font-size: 14px;
    display: inline-flex;
    align-items: center;
    gap: 4px;
}

.stock-out {
    color: #ff4757;
    font-weight: 700;
    font-size: 14px;
    display: inline-flex;
    align-items: center;
    gap: 4px;
}

.short-description {
    font-size: 14px;
    color: #777;
    line-height: 1.6;
    margin: 0 0 12px;
}

.sku-info {
    font-size: 13px;
    color: #aaa;
    margin-bottom: 16px;
}

/* Desktop Add-to-Cart Section */
.desktop-add-section {
    display: none;
}

/* Description Section */
.section-t-space-3 {
    padding-top: 10px;
}

.description-box {
    background: #fff;
    border-radius: 16px;
    padding: 20px 15px;
    margin: 0 15px;
}

.desc-title {
    font-size: 16px;
    font-weight: 700;
    color: rgb(var(--grocery-title));
    margin: 0 0 14px;
}

/* Accordion Style 1 */
.accordion-style-1 .accordion-item {
    border: none;
    border-radius: 12px;
    overflow: hidden;
    background: #fafafa;
}

.accordion-style-1 .accordion-button {
    font-size: 14px;
    font-weight: 700;
    color: #333;
    background: #fafafa;
    padding: 14px 16px;
    box-shadow: none;
}

.accordion-style-1 .accordion-button:not(.collapsed) {
    color: rgb(var(--grocery-theme));
    background: #fff8f2;
}

.accordion-style-1 .accordion-button::after {
    background-size: 16px;
}

.accordion-style-1 .accordion-body {
    padding: 0 16px 16px;
    font-size: 14px;
    color: #555;
    line-height: 1.7;
}

/* Similar Products Section */
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
    background: #e67a1f;
    color: #fff;
}

/* Bottom Cart Box (Mobile) */
.product-cart-box {
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    background: #fff;
    padding: 12px 15px;
    box-shadow: 0 -4px 16px rgba(0, 0, 0, 0.08);
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
    color: #333;
    outline: none;
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
    background: #e67a1f;
}

.add-cart-mobile-btn:disabled {
    background: #ddd;
    cursor: not-allowed;
}

.add-cart-mobile-btn i {
    font-size: 18px;
}

/* Confirm Modal - Grocery Style */
.grocery-modal {
    border: none;
    border-radius: 20px;
    overflow: hidden;
    font-family: 'Public Sans', sans-serif;
}

.grocery-modal .modal-header {
    border-bottom: 1px solid rgb(var(--grocery-border));
    padding: 16px 20px;
}

.grocery-modal .modal-title {
    font-weight: 700;
    font-size: 16px;
    color: rgb(var(--grocery-title));
}

.grocery-modal .modal-body {
    padding: 20px;
}

.grocery-modal .modal-footer {
    border-top: 1px solid rgb(var(--grocery-border));
    padding: 14px 20px;
    gap: 10px;
    display: flex;
}

.confirm-product {
    display: flex;
    gap: 14px;
    align-items: center;
}

.confirm-img {
    width: 70px;
    height: 70px;
    object-fit: cover;
    border-radius: 12px;
    background: #f8f8f8;
}

.confirm-info h6 {
    font-weight: 700;
    margin: 0 0 4px;
    font-size: 15px;
    color: rgb(var(--grocery-title));
}

.confirm-price {
    font-weight: 800;
    color: rgb(var(--grocery-theme));
    font-size: 15px;
}

.confirm-summary {
    margin-top: 16px;
    padding-top: 16px;
    border-top: 1px solid #f5f5f5;
}

.summary-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 6px 0;
    font-size: 14px;
    color: #777;
}

.summary-row.total {
    border-top: 1px solid rgb(var(--grocery-border));
    padding-top: 12px;
    margin-top: 6px;
    color: rgb(var(--grocery-title));
    font-size: 16px;
}

.summary-row.total strong {
    color: rgb(var(--grocery-theme));
}

.btn-cancel {
    flex: 1;
    height: 44px;
    border: 1.5px solid #e0e0e0;
    background: #fff;
    color: #555;
    border-radius: 12px;
    font-size: 14px;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.2s;
}

.btn-cancel:hover {
    border-color: #ccc;
    background: #f9f9f9;
}

.btn-confirm {
    flex: 1;
    height: 44px;
    background: rgb(var(--grocery-theme));
    color: #fff;
    border: none;
    border-radius: 12px;
    font-size: 14px;
    font-weight: 700;
    cursor: pointer;
    transition: background 0.2s;
}

.btn-confirm:hover {
    background: #e67a1f;
}

.btn-confirm:disabled {
    background: #ddd;
    cursor: not-allowed;
}

/* ========================
   Desktop Layout (768px+)
   ======================== */
@media (min-width: 768px) {
    .grocery-product-page {
        padding-bottom: 40px;
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
        padding: 20px;
        border-radius: 16px;
        margin: 0 20px;
    }

    .slider-box {
        flex: 0 0 50%;
        max-width: 50%;
        padding: 0;
    }

    .main-product-image img {
        max-height: 500px;
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
    }

    .qty-btn {
        width: 44px;
        height: 44px;
        border: none;
        background: none;
        color: rgb(var(--grocery-theme));
        font-size: 18px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: background 0.2s;
    }

    .qty-btn:hover {
        background: #fff0e6;
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
        color: #333;
        outline: none;
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
        background: rgb(var(--grocery-theme));
        color: #fff;
        border: none;
        border-radius: 12px;
        font-size: 15px;
        font-weight: 700;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        transition: background 0.2s;
    }

    .add-cart-btn:hover {
        background: #e67a1f;
    }

    .add-cart-btn:disabled {
        background: #ddd;
        cursor: not-allowed;
    }

    .add-cart-btn i {
        font-size: 18px;
    }

    /* Hide mobile bottom cart box on desktop */
    .product-cart-box {
        display: none;
    }

    /* Sections layout */
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

/* Large Screens */
@media (min-width: 1024px) {
    .similar-grid {
        grid-template-columns: repeat(4, 1fr);
    }
}

/* Small screens (up to 767px) */
@media (max-width: 767px) {
    .grocery-product-page {
        padding-bottom: 80px;
    }

    .product-header {
        position: sticky;
        top: 0;
    }

    .main-product-image img {
        max-height: 320px;
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

/* Extra small screens */
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
