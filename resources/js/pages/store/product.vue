<template>
    <Head :title="`${product.name} - ${brandPartner.name}`" />

    <div class="product-page">
        <!-- Breadcrumb -->
        <div class="breadcrumb-bar">
            <div class="container-wrap">
                <Breadcrumb :items="breadcrumbItems" />
            </div>
        </div>

        <!-- Main Product Section -->
        <div class="container-wrap">
            <div class="product-main-grid">
                <!-- LEFT: Images -->
                <div class="image-col">
                    <div class="main-image-wrap">
                        <img
                            :src="selectedImage || product.image_url || '/img/tshirt-placeholder.svg'"
                            :alt="product.name"
                            class="main-img"
                        />
                        <span v-if="discountPercent > 0" class="sale-badge">SALE</span>
                    </div>
                    <div class="thumbnail-row" v-if="product.images && product.images.length > 1">
                        <div
                            v-for="(image, index) in product.images"
                            :key="index"
                            class="thumb"
                            :class="{ active: selectedImage === image.url }"
                            @click="selectedImage = image.url"
                        >
                            <img :src="image.url" :alt="`${product.name} ${index + 1}`" />
                        </div>
                    </div>
                </div>

                <!-- RIGHT: Info -->
                <div class="info-col">
                    <div class="sku-line" v-if="product.sku">SKU CODE: {{ product.sku }}</div>

                    <h1 class="product-name">{{ product.name }}</h1>

                    <div class="collection-tag" v-if="product.category">
                        {{ product.category.name }}
                    </div>

                    <div class="price-row">
                        <span class="price-current">{{ formatCurrency(product.price) }}</span>
                        <del v-if="product.compare_price && product.compare_price > product.price" class="price-old">
                            {{ formatCurrency(product.compare_price) }}
                        </del>
                        <span v-if="discountPercent > 0" class="price-save-badge">Save {{ discountPercent }}%</span>
                    </div>

                    <div class="stock-row">
                        <span v-if="currentInStock" class="in-stock">✓ In Stock</span>
                        <span v-else class="out-stock">✕ Out of Stock</span>
                    </div>

                    <!-- Garment / Color Variations -->
                    <div v-if="hasColors" class="variation-block">
                        <div class="variation-heading">Color</div>
                        <div class="pill-group">
                            <button
                                v-for="color in availableColors"
                                :key="color"
                                type="button"
                                class="pill-btn"
                                :class="{ active: selectedColor === color }"
                                @click="selectColor(color)"
                            >{{ color }}</button>
                        </div>
                    </div>

                    <!-- Size Variations -->
                    <div v-if="hasSizes" class="variation-block">
                        <div class="variation-heading">SIZE</div>
                        <div class="pill-group">
                            <button
                                v-for="size in availableSizes"
                                :key="size"
                                type="button"
                                class="pill-btn"
                                :class="{ active: selectedSize === size }"
                                @click="selectSize(size)"
                            >{{ size }}</button>
                        </div>
                    </div>

                    <p v-if="hasVariations && !variationReady" class="variation-hint">
                        Please select{{ hasColors ? ' a garment' : '' }}{{ hasColors && hasSizes ? ' and' : '' }}{{ hasSizes ? ' a size' : '' }} to continue.
                    </p>

                    <!-- Quantity -->
                    <div class="variation-block">
                        <div class="variation-heading">QUANTITY</div>
                        <div class="qty-row">
                            <div class="qty-control">
                                <button class="qty-btn" @click="decrementQuantity" :disabled="quantity <= 1">−</button>
                                <input type="number" v-model.number="quantity" min="1" class="qty-input" />
                                <button class="qty-btn" @click="incrementQuantity">+</button>
                            </div>
                            <Link :href="route('store.brand-partner.cart')" class="cart-icon-btn">
                                <i class="ri-shopping-cart-2-line"></i>
                                <span v-if="cartCount > 0" class="cart-dot">{{ cartCount }}</span>
                            </Link>
                        </div>
                    </div>

                    <!-- Add to Cart -->
                    <div class="cta-row">
                        <button
                            class="add-to-cart-btn"
                            :disabled="!currentInStock || isAddingToCart || !variationReady"
                            @click="showConfirmModal"
                        >
                            <span v-if="isAddingToCart">ADDING...</span>
                            <span v-else-if="!currentInStock">OUT OF STOCK</span>
                            <span v-else>ADD TO CART</span>
                        </button>
                        <button
                            class="wishlist-btn"
                            title="Save to wishlist"
                            @click="toggleWishlist(product.id)"
                        >
                            <i
                                :class="
                                    wishlistedIds.includes(product.id)
                                        ? 'ri-heart-fill text-danger'
                                        : 'ri-heart-line'
                                "
                            ></i>
                        </button>
                    </div>

                    <!-- Trust Badges -->
                    <ul class="trust-list">
                        <li><i class="ri-truck-line"></i> Complimentary delivery</li>
                        <li><i class="ri-shield-check-line"></i> 1-year warranty</li>
                        <li><i class="ri-leaf-line"></i> Ethically and sustainably made</li>
                        <li><i class="ri-heart-pulse-line"></i> Safe for sensitive skin</li>
                        <li><i class="ri-earth-line"></i> Carbon-neutral shipping</li>
                    </ul>
                </div>
            </div>

            <!-- Description Tabs -->
            <div class="tabs-section" v-if="product.description">
                <div class="tab-bar">
                    <button
                        class="tab-btn"
                        :class="{ active: activeTab === 'description' }"
                        @click="activeTab = 'description'"
                    >DESCRIPTION</button>
                    <button
                        class="tab-btn"
                        :class="{ active: activeTab === 'shipping' }"
                        @click="activeTab = 'shipping'"
                    >SHIPPING INFORMATION</button>
                    <button
                        class="tab-btn"
                        :class="{ active: activeTab === 'style' }"
                        @click="activeTab = 'style'"
                    >STYLE GUIDE</button>
                </div>

                <div class="tab-content-area">
                    <div v-if="activeTab === 'description'" class="tab-panel">
                        <div class="tab-panel-inner">
                            <div class="tab-panel-left">
                                <h2 class="tab-section-title">Description</h2>
                            </div>
                            <div class="tab-panel-right" v-html="product.description"></div>
                        </div>
                    </div>
                    <div v-if="activeTab === 'shipping'" class="tab-panel">
                        <div class="tab-panel-inner">
                            <div class="tab-panel-left">
                                <h2 class="tab-section-title">Shipping Information</h2>
                            </div>
                            <div class="tab-panel-right">
                                <p>We offer complimentary delivery on all orders. Standard shipping takes 5–7 business days. Express options available at checkout.</p>
                            </div>
                        </div>
                    </div>
                    <div v-if="activeTab === 'style'" class="tab-panel">
                        <div class="tab-panel-inner">
                            <div class="tab-panel-left">
                                <h2 class="tab-section-title">Style Guide</h2>
                            </div>
                            <div class="tab-panel-right">
                                <p>For the best fit, refer to our size chart. This piece is designed for an athletic cut — we recommend sizing up for a relaxed feel.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- You May Also Like -->
            <section class="related-section" v-if="relatedProducts.length > 0">
                <h2 class="section-heading">You May Also Like</h2>
                <div class="related-grid">
                    <div v-for="related in relatedProducts" :key="related.id" class="related-card">
                        <Link :href="route('store.brand-partner.product', related.slug)" class="related-card-link">
                            <div class="related-img-wrap">
                                <img
                                    :src="related.image_url || '/img/tshirt-placeholder.svg'"
                                    :alt="related.name"
                                />
                                <span v-if="related.compare_price && related.compare_price > related.price" class="card-sale-badge">SALE</span>
                                <button class="card-wish-btn"><i class="ri-heart-line"></i></button>
                            </div>
                            <div class="related-info">
                                <p class="related-name">{{ related.name }}</p>
                                <p class="related-collection" v-if="related.category">{{ related.category.name }}</p>
                                <div class="star-row">
                                    <span v-for="n in 5" :key="n" class="star">★</span>
                                </div>
                                <div class="related-price-row">
                                    <span class="related-price-current">{{ formatCurrency(related.price) }}</span>
                                    <del v-if="related.compare_price && related.compare_price > related.price" class="related-price-old">
                                        {{ formatCurrency(related.compare_price) }}
                                    </del>
                                    <span v-if="related.compare_price && related.compare_price > related.price" class="card-save-badge">SALE</span>
                                </div>
                            </div>
                        </Link>
                        <button class="card-add-btn" :disabled="!related.in_stock">
                            {{ related.in_stock ? 'ADD TO CART' : 'OUT OF STOCK' }}
                        </button>
                    </div>
                </div>
            </section>
        </div>

        <!-- Mobile Bottom Bar -->
        <div class="mobile-bar">
            <div class="mobile-qty">
                <button class="qty-btn-m" @click="decrementQuantity" :disabled="quantity <= 1">−</button>
                <input type="number" v-model.number="quantity" min="1" class="qty-input-m" />
                <button class="qty-btn-m" @click="incrementQuantity">+</button>
            </div>
            <button
                class="mobile-add-btn"
                :disabled="!currentInStock || isAddingToCart || !variationReady"
                @click="showConfirmModal"
            >
                <span v-if="isAddingToCart">Adding...</span>
                <span v-else-if="!currentInStock">OUT OF STOCK</span>
                <span v-else>ADD TO CART | {{ formatCurrency(product.price * quantity) }}</span>
            </button>
        </div>

        <!-- Confirm Modal -->
        <div class="modal fade" id="addToCartConfirmModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content confirm-modal">
                    <div class="confirm-modal-header">
                        <h5 class="confirm-modal-title">Confirm Add to Cart</h5>
                        <button type="button" class="confirm-close-btn" @click="confirmModal?.hide()">✕</button>
                    </div>
                    <div class="confirm-modal-body">
                        <div class="confirm-product-row">
                            <img
                                :src="selectedImage || product.image_url || '/img/tshirt-placeholder.svg'"
                                :alt="product.name"
                                class="confirm-img"
                            />
                            <div class="confirm-details">
                                <h6 class="confirm-name">{{ product.name }}</h6>
                                <span class="confirm-price">{{ formatCurrency(product.price) }}</span>
                                <div v-if="selectedColor || selectedSize" class="confirm-vars">
                                    <span v-if="selectedColor">{{ selectedColor }}</span>
                                    <span v-if="selectedColor && selectedSize"> · </span>
                                    <span v-if="selectedSize">{{ selectedSize }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="confirm-summary">
                            <div class="confirm-row">
                                <span>Quantity</span>
                                <strong>{{ quantity }}</strong>
                            </div>
                            <div class="confirm-row confirm-total">
                                <span>Total</span>
                                <strong>{{ formatCurrency(product.price * quantity) }}</strong>
                            </div>
                        </div>
                    </div>
                    <div class="confirm-modal-footer">
                        <button class="confirm-cancel" @click="confirmModal?.hide()">Cancel</button>
                        <button class="confirm-ok" @click="addToCart" :disabled="isAddingToCart">
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
    wishlistedIds: {
        type: Array,
        default: () => [],
    },
    auth: Object,
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
const activeTab = ref('description');

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

const toggleWishlist = (productId) => {
    if (!props.auth?.user) {
        window.dispatchEvent(new CustomEvent('open-login-modal'));
        return;
    }

    router.post(
        route('store.brand-partner.wishlist.toggle'),
        { product_id: productId },
        {
            preserveScroll: true,
            onFinish: () => {
                router.reload({ only: ['wishlistedIds'] });
            },
        },
    );
};
</script>

<style scoped>
/* ========================
   Root & Typography
   ======================== */
.product-page {
    font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
    background: #ffffff;
    color: #1a1a1a;
    min-height: 100vh;
    padding-bottom: 100px;
    padding-top: 100px;
}

.container-wrap {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 24px;
}

/* ========================
   Breadcrumb
   ======================== */
.breadcrumb-bar {
    border-bottom: 1px solid #e8e8e8;
    padding: 12px 0;
    background: #fff;
}

.breadcrumb-bar :deep(.breadcrumb) {
    margin: 0;
    padding: 0;
    font-size: 13px;
    color: #888;
}

.breadcrumb-bar :deep(.breadcrumb-item a) {
    color: #888;
    text-decoration: none;
}

.breadcrumb-bar :deep(.breadcrumb-item a:hover) {
    color: #1a1a1a;
}

.breadcrumb-bar :deep(.breadcrumb-item.active) {
    color: #1a1a1a;
}

/* ========================
   Product Main Grid
   ======================== */
.product-main-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 60px;
    padding: 40px 0 60px;
}

