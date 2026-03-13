<template>
    <Head :title="brandPartner.name" />

    <div class="grocery-store-page">
        <!-- Search Section -->
        <section id="search" class="grocery-search-section">
            <div class="custom-container">
                <div class="search-box">
                    <form class="form-style-7" @submit.prevent="applySearch">
                        <div class="search-input-wrap">
                            <i class="ri-search-line search-icon"></i>
                            <input
                                type="text"
                                class="form-control"
                                v-model="searchQuery"
                                placeholder="Search for products..."
                                @input="debounceSearch"
                            />
                            <button
                                v-if="searchQuery"
                                type="button"
                                class="clear-search-btn"
                                @click="clearSearch"
                            >
                                <i class="ri-close-line"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </section>

        <!-- Category Section -->
        <section
            id="categories"
            class="grocery-category-section"
            v-if="categories.length > 0"
        >
            <div class="custom-container">
                <div class="grocery-category-slider">
                    <div class="category-scroll-wrap">
                        <a
                            href="javascript:void(0)"
                            class="grocery-category-box"
                            :class="{
                                active: !selectedCategory && !selectedEvent,
                            }"
                            @click="clearFilters"
                        >
                            <div class="category-icon-wrap">
                                <i class="ri-apps-line"></i>
                            </div>
                            <h5>All</h5>
                        </a>
                        <a
                            v-for="category in categories"
                            :key="category.id"
                            href="javascript:void(0)"
                            class="grocery-category-box"
                            :class="{ active: selectedCategory == category.id }"
                            @click="selectCategory(category)"
                        >
                            <div class="category-icon-wrap">
                                <i class="ri-price-tag-3-line"></i>
                            </div>
                            <h5>{{ category.name }}</h5>
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <!-- Event Tabs -->
        <section class="grocery-events-section" v-if="events.length > 0">
            <div class="custom-container">
                <ul class="nav nav-pills tab-style-5">
                    <li
                        class="nav-item"
                        v-for="event in events"
                        :key="event.id"
                    >
                        <button
                            class="nav-link"
                            :class="{ active: selectedEvent == event.id }"
                            type="button"
                            @click="selectEvent(event)"
                        >
                            {{ event.name }}
                        </button>
                    </li>
                </ul>
            </div>
        </section>

        <!-- Products Grid -->
        <section class="grocery-products-section">
            <div class="custom-container">
                <div class="section-header" v-if="products.data.length > 0">
                    <h4 class="section-title">{{ sectionLabel }}</h4>
                    <span class="product-count">
                        {{ products.total || products.data.length }} items
                    </span>
                </div>

                <ul class="product-offer-list" v-if="products.data.length > 0">
                    <li
                        v-for="product in products.data"
                        :key="product.id"
                        class="product-offer-item"
                    >
                        <div class="product-box">
                            <div class="product-image">
                                <Link
                                    :href="
                                        route('store.brand-partner.product', product.slug)
                                    "
                                    class="product-image-link"
                                >
                                    <img
                                        :src="
                                            product.image_url ||
                                            '/img/tshirt-placeholder.svg'
                                        "
                                        :alt="product.name"
                                        class="img-fluid"
                                    />
                                    <div
                                        class="discount-badge"
                                        v-if="
                                            product.compare_price &&
                                            product.compare_price >
                                                product.price
                                        "
                                    >
                                        <span>
                                            {{
                                                Math.round(
                                                    (1 -
                                                        product.price /
                                                            product.compare_price) *
                                                        100,
                                                )
                                            }}% OFF
                                        </span>
                                    </div>
                                </Link>
                            </div>
                            <div class="product-content">
                                <Link
                                    :href="
                                        route('store.brand-partner.product', product.slug)
                                    "
                                    class="product-name-link"
                                >
                                    <h5 class="product-name">
                                        {{ product.name }}
                                    </h5>
                                </Link>
                                <h5
                                    class="product-category"
                                    v-if="product.short_description"
                                >
                                    {{
                                        truncate(product.short_description, 40)
                                    }}
                                </h5>
                                <h5 class="product-price">
                                    {{ formatCurrency(product.price) }}
                                    <span
                                        v-if="
                                            product.compare_price &&
                                            product.compare_price >
                                                product.price
                                        "
                                        class="old-price"
                                    >
                                        {{
                                            formatCurrency(
                                                product.compare_price,
                                            )
                                        }}
                                    </span>
                                </h5>
                                <!-- Color & Size chips -->
                                <div v-if="product.colors_array?.length || product.sizes_array?.length" class="product-options-chips">
                                    <span
                                        v-for="color in (product.colors_array ?? []).slice(0, 4)"
                                        :key="color"
                                        class="option-chip color-chip"
                                    >{{ color }}</span>
                                    <span
                                        v-for="size in (product.sizes_array ?? []).slice(0, 4)"
                                        :key="size"
                                        class="option-chip size-chip"
                                    >{{ size }}</span>
                                </div>

                                <div class="add-quantity-wrap">
                                    <Link
                                        v-if="product.colors_array?.length || product.sizes_array?.length"
                                        :href="route('store.brand-partner.product', product.slug)"
                                        class="btn-add-quantity"
                                        title="Select options"
                                    >
                                        <i class="ri-equalizer-line"></i>
                                    </Link>
                                    <button
                                        v-else-if="product.in_stock"
                                        class="btn-add-quantity"
                                        @click.prevent="addToCart(product)"
                                    >
                                        <i class="ri-add-line"></i>
                                    </button>
                                    <span v-else class="out-of-stock-badge">
                                        Out of Stock
                                    </span>
                                </div>
                            </div>
                        </div>
                    </li>
                </ul>

                <!-- Empty State -->
                <div v-else class="grocery-empty-state">
                    <div class="empty-icon-circle">
                        <i class="ri-shopping-bag-line"></i>
                    </div>
                    <h4>No products found</h4>
                    <p>Try adjusting your filters or search query.</p>
                    <button
                        class="btn btn-grocery-primary"
                        @click="clearFilters"
                    >
                        <i class="ri-store-2-line"></i> View All Products
                    </button>
                </div>

                <!-- Pagination -->
                <div
                    class="grocery-pagination"
                    v-if="products.links && products.links.length > 3"
                >
                    <nav>
                        <ul class="pagination">
                            <li
                                v-for="link in products.links"
                                :key="link.label"
                                class="page-item"
                                :class="{
                                    active: link.active,
                                    disabled: !link.url,
                                }"
                            >
                                <Link
                                    v-if="link.url"
                                    :href="link.url"
                                    class="page-link"
                                    v-html="link.label"
                                    preserve-scroll
                                />
                                <span
                                    v-else
                                    class="page-link"
                                    v-html="link.label"
                                />
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>
        </section>

        <!-- Cart Bottom Bar -->
        <div class="product-cart-box" v-if="cartCount > 0">
            <div class="custom-container">
                <div class="cart-bar-inner">
                    <div class="cart-bar-info">
                        <h5 class="cart-item-count">
                            {{ cartCount }}
                            {{ cartCount === 1 ? 'item' : 'items' }}
                        </h5>
                        <h4 class="cart-bar-title">View Cart</h4>
                    </div>
                    <Link
                        :href="route('store.brand-partner.cart')"
                        class="btn btn-grocery-primary cart-bar-btn"
                    >
                        View Cart <i class="ri-arrow-right-line"></i>
                    </Link>
                </div>
            </div>
        </div>

        <!-- Add to Cart Modal -->
        <div
            class="modal fade"
            id="addToCartModal"
            tabindex="-1"
            aria-hidden="true"
        >
            <div class="modal-dialog modal-dialog-centered modal-sm-fullwidth">
                <div
                    class="modal-content grocery-modal-content"
                    v-if="selectedProduct"
                >
                    <div class="grocery-modal-header">
                        <h4 class="grocery-modal-title">
                            {{ selectedProduct.name }}
                        </h4>
                        <button
                            type="button"
                            class="btn-close"
                            @click="cartModal?.hide()"
                        ></button>
                    </div>
                    <div class="grocery-modal-body">
                        <div class="modal-product-detail">
                            <img
                                :src="
                                    selectedProduct.image_url ||
                                    '/img/tshirt-placeholder.svg'
                                "
                                :alt="selectedProduct.name"
                                class="modal-product-img"
                            />
                            <div class="modal-product-info">
                                <p v-if="selectedProduct.short_description">
                                    {{
                                        truncate(
                                            selectedProduct.short_description,
                                            80,
                                        )
                                    }}
                                </p>
                                <h5 class="modal-product-price">
                                    {{ formatCurrency(selectedProduct.price) }}
                                </h5>
                                <span
                                    v-if="
                                        selectedProduct.compare_price &&
                                        selectedProduct.compare_price >
                                            selectedProduct.price
                                    "
                                    class="old-price"
                                >
                                    {{
                                        formatCurrency(
                                            selectedProduct.compare_price,
                                        )
                                    }}
                                </span>
                            </div>
                        </div>

                        <div class="qty-section-title">
                            <h5>Quantity</h5>
                        </div>
                        <div class="qty-selector">
                            <div class="qty-box">
                                <div class="input-group">
                                    <button
                                        type="button"
                                        class="qty-btn qty-minus"
                                        @click="
                                            modalQuantity > 1 && modalQuantity--
                                        "
                                        :disabled="modalQuantity <= 1"
                                    >
                                        -
                                    </button>
                                    <input
                                        class="form-control qty-input"
                                        type="text"
                                        v-model.number="modalQuantity"
                                        min="1"
                                    />
                                    <button
                                        type="button"
                                        class="qty-btn qty-plus"
                                        @click="modalQuantity++"
                                    >
                                        +
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="grocery-modal-footer">
                        <div class="modal-footer-info">
                            <h5>
                                {{ modalQuantity }}
                                {{ modalQuantity === 1 ? 'item' : 'items' }}
                            </h5>
                            <h4>
                                {{
                                    formatCurrency(
                                        selectedProduct.price * modalQuantity,
                                    )
                                }}
                            </h4>
                        </div>
                        <button
                            class="btn btn-grocery-primary cart-bar-btn"
                            @click="confirmAddToCart"
                            :disabled="isAddingToCart"
                        >
                            <span v-if="isAddingToCart">Adding...</span>
                            <span v-else>
                                Add to Cart
                                <i class="ri-arrow-right-line"></i>
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bottom Spacing -->
        <div class="grocery-bottom-space"></div>
    </div>
