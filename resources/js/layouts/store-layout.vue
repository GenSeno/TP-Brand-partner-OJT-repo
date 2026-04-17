<template>
    <div class="grocery-color public-san-body">
        <!-- Header Start -->
        <header
            class="header-style-6 dark-theme-header"
            :class="{ 'header-hidden': headerHide }"
        >
            <div class="header-inner">
                <!-- Brand Logo -->
                <Link
                    v-if="brandPartner"
                    :href="
                        route('store.brand-partner.index', brandPartner.slug)
                    "
                    class="pakaras-logo"
                >
                    <img
                        src="/img/logo/pakaras-logo.png"
                        alt="Pakaras Logo"
                        class="img-fluid brand-logo-raw"
                    />
                </Link>
                <span v-else class="brand-link">
                    <span class="brand-name text-white">Store</span>
                </span>

                <!-- Center Nav Links -->
                <div class="center-nav-wrapper d-none d-lg-block">
                    <nav class="center-nav">
                        <Link
                            :href="
                                brandPartner
                                    ? route(
                                          'store.brand-partner.shop',
                                          brandPartner.slug,
                                      )
                                    : '#'
                            "
                            class="nav-item"
                        >
                            SHOP <i class="ri-arrow-down-s-line"></i>
                        </Link>

                        <Link
                            :href="
                                brandPartner
                                    ? route(
                                          'store.brand-partner.collections',
                                          brandPartner.slug,
                                      )
                                    : '#'
                            "
                            class="nav-item"
                        >
                            COLLECTIONS <i class="ri-arrow-down-s-line"></i>
                        </Link>

                        <Link
                            :href="
                                brandPartner
                                    ? route(
                                          'store.brand-partner.about',
                                          brandPartner.slug,
                                      )
                                    : '#'
                            "
                            class="nav-item"
                        >
                            About us
                        </Link>

                        <Link
                            :href="
                                brandPartner
                                    ? route(
                                          'store.brand-partner.contact',
                                          brandPartner.slug,
                                      )
                                    : '#'
                            "
                            class="nav-item"
                        >
                            CONTACT US
                        </Link>

                        <Link
                            :href="
                                brandPartner
                                    ? route(
                                          'store.brand-partner.partner',
                                          brandPartner.slug,
                                      )
                                    : '#'
                            "
                            class="nav-item"
                        >
                            BE OUR PARTNER
                        </Link>
                    </nav>
                </div>

                <!-- Right Utility Icons & CTA -->
                <div class="right-nav-wrapper d-none d-lg-block">
                    <nav class="right-nav">
                        <a
                            href="#"
                            class="utility-link"
                            @click.prevent="openAccountModal"
                        >
                            <i class="ri-user-line"></i>
                            <span v-if="user">{{ user.name }}</span>
                            <span v-else>Account</span>
                        </a>
                        <a href="javascript:void(0)" class="utility-link"
                            ><i class="ri-heart-line"></i> Wishlist</a
                        >
                        <Link
                            :href="
                                brandPartner
                                    ? route(
                                          'store.brand-partner.cart',
                                          brandPartner.slug,
                                      )
                                    : '#'
                            "
                            class="utility-link cart-link"
                        >
                            <i class="ri-shopping-cart-2-line"></i> Cart
                            <span class="cart-badge" v-if="cartCount > 0">{{
                                cartCount
                            }}</span>
                        </Link>
                        <a href="javascript:void(0)" class="race-cta-btn"
                            >RACE WITH US</a
                        >
                    </nav>
                </div>

                <!-- Mobile Menu Button -->
                <div class="mobile-menu-header d-lg-none">
                    <button
                        type="button"
                        class="btn menu-btn text-white"
                        @click="toggleSideMenu"
                    >
                        <i class="ri-menu-line"></i>
                    </button>
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

        <!-- Account Modal -->
        <div
            class="account-modal-overlay"
            v-if="accountModalOpen"
            @click.self="closeAccountModal"
        >
            <div class="account-modal">
                <button
                    type="button"
                    class="modal-close"
                    @click="closeAccountModal"
                >
                    <i class="ri-close-line"></i>
                </button>

                <div class="account-card-left">
                    <div class="account-card-copy">
                        <h2 class="auth-title">Welcome, Runner</h2>
                        <p class="auth-subtitle">
                            Log in to continue your journey—track events, manage
                            registrations, and stay connected with the tribe.
                        </p>
                    </div>

                    <form
                        class="account-form"
                        @submit.prevent="
                            showRegister ? submitRegister() : submitLogin()
                        "
                    >
                        <template v-if="!showRegister">
                            <div class="auth-field">
                                <label class="auth-label">EMAIL ADDRESS</label>
                                <input
                                    type="email"
                                    class="auth-input"
                                    placeholder="Email Address"
                                    v-model="loginForm.email"
                                    required
                                />
                            </div>

                            <div class="auth-field">
                                <label class="auth-label">PASSWORD</label>
                                <input
                                    type="password"
                                    class="auth-input"
                                    placeholder="Password"
                                    v-model="loginForm.password"
                                    required
                                />
                            </div>

                            <div class="auth-remember-row">
                                <label class="auth-remember">
                                    <input
                                        type="checkbox"
                                        v-model="loginForm.remember"
                                    />
                                    <span>Remember Password?</span>
                                </label>
                                <a href="#" class="auth-forgot"
                                    >Forgot Password?</a
                                >
                            </div>

                            <button type="submit" class="auth-btn-primary">
                                LOGIN
                            </button>

                            <button type="button" class="auth-btn-google">
                                <img
                                    src="https://www.svgrepo.com/show/475656/google-color.svg"
                                    alt="Google"
                                    class="google-icon"
                                />
                                LOGIN WITH GOOGLE
                            </button>

                            <p class="auth-switch">
                                Don't have an account?
                                <button
                                    type="button"
                                    class="auth-link-btn"
                                    @click="showRegister = true"
                                >
                                    SignUp
                                </button>
                            </p>

                            <div class="auth-do-later">
                                <a href="/" class="auth-do-later-link"
                                    >Do it Later.</a
                                >
                            </div>
                        </template>

                        <template v-else>
                            <div class="auth-field">
                                <label class="auth-label">FULL NAME</label>
                                <input
                                    type="text"
                                    class="auth-input"
                                    placeholder="Enter Full name"
                                    v-model="registerForm.name"
                                    required
                                />
                            </div>

                            <div class="auth-field">
                                <label class="auth-label">EMAIL ADDRESS</label>
                                <input
                                    type="email"
                                    class="auth-input"
                                    placeholder="Enter Email Address"
                                    v-model="registerForm.email"
                                    required
                                />
                            </div>

                            <div class="auth-field">
                                <label class="auth-label">PASSWORD</label>
                                <input
                                    type="password"
                                    class="auth-input"
                                    placeholder="Enter Password"
                                    v-model="registerForm.password"
                                    required
                                />
                            </div>

                            <div class="auth-field">
                                <label class="auth-label"
                                    >CONFIRM PASSWORD</label
                                >
                                <input
                                    type="password"
                                    class="auth-input"
                                    placeholder="Confirm Password"
                                    v-model="registerForm.password_confirmation"
                                    required
                                />
                            </div>

                            <label class="auth-terms">
                                <input
                                    type="checkbox"
                                    v-model="registerForm.terms"
                                    required
                                />
                                <span>
                                    By creating an account, you agree to our
                                    <a href="#" class="auth-terms-link"
                                        >Terms &amp; Conditions</a
                                    >
                                    and
                                    <a href="#" class="auth-terms-link"
                                        >Privacy Policy</a
                                    >.
                                </span>
                            </label>

                            <button type="submit" class="auth-btn-primary">
                                CREATE ACCOUNT
                            </button>

                            <p class="auth-switch">
                                Already have an account?
                                <button
                                    type="button"
                                    class="auth-link-btn"
                                    @click="showRegister = false"
                                >
                                    Log In
                                </button>
                            </p>
                        </template>
                    </form>
                </div>

                <div class="account-card-right">
                    <div class="account-card-img-placeholder"></div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <main class="store-main">
            <slot/>
        </main>

        <!-- Footer -->
        <footer class="store-footer">
            <div class="custom-container">
                <div class="footer-grid">
                    <div class="footer-brand">
                        <div class="footer-logo">
                            <img
                                src="/img/logo/pakaras-logo2.png"
                                alt="Tribu Pakaras"
                            />
                        </div>
                        <div class="pakaras-adjective">
                            <h4>Pakaras /'pa-kah-ras/</h4>
                            <span>adjective</span>
                        </div>
                        <p>
                            Showing fearlessness and determination without
                            thinking or caring about the probable consequences
                            of ther actions
                        </p>
                    </div>
                    <div class="footer-info">
                        <h4>Sitemap</h4>
                        <p>Shop</p>
                        <p>Collections</p>
                        <p>Header 1</p>
                        <p>Header 2</p>
                        <p>Header 3</p>
                    </div>
                    <div class="footer-info">
                        <h4>Account</h4>
                        <p>Wishlist</p>
                        <p>Cart</p>
                    </div>
                    <div class="footer-info">
                        <h4>Contact Information</h4>
                        <p>
                            <i class="ri-map-pin-line"></i> Davao City,
                            Philippines
                        </p>
                        <p>
                            <i class="ri-mail-line"></i>tribupakarasph@gmail.com
                        </p>
                        <p><i class="ri-phone-line"></i>0906 496 1393</p>
                    </div>
                    <div class="socials">
                        <h4>Social</h4>
                        <p>
                            <i class="fab fa-facebook-f"></i>
                            <i class="fab fa-tiktok"></i>
                            <i class="fab fa-youtube"></i>
                            <i class="fab fa-instagram"></i>
                        </p>
                    </div>
                    <div class="news-letter">
                        <h1>Be among the first to experience it</h1>
                        <p>
                            From rugged trails to everyday runs,we've got you
                            covered.
                        </p>
                        <div class="subscribe">
                            <input
                                type="text"
                                class="footer-input"
                                placeholder="Email Address"
                            />
                            <input
                                type="submit"
                                value="Subscribe"
                                class="subscribe-button"
                            />
                        </div>
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
import { Link, useForm, usePage, router } from '@inertiajs/vue3';
import { computed, ref, onMounted, onUnmounted } from 'vue';

