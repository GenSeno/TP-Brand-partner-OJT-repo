<template>
    <Head :title="`Cart - ${brandPartner.name}`" />

    <div class="grocery-cart-section">
        <!-- Header -->
        <div class="grocery-header">
            <div class="grocery-container">
                <div class="header-inner">
                    <Link
                        :href="
                            route(
                                'store.brand-partner.index',
                                brandPartner.slug,
                            )
                        "
                        class="header-back"
                    >
                        <i class="ri-arrow-left-s-line"></i>
                    </Link>
                    <h2 class="header-title">Shopping Cart</h2>
                    <Link
                        :href="
                            route(
                                'store.brand-partner.index',
                                brandPartner.slug,
                            )
                        "
                        class="continue-link"
                    >
                        Continue Shopping
                    </Link>
                </div>
            </div>
        </div>

        <div class="grocery-container">
            <!-- Cart with items -->
            <div v-if="cart.items.length > 0" class="cart-grid">
                <!-- Left: Cart Items -->
                <div class="cart-items-section">
                    <div class="cart-box-list">
                        <div
                            class="cart-box"
                            v-for="item in cart.items"
                            :key="item.id"
                        >
                            <div class="cart-box-left">
                                <Link
                                    :href="
                                        route('store.brand-partner.product', item.product.slug)
                                    "
                                    class="product-image"
                                >
                                    <img
                                        :src="
                                            item.product.image_url ||
                                            '/img/tshirt-placeholder.svg'
                                        "
                                        :alt="item.product.name"
                                    />
                                </Link>
                                <div class="product-name">
                                    <h5>
                                        <Link
                                            :href="
                                                route(
                                                    'store.brand-partner.product',
                                                    item.product.slug,
                                                )
                                            "
                                        >
                                            {{ item.product.name }}
                                        </Link>
                                    </h5>
                                    <h6 v-if="item.color || item.size" class="text-muted" style="font-size:12px;">
                                        <span v-if="item.color">Color: <strong>{{ item.color }}</strong></span>
                                        <span v-if="item.size" class="ms-2">Size: <strong>{{ item.size }}</strong></span>
                                    </h6>
                                    <h6 v-else-if="item.product.sku" style="font-size:12px;">
                                        SKU: {{ item.product.sku }}
                                    </h6>
                                    <div class="qty-controls">
                                        <button
                                            class="qty-btn"
                                            @click="
                                                updateQuantity(
                                                    item.id,
                                                    item.quantity - 1,
                                                )
                                            "
                                            :disabled="item.quantity <= 1"
                                        >
                                            <i class="ri-subtract-line"></i>
                                        </button>
                                        <input
                                            type="number"
                                            class="qty-input"
                                            :value="item.quantity"
                                            min="1"
                                            @change="
                                                updateQuantity(
                                                    item.id,
                                                    $event.target.value,
                                                )
                                            "
                                        />
                                        <button
                                            class="qty-btn"
                                            @click="
                                                updateQuantity(
                                                    item.id,
                                                    item.quantity + 1,
                                                )
                                            "
                                        >
                                            <i class="ri-add-line"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="cart-box-right">
                                <h5 class="item-price">
                                    {{ formatCurrency(item.total) }}
                                </h5>
                                <span class="item-unit-price"
                                    >{{ formatCurrency(item.price) }} each</span
                                >
                                <button
                                    class="delete-btn"
                                    @click="removeItem(item.id)"
                                    title="Remove item"
                                >
                                    <i class="ri-delete-bin-line"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <button class="clear-cart-btn" @click="clearCart">
                        <i class="ri-delete-bin-line"></i> Clear Cart
                    </button>
                </div>

                <!-- Right: Order Summary Sidebar -->
                <div class="cart-summary-sidebar">
                    <div class="summary-box">
                        <h4 class="summary-title">Order Details</h4>
                        <ul class="order-details-list">
                            <li>
                                <span>Subtotal</span>
                                <span>{{ formatCurrency(cart.subtotal) }}</span>
                            </li>
                            <li v-if="cart.discount > 0" class="savings-line">
                                <span>Savings</span>
                                <span class="savings-val"
                                    >-{{ formatCurrency(cart.discount) }}</span
                                >
                            </li>
                            <li>
                                <span>Delivery</span>
                                <span class="delivery-val"
                                    >To be determined</span
                                >
                            </li>
                        </ul>
                        <div class="order-total-row">
                            <span>Total</span>
                            <span>{{ formatCurrency(cart.total) }}</span>
                        </div>
                        <div class="checkout-button-box">
                            <Link
                                :href="
                                    route(
                                        'store.brand-partner.checkout',
                                        brandPartner.slug,
                                    )
                                "
                                class="grocery-btn theme-btn"
                            >
                                Proceed to Checkout
                                <i class="ri-arrow-right-line"></i>
                            </Link>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Empty Cart State -->
            <div v-else class="empty-cart-state">
                <div class="empty-cart-icon">
                    <i class="ri-shopping-cart-2-line"></i>
                </div>
                <h3>Your cart is empty</h3>
                <p>Looks like you haven't added any products yet.</p>
                <Link
                    :href="
                        route('store.brand-partner.index')
                    "
                    class="grocery-btn theme-btn"
                >
                    <i class="ri-store-2-line"></i> Start Shopping
                </Link>
            </div>
        </div>
    </div>
