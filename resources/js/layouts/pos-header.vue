<template>
    <!-- Header -->
    <div class="header pos-header">
        <!-- Logo -->
        <div class="header-left active">
            <Link href="/dashboard/" class="logo logo-normal">
                <img src="/img/logo/logo-pdf.png" alt="Img" />
            </Link>
            <Link href="/dashboard/" class="logo logo-white">
                <img src="/img/logo/tpinklab_logo_white_full.svg" alt="Img" />
            </Link>
            <Link href="/dashboard/" class="logo-small">
                <img src="/img/logo/logo-tp.png" alt="Img" />
            </Link>
        </div>
        <!-- /Logo -->

        <a id="mobile_btn" class="mobile_btn d-none" href="#sidebar">
            <span class="bar-icon">
                <span></span>
                <span></span>
                <span></span>
            </span>
        </a>

        <!-- Header Menu -->
        <ul class="nav user-menu">
            <!-- Search -->
            <li class="nav-item time-nav">
                <span
                    class="bg-teal text-white d-inline-flex align-items-center"
                    ><img
                        src="/img/icons/clock-icon.svg"
                        alt="img"
                        class="me-2"
                    />09:25:32</span
                >
            </li>
            <!-- /Search -->

            <li class="nav-item pos-nav">
                <Link
                    href="/dashboard/"
                    class="btn btn-purple btn-md d-inline-flex align-items-center"
                >
                    <i class="ti ti-world me-1"></i>Dashboard
                </Link>
            </li>

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

            <li class="nav-item nav-item-box">
                <a
                    href="#"
                    data-bs-toggle="modal"
                    data-bs-target="#calculator"
                    class="bg-orange border-orange text-white"
                    ><i class="ti ti-calculator"></i
                ></a>
            </li>
            <li class="nav-item nav-item-box">
                <a
                    href="javascript:void(0);"
                    id="btnFullscreen"
                    data-bs-toggle="tooltip"
                    data-bs-placement="top"
                    data-bs-title="Maximize"
                    @click="initFullScreen"
                >
                    <i class="ti ti-maximize"></i>
                </a>
            </li>
            <li
                class="nav-item nav-item-box"
                data-bs-toggle="tooltip"
                data-bs-placement="top"
                data-bs-title="Cash Register"
            >
                <a
                    href="#"
                    data-bs-toggle="modal"
                    data-bs-target="#cash-register"
                    ><i class="ti ti-cash"></i
                ></a>
            </li>
            <li
                class="nav-item nav-item-box"
                data-bs-toggle="tooltip"
                data-bs-placement="top"
                data-bs-title="Print Last Reciept"
            >
                <a href="#"><i class="ti ti-printer"></i></a>
            </li>
            <li
                class="nav-item nav-item-box"
                data-bs-toggle="tooltip"
                data-bs-placement="top"
                data-bs-title="Today’s Sale"
            >
                <a href="#" data-bs-toggle="modal" data-bs-target="#today-sale"
                    ><i class="ti ti-progress"></i
                ></a>
            </li>
            <li
                class="nav-item nav-item-box"
                data-bs-toggle="tooltip"
                data-bs-placement="top"
                data-bs-title="Today’s Profit"
            >
                <a
                    href="#"
                    data-bs-toggle="modal"
                    data-bs-target="#today-profit"
                    ><i class="ti ti-chart-infographic"></i
                ></a>
            </li>
            <li
                class="nav-item nav-item-box"
                data-bs-toggle="tooltip"
                data-bs-placement="top"
                data-bs-title="POS Settings"
            >
                <Link href="/app-settings/pos-settings"
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
                                alt="Img"
                                class="img-fluid"
                            />
                        </span>
                    </span>
                </a>
                <div class="dropdown-menu menu-drop-user">
                    <div class="profilename">
                        <div class="profileset">
                            <span class="user-img"
                                ><img
                                    src="/img/profiles/avator1.jpg"
                                    alt="Img" />
                                <span class="status online"></span
                            ></span>
                            <div class="profilesets">
                                <h6>John Smilga</h6>
                                <h5>Super Admin</h5>
                            </div>
                        </div>
                        <hr class="m-0" />
                        <Link class="dropdown-item" href="/pages/profile"
                            ><i class="me-2" data-feather="user"></i>My
                            Profile</Link
                        >
                        <Link
                            class="dropdown-item"
                            href="/settings/general-settings"
                            ><i class="me-2" data-feather="settings"></i
                            >Settings</Link
                        >
                        <hr class="m-0" />
                        <Link class="dropdown-item logout pb-0" href="/"
                            ><img
                                src="/img/icons/log-out.svg"
                                class="me-2"
                                alt="img"
                            />Logout</Link
                        >
                    </div>
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
                <Link class="dropdown-item" href="/settings/general-settings"
                    >Settings</Link
                >
                <Link class="dropdown-item" href="/">Logout</Link>
            </div>
        </div>
        <!-- /Mobile Menu -->
    </div>
    <!-- Header -->
    <ThemeSettings></ThemeSettings>
    <PosLoader></PosLoader>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
import ThemeSettings from '@/layouts/theme-settings.vue';
import PosLoader from '@/layouts/pos-loader.vue';

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
</script>
