<template>
    <div class="sidebar" id="sidebar">
        <!-- Logo -->
        <div class="sidebar-logo active">
            <Link
                :href="route('brand-partner.dashboard')"
                class="logo logo-normal"
            >
                <img src="/img/logo/logo-pdf.png" alt="Logo" />
            </Link>
            <Link
                :href="route('brand-partner.dashboard')"
                class="logo logo-white"
            >
                <img src="/img/logo/tpinklab_logo_white_full.svg" alt="Logo" />
            </Link>
            <Link :href="route('brand-partner.dashboard')" class="logo-small">
                <img src="/img/logo/logo-tp.png" alt="Logo" />
            </Link>
            <a
                id="toggle_btn"
                href="javascript:void(0);"
                @click="toggleSidebar"
            >
                <i class="ti ti-chevrons-left"></i>
            </a>
        </div>
        <!-- /Logo -->

        <simplebar class="sidebar-inner">
            <div class="sidebar-inner slimscroll flex-fill">
                <div id="sidebar-menu" class="sidebar-menu">
                    <ul>
                        <li class="submenu-open">
                            <h6 class="submenu-hdr">Main Menu</h6>
                            <ul>
                                <li
                                    :class="{
                                        active: isActive(
                                            '/brand-partner/dashboard',
                                        ),
                                    }"
                                >
                                    <Link
                                        :href="route('brand-partner.dashboard')"
                                    >
                                        <vue-feather
                                            type="grid"
                                            class="feather-16 me-2"
                                        ></vue-feather>
                                        <span>Dashboard</span>
                                    </Link>
                                </li>
                            </ul>
                        </li>

                        <li class="submenu-open">
                            <h6 class="submenu-hdr">Catalog</h6>
                            <ul>
                                <li
                                    :class="{
                                        active: isActive(
                                            '/brand-partner/events',
                                        ),
                                    }"
                                >
                                    <Link
                                        :href="
                                            route('brand-partner.events.index')
                                        "
                                    >
                                        <vue-feather
                                            type="calendar"
                                            class="feather-16 me-2"
                                        ></vue-feather>
                                        <span>Events</span>
                                    </Link>
                                </li>
                                <li
                                    :class="{
                                        active: isActive(
                                            '/brand-partner/products',
                                        ),
                                    }"
                                >
                                    <Link
                                        :href="
                                            route(
                                                'brand-partner.products.index',
                                            )
                                        "
                                    >
                                        <vue-feather
                                            type="box"
                                            class="feather-16 me-2"
                                        ></vue-feather>
                                        <span>Products</span>
                                        <span
                                            v-if="
                                                $page.props
                                                    .pendingProductsCount > 0
                                            "
                                            class="badge bg-warning ms-auto"
                                            style="font-size: 10px"
                                            >{{
                                                $page.props.pendingProductsCount
                                            }}</span
                                        >
                                    </Link>
                                </li>
                            </ul>
                        </li>

                        <li class="submenu-open">
                            <h6 class="submenu-hdr">Sales</h6>
                            <ul>
                                <li
                                    :class="{
                                        active: isActive(
                                            '/brand-partner/orders',
                                        ),
                                    }"
                                >
                                    <Link
                                        :href="
                                            route('brand-partner.orders.index')
                                        "
                                    >
                                        <vue-feather
                                            type="shopping-cart"
                                            class="feather-16 me-2"
                                        ></vue-feather>
                                        <span>Orders</span>
                                    </Link>
                                </li>
                            </ul>
                        </li>

                        <li class="submenu-open">
                            <h6 class="submenu-hdr">Settings</h6>
                            <ul>
                                <li
                                    :class="{
                                        active: isActive(
                                            '/brand-partner/settings',
                                        ),
                                    }"
                                >
                                    <Link
                                        :href="
                                            route(
                                                'brand-partner.settings.index',
                                            )
                                        "
                                    >
                                        <vue-feather
                                            type="settings"
                                            class="feather-16 me-2"
                                        ></vue-feather>
                                        <span>Profile Settings</span>
                                    </Link>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </div>
        </simplebar>
    </div>
    <div class="sidebar-overlay" @click="closeSidebar"></div>
</template>

<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import simplebar from 'simplebar-vue';
import 'simplebar-vue/dist/simplebar.min.css';

const page = usePage();

const isActive = (pathPrefix) => {
    const currentPath = page.url.split('?')[0];
    return currentPath.startsWith(pathPrefix);
};

const closeSidebar = () => {
    document.querySelector('.main-wrapper').classList.remove('slide-nav');
    document.querySelector('.sidebar-overlay').classList.remove('opened');
};

const toggleSidebar = () => {
    document.body.classList.toggle('mini-sidebar');
};
</script>