</template>

<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, computed, onMounted, onBeforeUnmount } from 'vue';
import { Modal } from 'bootstrap';

const props = defineProps({
    brandPartner: Object,
    products: Object,
    categories: Array,
    events: Array,
    cartCount: {
        type: Number,
        default: 0,
    },
    filter: {
        type: Object,
        default: () => ({}),
    },
});

const selectedCategory = ref(props.filter.category || null);
const selectedEvent = ref(props.filter.event || null);
const searchQuery = ref(props.filter.search || '');
let searchTimeout = null;

const sectionLabel = computed(() => {
    if (searchQuery.value) return `Results for "${searchQuery.value}"`;
    if (selectedCategory.value) {
        const cat = props.categories.find(
            (c) => c.id == selectedCategory.value,
        );
        return cat?.name || 'Products';
    }
    if (selectedEvent.value) {
        const ev = props.events.find((e) => e.id == selectedEvent.value);
        return ev?.name || 'Products';
    }
    return 'All Products';
});

const formatCurrency = (amount) => {
    return new Intl.NumberFormat('en-PH', {
        style: 'currency',
        currency: 'PHP',
    }).format(amount / 100);
};

const truncate = (text, length) => {
    if (!text) return '';
    return text.length > length ? text.substring(0, length) + '...' : text;
};