const page = usePage();

const brandPartner = computed(() => page.props.brandPartner);
const cartCount = computed(() => page.props.cartCount || 0);
const auth = computed(() => page.props.auth);
const user = computed(() => auth.value?.user);
const sideMenuOpen = ref(false);
const accountModalOpen = ref(false);
const showRegister = ref(false);

const loginForm = useForm({
    email: '',
    password: '',
    remember: false,
});

const registerForm = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    terms: false,
});

const openAccountModal = () => {
    if (user.value) {
        router.visit(route('store.brand-partner.account'));
        return;
    }

    accountModalOpen.value = true;
    showRegister.value = false;
};

const closeAccountModal = () => {
    accountModalOpen.value = false;
    showRegister.value = false;
};

const submitLogin = () => {
    loginForm.post(route('store.brand-partner.login.submit'), {
        onFinish: () => loginForm.reset('password'),
        onSuccess: () => closeAccountModal(),
    });
};

const submitRegister = () => {
    registerForm.post(route('store.brand-partner.register.submit'), {
        onFinish: () =>
            registerForm.reset(
                'password',
                'password_confirmation',
                'terms',
            ),
        onSuccess: () => closeAccountModal(),
    });
};

const isRoute = (name) => {
    return route().current(name);
};

const toggleSideMenu = () => {
    // Only allow side menu toggle in mobile view
    if (window.innerWidth < 769) {
        sideMenuOpen.value = !sideMenuOpen.value;
    }
};

