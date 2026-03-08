<template>
    <!-- Header -->
    <div class="header">
        <div class="main-header">
            <!-- Logo -->
            <div class="header-left active">
                <Link href="/dashboard/" class="logo logo-normal">
                    <img src="/img/logo/logo-pdf.png" alt="Img" />
                </Link>
                <Link href="/dashboard/" class="logo logo-white">
                    <img
                        src="/img/logo/tpinklab_logo_white_full.svg"
                        alt="Img"
                    />
                </Link>
                <Link href="/dashboard/" class="logo-small">
                    <img src="/img/logo/logo-tp.png" alt="Img" />
                </Link>
                <a
                    id="toggle_btn"
                    href="javascript:void(0);"
                    @click="toggleSidebar"
                >
                    <vue-feather type="chevrons-left"></vue-feather>
                </a>
            </div>
            <!-- /Logo -->

            <a
                id="mobile_btn"
                class="mobile_btn"
                href="#sidebar"
                @click="toggleSidebar1"
            >
                <span class="bar-icon">
                    <span></span>
                    <span></span>
                    <span></span>
                </span>
            </a>

            <!-- Header Menu -->
            <ul class="nav user-menu">
                <!-- Search -->
                <li class="nav-item nav-searchinputs">
                    <div class="top-nav-search">
                        <a href="javascript:void(0);" class="responsive-search">
                            <i class="fa fa-search"></i>
                        </a>
                        <form action="#" class="dropdown">
                            <div
                                class="searchinputs input-group dropdown-toggle"
                                id="dropdownMenuClickable"
                                data-bs-toggle="dropdown"
                                data-bs-auto-close="false"
                            >
                                <input type="text" placeholder="Search" />
                                <div class="search-addon">
                                    <span><i class="ti ti-search"></i></span>
                                </div>
                                <span class="input-group-text">
                                    <kbd class="d-flex align-items-center"
                                        ><img
                                            src="/img/icons/command.svg"
                                            alt="img"
                                            class="ms-1"
                                        />K</kbd
                                    >
                                </span>
                            </div>
                            <div
                                class="dropdown-menu search-dropdown"
                                aria-labelledby="dropdownMenuClickable"
                            >
                                <div class="search-info">
                                    <h6>
                                        <span
                                            ><i
                                                data-feather="search"
                                                class="feather-16"
                                            ></i></span
                                        >Recent Searches
                                    </h6>
                                    <ul class="search-tags">
                                        <li>
                                            <a href="javascript:void(0);"
                                                >Products</a
                                            >
                                        </li>
                                        <li>
                                            <a href="javascript:void(0);"
                                                >Sales</a
                                            >
                                        </li>
                                        <li>
                                            <a href="javascript:void(0);"
                                                >Applications</a
                                            >
                                        </li>
                                    </ul>
                                </div>
                                <div class="search-info">
                                    <h6>
                                        <span
                                            ><i
                                                data-feather="help-circle"
                                                class="feather-16"
                                            ></i></span
                                        >Help
                                    </h6>
                                    <p>
                                        How to Change Product Volume from 0 to
                                        200 on Inventory management
                                    </p>
                                    <p>Change Product Name</p>
                                </div>
                                <div class="search-info">
                                    <h6>
                                        <span
                                            ><i
                                                data-feather="user"
                                                class="feather-16"
                                            ></i></span
                                        >Customers
                                    </h6>
                                    <ul class="customers">
                                        <li>
                                            <a href="javascript:void(0);"
                                                >Aron Varu<img
                                                    src="/img/profiles/avator1.jpg"
                                                    alt=""
                                                    class="img-fluid"
                                            /></a>
                                        </li>
                                        <li>
                                            <a href="javascript:void(0);"
                                                >Jonita<img
                                                    src="/img/profiles/avatar-01.jpg"
                                                    alt=""
                                                    class="img-fluid"
                                            /></a>
                                        </li>
                                        <li>
                                            <a href="javascript:void(0);"
                                                >Aaron<img
                                                    src="/img/profiles/avatar-10.jpg"
                                                    alt=""
                                                    class="img-fluid"
                                            /></a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </form>
                    </div>
                </li>
                <!-- /Search -->

                <!-- Select Store -->
                <li
                    class="nav-item dropdown has-arrow main-drop select-store-dropdown"
                >
                    <a
                        href="javascript:void(0);"
                        class="dropdown-toggle nav-link select-store"
                        data-bs-toggle="dropdown"
                    >
                        <span class="user-info">
                            <span class="user-letter">
                                <img
                                    src="/img/store/store-01.png"
                                    alt="Store Logo"
                                    class="img-fluid"
                                />
                            </span>
                            <span class="user-detail">
                                <span class="user-name">Freshmart</span>
                            </span>
                        </span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right">
                        <a href="javascript:void(0);" class="dropdown-item">
                            <img
                                src="/img/store/store-01.png"
                                alt="Store Logo"
                                class="img-fluid"
                            />Freshmart
                        </a>
                        <a href="javascript:void(0);" class="dropdown-item">
                            <img
                                src="/img/store/store-02.png"
                                alt="Store Logo"
                                class="img-fluid"
                            />Grocery Apex
                        </a>
                        <a href="javascript:void(0);" class="dropdown-item">
                            <img
                                src="/img/store/store-03.png"
                                alt="Store Logo"
                                class="img-fluid"
                            />Grocery Bevy
                        </a>
                        <a href="javascript:void(0);" class="dropdown-item">
                            <img
                                src="/img/store/store-04.png"
                                alt="Store Logo"
                                class="img-fluid"
                            />Grocery Eden
                        </a>
                    </div>
                </li>
                <!-- /Select Store -->

                <li class="nav-item dropdown link-nav">
                    <a
                        href="javascript:void(0);"
                        class="btn btn-primary btn-md d-inline-flex align-items-center"
                        data-bs-toggle="dropdown"
                    >
                        <i class="ti ti-circle-plus ms-1"></i>Add New
                    </a>
                    <div class="dropdown-menu dropdown-xl dropdown-menu-center">
                        <div class="row g-2">
                            <div class="col-md-2">
                                <Link
                                    href="/inventory/category-list"
                                    class="link-item"
                                >
                                    <span class="link-icon">
                                        <i class="ti ti-brand-codepen"></i>
                                    </span>
                                    <p>Category</p>
                                </Link>
                            </div>
                            <div class="col-md-2">
                                <Link
                                    href="/inventory/add-product"
                                    class="link-item"
                                >
                                    <span class="link-icon">
                                        <i class="ti ti-square-plus"></i>
                                    </span>
                                    <p>Product</p>
                                </Link>
                            </div>
                            <div class="col-md-2">
                                <Link
                                    href="/inventory/category-list"
                                    class="link-item"
                                >
                                    <span class="link-icon">
                                        <i class="ti ti-shopping-bag"></i>
                                    </span>
                                    <p>Purchase</p>
                                </Link>
                            </div>
                            <div class="col-md-2">
                                <Link
                                    href="/sales/online-orders"
                                    class="link-item"
                                >
                                    <span class="link-icon">
                                        <i class="ti ti-shopping-cart"></i>
                                    </span>
                                    <p>Sale</p>
                                </Link>
                            </div>
                            <div class="col-md-2">
                                <Link
                                    href="/expenses/expenses-list"
                                    class="link-item"
                                >
                                    <span class="link-icon">
                                        <i class="ti ti-file-text"></i>
                                    </span>
                                    <p>Expense</p>
                                </Link>
                            </div>
                            <div class="col-md-2">
                                <Link
                                    href="/sales/quotation-list"
                                    class="link-item"
                                >
                                    <span class="link-icon">
                                        <i class="ti ti-device-floppy"></i>
                                    </span>
                                    <p>Quotation</p>
                                </Link>
                            </div>
                            <div class="col-md-2">
                                <Link
                                    href="/sales/sales-returns"
                                    class="link-item"
                                >
                                    <span class="link-icon">
                                        <i class="ti ti-copy"></i>
                                    </span>
                                    <p>Return</p>
                                </Link>
                            </div>
                            <div class="col-md-2">
                                <Link
                                    href="/users/users-list"
                                    class="link-item"
                                >
                                    <span class="link-icon">
                                        <i class="ti ti-user"></i>
                                    </span>
                                    <p>User</p>
                                </Link>
                            </div>
                            <div class="col-md-2">
                                <Link
                                    href="/ecommerce/customers"
                                    class="link-item"
                                >
                                    <span class="link-icon">
                                        <i class="ti ti-users"></i>
                                    </span>
                                    <p>Customer</p>
                                </Link>
                            </div>
                            <div class="col-md-2">
                                <Link
                                    href="/sales-report/sales-report"
                                    class="link-item"
                                >
                                    <span class="link-icon">
                                        <i class="ti ti-shield"></i>
                                    </span>
                                    <p>Biller</p>
                                </Link>
                            </div>
                            <div class="col-md-2">
                                <Link
                                    href="/people/suppliers"
                                    class="link-item"
                                >
                                    <span class="link-icon">
                                        <i class="ti ti-user-check"></i>
                                    </span>
                                    <p>Supplier</p>
                                </Link>
                            </div>
                            <div class="col-md-2">
                                <Link
                                    href="/stock/stock-transfer"
                                    class="link-item"
                                >
                                    <span class="link-icon">
                                        <i class="ti ti-truck"></i>
                                    </span>
                                    <p>Transfer</p>
                                </Link>
                            </div>
                        </div>
                    </div>
                </li>

                <li class="nav-item pos-nav">
                    <Link
                        href="/pos/pos-1"
                        class="btn btn-dark btn-md d-inline-flex align-items-center"
                    >
                        <i class="ti ti-device-laptop ms-1"></i>POS
                    </Link>
                </li>

                <!-- Flag -->
                <li class="nav-item dropdown has-arrow flag-nav nav-item-box">
                    <a
                        class="nav-link dropdown-toggle"
                        data-bs-toggle="dropdown"
                        href="javascript:void(0);"
                        role="button"
                    >
                        <img
                            src="/img/flags/us-flag.svg"
                            alt="Language"
                            class="img-fluid"
                        />
                    </a>
                    <div class="dropdown-menu dropdown-menu-right">
                        <a href="javascript:void(0);" class="dropdown-item">
                            <img
                                src="/img/flags/english.svg"
                                alt=""
                                height="16"
                            />English
                        </a>
                        <a href="javascript:void(0);" class="dropdown-item">
                            <img
                                src="/img/flags/arabic.svg"
                                alt=""
                                height="16"
                            />Arabic
                        </a>
                    </div>
                </li>
                <!-- /Flag -->

                <li class="nav-item nav-item-box">
                    <a
                        href="javascript:void(0);"
                        id="btnFullscreen"
                        @click="initFullScreen"
                    >
                        <i class="ti ti-maximize"></i>
                    </a>
                </li>
                <li class="nav-item nav-item-box">
                    <Link href="/application/email">
                        <i class="ti ti-mail"></i>
                        <span class="badge rounded-pill">1</span>
                    </Link>
                </li>
                <!-- Notifications -->
                <li class="nav-item dropdown nav-item-box">
                    <a
                        href="javascript:void(0);"
                        class="dropdown-toggle nav-link"
                        data-bs-toggle="dropdown"
                    >
                        <i class="ti ti-bell"></i>
                    </a>
                    <div class="dropdown-menu notifications">
                        <div class="topnav-dropdown-header">
                            <h5 class="notification-title">Notifications</h5>
                            <a href="javascript:void(0)" class="clear-noti"
                                >Mark all as read</a
                            >
                        </div>
                        <div class="noti-content">
                            <ul class="notification-list">
                                <li class="notification-message">
                                    <Link href="/activities">
                                        <div class="media d-flex">
                                            <span class="avatar flex-shrink-0">
                                                <img
                                                    alt=""
                                                    src="/img/profiles/avatar-13.jpg"
                                                />
                                            </span>
                                            <div class="flex-grow-1">
                                                <p class="noti-details">
                                                    <span class="noti-title"
                                                        >James Kirwin</span
                                                    >
                                                    confirmed his order. Order
                                                    No: #78901.Estimated
                                                    delivery: 2 days
                                                </p>
                                                <p class="noti-time">
                                                    4 mins ago
                                                </p>
                                            </div>
                                        </div>
                                    </Link>
                                </li>
                                <li class="notification-message">
                                    <Link href="/activities">
                                        <div class="media d-flex">
                                            <span class="avatar flex-shrink-0">
                                                <img
                                                    alt=""
                                                    src="/img/profiles/avatar-03.jpg"
                                                />
                                            </span>
                                            <div class="flex-grow-1">
                                                <p class="noti-details">
                                                    <span class="noti-title"
                                                        >Leo Kelly</span
                                                    >
                                                    cancelled his order
                                                    scheduled for 17 Jan 2025
                                                </p>
                                                <p class="noti-time">
                                                    10 mins ago
                                                </p>
                                            </div>
                                        </div>
                                    </Link>
                                </li>
                                <li class="notification-message">
                                    <Link href="/activities" class="recent-msg">
                                        <div class="media d-flex">
                                            <span class="avatar flex-shrink-0">
                                                <img
                                                    alt=""
                                                    src="/img/profiles/avatar-17.jpg"
                                                />
                                            </span>
                                            <div class="flex-grow-1">
                                                <p class="noti-details">
                                                    Payment of $50 received for
                                                    Order #67890 from
                                                    <span class="noti-title"
                                                        >Antonio Engle</span
                                                    >
                                                </p>
                                                <p class="noti-time">
                                                    05 mins ago
                                                </p>
                                            </div>
                                        </div>
                                    </Link>
                                </li>
                                <li class="notification-message">
                                    <Link href="/activities" class="recent-msg">
                                        <div class="media d-flex">
                                            <span class="avatar flex-shrink-0">
                                                <img
                                                    alt=""
                                                    src="/img/profiles/avatar-02.jpg"
                                                />
                                            </span>
                                            <div class="flex-grow-1">
                                                <p class="noti-details">
                                                    <span class="noti-title"
                                                        >Andrea</span
                                                    >
                                                    confirmed his order. Order
                                                    No: #73401.Estimated
                                                    delivery: 3 days
                                                </p>
                                                <p class="noti-time">
                                                    4 mins ago
                                                </p>
                                            </div>
                                        </div>
                                    </Link>
                                </li>
                            </ul>
                        </div>
                        <div
                            class="topnav-dropdown-footer d-flex align-items-center gap-3"
                        >
                            <a href="#" class="btn btn-secondary btn-md w-100"
                                >Cancel</a
                            >
                            <Link
                                href="/activities"
                                class="btn btn-primary btn-md w-100"
                                >View all</Link
                            >
                        </div>
                    </div>
                </li>
                <!-- /Notifications -->

                <li class="nav-item nav-item-box">
                    <Link href="/settings/general-settings"
                        ><i class="ti ti-settings"></i
                    ></Link>
                </li>
                <li class="nav-item dropdown has-arrow main-drop profile-nav">
                    <a
                        href="javascript:void(0);"
                        class="nav-link userset"
                        data-bs-toggle="dropdown"
                    >
                        <span class="user-info p-0">
                            <span class="user-letter">
                                <img
                                    src="/img/profiles/avator1.jpg"
                                    alt=""
                                    class="img-fluid"
                                />
                            </span>
                        </span>
                    </a>
                    <div class="dropdown-menu menu-drop-user">
                        <div class="profileset d-flex align-items-center">
                            <span class="user-img ms-2">
                                <img src="/img/profiles/avator1.jpg" alt="" />
                            </span>
                            <div>
                                <h6 class="fw-medium">John Smilga</h6>
                                <p>Admin</p>
                            </div>
                        </div>
                        <Link class="dropdown-item" href="/pages/profile"
                            ><i class="ti ti-user-circle ms-2"></i
                            >MyProfile</Link
                        >
                        <Link
                            class="dropdown-item"
                            href="/sales-report/sales-report"
                            ><i class="ti ti-file-text ms-2"></i>Reports</Link
                        >
                        <Link
                            class="dropdown-item"
                            href="/settings/general-settings"
                            ><i class="ti ti-settings-2 ms-2"></i>Settings</Link
                        >
                        <hr class="my-2" />
                        <Link class="dropdown-item logout pb-0" href="/"
                            ><i class="ti ti-logout ms-2"></i>Logout</Link
                        >
                    </div>
                </li>
            </ul>
            <!-- /Header Menu -->

            <!-- Mobile Menu -->
            <div class="dropdown mobile-user-menu">
                <a
                    href="javascript:void(0);"
                    class="nav-link dropdown-toggle"
                    data-bs-toggle="dropdown"
                    aria-expanded="false"
                    ><i class="fa fa-ellipsis-v"></i
                ></a>
                <div class="dropdown-menu dropdown-menu-right">
                    <Link class="dropdown-item" href="/pages/profile"
                        >My Profile</Link
                    >
                    <Link
                        class="dropdown-item"
                        href="/settings/general-settings"
                        >Settings</Link
                    >
                    <Link class="dropdown-item" href="/">Logout</Link>
                </div>
            </div>
            <!-- /Mobile Menu -->
        </div>
    </div>
    <!-- /Header -->

    <!-- <SideSettings></SideSettings> -->
    <ThemeSettings></ThemeSettings>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