/* ========================
   Image Column
   ======================== */
.image-col {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.main-image-wrap {
    position: relative;
    background: #f5f5f5;
    border-radius: 4px;
    overflow: hidden;
    aspect-ratio: 1 / 1;
}

.main-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
}

.sale-badge {
    position: absolute;
    top: 14px;
    left: 14px;
    background: #ef4444;
    color: #fff;
    font-size: 11px;
    font-weight: 700;
    letter-spacing: 0.08em;
    padding: 4px 10px;
    border-radius: 2px;
}

.thumbnail-row {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
}

.thumb {
    width: 72px;
    height: 72px;
    border: 1.5px solid #e0e0e0;
    border-radius: 4px;
    overflow: hidden;
    cursor: pointer;
    transition: border-color 0.15s;
    background: #f5f5f5;
}

.thumb.active,
.thumb:hover {
    border-color: #1a1a1a;
}

.thumb img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

/* ========================
   Info Column
   ======================== */
.info-col {
    padding-top: 8px;
    display: flex;
    flex-direction: column;
}

.sku-line {
    font-size: 11px;
    letter-spacing: 0.12em;
    color: #999;
    text-transform: uppercase;
    margin-bottom: 10px;
}

.product-name {
    font-size: 28px;
    font-weight: 700;
    color: #1a1a1a;
    margin: 0 0 8px;
    line-height: 1.2;
    letter-spacing: -0.01em;
}