</template>

<script setup>
import { Head, Link, router } from '@inertiajs/vue3';

const props = defineProps({
    brandPartner: Object,
    cart: {
        type: Object,
        default: () => ({
            items: [],
            subtotal: 0,
            discount: 0,
            total: 0,
        }),
    },
});

const formatCurrency = (amount) => {
    return new Intl.NumberFormat('en-PH', {
        style: 'currency',
        currency: 'PHP',
    }).format(amount / 100);
};

const updateQuantity = (itemId, quantity) => {
    if (quantity < 1) return;
    router.patch(
        route('store.brand-partner.cart.update', itemId),
        { quantity: parseInt(quantity) },
        { preserveScroll: true },
    );
};

const removeItem = (itemId) => {
    router.delete(
        route('store.brand-partner.cart.remove', itemId),
        { preserveScroll: true },
    );
};

const clearCart = () => {
    if (confirm('Are you sure you want to clear your cart?')) {
        router.delete(
            route('store.brand-partner.cart.clear'),
            {
                preserveScroll: true,
            },
        );
    }
};
</script>

<style scoped>
.grocery-cart-section {
    min-height: 60vh;
    padding-bottom: 40px;
    font-family: 'Public Sans', sans-serif;
    background: rgb(var(--grocery-light-bg));
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
.grocery-header {
    background: #fff;
    padding: 16px 0;
    border-bottom: 1px solid #f0f0f0;
    margin-bottom: 24px;
}

.grocery-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 16px;
}

.header-inner {
    display: flex;
    align-items: center;
    gap: 12px;
}

.header-back {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 36px;
    height: 36px;
    border-radius: 50%;
    background: rgb(var(--grocery-light-bg));
    color: rgb(var(--grocery-title));
    text-decoration: none;
    font-size: 20px;
    transition: background 0.2s;
}

.header-back:hover {
    background: rgb(var(--grocery-border));
}

.header-title {
    font-size: 20px;
    font-weight: 800;
    color: rgb(var(--grocery-title));
    margin: 0;
    flex: 1;
    display: flex;
    align-items: center;
    gap: 8px;
}

.cart-badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    background: rgb(var(--grocery-theme));
    color: #fff;
    font-size: 12px;
    font-weight: 700;
    min-width: 22px;
    height: 22px;
    border-radius: 11px;
    padding: 0 6px;
}

