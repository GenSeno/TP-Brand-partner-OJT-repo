<template>
    <div class="grocery-color public-san-body">
        <!-- Header Start -->
        <header class="header-style-6">
            <div class="header-inner">
                <div class="left-header">
                    <Link
                        v-if="brandPartner"
                        :href="
                            route(
                                'store.brand-partner.index',
                                brandPartner.slug,
                            )
                        "
                        class="brand-link"
                    >
                        <img
                            v-if="brandPartner.logo_url"
                            :src="brandPartner.logo_url"
                            :alt="brandPartner.name"
                            class="img-fluid brand-logo"
                        />
                        <span class="brand-name">{{
                            brandPartner.name || 'Store'
                        }}</span>
                    </Link>
                    <span v-else class="brand-link">
                        <span class="brand-name">Store</span>
                    </span>
                </div>

                <!-- Mobile Menu Button -->
                <div class="mobile-menu-header">
                    <button
                        type="button"
                        class="btn menu-btn"
                        @click="toggleSideMenu"
                    >
                        <i class="ri-menu-line"></i>
                    </button>
                </div>

                <!-- Desktop Nav -->
                <div class="desktop-nav-wrapper" v-if="brandPartner">
                    <nav class="desktop-nav">
                        <Link
                            :href="
                                route(
                                    'store.brand-partner.index',
                                    brandPartner.slug,
                                )
                            "
                            class="nav-shop"
                            :class="{
                                active: isRoute('store.brand-partner.index'),
                            }"
                        >
                            <i class="ri-shopping-bag-3-line"></i>
                            Shop
                        </Link>
                        <Link
                            :href="
                                route(
                                    'store.brand-partner.cart',
                                    brandPartner.slug,
                                )
                            "
                            class="nav-cart"
                            :class="{
                                active: isRoute('store.brand-partner.cart'),
                            }"
                        >
                            <i class="ri-shopping-cart-line"></i>
                            Cart
                            <span class="cart-badge" v-if="cartCount > 0">{{
                                cartCount
                            }}</span>
                        </Link>
                        <Link
                            :href="
                                route(
                                    'store.brand-partner.checkout',
                                    brandPartner.slug,
                                )
                            "
                            class="nav-checkout"
                            :class="{
                                active: isRoute('store.brand-partner.checkout'),
                            }"
                        >
                            <i class="ri-file-list-3-line"></i>
                            Checkout
                        </Link>
                    </nav>
                </div>
            </div>
        </header>
        <!-- Header End -->

        <!-- Mobile Bottom Nav -->
        <div class="mobile-style-6" v-if="brandPartner">
            <ul>
                <li :class="{ active: isRoute('store.brand-partner.index') }">
                    <Link
                        :href="
                            route(
                                'store.brand-partner.index',
                                brandPartner.slug,
                            )
                        "
                        class="mobile-box"
                    >
                        <i class="ri-home-line"></i>
                        <h6>Home</h6>
                    </Link>
                </li>
                <li :class="{ active: isRoute('store.brand-partner.cart') }">
                    <Link
                        :href="
                            route('store.brand-partner.cart', brandPartner.slug)
                        "
                        class="mobile-box cart-mobile-link"
                    >
                        <i class="ri-shopping-cart-line"></i>
                        <span class="mobile-cart-badge" v-if="cartCount > 0">{{
                            cartCount
                        }}</span>
                        <h6>Cart</h6>
                    </Link>
                </li>
                <li class="search-mobile-item">
                    <a
                        href="javascript:void(0)"
                        class="mobile-box"
                        @click="focusSearchField"
                    >
                        <i class="ri-search-line"></i>
                        <h6>Search</h6>
                    </a>
                </li>
                <li
                    :class="{ active: isRoute('store.brand-partner.checkout') }"
                >
                    <Link
                        :href="
                            route(
                                'store.brand-partner.checkout',
                                brandPartner.slug,
                            )
                        "
                        class="mobile-box"
                    >
                        <i class="ri-file-list-3-line"></i>
                        <h6>Checkout</h6>
                    </Link>
                </li>
            </ul>
        </div>
        <!-- Mobile Bottom Nav End -->

        <!-- Side Menu Offcanvas -->
        <div
            class="offcanvas-overlay"
            :class="{ show: sideMenuOpen }"
            @click="toggleSideMenu"
        ></div>
        <div class="offcanvas grocery-sidemenu" :class="{ show: sideMenuOpen }">
            <div class="offcanvas-body">
                <div class="profile-box" v-if="brandPartner">
                    <div class="profile-img">
                        <img
                            v-if="brandPartner.logo_url"
                            :src="brandPartner.logo_url"
                            :alt="brandPartner.name"
                            class="img-fluid"
                        />
                        <i v-else class="ri-store-2-line"></i>
                    </div>
                    <div class="profile-content">
                        <h4>{{ brandPartner.name }}</h4>
                        <h5>Online Store</h5>
                    </div>
                </div>

                <ul class="menu-list" v-if="brandPartner">
                    <li>
                        <Link
                            :href="
                                route(
                                    'store.brand-partner.index',
                                    brandPartner.slug,
                                )
                            "
                            @click="toggleSideMenu"
                        >
                            <i class="ri-home-line"></i> Home
                        </Link>
                    </li>
                    <li>
                        <Link
                            :href="
                                route(
                                    'store.brand-partner.cart',
                                    brandPartner.slug,
                                )
                            "
                            @click="toggleSideMenu"
                        >
                            <i class="ri-shopping-cart-line"></i> Cart
                            <span class="menu-badge" v-if="cartCount > 0">{{
                                cartCount
                            }}</span>
                        </Link>
                    </li>
                    <li>
                        <Link
                            :href="
                                route(
                                    'store.brand-partner.checkout',
                                    brandPartner.slug,
                                )
                            "
                            @click="toggleSideMenu"
                        >
                            <i class="ri-file-list-3-line"></i> Checkout
                        </Link>
                    </li>
                </ul>
            </div>
        </div>
        <!-- Side Menu End -->

        <!-- Main Content -->
        <main class="store-main">
            <slot />
        </main>

        <!-- Footer -->
        <footer class="store-footer">
            <div class="custom-container">
                <div class="footer-grid">
                    <div class="footer-brand">
                        <img
                            src="/img/logo/logo-pdf.png"
                            alt="TP Ink Lab"
                            class="footer-logo"
                        />
                        <p>Quality custom printing solutions</p>
                    </div>
                    <div class="footer-info">
                        <h5>Contact Us</h5>
                        <p>
                            <i class="ri-mail-line"></i> contact@printmyshirt.ph
                        </p>
                        <p><i class="ri-phone-line"></i> +63 9923090084</p>
                    </div>
                    <div class="footer-info">
                        <h5>Location</h5>
                        <p>
                            <i class="ri-map-pin-line"></i> Charlotte Dormitel
                            Bldg, Roxas, Davao City
                        </p>
                    </div>
                </div>
                <div class="footer-bottom">
                    <p>
                        &copy; {{ new Date().getFullYear() }} TP Ink Lab. All
                        rights reserved.
                    </p>
                </div>
            </div>
        </footer>
    </div>