const headerHide = ref(false);
let lastScrollY = 0;

const handleScroll = () => {
    const currentScrollY = window.scrollY;

    if (currentScrollY > lastScrollY && currentScrollY > 80) {
        headerHide.value = true;
    } else {
        headerHide.value = false;
    }

    lastScrollY = currentScrollY;
};

onMounted(() => window.addEventListener('scroll', handleScroll));
onUnmounted(() => window.removeEventListener('scroll', handleScroll));

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
    cursor: auto;
}

.grocery-color {
    --grocery-theme: 60, 133, 153;
    --grocery-content: 143, 143, 178;
    --grocery-title: 27, 27, 62;
    --grocery-border: 232, 232, 232;
    --grocery-primary: 254, 175, 24;
    --grocery-light-bg: 247, 247, 247;
    --grocery-rating: 255, 191, 19;
    --grocery-success: #2ed573;
    --grocery-danger: #ff4757;
    --grocery-dark: #222;
    --grocery-gray: #777;
    --grocery-light-gray: #999;
    --grocery-bg: #fafafa;

    --grocery-primary-color: rgb(var(--grocery-theme));
    --grocery-primary-light: rgba(var(--grocery-theme), 0.1);
    background: var(--grocery-bg);
    cursor: auto;
}

* {
    box-sizing: border-box;
}

