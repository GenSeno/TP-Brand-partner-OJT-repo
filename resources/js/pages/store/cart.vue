<template>
    <Head :title="`My Cart - ${brandPartner?.name ?? 'Store'}`" />

    <div class="account-cart-section">
        <div class="custom-container container">
            <!-- Breadcrumbs -->
            <div class="breadcrumbs mt-4 mb-5">
                <span class="text-muted" style="font-size: 12px;">Home > </span>
                <span style="font-size: 12px; color: #111;">My Cart</span>
            </div>

            <div class="row">
                <!-- Sidebar -->
                <div class="col-md-3">
                    <h2 class="account-title mb-4">My Account</h2>
                    <ul class="account-sidebar-menu">
                        <li><a href="#">My Profile</a></li>
                        <li><a href="#">My Orders</a></li>
                        <li><a href="#">My Addresses</a></li>
                        <li class="active"><Link :href="route('store.brand-partner.cart')">My Cart</Link></li>
                        <li><a href="#">My Wishlist</a></li>
                        <li><a href="#">Logout</a></li>
                    </ul>
                </div>

                <!-- Main Content -->
                <div class="col-md-9 px-lg-4">
                    <div v-if="cart && cart.items && cart.items.length > 0">
                        <!-- Table Header -->
                        <div class="cart-table-header d-flex text-muted fw-bold pb-3 border-bottom mb-4">
                            <div style="flex: 2; font-size: 13px; font-weight: 700;">Item</div>
                            <div style="flex: 1; text-align: center; font-size: 13px; font-weight: 700;">Price</div>
                            <div style="flex: 1; text-align: center; font-size: 13px; font-weight: 700;">Quantity</div>
                            <div style="flex: 1; text-align: center; font-size: 13px; font-weight: 700;">Total</div>
                            <div style="width: 70px;"></div>
                        </div>

                        <!-- Table Rows -->
                        <div 
                            class="cart-item-row d-flex align-items-center mb-4 pb-4 border-bottom" 
                            v-for="item in cart.items" 
                            :key="item.id"
                        >
                            <!-- Item Column -->
                            <div class="item-col d-flex gap-4" style="flex: 2;">
                                <div class="item-image bg-light d-flex align-items-center justify-content-center p-2" style="width: 120px; height: 120px; flex-shrink: 0;">
                                    <img 
                                        :src="item.product?.image_url || '/img/tshirt-placeholder.svg'" 
                                        alt="Product" 
                                        class="img-fluid" 
                                        style="max-height: 100px; object-fit: contain;"
                                    />
                                </div>
                                <div class="item-details d-flex flex-column justify-content-center">
                                    <h5 class="item-name fw-bold mb-1" style="font-size: 13px; font-family: 'Public Sans', sans-serif;">
                                        {{ item.product?.name || 'Product Name Goes Here' }}
                                    </h5>
                                    
                                    <div class="mb-2">
                                        <span class="badge" style="font-size: 8px; border-radius: 12px; padding: 4px 10px; font-weight: 800; background-color: #ffb800; color: #fff; letter-spacing: 0.5px;">
                                            PRE-ORDER
                                        </span>
                                    </div>
                                    
                                    <p class="text-muted mb-1" style="font-size: 12px;" v-if="item.product?.short_description">
                                        Garment: {{ item.product.short_description }}
                                    </p>
                                    <p class="text-muted mb-1" style="font-size: 12px;" v-else>
                                        Garment: Longsleeves
                                    </p>
                                    
                                    <p class="text-muted mb-1" style="font-size: 12px;">
                                        Design: SPace Adventure
                                    </p>
                                    
                                    <p class="text-muted mb-0" style="font-size: 12px;" v-if="item.size">
                                        Size: {{ item.size }}
                                    </p>
                                    <p class="text-muted mb-0" style="font-size: 12px;" v-else-if="item.color">
                                        Color: {{ item.color }}
                                    </p>
                                    <p class="text-muted mb-0" style="font-size: 12px;" v-else>
                                        Size: XXS
                                    </p>
                                </div>
                            </div>
                            
                            <!-- Price Column -->
                            <div class="price-col text-center d-flex flex-column justify-content-center" style="flex: 1;">
                                <div class="fw-bold" style="color: #ff5252; font-size: 12px;">{{ formatCurrencyPHP(item.price) }}</div>
                                <small class="text-muted text-decoration-line-through mt-1" style="font-size: 11px;" v-if="item.product?.compare_price && item.product.compare_price > item.price">
                                    {{ formatCurrencyPHP(item.product.compare_price) }}
                                </small>
                            </div>
                            
                            <!-- Quantity Column -->
                            <div class="qty-col d-flex justify-content-center align-items-center" style="flex: 1;">
                                <div class="qty-box d-flex align-items-center m-auto">
                                    <button class="qty-btn text-success" @click="updateQuantity(item.id, item.quantity - 1)" :disabled="item.quantity <= 1">
                                        <i class="ri-subtract-line"></i>
                                    </button>
                                    <input type="text" class="qty-input" :value="item.quantity" @change="updateQuantity(item.id, $event.target.value)" />
                                    <button class="qty-btn text-success" @click="updateQuantity(item.id, item.quantity + 1)">
                                        <i class="ri-add-line"></i>
                                    </button>
                                </div>
                            </div>
                            
                            <!-- Total Column -->
                            <div class="total-col text-center d-flex justify-content-center align-items-center" style="flex: 1;">
                                <span class="text-muted" style="font-size: 12px;">{{ formatCurrencyPHP(item.total) }}</span>
                            </div>
                            
                            <!-- Delete Column -->
                            <div class="delete-col text-end border-0 bg-transparent flex-shrink-0" style="width: 70px;">
                                <button class="btn btn-link text-dark fw-bold text-decoration-none p-0" style="font-size: 11px; font-family: 'Public Sans', sans-serif;" @click="removeItem(item.id)">
                                    DELETE
                                </button>
                            </div>
                        </div>

                    </div>
                    
                    <div v-else class="text-center py-5 border bg-light mt-4">
                        <i class="ri-shopping-cart-2-line text-muted mb-3" style="font-size: 48px;"></i>
                        <h4 class="fw-bold fs-5">Your cart is empty</h4>
                        <p class="text-muted fs-6 mb-4">Looks like you haven't added any products yet.</p>
                        <Link :href="route('store.brand-partner.index')" class="btn btn-dark px-4 py-2 rounded-0 fw-bold">
                            START SHOPPING
                        </Link>
                    </div>
                </div>
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