import { onMounted, onBeforeUnmount } from 'vue';
// import SideSettings from '@/layouts/side-settings.vue';
import ThemeSettings from '@/layouts/theme-settings.vue';

const toggleSidebar1 = () => {
    const body = document.body;
    body.classList.toggle('slide-nav');
};

const toggleSidebar = () => {
    const body = document.body;
    body.classList.toggle('mini-sidebar');
};

const initFullScreen = () => {
    document.body.classList.toggle('fullscreen-enable');
    if (
        !document.fullscreenElement &&
        /* alternative standard method */
        !document.mozFullScreenElement &&
        !document.webkitFullscreenElement
    ) {
        // current working methods
        if (document.documentElement.requestFullscreen) {
            document.documentElement.requestFullscreen();
        } else if (document.documentElement.mozRequestFullScreen) {
            document.documentElement.mozRequestFullScreen();
        } else if (document.documentElement.webkitRequestFullscreen) {
            document.documentElement.webkitRequestFullscreen(
                Element.ALLOW_KEYBOARD_INPUT,
            );
        }
    } else {
        if (document.cancelFullScreen) {
            document.cancelFullScreen();
        } else if (document.mozCancelFullScreen) {
            document.mozCancelFullScreen();
        } else if (document.webkitCancelFullScreen) {
            document.webkitCancelFullScreen();
        }
    }
};