.continue-link {
    font-size: 13px;
    font-weight: 600;
    color: rgb(var(--grocery-theme));
    text-decoration: none;
    white-space: nowrap;
}

.continue-link:hover {
    color: rgba(var(--grocery-theme), 0.8);
}

/* Cart Grid */
.cart-grid {
    display: grid;
    grid-template-columns: 1fr 360px;
    gap: 24px;
    align-items: start;
}

/* Cart Items */
.cart-items-section {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.cart-box-list {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.cart-box {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    background: #fff;
    border-radius: 14px;
    padding: 16px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
    transition: box-shadow 0.2s;
}

.cart-box:hover {
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.07);
}

.cart-box-left {
    display: flex;
    gap: 14px;
    flex: 1;
    min-width: 0;
}

.product-image {
    flex-shrink: 0;
}

.product-image img {
    width: 80px;
    height: 80px;
    object-fit: cover;
    border-radius: 12px;
    background: #f5f5f5;
}

.product-name {
    flex: 1;
    min-width: 0;
}

.product-name h5 {
    font-size: 15px;
    font-weight: 700;
    margin: 0 0 2px;
    line-height: 1.3;
}

.product-name h5 a {
    color: rgb(var(--grocery-title));
    text-decoration: none;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.product-name h5 a:hover {
    color: rgb(var(--grocery-theme));
}

.product-name h6 {
    font-size: 11px;
    font-weight: 400;
    color: rgb(var(--grocery-content));
    margin: 0 0 10px;
}

/* Quantity Controls */
.qty-controls {
    display: inline-flex;
    align-items: center;
    background: #f5f5f5;
    border-radius: 10px;
    overflow: hidden;
}

.qty-btn {
    width: 32px;
    height: 32px;
    border: none;
    background: none;
    color: rgb(var(--grocery-theme));
    font-size: 14px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: background 0.15s;
}

.qty-btn:hover:not(:disabled) {
    background: rgba(var(--grocery-theme), 0.08);
}

.qty-btn:disabled {
    color: #ccc;
    cursor: not-allowed;
}

.qty-input {
    width: 38px;
    height: 32px;
    border: none;
    background: none;
    text-align: center;
    font-weight: 700;
    font-size: 14px;
    font-family: 'Public Sans', sans-serif;
    color: rgb(var(--grocery-title));
    outline: none;
    -moz-appearance: textfield;
}

.qty-input::-webkit-outer-spin-button,
.qty-input::-webkit-inner-spin-button {
    -webkit-appearance: none;
    margin: 0;
}

/* Cart Box Right */
.cart-box-right {
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    gap: 4px;
    flex-shrink: 0;
    margin-left: 12px;
}

.item-price {
    font-size: 16px;
    font-weight: 800;
    color: rgb(var(--grocery-theme));
    margin: 0;
}

.item-unit-price {
    font-size: 11px;
    color: rgb(var(--grocery-content));
}

.delete-btn {
    background: none;
    border: none;
    color: rgb(var(--grocery-content));
    font-size: 18px;
    cursor: pointer;
    padding: 4px;
    margin-top: 4px;
    border-radius: 6px;
    transition:
        background 0.15s,
        transform 0.15s;
}

.delete-btn:hover {
    background: rgba(var(--grocery-content), 0.1);
    transform: scale(1.1);
}

/* Clear Cart */
.clear-cart-btn {
    background: none;
    border: 1.5px solid rgb(var(--grocery-content));
    color: rgb(var(--grocery-content));
    border-radius: 12px;
    padding: 12px;
    font-size: 13px;
    font-weight: 600;
    font-family: 'Public Sans', sans-serif;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    transition: background 0.2s;
}

.clear-cart-btn:hover {
    background: rgba(var(--grocery-content), 0.05);
}

.delete-btn:hover {
    background: #fff0f0;
    transform: scale(1.1);
}

/* Clear Cart */
.clear-cart-btn {
    background: none;
    border: 1.5px solid #ffcdd2;
    color: #ff4757;
    border-radius: 12px;
    padding: 12px;
    font-size: 13px;
    font-weight: 600;
    font-family: 'Public Sans', sans-serif;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    transition: background 0.2s;
}

.clear-cart-btn:hover {
    background: #fff5f5;
}

/* Summary Sidebar */
.cart-summary-sidebar {
    position: sticky;
    top: 80px;
}

.summary-box {
    background: #fff;
    border-radius: 14px;
    padding: 24px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
}

.summary-title {
    font-size: 18px;
    font-weight: 800;
    color: rgb(var(--grocery-title));
    margin: 0 0 18px;
}

.order-details-list {
    list-style: none;
    padding: 0;
    margin: 0 0 16px;
}

.order-details-list li {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 8px 0;
    font-size: 14px;
    color: rgb(var(--grocery-content));
    border-bottom: 1px solid rgb(var(--grocery-border));
}

.order-details-list li:last-child {
    border-bottom: none;
}

.savings-val {
    color: rgb(var(--grocery-rating));
    font-weight: 600;
}

.delivery-val {
    font-size: 12px;
    color: rgb(var(--grocery-content));
    font-style: italic;
}

.order-total-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 14px 0;
    border-top: 2px solid rgb(var(--grocery-border));
    margin-bottom: 18px;
}

.order-total-row span:first-child {
    font-size: 16px;
    font-weight: 800;
    color: rgb(var(--grocery-title));
}

.order-total-row span:last-child {
    font-size: 20px;
    font-weight: 900;
    color: rgb(var(--grocery-theme));
}

/* Checkout Button */
.checkout-button-box {
    display: flex;
}

.grocery-btn.theme-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    width: 100%;
    padding: 14px 24px;
    background: rgb(var(--grocery-theme));
    color: #fff;
    border: none;
    border-radius: 12px;
    font-size: 15px;
    font-weight: 700;
    font-family: 'Public Sans', sans-serif;
    text-decoration: none;
    cursor: pointer;
    transition:
        background 0.2s,
        transform 0.15s;
}