.collection-tag {
    font-size: 12px;
    letter-spacing: 0.1em;
    text-transform: uppercase;
    color: #888;
    margin-bottom: 16px;
}

/* ========================
   Price
   ======================== */
.price-row {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 10px;
    flex-wrap: wrap;
}

.price-current {
    font-size: 22px;
    font-weight: 700;
    color: #f97316;
}

.price-old {
    font-size: 15px;
    color: #bbb;
    font-weight: 400;
}

.price-save-badge {
    background: #fef3c7;
    color: #92400e;
    font-size: 11px;
    font-weight: 700;
    letter-spacing: 0.05em;
    padding: 3px 8px;
    border-radius: 2px;
}

/* ========================
   Stock
   ======================== */
.stock-row {
    margin-bottom: 24px;
}

.in-stock {
    font-size: 13px;
    font-weight: 600;
    color: #16a34a;
}

.out-stock {
    font-size: 13px;
    font-weight: 600;
    color: #dc2626;
}

/* ========================
   Variations
   ======================== */
.variation-block {
    margin-bottom: 20px;
}

.variation-heading {
    font-size: 11px;
    font-weight: 700;
    letter-spacing: 0.12em;
    color: #555;
    text-transform: uppercase;
    margin-bottom: 10px;
}

.pill-group {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
}