</template>

<script setup>
import { Link, usePage, router } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const page = usePage();

const brandPartner = computed(() => page.props.brandPartner);
const cartCount = computed(() => page.props.cartCount || 0);
const sideMenuOpen = ref(false);

const isRoute = (name) => {
    return route().current(name);
};

const toggleSideMenu = () => {
    // Only allow side menu toggle in mobile view
    if (window.innerWidth < 769) {
        sideMenuOpen.value = !sideMenuOpen.value;
    }
};

const focusSearchOnPage = () => {
    setTimeout(() => {
        const searchInput = document.querySelector(
            'input[type="search"], .search-input, input[placeholder*="search" i]',
        );
        if (searchInput) {
            searchInput.focus();
            searchInput.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    }, 300);
};

const focusSearchField = () => {
    if (sideMenuOpen.value) {
        sideMenuOpen.value = false;
    }

    if (isRoute('store.brand-partner.index')) {
        focusSearchOnPage();
    } else if (brandPartner.value) {
        router.visit(
            route('store.brand-partner.index', brandPartner.value.slug),
            {
                onSuccess: () => {
                    setTimeout(() => focusSearchOnPage(), 500);
                },
            },
        );
    }
};
</script>

<style>
/* ============================================
   GROCERY STORE LAYOUT - Global Styles
   ============================================ */

/* Base */
.public-san-body {
    font-family: 'Public Sans', sans-serif;
    color: #333;
    line-height: 1.6;
    min-height: 100vh;
    min-height: 100dvh;
    display: flex;
    flex-direction: column;
    margin: 0;
    padding: 0;
    overflow-x: clip;
    max-width: 100%;
}

.grocery-color {
    /* Updated Grocery Theme Color Variables */
    --grocery-theme: 60, 133, 153; /* Main teal/cyan color: rgb(60, 133, 153) */
    --grocery-content: 143, 143, 178; /* Light gray-blue content text */
    --grocery-title: 27, 27, 62; /* Dark blue-gray for titles */
    --grocery-border: 232, 232, 232; /* Light gray borders */
    --grocery-primary: 254, 175, 24; /* Yellow/orange accent */
    --grocery-light-bg: 247, 247, 247; /* Light gray background */
    --grocery-rating: 255, 191, 19; /* Gold/yellow for ratings */
    --grocery-success: #2ed573;
    --grocery-danger: #ff4757;
    --grocery-dark: #222;
    --grocery-gray: #777;
    --grocery-light-gray: #999;
    --grocery-bg: #fafafa;

    /* Updated primary color references */
    --grocery-primary-color: rgb(var(--grocery-theme));
    --grocery-primary-light: rgba(var(--grocery-theme), 0.1);
    background: var(--grocery-bg);
}

* {
    box-sizing: border-box;
}

/* Custom Container */
.custom-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 15px;
}

