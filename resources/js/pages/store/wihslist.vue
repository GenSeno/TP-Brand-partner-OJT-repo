<template>
    <Head :title="`Wishlist - ${brandPartner.name}`" />

    <div class="wishlist-section">
        <!-- Header -->
        <div class="wishlist-header">
            <div class="wishlist-container">
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
                    <h2 class="header-title">
                        My Wishlist
                        <span class="wishlist-count" v-if="items.length > 0">{{
                            items.length
                        }}</span>
                    </h2>
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

        <div class="wishlist-container">
            <!-- Items Grid -->
            <div v-if="items.length > 0" class="wishlist-grid">
                <div class="wishlist-card" v-for="item in items" :key="item.id">
                    <!-- Remove button -->
                    <button
                        class="remove-btn"
                        @click="removeItem(item.id)"
                        title="Remove from wishlist"
                    >
                        <i class="ri-close-line"></i>
                    </button>

                    <!-- Product Image -->
                    <Link
                        :href="
                            route(
                                'store.brand-partner.product',
                                item.product.slug,
                            )
                        "
                        class="card-img-link"
                    >
                        <img
                            :src="
                                item.product.image_url ||
                                '/img/tshirt-placeholder.svg'
                            "
                            :alt="item.product.name"
                            class="card-img"
                        />
                        <div class="card-img-overlay">
                            <span class="quick-view">View Product</span>
                        </div>
                    </Link>

                    <!-- Product Info -->
                    <div class="card-body">
                        <Link
                            :href="
                                route(
                                    'store.brand-partner.product',
                                    item.product.slug,
                                )
                            "
                            class="card-name"
                        >
                            {{ item.product.name }}
                        </Link>

                        <div class="card-price-row">
                            <span class="card-price">{{
                                formatCurrency(item.product.price)
                            }}</span>
                            <span
                                class="card-old-price"
                                v-if="
                                    item.product.compare_price &&
                                    item.product.compare_price >
                                        item.product.price
                                "
                            >
                                {{ formatCurrency(item.product.compare_price) }}
                            </span>
                        </div>

                        <button
                            class="add-to-cart-btn"
                            @click="addToCart(item.product)"
                            :disabled="addingToCart === item.product.id"
                        >
                            <i class="ri-shopping-cart-line"></i>
                            <span v-if="addingToCart === item.product.id"
                                >Adding...</span
                            >
                            <span v-else>Add to Cart</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Empty State -->
            <div v-else class="empty-wishlist">
                <div class="empty-icon">
                    <i class="ri-heart-line"></i>
                </div>
                <h3>Your wishlist is empty</h3>
                <p>
                    Save your favorite items here and come back to them anytime.
                </p>
                <Link
                    :href="
                        route('store.brand-partner.index', brandPartner.slug)
                    "
                    class="shop-btn"
                >
                    <i class="ri-store-2-line"></i> Start Shopping
                </Link>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';

const props = defineProps({
    brandPartner: Object,
    items: Array,
});

const addingToCart = ref(null);

const formatCurrency = (amount) => {
    return new Intl.NumberFormat('en-PH', {
        style: 'currency',
        currency: 'PHP',
    }).format(amount / 100);
};

const removeItem = (itemId) => {
    router.delete(route('store.brand-partner.wishlist.remove', itemId), {
        preserveScroll: true,
    });
};

const addToCart = (product) => {
    addingToCart.value = product.id;
    router.post(
        route('store.brand-partner.cart.add'),
        { product_id: product.id, quantity: 1 },
        {
            preserveScroll: true,
            onFinish: () => {
                addingToCart.value = null;
            },
        },
    );
};
</script>

<style scoped>
.wishlist-section {
    min-height: 60vh;
    padding-bottom: 60px;
    font-family: 'Public Sans', sans-serif;
    background: #f9f9f9;
}

/* Header */
.wishlist-header {
    background: #fff;
    padding: 16px 0;
    border-bottom: 1px solid #f0f0f0;
    margin-bottom: 32px;
    margin-top: 80px;
}

.wishlist-container {
    max-width: 1100px;
    margin: 0 auto;
    padding: 0 24px;
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
    background: #f5f5f5;
    border: 1.5px solid #ff9505;
    color: #ff9505;
    text-decoration: none;
    font-size: 20px;
    transition: background 0.2s;
    flex-shrink: 0;
}

.header-back:hover {
    background: #fff3e0;
}