.pill-btn {
    padding: 7px 18px;
    border: 1px solid #d1d1d1;
    background: #fff;
    border-radius: 2px;
    font-size: 13px;
    font-weight: 500;
    color: #1a1a1a;
    cursor: pointer;
    transition: all 0.15s;
    letter-spacing: 0.02em;
    text-transform: uppercase;
}

.pill-btn:hover {
    border-color: #1a1a1a;
    background: #f9f9f9;
}

.pill-btn.active {
    border-color: #FF9505;
    background: #FF9505;
    color: #fff;
}

.variation-hint {
    font-size: 12px;
    color: #dc2626;
    margin: -8px 0 16px;
}

/* ========================
   Quantity
   ======================== */
.qty-row {
    display: flex;
    align-items: center;
    gap: 16px;
}

.qty-control {
    display: flex;
    align-items: center;
    border: 1px solid #d1d1d1;
    border-radius: 2px;
    overflow: hidden;
}

.qty-btn {
    width: 40px;
    height: 40px;
    border: none;
    background: #fff;
    color: #1a1a1a;
    font-size: 18px;
    font-weight: 400;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: background 0.15s;
    line-height: 1;
}

.qty-btn:hover:not(:disabled) {
    background: #f5f5f5;
}

.qty-btn:disabled {
    color: #ccc;
    cursor: not-allowed;
}