.px-15 {
    padding-left: 15px;
    padding-right: 15px;
}

/* ============================================
   HEADER - header-style-6
   ============================================ */
.header-style-6 {
    background: #fff;
    position: sticky;
    top: 0;
    z-index: 1000;
    box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
    border-bottom: 1px solid rgb(var(--grocery-border));
    width: 100%;
    left: 0;
    right: 0;
}

.header-inner {
    max-width: 1200px;
    margin: 0 auto;
    padding: 4px 15px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    min-height: 42px;
}

.left-header {
    display: flex;
    align-items: center;
    gap: 12px;
    flex: 0 0 auto;
    order: 1; /* Brand on left */
}

.menu-btn {
    background: none;
    border: none;
    padding: 8px;
    font-size: 22px;
    color: rgb(var(--grocery-title));
    cursor: pointer;
    line-height: 1;
    border-radius: 8px;
    transition: background 0.2s ease;
}

.menu-btn:hover {
    background: rgba(var(--grocery-theme), 0.08);
}

.brand-link {
    display: flex;
    align-items: center;
    gap: 12px;
    text-decoration: none;
    color: inherit;
    transition: opacity 0.2s ease;
    align-self: flex-start;
}

.brand-link:hover {
    opacity: 0.8;
}

.brand-logo {
    width: 42px;
    height: 42px;
    border-radius: 50%;
    object-fit: cover;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    border: 2px solid rgb(var(--grocery-border));
}

.brand-name {
    font-size: 20px;
    font-weight: 700;
    color: rgb(var(--grocery-title));
    letter-spacing: -0.5px;
    line-height: 1.1;
}

.menu-btn {
    background: none;
    border: none;
    padding: 6px;
    font-size: 22px;
    color: var(--grocery-dark);
    cursor: pointer;
    line-height: 1;
}

.brand-link {
    display: flex;
    align-items: center;
    gap: 8px;
    text-decoration: none;
    color: inherit;
}

.brand-logo {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    object-fit: cover;
}

.brand-name {
    font-size: 16px;
    font-weight: 700;
    color: var(--grocery-dark);
}

/* Desktop Nav Wrapper */
.desktop-nav-wrapper {
    display: none;
    margin-left: auto;
    order: 3; /* Position on right */
}

/* Desktop Nav - Professional Top Navigation */
.desktop-nav {
    display: flex;
    align-items: center;
    background: transparent;
    border-radius: 0;
    padding: 0;
    box-shadow: none;
    border: none;
    gap: 4px;
    max-width: 300px;
    width: fit-content;
}

/* Professional navigation items */
.desktop-nav a {
    text-decoration: none;
    color: rgb(var(--grocery-content));
    font-size: 14px;
    font-weight: 600;
    padding: 8px 12px;
    border-radius: 8px;
    transition: all 0.25s ease;
    position: relative;
    white-space: nowrap;
    display: flex;
    align-items: center;
    gap: 6px;
    flex: 1;
    justify-content: center;
    text-align: center;
}