const isElementVisible = (element) => {
    return element.offsetWidth > 0 || element.offsetHeight > 0;
};

const slideDownSubmenu = () => {
    const subdropPlusUl = document.getElementsByClassName('subdrop');
    for (let i = 0; i < subdropPlusUl.length; i++) {
        const submenu = subdropPlusUl[i].nextElementSibling;
        if (submenu && submenu.tagName.toLowerCase() === 'ul') {
            submenu.style.display = 'block';
        }
    }
};

const slideUpSubmenu = () => {
    const subdropPlusUl = document.getElementsByClassName('subdrop');
    for (let i = 0; i < subdropPlusUl.length; i++) {
        const submenu = subdropPlusUl[i].nextElementSibling;
        if (submenu && submenu.tagName.toLowerCase() === 'ul') {
            submenu.style.display = 'none';
        }
    }
};

const handleMouseover = (e) => {
    e.stopPropagation();

    const body = document.body;
    const toggleBtn = document.getElementById('toggle_btn');

    if (
        body.classList.contains('mini-sidebar') &&
        isElementVisible(toggleBtn)
    ) {
        const target = e.target.closest('.sidebar, .header-left');

        if (target) {
            body.classList.add('expand-menu');
            slideDownSubmenu();
        } else {
            body.classList.remove('expand-menu');
            slideUpSubmenu();
        }

        e.preventDefault();
    }
};

const initMouseoverListener = () => {
    document.addEventListener('mouseover', handleMouseover);
};

onMounted(() => {
    initMouseoverListener();
});

onBeforeUnmount(() => {
    document.removeEventListener('mouseover', handleMouseover);
});
</script>