.qty-input {
    width: 52px;
    height: 40px;
    border: none;
    border-left: 1px solid #d1d1d1;
    border-right: 1px solid #d1d1d1;
    text-align: center;
    font-size: 14px;
    font-weight: 600;
    color: #1a1a1a;
    background: #fff;
    outline: none;
    appearance: textfield;
    -moz-appearance: textfield;
}

.qty-input::-webkit-outer-spin-button,
.qty-input::-webkit-inner-spin-button {
    -webkit-appearance: none;
    margin: 0;
}

.cart-icon-btn {
    position: relative;
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 1px solid #d1d1d1;
    border-radius: 2px;
    color: #1a1a1a;
    text-decoration: none;
    font-size: 18px;
    transition: all 0.15s;
    background: #fff;
}

.cart-icon-btn:hover {
    border-color: #1a1a1a;
    background: #f9f9f9;
}

.cart-dot {
    position: absolute;
    top: -5px;
    right: -5px;
    background: #f97316;
    color: #fff;
    font-size: 10px;
    font-weight: 700;
    width: 18px;
    height: 18px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}

/* ========================
   CTA Row
   ======================== */
.cta-row {
    display: flex;
    gap: 10px;
    margin-bottom: 28px;
    margin-top: 8px;
}

.add-to-cart-btn {
    flex: 1;
    height: 50px;
    background: #1b4332;
    color: #fff;
    border: none;
    border-radius: 2px;
    font-size: 13px;
    font-weight: 700;
    letter-spacing: 0.1em;
    text-transform: uppercase;
    cursor: pointer;
    transition: background 0.2s;
}

.add-to-cart-btn:hover:not(:disabled) {
    background: #155d3a;
}

.add-to-cart-btn:disabled {
    background: #d1d5db;
    cursor: not-allowed;
}

.wishlist-btn {
    width: 50px;
    height: 50px;
    border: 1px solid #d1d1d1;
    background: #fff;
    border-radius: 2px;
    font-size: 20px;
    color: #555;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.15s;
    flex-shrink: 0;
}

.wishlist-btn:hover {
    border-color: #1a1a1a;
    color: #e74c3c;
}

/* ========================
   Trust List
   ======================== */
.trust-list {
    list-style: none;
    padding: 0;
    margin: 0;
    border-top: 1px solid #e8e8e8;
    padding-top: 20px;
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.trust-list li {
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 13px;
    color: #555;
}

.trust-list li i {
    font-size: 16px;
    color: #888;
    width: 18px;
    text-align: center;
}

/* ========================
   Tabs Section
   ======================== */
.tabs-section {
    border-top: 1px solid #e8e8e8;
    margin-bottom: 60px;
}

.tab-bar {
    display: flex;
    gap: 0;
    border-bottom: 1px solid #e8e8e8;
}

.tab-btn {
    padding: 14px 24px;
    border: none;
    border-bottom: 2px solid transparent;
    background: none;
    font-size: 12px;
    font-weight: 700;
    letter-spacing: 0.1em;
    text-transform: uppercase;
    color: #888;
    cursor: pointer;
    transition: all 0.15s;
    margin-bottom: -1px;
}

.tab-btn:hover {
    color: #1a1a1a;
}

.tab-btn.active {
    color: #f97316;
    border-bottom-color: #f97316;
}

.tab-content-area {
    padding: 40px 0;
}

.tab-panel-inner {
    display: grid;
    grid-template-columns: 220px 1fr;
    gap: 60px;
}

.tab-section-title {
    font-size: 20px;
    font-weight: 700;
    color: #1a1a1a;
    margin: 0;
}

.tab-panel-right {
    font-size: 14px;
    line-height: 1.8;
    color: #555;
}

.tab-panel-right :deep(p) {
    margin: 0 0 16px;
}

.tab-panel-right :deep(strong) {
    color: #1a1a1a;
    font-weight: 700;
}

/* ========================
   Related Products
   ======================== */
.related-section {
    padding-bottom: 60px;
}

.section-heading {
    font-size: 22px;
    font-weight: 700;
    color: #1a1a1a;
    margin: 0 0 28px;
}

.related-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 20px;
}