const selectCategory = (category) => {
    selectedCategory.value = category.id;
    selectedEvent.value = null;
    applyFilters();
};

const selectEvent = (event) => {
    selectedEvent.value = event.id;
    applyFilters();
};

const clearFilters = () => {
    selectedCategory.value = null;
    selectedEvent.value = null;
    searchQuery.value = '';
    applyFilters();
};

const applySearch = () => {
    applyFilters();
};

const debounceSearch = () => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        applyFilters();
    }, 400);
};

const clearSearch = () => {
    searchQuery.value = '';
    applyFilters();
};

const applyFilters = () => {
    const params = {};
    if (selectedCategory.value) params.category = selectedCategory.value;
    if (selectedEvent.value) params.event = selectedEvent.value;
    if (searchQuery.value) params.search = searchQuery.value;

    router.get(
        route('store.brand-partner.index'),
        params,
        {
            preserveState: true,
            replace: true,
        },
    );
};

const selectedProduct = ref(null);
const modalQuantity = ref(1);
const isAddingToCart = ref(false);
let cartModal = null;

onMounted(() => {
    const modalEl = document.getElementById('addToCartModal');
    if (modalEl) {
        cartModal = new Modal(modalEl);
        modalEl.addEventListener('hidden.bs.modal', () => {
            selectedProduct.value = null;
            modalQuantity.value = 1;
        });
    }
});

