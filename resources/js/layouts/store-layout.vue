<template>
    <div class="grocery-color public-san-body">
        <!-- Header Start -->
        <header
            class="header-style-6 dark-theme-header"
            :class="{ 'header-hidden': headerHide }"
        >
            <div class="header-inner">
                <!-- Brand Logo -->
                <div class="header-container">
                    <Link
                        v-if="brandPartner"
                        :href="
                            route(
                                'store.brand-partner.index',
                                brandPartner.slug,
                            )
                        "
                        class="pakaras-logo"
                    >
                        <img
                            src="/img/logo/pakaras-logo2.png"
                            alt="Pakaras Logo"
                            class="img-fluid brand-logo-raw"
                        />
                    </Link>
                    <span v-else class="brand-link">
                        <span class="brand-name text-white">Store</span>
                    </span>
                </div>

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

                        <div class="nav-item-dropdown">
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

                            <div v-if="navCollections.length > 0" class="dropdown-menu-list">
                                <Link
                                    v-for="collection in navCollections"
                                    :key="collection.id"
                                    :href="
                                        brandPartner 
                                            ? route('store.brand-partner.shop', { brandPartner: brandPartner.slug, collection: collection.id })
                                            : '#'
                                    "
                                    class="dropdown-item"
                                >
                                    {{ collection.label }}
                                </Link>
                            </div>
                        </div>

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
                        <a href="/wishlist" class="utility-link"
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
            <div class="account-modal" :class="{ 'register-mode': showRegister }">
                <button type="button" class="modal-close" @click="closeAccountModal">
                    <i class="ri-close-line"></i>
                </button>

                <!-- LOGIN VIEW -->
                <template v-if="!showRegister">
                    <!-- Left: Form -->
                    <div class="account-card-left">
                        <div class="account-card-copy">
                            <h2 class="auth-title">Welcome, Runner</h2>
                            <p class="auth-subtitle">
                                Log in to continue your journey—track events, manage
                                registrations, and stay connected with the tribe.
                            </p>
                        </div>

                        <form class="account-form" @submit.prevent="submitLogin()">
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
                                    <input type="checkbox" v-model="loginForm.remember" />
                                    <span>Remember Password?</span>
                                </label>
                                <a href="#" class="auth-forgot">Forgot Password?</a>
                            </div>

                            <button type="submit" class="auth-btn-primary">LOGIN</button>

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
                                <button type="button" class="auth-link-btn" @click="showRegister = true">SignUp</button>
                            </p>

                            <div class="auth-do-later">
                                <a href="/" class="auth-do-later-link">Do it Later.</a>
                            </div>
                        </form>
                    </div>

                    <!-- Right: Photo -->
                    <div class="account-card-right">
                        <div class="account-card-img-placeholder"></div>
                    </div>
                </template>

                <!-- REGISTER VIEW -->
                <template v-else>
                    <!-- Left: Intro + Google -->
                    <div class="account-card-left register-left">
                        <button type="button" class="auth-back-btn" @click="showRegister = false">
                            Back
                        </button>

                        <div class="account-card-copy">
                            <h2 class="auth-title register-title">Start Your Journey</h2>
                            <p class="auth-subtitle">
                                Run with heart. Move with purpose. Join Tribu Pakaras and conquer every trail ahead.
                            </p>
                        </div>

                        <button type="button" class="auth-btn-google">
                            <img
                                src="https://www.svgrepo.com/show/475656/google-color.svg"
                                alt="Google"
                                class="google-icon"
                            />
                            LOGIN WITH GOOGLE
                        </button>

                        <p class="auth-switch" style="margin-top: 20px;">
                            Already have an account?
                            <button type="button" class="auth-link-btn" @click="showRegister = false">Log In</button>
                        </p>
                    </div>

                    <!-- Right: Form Fields -->
                    <div class="account-card-right register-right">
                        <form class="account-form" @submit.prevent="submitRegister()">
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
                                <label class="auth-label">CONFIRM PASSWORD</label>
                                <input
                                    type="password"
                                    class="auth-input"
                                    placeholder="Confirm Password"
                                    v-model="registerForm.password_confirmation"
                                    required
                                />
                            </div>

                            <label class="auth-terms">
                                <input type="checkbox" v-model="registerForm.terms" required />
                                <span>
                                    By creating an account, you agree to our
                                    <a href="#" class="auth-terms-link">Terms &amp; Conditions</a>
                                    and
                                    <a href="#" class="auth-terms-link">Privacy Policy</a>.
                                </span>
                            </label>

                            <button type="submit" class="auth-btn-primary">CREATE ACCOUNT</button>
                        </form>
                    </div>
                </template>
            </div>
        </div>

        <!-- Main Content -->
        <main class="store-main">
            <slot />
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
                        <div>
                            <a
                                href="https://www.facebook.com/TribuPakarasOutdoor"
                                target="_blank"
                            >
                                <i class="fab fa-facebook-f"></i>
                            </a>
                            <a href="#">
                                <i class="fab fa-tiktok"></i>
                            </a>
                            <a href="#">
                                <i class="fab fa-youtube"></i>
                            </a>
                            <a
                                href="https://www.instagram.com/tribupakarasoutdoor"
                                target="_blank"
                            >
                                <i class="fab fa-instagram"></i>
                            </a>
                        </div>
                    </div>
                    <div class="news-letter">
                        <h2>Be among the first to experience it</h2>
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
import { computed, ref, onMounted, onUnmounted, watch } from 'vue';