.related-card {
    display: flex;
    flex-direction: column;
}

.related-card-link {
    text-decoration: none;
    color: inherit;
}

.related-img-wrap {
    position: relative;
    background: #f5f5f5;
    aspect-ratio: 1 / 1;
    border-radius: 4px;
    overflow: hidden;
    margin-bottom: 12px;
}

.related-img-wrap img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s;
}

.related-card:hover .related-img-wrap img {
    transform: scale(1.04);
}

.card-sale-badge {
    position: absolute;
    top: 10px;
    left: 10px;
    background: #ef4444;
    color: #fff;
    font-size: 10px;
    font-weight: 700;
    letter-spacing: 0.08em;
    padding: 3px 8px;
    border-radius: 2px;
}

.card-wish-btn {
    position: absolute;
    top: 10px;
    right: 10px;
    width: 30px;
    height: 30px;
    border: none;
    background: #fff;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 15px;
    color: #888;
    cursor: pointer;
    opacity: 0;
    transition: opacity 0.2s, color 0.15s;
}

.related-card:hover .card-wish-btn {
    opacity: 1;
}

.card-wish-btn:hover {
    color: #e74c3c;
}

.related-info {
    flex: 1;
    padding: 0 2px;
}

.related-name {
    font-size: 14px;
    font-weight: 600;
    color: #1a1a1a;
    margin: 0 0 2px;
    line-height: 1.3;
}

.related-collection {
    font-size: 11px;
    color: #999;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    margin: 0 0 6px;
}

.star-row {
    display: flex;
    gap: 1px;
    margin-bottom: 8px;
}

.star {
    font-size: 13px;
    color: #f97316;
}

.related-price-row {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 10px;
    flex-wrap: wrap;
}

.related-price-current {
    font-size: 15px;
    font-weight: 700;
    color: #f97316;
}

.related-price-old {
    font-size: 13px;
    color: #bbb;
}

.card-save-badge {
    font-size: 10px;
    font-weight: 700;
    color: #fff;
    background: #f97316;
    padding: 2px 7px;
    border-radius: 2px;
    letter-spacing: 0.06em;
}

.card-add-btn {
    width: 100%;
    height: 38px;
    border: 1.5px solid #1a1a1a;
    background: #fff;
    color: #1b4332;
    font-size: 11px;
    font-weight: 700;
    letter-spacing: 0.1em;
    text-transform: uppercase;
    cursor: pointer;
    border-radius: 2px;
    transition: all 0.15s;
    margin-top: 6px;
}

.card-add-btn:hover {
    background: #1b4332;
    color: #fff;
}

/* ========================
   Mobile Bottom Bar
   ======================== */
.mobile-bar {
    display: none;
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    background: #fff;
    border-top: 1px solid #e8e8e8;
    padding: 12px 16px;
    gap: 12px;
    z-index: 999;
    align-items: center;
}

.mobile-qty {
    display: flex;
    align-items: center;
    border: 1px solid #d1d1d1;
    border-radius: 2px;
    overflow: hidden;
}

.qty-btn-m {
    width: 38px;
    height: 44px;
    border: none;
    background: #fff;
    font-size: 18px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #1a1a1a;
}

.qty-btn-m:disabled {
    color: #ccc;
}

.qty-input-m {
    width: 40px;
    height: 44px;
    border: none;
    border-left: 1px solid #d1d1d1;
    border-right: 1px solid #d1d1d1;
    text-align: center;
    font-size: 14px;
    font-weight: 700;
    outline: none;
    appearance: textfield;
    -moz-appearance: textfield;
}

.qty-input-m::-webkit-outer-spin-button,
.qty-input-m::-webkit-inner-spin-button {
    -webkit-appearance: none;
    margin: 0;
}

.mobile-add-btn {
    flex: 1;
    height: 44px;
    background: #1b4332;
    color: #fff;
    border: none;
    border-radius: 2px;
    font-size: 13px;
    font-weight: 700;
    letter-spacing: 0.08em;
    cursor: pointer;
    text-transform: uppercase;
}