onBeforeUnmount(() => {
    clearTimeout(searchTimeout);
    if (cartModal) {
        cartModal.dispose();
        cartModal = null;
    }
});

const addToCart = (product) => {
    selectedProduct.value = product;
    modalQuantity.value = 1;
    cartModal?.show();
};

const confirmAddToCart = () => {
    if (!selectedProduct.value) return;
    isAddingToCart.value = true;
    router.post(
        route('store.brand-partner.cart.add'),
        {
            product_id: selectedProduct.value.id,
            quantity: modalQuantity.value,
        },
        {
            preserveScroll: true,
            onSuccess: () => {
                cartModal?.hide();
            },
            onError: (errors) => {
                console.error('Error adding to cart:', errors);
            },
            onFinish: () => {
                isAddingToCart.value = false;
            },
        },
    );
};
</script>

<style scoped>
/* ===== Grocery Template Styles ===== */
.grocery-store-page {
    font-family: 'Public Sans', sans-serif;
    background: #f7f7f7;
    min-height: 100vh;
    overflow-x: hidden;
    max-width: 100%;
    /* Grocery Theme Color Variables */
    --grocery-theme: 60, 133, 153; /* Main teal/cyan color: rgb(60, 133, 153) */
    --grocery-content: 143, 143, 178; /* Light gray-blue content text */
    --grocery-title: 27, 27, 62; /* Dark blue-gray for titles */
    --grocery-border: 232, 232, 232; /* Light gray borders */
    --grocery-primary: 254, 175, 24; /* Yellow/orange accent */
    --grocery-light-bg: 247, 247, 247; /* Light gray background */
    --grocery-rating: 255, 191, 19; /* Gold/yellow for ratings */
}

/* ===== Search Section - form-style-7 ===== */
.grocery-search-section {
    padding: 16px 0 8px;
    background: #fff;
}

.search-box {
    margin-bottom: 0;
}

.form-style-7 .search-input-wrap {
    position: relative;
    display: flex;
    align-items: center;
}

.form-style-7 .search-icon {
    position: absolute;
    left: 16px;
    font-size: 20px;
    color: #9e9e9e;
    z-index: 1;
}

.form-style-7 .form-control {
    width: 100%;
    padding: 14px 48px 14px 48px;
    border: 2px solid #eeeeee;
    border-radius: 16px;
    font-size: 14px;
    font-family: 'Public Sans', sans-serif;
    color: #333;
    background: #fafafa;
    outline: none;
    transition: all 0.25s ease;
}

.form-style-7 .form-control:focus {
    border-color: rgb(var(--grocery-theme));
    background: #fff;
    box-shadow: 0 0 0 4px rgba(var(--grocery-theme), 0.08);
}

.form-style-7 .form-control::placeholder {
    color: #bdbdbd;
}

.clear-search-btn {
    position: absolute;
    right: 14px;
    background: none;
    border: none;
    color: #9e9e9e;
    font-size: 20px;
    cursor: pointer;
    padding: 4px;
    display: flex;
    align-items: center;
    transition: color 0.2s;
}