.desktop-nav a:hover {
    color: rgb(var(--grocery-theme));
    background: rgba(var(--grocery-theme), 0.08);
}

.desktop-nav a.active {
    color: rgb(var(--grocery-theme));
    background: rgba(var(--grocery-theme), 0.12);
    font-weight: 700;
}

/* Desktop Nav - Show only Shop and Cart */
.desktop-nav .nav-checkout {
    display: none; /* Hide Checkout in desktop nav */
}

.desktop-nav .nav-shop,
.desktop-nav .nav-cart {
    display: flex; /* Show Shop and Cart */
    min-width: 80px; /* Ensure consistent button widths */
}

/* Cart icon styling in desktop nav */
.desktop-nav .nav-cart {
    position: relative;
}

.desktop-nav .nav-cart .cart-badge {
    position: absolute;
    top: -6px;
    right: -6px;
    background: rgb(var(--grocery-primary)); /* Yellow accent */
    color: #fff;
    font-size: 10px;
    font-weight: 700;
    min-width: 16px;
    height: 16px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    line-height: 1;
    box-shadow: 0 2px 4px rgba(var(--grocery-primary), 0.3);
    border: 2px solid #fff;
}

.desktop-nav a {
    text-decoration: none;
    color: rgb(var(--grocery-content));
    font-size: 15px;
    font-weight: 600;
    padding: 10px 16px;
    border: 2px solid transparent;
    border-radius: 8px;
    transition: all 0.25s ease;
    position: relative;
    background: transparent;
}

.desktop-nav a:hover {
    color: rgb(var(--grocery-theme));
    background: rgba(var(--grocery-theme), 0.08);
    border-color: rgba(var(--grocery-theme), 0.2);
    transform: translateY(-1px);
}

.desktop-nav a.active {
    color: rgb(var(--grocery-theme));
    background: rgba(var(--grocery-theme), 0.1);
    border-color: rgb(var(--grocery-theme));
    font-weight: 700;
}

.desktop-nav a::after {
    content: '';
    position: absolute;
    bottom: -2px;
    left: 50%;
    transform: translateX(-50%);
    width: 0;
    height: 2px;
    background: rgb(var(--grocery-theme));
    transition: width 0.25s ease;
}

.desktop-nav a.active::after {
    width: 30px;
}

/* Mobile Menu Button - Only visible on mobile */
.mobile-menu-header {
    display: none;
    order: 3;
}

.cart-badge {
    position: absolute;
    top: -6px;
    right: -6px;
    background: rgb(
        var(--grocery-primary)
    ); /* Using yellow accent for visibility */
    color: #fff;
    font-size: 10px;
    font-weight: 700;
    min-width: 18px;
    height: 18px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    line-height: 1;
    box-shadow: 0 2px 6px rgba(var(--grocery-primary), 0.3);
    border: 2px solid #fff;
}

/* ============================================
   MOBILE BOTTOM NAV - mobile-style-6
   ============================================ */
.mobile-style-6 {
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    width: 100%;
    height: calc(60px + env(safe-area-inset-bottom, 0px));
    background: rgb(var(--grocery-theme));
    box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.08);
    z-index: 9999;
    padding: 8px 0;
    padding-bottom: env(safe-area-inset-bottom, 0px);
    border-top: 1px solid rgba(var(--grocery-theme), 0.2);
    display: flex;
    align-items: center;
}

.mobile-style-6 ul {
    display: flex;
    justify-content: space-around;
    align-items: center;
    list-style: none;
    margin: 0;
    padding: 0;
    width: 100%;
}

.mobile-style-6 li {
    flex: 1;
    text-align: center;
    position: relative;
}

.mobile-box {
    display: flex;
    flex-direction: column;
    align-items: center;
    text-decoration: none;
    color: rgba(255, 255, 255, 0.7);
    gap: 4px;
    position: relative;
    transition: all 0.25s ease;
    cursor: pointer;
    background: none;
    border: none;
    width: 100%;
    padding: 8px 4px;
    font-family: inherit;
    border-radius: 8px;
}

.mobile-style-6 li.active .mobile-box {
    color: #fff;
    background: rgba(255, 255, 255, 0.15);
}

.mobile-box:hover {
    color: #fff;
    background: rgba(255, 255, 255, 0.1);
}