const page = usePage();

const brandPartner = computed(() => page.props.brandPartner);
const navCollections = computed(() => page.props.navCollections || []);
const cartCount = computed(() => page.props.cartCount || 0);
const auth = computed(() => page.props.auth);
const user = computed(() => auth.value?.user);
const sideMenuOpen = ref(false);
const accountModalOpen = ref(false);
const showRegister = ref(false);

watch(accountModalOpen, (isOpen) => {
    if (isOpen) {
        document.body.style.overflow = 'hidden';
        document.body.style.paddingRight = 'var(--scrollbar-width, 0px)'; // Prevents layout shift
    } else {
        document.body.style.overflow = '';
        document.body.style.paddingRight = '';
    }
});

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
    console.log('user:', user.value);
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
            registerForm.reset('password', 'password_confirmation', 'terms'),
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
    max-width: 1450px;
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

.header-container {
    display: flex;
    justify-content: center;
    align-items: center;
    height: 4rem;
    width: 9rem;
    padding-right: 10px;
}

.header-container img {
    width: 100%;
    height: 100%;
    object-fit: contain;
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

/* ============================================
   COLLECTIONS DROPDOWN STYLES
   ============================================ */
.nav-item-dropdown {
    position: relative;
}

.nav-item-dropdown .dropdown-menu-list {
    position: absolute;
    top: 100%;
    left: 0;
    min-width: 200px;
    background: rgba(26, 26, 26, 0.5);
    backdrop-filter: blur(8px);
    -webkit-backdrop-filter: blur(8px);
    border-radius: 8px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
    opacity: 0;
    visibility: hidden;
    transform: translateY(-10px);
    transition: all 0.25s ease;
    padding: 8px 0;
    margin-top: 10px;
    border: 1px solid rgba(255, 255, 255, 0.1);
    z-index: 1000;
}

.nav-item-dropdown:hover .dropdown-menu-list {
    opacity: 1;
    visibility: visible;
    transform: translateY(0);
}

.nav-item-dropdown .dropdown-item {
    display: block;
    padding: 12px 20px;
    color: rgba(255, 255, 255, 0.9) !important;
    font-size: 14px;
    font-weight: 500;
    text-decoration: none;
    text-transform: none;
    letter-spacing: 0.3px;
    transition: all 0.2s ease;
    border-bottom: 1px solid rgba(255, 255, 255, 0.05);
    white-space: nowrap;
}

.nav-item-dropdown .dropdown-item:hover {
    background: rgba(255, 255, 255, 0.1);
    color: #FF9505 !important;
}

.nav-item-dropdown .dropdown-item:last-child {
    border-bottom: none;
}

/* Arrow indicator animation */
.nav-item-dropdown .nav-item i {
    transition: transform 0.2s ease;
}

.nav-item-dropdown:hover .nav-item i {
    transform: rotate(180deg);
}

/* Triangle/arrow pointing up */
.nav-item-dropdown .dropdown-menu-list::before {
    content: '';
    position: absolute;
    top: -6px;
    left: 20px;
    width: 12px;
    height: 12px;
    background: rgba(26, 26, 26, 0.5);
    backdrop-filter: blur(8px);
    -webkit-backdrop-filter: blur(8px);
    transform: rotate(45deg);
    border-left: 1px solid rgba(255, 255, 255, 0.1);
    border-top: 1px solid rgba(255, 255, 255, 0.1);
    z-index: -1;
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
    
    /* Mobile dropdown styles */
    .nav-item-dropdown .dropdown-menu-list {
        position: static;
        opacity: 1;
        visibility: visible;
        transform: none;
        box-shadow: none;
        border: none;
        background: transparent;
        backdrop-filter: none;
        -webkit-backdrop-filter: none;
        padding: 0;
        margin: 5px 0 0 20px;
        min-width: auto;
    }
    
    .nav-item-dropdown .dropdown-item {
        color: rgba(255, 255, 255, 0.7) !important;
        padding: 8px 0;
        white-space: normal;
    }
    
    .nav-item-dropdown .dropdown-item:hover {
        color: #fff !important;
        background: transparent;
    }
    
    .nav-item-dropdown .dropdown-menu-list::before {
        display: none;
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
    height: 80%;
    width: 80%;
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

.socials div {
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

.news-letter h2 {
    font-weight: 700;
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

/* ============================================
   ACCOUNT MODAL - Redesigned
   ============================================ */
.account-modal-overlay {
    position: fixed;
    inset: 0;
    background: rgba(0, 0, 0, 0.75);
    z-index: 2000;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 24px;
    backdrop-filter: blur(4px);
    overflow-y: auto; /* allows modal content to scroll if needed */
    -webkit-overflow-scrolling: touch; /* smooth scroll on iOS */
}

.account-modal {
    width: min(100%, 960px);
    max-height: 92vh;
    overflow: hidden; 
    border-radius: 28px;
    background: #fff;
    box-shadow: 0 40px 100px rgba(0, 0, 0, 0.35);
    display: grid;
    grid-template-columns: 1fr 1fr;
    position: relative;
    margin: auto; 
}

/* Register mode flips the layout feel */
.account-modal.register-mode {
    grid-template-columns: 1fr 1fr;
}

.modal-close {
    position: absolute;
    top: 18px;
    right: 18px;
    width: 38px;
    height: 38px;
    border-radius: 50%;
    border: none;
    background: rgba(255, 255, 255, 0.92);
    color: #222;
    font-size: 20px;
    cursor: pointer;
    display: grid;
    place-items: center;
    z-index: 10;
    box-shadow: 0 2px 8px rgba(0,0,0,0.15);
    transition: background 0.2s;
}

.modal-close:hover {
    background: #fff;
}

/* LEFT PANEL — Login Form */
.account-card-left {
    padding: 48px 44px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    background: #fff;
    overflow-y: auto; 
    max-height: 92vh;
}

.account-card-copy {
    margin-bottom: 28px;
}

.auth-title {
    font-size: clamp(1.8rem, 2.4vw, 2.8rem);
    font-weight: 900;
    line-height: 1.1;
    margin: 0 0 14px;
    color: #111;
    font-family: 'Poppins', 'Public Sans', sans-serif;
}

.register-title {
    font-size: clamp(1.6rem, 2.2vw, 2.4rem);
}

.auth-subtitle {
    color: #666;
    font-size: 14px;
    line-height: 1.75;
    margin: 0;
}

.account-form {
    display: grid;
    gap: 16px;
}

.auth-field {
    display: grid;
    gap: 7px;
}

.auth-label {
    font-size: 11px;
    font-weight: 700;
    letter-spacing: 0.1em;
    text-transform: uppercase;
    color: #444;
}

.auth-input {
    width: 100%;
    height: 50px;
    padding: 0 18px;
    border: 1.5px solid #e0e0e0;
    border-radius: 12px;
    background: #f9f9f9;
    font-size: 14px;
    color: #222;
    font-family: 'Public Sans', sans-serif;
    transition: border-color 0.2s, background 0.2s;
}

.auth-input:focus {
    outline: none;
    border-color: rgb(var(--grocery-theme));
    background: #fff;
}

.auth-remember-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 16px;
    flex-wrap: wrap;
}

.auth-remember {
    display: flex;
    align-items: center;
    gap: 8px;
    color: #555;
    font-size: 13px;
}

.auth-remember input {
    width: 15px;
    height: 15px;
    accent-color: rgb(var(--grocery-theme));
}

.auth-forgot {
    color: #f15a24;
    text-decoration: none;
    font-size: 13px;
    font-weight: 600;
}

.auth-btn-primary {
    border: none;
    border-radius: 50px;
    height: 52px;
    font-weight: 700;
    font-family: 'Public Sans', sans-serif;
    font-size: 14px;
    letter-spacing: 0.08em;
    cursor: pointer;
    background: #1a6fa8;
    color: #fff;
    transition: background 0.2s, transform 0.15s;
}

.auth-btn-primary:hover {
    background: #155d8e;
    transform: translateY(-1px);
}

.auth-btn-google {
    height: 52px;
    border-radius: 50px;
    border: 1.5px solid #e0e0e0;
    background: #fff;
    color: #333;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 12px;
    font-size: 13px;
    font-weight: 600;
    letter-spacing: 0.06em;
    font-family: 'Public Sans', sans-serif;
    cursor: pointer;
    transition: border-color 0.2s, background 0.2s;
}

.auth-btn-google:hover {
    border-color: #bbb;
    background: #fafafa;
}

.google-icon {
    width: 20px;
    height: 20px;
}

.auth-switch {
    font-size: 13px;
    color: #666;
    margin: 0;
    text-align: center;
}

.auth-link-btn {
    border: none;
    padding: 0;
    font-weight: 700;
    color: #f15a24;
    background: none;
    cursor: pointer;
    font-size: 13px;
    font-family: 'Public Sans', sans-serif;
}

.auth-do-later {
    text-align: center;
}

.auth-do-later-link {
    font-size: 13px;
    color: #f15a24;
    text-decoration: none;
    font-weight: 600;
}

.auth-terms {
    display: flex;
    gap: 12px;
    align-items: flex-start;
    color: #555;
    font-size: 13px;
    line-height: 1.6;
}

.auth-terms input {
    margin-top: 3px;
    width: 15px;
    height: 15px;
    flex-shrink: 0;
    accent-color: rgb(var(--grocery-theme));
}

.auth-terms-link {
    color: rgb(var(--grocery-theme));
    text-decoration: none;
    font-weight: 600;
}

/* RIGHT PANEL — Photo (Login) */
.account-card-right {
    position: relative;
    overflow: hidden;
    min-height: 520px;
}

.account-card-img-placeholder {
    width: 100%;
    height: 100%;
    background: url('/img/img-login.png') center/cover no-repeat;
}

/* REGISTER — Right panel becomes the form area */
.register-right {
    background: #f7f8fa;
    padding: 48px 44px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    overflow-y: auto;
    max-height: 92vh;
}

/* REGISTER — Left panel styles */
.register-left {
    padding: 48px 44px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    background: #fff;
    border-right: 1px solid #efefef;
}

.auth-back-btn {
    border: none;
    background: none;
    color: #888;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    padding: 0;
    margin-bottom: 28px;
    font-family: 'Public Sans', sans-serif;
    text-align: left;
    display: flex;
    align-items: center;
    gap: 6px;
    transition: color 0.2s;
}

.auth-back-btn:hover {
    color: #333;
}

/* Responsive */
@media (max-width: 1024px) {
    .account-modal {
        grid-template-columns: 1fr;
        max-height: 92vh;
        overflow-y: auto;
        border-radius: 20px;
    }

    .account-modal.register-mode {
        grid-template-columns: 1fr;
    }

    .account-card-right:not(.register-right) {
        display: none;
    }

    .account-card-left,
    .register-left,
    .register-right {
        padding: 36px 28px;
        min-height: auto;
    }

    .register-left {
        border-right: none;
        border-bottom: 1px solid #efefef;
    }
}

@media (max-width: 680px) {
    .account-modal-overlay {
        padding: 12px;
    }

    .auth-title {
        font-size: 1.9rem;
    }

    .auth-remember-row {
        flex-direction: column;
        align-items: flex-start;
        gap: 10px;
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