.clear-search-btn:hover {
    color: rgb(var(--grocery-theme));
}

/* ===== Category Section - grocery-category-box ===== */
.grocery-category-section {
    padding: 16px 0 12px;
    background: #fff;
}

.grocery-category-slider {
    overflow: hidden;
}

.category-scroll-wrap {
    display: flex;
    gap: 14px;
    overflow-x: auto;
    scrollbar-width: none;
    -ms-overflow-style: none;
    -webkit-overflow-scrolling: touch;
    padding: 4px 0 8px;
}

.category-scroll-wrap::-webkit-scrollbar {
    display: none;
}

.grocery-category-box {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 8px;
    min-width: 76px;
    text-decoration: none;
    cursor: pointer;
    transition: all 0.25s ease;
    flex-shrink: 0;
}

.grocery-category-box .category-icon-wrap {
    width: 60px;
    height: 60px;
    border-radius: 16px;
    background: rgba(var(--grocery-theme), 0.05);
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.25s ease;
    border: 2px solid transparent;
    box-shadow: 0 2px 8px rgba(var(--grocery-theme), 0.06);
}

.grocery-category-box.active .category-icon-wrap {
    background: rgb(var(--grocery-theme));
    border-color: rgb(var(--grocery-theme));
    box-shadow: 0 4px 14px rgba(var(--grocery-theme), 0.3);
}

.grocery-category-box .category-icon-wrap i {
    font-size: 26px;
    color: rgb(var(--grocery-theme));
    transition: color 0.25s ease;
}

.grocery-category-box.active .category-icon-wrap i {
    color: #fff;
}

.grocery-category-box h5 {
    font-size: 11px;
    font-weight: 600;
    color: rgb(var(--grocery-content));
    margin: 0;
    white-space: nowrap;
    text-align: center;
    letter-spacing: 0.2px;
}

.grocery-category-box.active h5 {
    color: rgb(var(--grocery-theme));
    font-weight: 700;
}

/* ===== Event Tabs - tab-style-5 ===== */
.grocery-events-section {
    padding: 12px 0;
    background: #fff;
    border-bottom: 1px solid #f0f0f0;
}

.tab-style-5 {
    display: flex;
    gap: 8px;
    overflow-x: auto;
    scrollbar-width: none;
    -ms-overflow-style: none;
    border-bottom: none;
    padding: 0;
    margin: 0;
    list-style: none;
}

.tab-style-5::-webkit-scrollbar {
    display: none;
}

.tab-style-5 .nav-item {
    flex-shrink: 0;
}

.tab-style-5 .nav-link {
    padding: 8px 20px;
    border-radius: 24px;
    border: 2px solid #e8e8e8;
    background: #fff;
    font-size: 13px;
    font-weight: 600;
    color: rgb(var(--grocery-content));
    cursor: pointer;
    white-space: nowrap;
    transition: all 0.25s ease;
    font-family: 'Public Sans', sans-serif;
}

.tab-style-5 .nav-link:hover {
    border-color: rgb(var(--grocery-theme));
    color: rgb(var(--grocery-theme));
    background: rgba(var(--grocery-theme), 0.05);
}

.tab-style-5 .nav-link.active {
    background: rgb(var(--grocery-theme));
    border-color: rgb(var(--grocery-theme));
    color: #fff;
    box-shadow: 0 3px 10px rgba(var(--grocery-theme), 0.25);
}

/* ===== Products Section ===== */
.grocery-products-section {
    padding: 20px 0 24px;
}

.section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 16px;
}

.section-title {
    font-size: 18px;
    font-weight: 800;
    color: rgb(var(--grocery-title));
    margin: 0;
    font-family: 'Public Sans', sans-serif;
}

.product-count {
    font-size: 13px;
    color: rgb(var(--grocery-content));
    font-weight: 500;
}

/* Product Grid - product-offer-list */
.product-offer-list {
    list-style: none;
    padding: 0;
    margin: 0;
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 12px;
}

.product-offer-item {
    display: flex;
}