.mobile-style-6 li i {
    font-size: 20px;
    line-height: 1;
    transition: transform 0.2s ease;
}

.mobile-style-6 li.active i {
    transform: scale(1.1);
}

.mobile-style-6 li h6 {
    font-size: 10px;
    font-weight: 600;
    margin: 0;
    transition: color 0.2s ease;
    color: inherit;
}

.cart-mobile-link {
    position: relative;
}

.mobile-cart-badge {
    position: absolute;
    top: -4px;
    right: 50%;
    transform: translateX(14px);
    background: #ff4757;
    color: #fff;
    font-size: 9px;
    font-weight: 700;
    min-width: 18px;
    height: 18px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    line-height: 1;
    border: 2px solid rgb(var(--grocery-theme));
    box-shadow: 0 2px 4px rgba(var(--grocery-theme), 0.3);
}

/* ============================================
   OFFCANVAS SIDE MENU - grocery-sidemenu
   ============================================ */
.offcanvas-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.5);
    z-index: 1040;
    opacity: 0;
    visibility: hidden;
    transition: all 0.3s;
}

.offcanvas-overlay.show {
    opacity: 1;
    visibility: visible;
}

.grocery-sidemenu {
    position: fixed;
    top: 0;
    left: -300px;
    width: 280px;
    height: 100%;
    background: #fff;
    z-index: 1050;
    transition: left 0.3s ease;
    overflow-y: auto;
}

.grocery-sidemenu.show {
    left: 0;
}

/* Mobile only - hide side menu in desktop */
@media (min-width: 769px) {
    .offcanvas-overlay {
        display: none !important;
        visibility: hidden !important;
        opacity: 0 !important;
    }

    .grocery-sidemenu {
        display: none !important;
        visibility: hidden !important;
        transform: translateX(-100%) !important;
    }
}

.grocery-sidemenu .offcanvas-body {
    padding: 24px 20px;
}

.profile-box {
    display: flex;
    align-items: center;
    gap: 12px;
    padding-bottom: 20px;
    margin-bottom: 20px;
    border-bottom: 1px solid var(--grocery-border);
}

.profile-img {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    overflow: hidden;
    background: var(--grocery-primary-light);
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.profile-img img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.profile-img i {
    font-size: 24px;
    color: var(--grocery-primary-color);
}

.profile-content h4 {
    font-size: 16px;
    font-weight: 700;
    margin: 0;
    color: var(--grocery-dark);
}

.profile-content h5 {
    font-size: 12px;
    font-weight: 400;
    margin: 0;
    color: var(--grocery-light-gray);
}

.menu-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.menu-list li {
    margin-bottom: 4px;
}

.menu-list li a {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px 14px;
    border-radius: 10px;
    text-decoration: none;
    color: var(--grocery-dark);
    font-size: 14px;
    font-weight: 500;
    transition: all 0.2s;
}

.menu-list li a:hover {
    background: var(--grocery-primary-light);
    color: var(--grocery-primary-color);
}

.menu-list li a i {
    font-size: 20px;
    width: 22px;
    text-align: center;
}

.menu-badge {
    background: var(--grocery-danger);
    color: #fff;
    font-size: 10px;
    font-weight: 700;
    min-width: 18px;
    height: 18px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-left: auto;
}

/* ============================================
   MAIN CONTENT
   ============================================ */
.store-main {
    flex: 1;
}

/* ============================================
   FOOTER
   ============================================ */
.store-footer {
    background: #2b2b2b;
    color: #ccc;
    padding: 40px 0 20px;
}

.footer-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 30px;
    margin-bottom: 25px;
}

.footer-brand .footer-logo {
    height: 40px;
    margin-bottom: 12px;
    filter: brightness(0) invert(1);
}

.footer-brand p {
    color: #999;
    font-size: 14px;
    margin: 0;
}

.footer-info h5 {
    font-size: 15px;
    font-weight: 700;
    margin-bottom: 12px;
    color: #fff;
}