.grocery-btn.theme-btn:hover {
    background: rgba(var(--grocery-theme), 0.9);
    color: #fff;
    transform: translateY(-1px);
}

.grocery-btn.theme-btn:hover {
    background: #e67a1f;
    color: #fff;
    transform: translateY(-1px);
}

/* Empty Cart */
.empty-cart-state {
    text-align: center;
    padding: 80px 20px;
    background: #fff;
    border-radius: 14px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
}

.empty-cart-icon {
    width: 110px;
    height: 110px;
    background: rgba(var(--grocery-theme), 0.1);
    border-radius: 50%;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 24px;
}

.empty-cart-icon i {
    font-size: 48px;
    color: rgb(var(--grocery-theme));
}

.empty-cart-state h3 {
    font-size: 22px;
    font-weight: 800;
    color: rgb(var(--grocery-title));
    margin: 0 0 8px;
}

.empty-cart-state p {
    font-size: 14px;
    color: rgb(var(--grocery-content));
    margin: 0 0 24px;
}

.empty-cart-state .grocery-btn.theme-btn {
    display: inline-flex;
    width: auto;
}

/* Responsive */
@media (max-width: 768px) {
    .cart-grid {
        grid-template-columns: 1fr;
        gap: 16px;
    }

    .cart-summary-sidebar {
        position: static;
    }

    .continue-link {
        display: none;
    }

    .product-image img {
        width: 64px;
        height: 64px;
    }

    .cart-box {
        padding: 12px;
        flex-direction: column;
        gap: 12px;
    }

    .cart-box-right {
        flex-direction: row;
        align-items: center;
        justify-content: space-between;
        width: 100%;
        margin-left: 0;
        padding-top: 10px;
        border-top: 1px solid #f5f5f5;
    }

    .cart-box-right .delete-btn {
        margin-top: 0;
    }
}
</style>