.header-title {
    font-size: 20px;
    font-weight: 800;
    color: #1b1b3e;
    margin: 0;
    flex: 1;
    display: flex;
    align-items: center;
    gap: 10px;
}

.wishlist-count {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    background: #ff9505;
    color: #fff;
    font-size: 12px;
    font-weight: 700;
    width: 24px;
    height: 24px;
    border-radius: 50%;
}

.continue-link {
    font-size: 13px;
    font-weight: 600;
    color: #ff9505;
    text-decoration: none;
    white-space: nowrap;
}

.continue-link:hover {
    text-decoration: underline;
}

/* Grid */
.wishlist-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
    gap: 20px;
}

/* Card */
.wishlist-card {
    background: #fff;
    border-radius: 14px;
    border: 1px solid #eee;
    overflow: hidden;
    position: relative;
    transition:
        box-shadow 0.2s,
        transform 0.2s;
}

.wishlist-card:hover {
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
    transform: translateY(-2px);
}

.remove-btn {
    position: absolute;
    top: 10px;
    right: 10px;
    z-index: 2;
    width: 28px;
    height: 28px;
    border-radius: 50%;
    border: none;
    background: rgba(255, 255, 255, 0.9);
    color: #aaa;
    font-size: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition:
        background 0.2s,
        color 0.2s;
    backdrop-filter: blur(4px);
}

.remove-btn:hover {
    background: #fff;
    color: #e74c3c;
}

.card-img-link {
    display: block;
    position: relative;
    overflow: hidden;
    aspect-ratio: 1;
}

.card-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s;
}

.wishlist-card:hover .card-img {
    transform: scale(1.04);
}

.card-img-overlay {
    position: absolute;
    inset: 0;
    background: rgba(0, 0, 0, 0.3);
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: opacity 0.2s;
}

.wishlist-card:hover .card-img-overlay {
    opacity: 1;
}

.quick-view {
    background: #fff;
    color: #1b1b3e;
    font-size: 12px;
    font-weight: 700;
    padding: 8px 18px;
    border-radius: 20px;
    letter-spacing: 0.5px;
}

.card-body {
    padding: 14px 16px 16px;
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.card-name {
    font-size: 14px;
    font-weight: 700;
    color: #1b1b3e;
    text-decoration: none;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    line-height: 1.3;
    transition: color 0.2s;
}

.card-name:hover {
    color: #ff9505;
}

.card-price-row {
    display: flex;
    align-items: center;
    gap: 8px;
}

.card-price {
    font-size: 15px;
    font-weight: 800;
    color: #ff9505;
}

.card-old-price {
    font-size: 12px;
    color: #bbb;
    text-decoration: line-through;
}

.add-to-cart-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    width: 100%;
    padding: 10px;
    background: #1b5e38;
    color: #fff;
    border: none;
    border-radius: 8px;
    font-size: 13px;
    font-weight: 700;
    font-family: 'Public Sans', sans-serif;
    cursor: pointer;
    transition: background 0.2s;
    margin-top: 4px;
}

.add-to-cart-btn:hover:not(:disabled) {
    background: #ff9505;
}
.add-to-cart-btn:disabled {
    background: #ccc;
    cursor: not-allowed;
}

/* Empty State */
.empty-wishlist {
    text-align: center;
    padding: 80px 20px;
    background: #fff;
    border-radius: 14px;
    border: 1px solid #eee;
}

.empty-icon {
    width: 110px;
    height: 110px;
    background: rgba(255, 149, 5, 0.08);
    border-radius: 50%;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 24px;
}

.empty-icon i {
    font-size: 48px;
    color: #ff9505;
}

.empty-wishlist h3 {
    font-size: 22px;
    font-weight: 800;
    color: #1b1b3e;
    margin: 0 0 8px;
}

.empty-wishlist p {
    font-size: 14px;
    color: #888;
    margin: 0 0 28px;
}

.shop-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 12px 28px;
    background: #ff9505;
    color: #fff;
    border-radius: 10px;
    font-size: 13px;
    font-weight: 800;
    text-decoration: none;
    letter-spacing: 0.5px;
    transition: background 0.2s;
}

.shop-btn:hover {
    background: #1b5e38;
    color: #fff;
}

@media (max-width: 768px) {
    .wishlist-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 12px;
    }
    .wishlist-header {
        margin-top: 60px;
    }
}

@media (max-width: 420px) {
    .wishlist-grid {
        grid-template-columns: 1fr;
    }
}
</style>