.mobile-add-btn:disabled {
    background: #d1d5db;
    cursor: not-allowed;
}

/* ========================
   Confirm Modal
   ======================== */
.confirm-modal {
    border: none;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 20px 50px rgba(0, 0, 0, 0.12);
}

.confirm-modal-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 20px 24px;
    border-bottom: 1px solid #f0f0f0;
}

.confirm-modal-title {
    font-size: 16px;
    font-weight: 700;
    color: #1a1a1a;
    margin: 0;
    letter-spacing: 0.02em;
}

.confirm-close-btn {
    border: none;
    background: none;
    font-size: 14px;
    color: #888;
    cursor: pointer;
    padding: 0;
    line-height: 1;
}

.confirm-close-btn:hover {
    color: #1a1a1a;
}

.confirm-modal-body {
    padding: 24px;
}

.confirm-product-row {
    display: flex;
    gap: 16px;
    align-items: flex-start;
    margin-bottom: 20px;
}

.confirm-img {
    width: 80px;
    height: 80px;
    object-fit: cover;
    border-radius: 4px;
    border: 1px solid #f0f0f0;
    flex-shrink: 0;
}

.confirm-details {
    flex: 1;
}

.confirm-name {
    font-size: 15px;
    font-weight: 600;
    color: #1a1a1a;
    margin: 0 0 6px;
}

.confirm-price {
    font-size: 18px;
    font-weight: 700;
    color: #f97316;
    display: block;
    margin-bottom: 6px;
}

.confirm-vars {
    font-size: 13px;
    color: #888;
}

.confirm-summary {
    border-top: 1px solid #f0f0f0;
    padding-top: 16px;
}

.confirm-row {
    display: flex;
    justify-content: space-between;
    font-size: 14px;
    color: #555;
    padding: 6px 0;
}

.confirm-total {
    border-top: 1px solid #f0f0f0;
    padding-top: 12px;
    margin-top: 6px;
    font-size: 16px;
    font-weight: 700;
    color: #1a1a1a;
}

.confirm-total strong {
    color: #1a1a1a;
}

.confirm-modal-footer {
    display: flex;
    gap: 10px;
    padding: 20px 24px;
    border-top: 1px solid #f0f0f0;
    background: #fafafa;
}

.confirm-cancel {
    flex: 1;
    height: 44px;
    border: 1.5px solid #d1d1d1;
    background: #fff;
    border-radius: 2px;
    font-size: 13px;
    font-weight: 600;
    color: #555;
    cursor: pointer;
    transition: all 0.15s;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.confirm-cancel:hover {
    border-color: #1a1a1a;
    color: #1a1a1a;
}

.confirm-ok {
    flex: 1;
    height: 44px;
    background: #1b4332;
    color: #fff;
    border: none;
    border-radius: 2px;
    font-size: 13px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    cursor: pointer;
    transition: background 0.15s;
}

.confirm-ok:hover:not(:disabled) {
    background: #155d3a;
}

.confirm-ok:disabled {
    background: #d1d5db;
    cursor: not-allowed;
}

/* ========================
   Responsive - Tablet
   ======================== */
@media (max-width: 1024px) {
    .product-main-grid {
        gap: 40px;
    }

    .related-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

/* ========================
   Responsive - Mobile
   ======================== */
@media (max-width: 767px) {
    .product-page {
        padding-bottom: 80px;
    }

    .container-wrap {
        padding: 0 16px;
    }

    .product-main-grid {
        grid-template-columns: 1fr;
        gap: 24px;
        padding: 20px 0 40px;
    }

    .product-name {
        font-size: 22px;
    }

    .tab-panel-inner {
        grid-template-columns: 1fr;
        gap: 20px;
    }

    .tab-btn {
        padding: 12px 14px;
        font-size: 11px;
    }

    .related-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 14px;
    }

    .mobile-bar {
        display: flex;
    }

    .cta-row {
        display: none;
    }

    .wishlist-btn {
        display: none;
    }
}

@media (max-width: 374px) {
    .mobile-add-btn {
        font-size: 11px;
    }
}
</style>