const formatCurrencyPHP = (amount) => {
    return 'PHP ' + (amount / 100).toLocaleString('en-PH', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    });
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
    router.delete(route('store.brand-partner.cart.remove', itemId), {
        preserveScroll: true,
    });
};

const clearCart = () => {
    if (confirm('Are you sure you want to clear your cart?')) {
        router.delete(route('store.brand-partner.cart.clear'), {
            preserveScroll: true,
        });
    }
};
</script>

<style scoped>
.account-cart-section {
    min-height: 60vh;
    padding-bottom: 80px;
    padding-top: 50px;
    font-family: 'Public Sans', sans-serif;
    background: #fff;
    padding-top: 100px;
}

.account-title {
    font-size: 24px;
    font-weight: 800;
    color: #111;
}

/* Sidebar Menu */
.account-sidebar-menu {
    list-style: none;
    padding: 0;
    margin: 0;
    border-top: 1px solid #f0f0f0;
}

.account-sidebar-menu li {
    border-bottom: 1px solid #f0f0f0;
}

.account-sidebar-menu li a {
    display: block;
    padding: 16px 20px;
    color: #444;
    text-decoration: none;
    font-size: 13px;
    font-weight: 500;
    font-family: 'Public Sans', sans-serif;
    transition: all 0.2s ease;
}

.account-sidebar-menu li.active a {
    background-color: #055122; /* Dark green matching mockup */
    color: #fff;
    font-weight: 600;
}

.account-sidebar-menu li a:hover:not(.active > a) {
    background-color: #f7f7f7;
    color: #111;
}

/* Qty Input Box matching mockup */
.qty-box {
    border: 1px solid #e0e0e0;
    height: 38px;
    width: 100px;
}

.qty-btn {
    width: 30px;
    height: 100%;
    border: none;
    background: transparent;
    font-size: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: background 0.2s;
}

.qty-btn:hover:not(:disabled) {
    background: rgba(25, 135, 84, 0.1);
}

.qty-btn:disabled {
    color: #ccc !important;
    cursor: not-allowed;
}

.qty-input {
    width: 40px;
    height: 100%;
    border: none;
    border-left: 1px solid #e0e0e0;
    border-right: 1px solid #e0e0e0;
    text-align: center;
    font-size: 13px;
    color: #666;
    outline: none;
    padding: 0;
}

.delete-col button:hover {
    color: #ff5252 !important;
}

@media (max-width: 768px) {
    .cart-table-header {
        display: none !important; /* Hide headers on mobile */
    }
    
    .cart-item-row {
        flex-direction: column;
        align-items: flex-start !important;
        position: relative;
    }
    
    .item-col {
        width: 100%;
        margin-bottom: 20px;
    }
    
    .price-col, .qty-col, .total-col, .delete-col {
        width: 100%;
        text-align: left !important;
        justify-content: flex-start !important;
        margin-bottom: 15px;
    }
    
    .delete-col {
        position: absolute;
        top: 0;
        right: 0;
        width: auto;
    }
}
</style>