.product-box {
    width: 100%;
    background: linear-gradient(
        180deg,
        rgba(var(--grocery-theme), 0.05) 61.46%,
        rgba(245, 249, 250, 0) 100%
    );
    border-radius: 6px;
    overflow: hidden;
    box-shadow: 0 2px 12px rgba(0, 0, 0, 0.04);
    transition: all 0.25s ease;
    display: flex;
    flex-direction: column;
    border: 1px solid rgb(var(--grocery-border));
}

.product-box:hover {
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    transform: translateY(-2px);
}

.product-image {
    position: relative;
    width: 100%;
    aspect-ratio: 1 / 1;
    overflow: hidden;
    background: #f8f8f8;
}

.product-image-link {
    display: block;
    width: 100%;
    height: 100%;
    text-decoration: none;
}

.product-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.product-box:hover .product-image img {
    transform: scale(1.05);
}

.discount-badge {
    position: absolute;
    top: 8px;
    left: 8px;
}

.discount-badge span {
    display: inline-block;
    background: linear-gradient(135deg, #ff4757 0%, #ff3344 100%);
    color: #fff;
    padding: 3px 8px;
    border-radius: 8px;
    font-size: 10px;
    font-weight: 700;
    letter-spacing: 0.3px;
}

.product-options-chips {
    display: flex;
    flex-wrap: wrap;
    gap: 4px;
    margin-bottom: 6px;
}
.option-chip {
    font-size: 10px;
    font-weight: 600;
    padding: 2px 7px;
    border-radius: 10px;
    letter-spacing: 0.2px;
}
.color-chip {
    background: rgba(var(--grocery-theme), 0.1);
    color: rgb(var(--grocery-theme));
}
.size-chip {
    background: #f0f0f0;
    color: #555;
}

.product-content {
    padding: 12px;
    display: flex;
    flex-direction: column;
    flex: 1;
    position: relative;
}

.product-name-link {
    text-decoration: none;
    color: inherit;
}

.product-name-link:hover .product-name {
    color: rgb(var(--grocery-theme));
}

.product-name {
    font-size: 14px;
    font-weight: 700;
    color: rgb(var(--grocery-title));
    margin: 0 0 4px;
    line-height: 1.3;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    font-family: 'Public Sans', sans-serif;
    transition: color 0.2s;
}

.product-category {
    font-size: 11px;
    color: rgb(var(--grocery-content));
    margin: 0 0 8px;
    font-weight: 400;
    line-height: 1.3;
    display: -webkit-box;
    -webkit-line-clamp: 1;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.product-price {
    font-size: 16px;
    font-weight: 800;
    color: rgb(var(--grocery-theme));
    margin: 0 0 8px;
    font-family: 'Public Sans', sans-serif;
}

.old-price {
    font-size: 12px;
    font-weight: 400;
    color: #bdbdbd;
    text-decoration: line-through;
    margin-left: 4px;
}

.add-quantity-wrap {
    margin-top: auto;
    display: flex;
    justify-content: flex-end;
}

.btn-add-quantity {
    width: 36px;
    height: 36px;
    border-radius: 12px;
    border: none;
    background: rgb(var(--grocery-theme));
    color: #fff;
    font-size: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.25s ease;
    box-shadow: 0 3px 10px rgba(var(--grocery-theme), 0.25);
}

.btn-add-quantity:hover {
    transform: scale(1.08);
    box-shadow: 0 4px 14px rgba(var(--grocery-theme), 0.35);
}

.btn-add-quantity:active {
    transform: scale(0.96);
}

.out-of-stock-badge {
    font-size: 10px;
    font-weight: 600;
    color: rgb(var(--grocery-content));
    padding: 5px 10px;
    background: rgba(var(--grocery-content), 0.1);
    border-radius: 8px;
    letter-spacing: 0.2px;
}

/* ===== Empty State ===== */
.grocery-empty-state {
    text-align: center;
    padding: 60px 20px;
    background: #fff;
    border-radius: 20px;
}

.empty-icon-circle {
    width: 100px;
    height: 100px;
    background: rgba(var(--grocery-theme), 0.1);
    border-radius: 50%;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 20px;
}

.empty-icon-circle i {
    font-size: 42px;
    color: rgb(var(--grocery-theme));
}

.grocery-empty-state h4 {
    font-weight: 800;
    color: rgb(var(--grocery-title));
    margin-bottom: 8px;
    font-family: 'Public Sans', sans-serif;
}

.grocery-empty-state p {
    color: rgb(var(--grocery-content));
    margin-bottom: 20px;
    font-size: 14px;
}

/* ===== Primary Button ===== */
.btn-grocery-primary {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 10px 22px;
    background: rgb(var(--grocery-theme));
    color: #fff;
    border: none;
    border-radius: 14px;
    font-size: 14px;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.25s ease;
    font-family: 'Public Sans', sans-serif;
    text-decoration: none;
    box-shadow: 0 3px 10px rgba(var(--grocery-theme), 0.25);
}

.btn-grocery-primary:hover {
    background: rgba(var(--grocery-theme), 0.9);
    box-shadow: 0 4px 16px rgba(var(--grocery-theme), 0.35);
    color: #fff;
    transform: translateY(-1px);
}

.btn-grocery-primary:active {
    transform: translateY(0);
}

.btn-grocery-primary:disabled {
    opacity: 0.65;
    cursor: not-allowed;
    transform: none;
}

.btn-grocery-primary i {
    font-size: 16px;
}

/* ===== Pagination ===== */
.grocery-pagination {
    display: flex;
    justify-content: center;
    margin-top: 28px;
    padding-bottom: 16px;
}

.pagination {
    gap: 4px;
}

.pagination .page-link {
    border: none;
    color: #616161;
    border-radius: 12px;
    font-weight: 600;
    font-size: 13px;
    padding: 8px 14px;
    font-family: 'Public Sans', sans-serif;
    transition: all 0.2s ease;
}

.pagination .page-item.active .page-link {
    background: rgb(var(--grocery-theme));
    color: #fff;
    box-shadow: 0 2px 8px rgba(var(--grocery-theme), 0.3);
}

.pagination .page-link:hover {
    background: rgba(var(--grocery-theme), 0.1);
    color: rgb(var(--grocery-theme));
}

/* ===== Cart Bottom Bar - product-cart-box ===== */
.product-cart-box {
    position: fixed;
    bottom: 60px;
    left: 0;
    right: 0;
    background: #fff;
    box-shadow: 0 -4px 20px rgba(0, 0, 0, 0.08);
    z-index: 998;
    padding: 14px 0;
    border-top: 1px solid #f0f0f0;
}

.cart-bar-inner {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.cart-item-count {
    font-size: 12px;
    color: rgb(var(--grocery-content));
    margin: 0;
    font-weight: 500;
}

.cart-bar-title {
    font-size: 16px;
    font-weight: 800;
    color: rgb(var(--grocery-title));
    margin: 0;
    font-family: 'Public Sans', sans-serif;
}

.cart-bar-btn {
    padding: 10px 22px;
}

.cart-bar-btn i {
    font-size: 16px;
}

/* ===== Modal - Grocery Styling ===== */
.grocery-modal-content {
    border: none;
    border-radius: 24px;
    overflow: hidden;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.12);
}

.grocery-modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 18px 22px;
    border-bottom: 1px solid #f0f0f0;
}

.grocery-modal-title {
    font-size: 17px;
    font-weight: 800;
    color: rgb(var(--grocery-title));
    margin: 0;
    font-family: 'Public Sans', sans-serif;
}

.grocery-modal-body {
    padding: 18px 22px;
}

.modal-product-detail {
    display: flex;
    gap: 16px;
    align-items: flex-start;
    margin-bottom: 18px;
}

.modal-product-img {
    width: 88px;
    height: 88px;
    object-fit: cover;
    border-radius: 16px;
    flex-shrink: 0;
    border: 1px solid #f0f0f0;
}

.modal-product-info p {
    font-size: 13px;
    color: rgb(var(--grocery-content));
    margin: 0 0 8px;
    line-height: 1.4;
}

.modal-product-price {
    font-size: 18px;
    font-weight: 800;
    color: rgb(var(--grocery-theme));
    margin: 0;
    font-family: 'Public Sans', sans-serif;
}

.qty-section-title {
    padding: 12px 0 10px;
    border-top: 1px solid #f0f0f0;
}

.qty-section-title h5 {
    font-size: 14px;
    font-weight: 700;
    color: rgb(var(--grocery-title));
    margin: 0;
    font-family: 'Public Sans', sans-serif;
}

/* Qty Box */
.qty-selector {
    padding-bottom: 10px;
}

.qty-box .input-group {
    display: flex;
    align-items: center;
    background: #f5f5f5;
    border-radius: 14px;
    overflow: hidden;
    width: fit-content;
}

.qty-btn {
    width: 44px;
    height: 44px;
    border: none;
    background: none;
    color: rgb(var(--grocery-theme));
    font-size: 20px;
    font-weight: 700;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    font-family: 'Public Sans', sans-serif;
    transition: background 0.2s;
}

.qty-btn:hover {
    background: rgba(var(--grocery-theme), 0.08);
}

.qty-btn:disabled {
    color: #ccc;
}

.qty-input {
    width: 54px;
    height: 44px;
    border: none;
    background: none;
    text-align: center;
    font-weight: 700;
    font-size: 16px;
    color: #333;
    outline: none;
    padding: 0;
    font-family: 'Public Sans', sans-serif;
    -moz-appearance: textfield;
}

.qty-input::-webkit-outer-spin-button,
.qty-input::-webkit-inner-spin-button {
    -webkit-appearance: none;
    margin: 0;
}

/* Modal Footer */
.grocery-modal-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 16px 22px;
    background: #fafafa;
    border-top: 1px solid #f0f0f0;
}