/* Custom Container */
.custom-container {
    max-width: 1800px;
    margin: 0 auto;
    padding: 0 10px;
}

.px-15 {
    padding-left: 15px;
    padding-right: 15px;
}

/* ============================================
   HEADER - Tribu Pakaras Dark Theme
   ============================================ */
.dark-theme-header {
    background-color: rgba(26, 26, 26, 0.5);
    backdrop-filter: blur(8px);
    -webkit-backdrop-filter: blur(8px);
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    z-index: 999;
    width: 100%;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    transition: transform 0.3s ease;
}

.header-hidden {
    transform: translateY(-100%);
}

.header-inner {
    position: relative;
    max-width: 1400px;
    margin: 0 auto;
    padding: 15px 30px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.pakaras-logo {
    display: flex;
    justify-content: center;
    align-items: center;
    height: 4rem;
    width: 9rem;
    padding-right: 10px;
}

.brand-logo-raw {
    width: 100%;
    height: 100%;
    object-fit: cover;
    scale: 1.6;
}

/* Center Nav */
.center-nav-wrapper {
    flex: 1;
    display: flex;
    justify-content: flex-start;
    padding-left: 5px;
    order: 2;
}

.center-nav {
    display: flex;
    gap: 15px;
    align-items: center;
}

.center-nav .nav-item {
    color: #fff;
    text-decoration: none;
    font-size: 13px;
    font-weight: 600;
    letter-spacing: 0.5px;
    text-transform: uppercase;
    display: flex;
    align-items: center;
    gap: 4px;
    transition: color 0.2s ease;
}

.center-nav .nav-item:hover {
    color: rgb(var(--grocery-primary));
}

.center-nav .nav-item i {
    font-size: 18px;
    margin-top: -2px;
}

/* Right Nav */
.right-nav-wrapper {
    flex: 0 0 auto;
    order: 3;
}

.right-nav {
    display: flex;
    align-items: center;
    gap: 25px;
}

.utility-link {
    color: #fff;
    text-decoration: none;
    font-size: 13px;
    font-weight: 500;
    display: flex;
    align-items: center;
    gap: 6px;
    transition: color 0.2s ease;
    position: relative;
    font-family: 'Public Sans', sans-serif;
}

.utility-link:hover {
    color: rgb(var(--grocery-primary));
}

.utility-link i {
    font-size: 18px;
}

.cart-link {
    position: relative;
}

.cart-badge {
    position: absolute;
    top: -8px;
    left: 8px;
    background: rgb(var(--grocery-primary));
    color: #000;
    font-size: 10px;
    font-weight: 700;
    min-width: 16px;
    height: 16px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}

/* CTA Button */
.race-cta-btn {
    background-color: #f39c12;
    color: #fff;
    font-weight: 500;
    font-size: 14px;
    padding: 10px 24px;
    border-radius: 0;
    text-decoration: none;
    text-transform: uppercase;
    border: none;
    margin-left: 10px;
    cursor: pointer;
}

.race-cta-btn:hover {
    background-color: #f39c12;
    color: #fff;
}

.text-white {
    color: #ffffff !important;
}

/* Mobile Adjustments */
@media (max-width: 991px) {
    .header-inner {
        padding: 10px 15px;
    }
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
    padding-top: 0;
    margin-top: 0;
}

/* ============================================
   FOOTER
   ============================================ */
.store-footer {
    background: #1a1a1a;
    color: #ccc;
    padding: 40px 0 20px;
}

.store-footer h4 {
    font-size: 15px;
    font-weight: 900;
    color: #fff;
    margin-bottom: 15px;
    font-family: 'poppins';
}

.footer-grid {
    display: grid;
    grid-template-columns: 2fr 1fr 1fr 1.5fr 1fr 2fr;
    gap: 40px;
    margin-bottom: 25px;
}

.footer-brand .footer-logo {
    height: 80px;
    margin-bottom: 12px;
    width: fit-content;
}

.footer-brand img {
    height: 100%;
    width: 100%;
    object-fit: contain;
}

.footer-brand .pakaras-adjective h4 {
    margin-bottom: 0;
}
.footer-brand .pakaras-adjective span {
    display: block;
    font-style: italic;
    font-size: 0.8rem;
    margin-bottom: 15px;
}

.footer-brand p {
    color: #999;
    font-size: 14px;
    margin: 0;
    width: fit-content;
}

.footer-info p {
    font-size: 13px;
    color: #999;
    margin-bottom: 6px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.footer-info i {
    color: #bcbcbc;
    font-size: 1.3rem;
    width: 16px;
    margin-right: 7px;
}

.socials p {
    display: flex;
    gap: 15px;
}

.socials i {
    gap: 30px;
    color: #bcbcbc;
    font-size: 0.9rem;
    width: 16px;
    margin-right: 7px;
}

.news-letter h1 {
    font-weight: 900;
    color: #fff;
    word-spacing: 0.1em;
    margin-bottom: 15px;
    font-family: 'poppins';
}

.subscribe {
    display: flex;
}

.footer-input {
    height: 45px;
    width: 250px;
    padding: 15px;
    border: none;
    border-radius: 0;
}

.subscribe-button {
    height: 45px;
    width: 90px;
    color: white;
    border: none;
    background: #ff9505;
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

.account-modal-overlay {
    position: fixed;
    inset: 0;
    background: rgba(0, 0, 0, 0.7);
    z-index: 2000;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 32px;
}

.account-modal {
    width: min(100%, 1040px);
    max-height: 94vh;
    overflow: hidden;
    border-radius: 32px;
    background: #fff;
    box-shadow: 0 32px 80px rgba(0, 0, 0, 0.3);
    display: grid;
    grid-template-columns: 1.1fr 0.9fr;
    position: relative;
}

.modal-close {
    position: absolute;
    top: 20px;
    right: 20px;
    width: 42px;
    height: 42px;
    border-radius: 50%;
    border: none;
    background: rgba(255, 255, 255, 0.95);
    color: #222;
    font-size: 20px;
    cursor: pointer;
    display: grid;
    place-items: center;
    z-index: 2;
}

.account-card-left,
.account-card-right {
    min-height: 560px;
}

.account-card-left {
    padding: 48px 46px;
    display: flex;
    flex-direction: column;
    justify-content: center;
}

.account-card-copy {
    max-width: 420px;
    margin-bottom: 32px;
}

.auth-title {
    font-size: clamp(2rem, 2.5vw, 3rem);
    line-height: 1.05;
    margin: 0 0 18px;
    color: #111;
}

.auth-subtitle {
    color: #5a5a5a;
    font-size: 16px;
    line-height: 1.8;
    max-width: 420px;
    margin: 0;
}

.account-form {
    display: grid;
    gap: 18px;
}

.auth-field {
    display: grid;
    gap: 8px;
}

.auth-label {
    font-size: 12px;
    font-weight: 700;
    letter-spacing: 0.12em;
    text-transform: uppercase;
    color: #444;
}

.auth-input {
    width: 100%;
    min-height: 54px;
    padding: 16px 18px;
    border: 1px solid #ddd;
    border-radius: 28px;
    background: #fafafa;
    font-size: 14px;
    color: #222;
}

.auth-remember-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 20px;
    flex-wrap: wrap;
}

.auth-remember {
    display: flex;
    align-items: center;
    gap: 10px;
    color: #555;
    font-size: 14px;
}

.auth-remember input {
    width: 16px;
    height: 16px;
    accent-color: rgb(var(--grocery-primary));
}

.auth-forgot {
    color: rgb(var(--grocery-primary));
    text-decoration: none;
    font-size: 14px;
}

.auth-btn-primary,
.auth-btn-google {
    border: none;
    border-radius: 28px;
    min-height: 54px;
    font-weight: 700;
    font-family: 'Public Sans', sans-serif;
    cursor: pointer;
}

.auth-btn-primary {
    background: rgb(var(--grocery-theme));
    color: #fff;
}

.auth-btn-google {
    background: #f6f7f8;
    color: #222;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 14px;
    border: 1px solid #e7e7e7;
}

.google-icon {
    width: 22px;
    height: 22px;
}

.auth-switch {
    font-size: 14px;
    color: #666;
    margin: 0;
}

.auth-link-btn {
    border: none;
    padding: 0;
    font-weight: 700;
    color: rgb(var(--grocery-primary));
    background: none;
    cursor: pointer;
}

.auth-do-later {
    margin-top: 8px;
}

.auth-do-later-link {
    font-size: 14px;
    color: #f15a24;
    text-decoration: none;
    font-weight: 700;
}

.auth-terms {
    display: flex;
    gap: 14px;
    align-items: flex-start;
    color: #555;
    font-size: 14px;
}

.auth-terms input {
    margin-top: 4px;
    width: 16px;
    height: 16px;
    accent-color: rgb(var(--grocery-primary));
}

.auth-terms-link {
    color: rgb(var(--grocery-primary));
    text-decoration: none;
}

.account-card-right {
    position: relative;
    background:
        radial-gradient(
            circle at top left,
            rgba(255, 118, 74, 0.12),
            transparent 34%
        ),
        linear-gradient(180deg, #111 0%, #1c1c1c 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
}

.account-card-img-placeholder {
    width: 100%;
    height: 100%;
    background: url('/img/img-login.png') center/cover no-repeat;
    filter: brightness(0.95);
}

@media (max-width: 1024px) {
    .account-modal {
        grid-template-columns: 1fr;
        max-height: 90vh;
    }

    .modal-close {
        top: 14px;
        right: 14px;
    }

    .account-card-left,
    .account-card-right {
        min-height: auto;
    }

    .account-card-left {
        padding: 34px 28px;
    }
}

@media (max-width: 680px) {
    .account-modal-overlay {
        padding: 16px;
    }

    .auth-title {
        font-size: 2rem;
    }

    .auth-remember-row {
        flex-direction: column;
        align-items: flex-start;
    }
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