.footer-info p {
    font-size: 13px;
    color: #999;
    margin-bottom: 6px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.footer-info i {
    color: var(--grocery-primary-color);
    font-size: 15px;
    width: 16px;
}

.footer-bottom {
    border-top: 1px solid #444;
    padding-top: 15px;
    text-align: center;
}

.footer-bottom p {
    font-size: 12px;
    color: #777;
    margin: 0;
}

/* ============================================
   SHARED COMPONENTS
   ============================================ */

/* Section Spacing */
.section-t-space-2 {
    padding-top: 12px;
}
.section-t-space-3 {
    padding-top: 16px;
}
.section-t-space-4 {
    padding-top: 20px;
}
.mb-10 {
    margin-bottom: 10px;
}
.mb-19 {
    margin-bottom: 19px;
}

/* Title */
.title {
    margin-bottom: 12px;
}

.title h4 {
    font-size: 16px;
    font-weight: 700;
    color: var(--grocery-dark);
    margin: 0;
}

.title-2 {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 14px;
}

.title-2 h4 {
    font-size: 16px;
    font-weight: 700;
    color: var(--grocery-dark);
    margin: 0;
}

.theme-color {
    color: var(--grocery-primary-color) !important;
    text-decoration: none;
    font-weight: 600;
    font-size: 13px;
}

.theme-color:hover {
    color: rgba(var(--grocery-theme), 0.8) !important;
}

/* Buttons */
.grocery-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 12px 24px;
    border-radius: 12px;
    font-weight: 700;
    font-family: 'Public Sans', sans-serif;
    font-size: 14px;
    cursor: pointer;
    transition: all 0.2s;
    text-decoration: none;
    border: none;
}

.theme-btn {
    background: var(--grocery-primary-color);
    color: #fff;
}

.theme-btn:hover {
    background: rgba(var(--grocery-theme), 0.9);
    color: #fff;
}

.theme-btn:disabled {
    background: #ddd;
    cursor: not-allowed;
}

.white-btn {
    background: #fff;
    color: var(--grocery-dark);
    border: 1px solid #e0e0e0;
}

.white-btn:hover {
    border-color: var(--grocery-primary-color);
    color: var(--grocery-primary-color);
}

/* Reset */
button,
.btn {
    box-shadow: none !important;
    outline: none !important;
}

button:focus,
button:active,
.btn:focus,
.btn:active {
    box-shadow: none !important;
    outline: none !important;
}

/* ============================================
   DESKTOP STYLES
   ============================================ */
@media (min-width: 769px) {
    .mobile-style-6 {
        display: none;
    }

    /* COMPLETELY hide menu button in desktop */
    .menu-btn {
        display: none !important;
        visibility: hidden !important;
        opacity: 0 !important;
        pointer-events: none !important;
        width: 0 !important;
        overflow: hidden !important;
    }

    .desktop-nav-wrapper {
        display: block;
    }

    .desktop-nav {
        display: flex;
    }

    /* Hide side menu elements completely in desktop */
    .offcanvas-overlay,
    .grocery-sidemenu {
        display: none !important;
        visibility: hidden !important;
        opacity: 0 !important;
        pointer-events: none !important;
        position: fixed !important;
        left: -9999px !important;
    }

    .store-main {
        padding-bottom: 0;
    }

    .store-footer {
        margin-bottom: 0;
    }

    /* Professional desktop layout */
    .left-header {
        flex: 0 0 auto;
        order: 1; /* Brand on left */
    }

    .desktop-nav-wrapper {
        flex: 0 0 auto;
        order: 2; /* Navigation on right */
        margin-left: auto;
    }

    .mobile-menu-header {
        display: none; /* Hidden in desktop */
    }
}

/* Mobile */
@media (max-width: 768px) {
    .desktop-nav {
        display: none;
    }

    .menu-btn {
        display: flex;
        font-size: 22px;
        color: rgb(var(--grocery-title));
        background: rgba(var(--grocery-theme), 0.08);
        border-radius: 8px;
        padding: 8px;
    }

    .menu-btn:hover {
        background: rgba(var(--grocery-theme), 0.15);
    }

    .mobile-menu-header {
        display: flex; /* Show menu button in mobile */
        align-items: center;
    }

    .store-main {
        padding-bottom: calc(60px + env(safe-area-inset-bottom, 0px));
    }

    .store-footer {
        padding: 30px 0 15px;
        margin-bottom: calc(60px + env(safe-area-inset-bottom, 0px));
    }

    .footer-grid {
        grid-template-columns: 1fr;
        text-align: center;
        gap: 20px;
    }

    .footer-info p {
        justify-content: center;
    }
}
</style>