.modal-footer-info h5 {
    font-size: 12px;
    color: rgb(var(--grocery-content));
    margin: 0;
    font-weight: 500;
}

.modal-footer-info h4 {
    font-size: 18px;
    font-weight: 800;
    color: rgb(var(--grocery-theme));
    margin: 0;
    font-family: 'Public Sans', sans-serif;
}

/* ===== Bottom Space ===== */
.grocery-bottom-space {
    height: 80px;
}

/* ===== Responsive ===== */
@media (min-width: 576px) {
    .product-offer-list {
        grid-template-columns: repeat(3, 1fr);
        gap: 16px;
    }
}

@media (min-width: 769px) {
    .grocery-search-section {
        margin-top: 16px;
    }

    .product-cart-box {
        bottom: 0;
    }
}

@media (min-width: 992px) {
    .product-offer-list {
        grid-template-columns: repeat(4, 1fr);
        gap: 20px;
    }

    .grocery-store-page {
        max-width: 1200px;
        margin: 0 auto;
    }

    .section-title {
        font-size: 22px;
    }

    .product-name {
        font-size: 15px;
    }

    .product-price {
        font-size: 17px;
    }

    .grocery-category-box .category-icon-wrap {
        width: 68px;
        height: 68px;
        border-radius: 18px;
    }

    .grocery-category-box .category-icon-wrap i {
        font-size: 28px;
    }

    .grocery-category-box h5 {
        font-size: 12px;
    }
}

@media (min-width: 1200px) {
    .product-offer-list {
        gap: 24px;
    }

    .product-content {
        padding: 14px;
    }
}

@media (max-width: 575px) {
    .product-offer-list {
        gap: 10px;
    }

    .product-content {
        padding: 10px;
    }

    .product-name {
        font-size: 13px;
    }

    .product-price {
        font-size: 14px;
    }

    .grocery-category-box .category-icon-wrap {
        width: 52px;
        height: 52px;
        border-radius: 14px;
    }

    .grocery-category-box .category-icon-wrap i {
        font-size: 22px;
    }

    .grocery-category-box h5 {
        font-size: 10px;
    }

    .modal-dialog {
        margin: 0;
        min-height: auto;
        display: flex;
        align-items: flex-end;
    }

    .grocery-modal-content {
        border-radius: 24px 24px 0 0;
        width: 100%;
    }
}
</style>
