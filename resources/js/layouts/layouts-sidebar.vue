<template>
    <div class="" :class="['sidebar', sidebarClass]" id="sidebar">
        <!-- Logo -->
        <div class="sidebar-logo active">
            <Link href="/dashboard/" class="logo logo-normal">
                <img src="/img/logo/logo-pdf.png" alt="Img" />
            </Link>
            <Link href="/dashboard/" class="logo logo-white">
                <img src="/img/logo/tpinklab_logo_white_full.svg" alt="Img" />
            </Link>
            <Link href="/dashboard/" class="logo-small">
                <img src="/img/logo/logo-tp.png" alt="Img" />
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

        <!-- Modern Profile -->
        <div class="modern-profile p-3 pb-0">
            <div class="text-center rounded bg-light p-3 mb-4 user-profile">
                <div class="avatar avatar-lg online mb-3">
                    <img
                        src="/img/customer/customer15.jpg"
                        alt="Img"
                        class="img-fluid rounded-circle"
                    />
                </div>
                <h6 class="fs-14 fw-bold mb-1">Adrian Herman</h6>
                <p class="fs-12 mb-0">System Admin</p>
            </div>
            <div class="sidebar-nav mb-3">
                <ul
                    class="nav nav-tabs nav-tabs-solid nav-tabs-rounded nav-justified bg-transparent"
                    role="tablist"
                >
                    <li class="nav-item">
                        <a class="nav-link active border-0" href="#">Menu</a>
                    </li>
                    <li class="nav-item">
                        <Link class="nav-link border-0" href="/application/chat"
                            >Chats</Link
                        >
                    </li>
                    <li class="nav-item">
                        <Link
                            class="nav-link border-0"
                            href="/application/email"
                            >Inbox</Link
                        >
                    </li>
                </ul>
            </div>
        </div>
        <!-- /Modern Profile -->

        <!-- Sidebar Header -->
        <div class="sidebar-header p-3 pb-0 pt-2">
            <div
                class="text-center rounded bg-light p-2 mb-4 sidebar-profile d-flex align-items-center"
            >
                <div class="avatar avatar-md online">
                    <img
                        src="/img/customer/customer15.jpg"
                        alt="Img"
                        class="img-fluid rounded-circle"
                    />
                </div>
                <div class="text-start sidebar-profile-info ms-2">
                    <h6 class="fs-14 fw-bold mb-1">Adrian Herman</h6>
                    <p class="fs-12">System Admin</p>
                </div>
            </div>
            <div
                class="d-flex align-items-center justify-content-between menu-item mb-3"
            >
                <div>
                    <Link
                        href="/dashboard/"
                        class="btn btn-sm btn-icon bg-light"
                    >
                        <i class="ti ti-layout-grid-remove"></i>
                    </Link>
                </div>
                <div>
                    <Link
                        href="/application/chat"
                        class="btn btn-sm btn-icon bg-light"
                    >
                        <i class="ti ti-brand-hipchat"></i>
                    </Link>
                </div>
                <div>
                    <Link
                        href="/application/email"
                        class="btn btn-sm btn-icon bg-light position-relative"
                    >
                        <i class="ti ti-message"></i>
                    </Link>
                </div>
                <div class="notification-item">
                    <Link
                        href="/activities"
                        class="btn btn-sm btn-icon bg-light position-relative"
                    >
                        <i class="ti ti-bell"></i>
                        <span class="notification-status-dot"></span>
                    </Link>
                </div>
                <div class="me-0">
                    <Link
                        href="/settings/general-settings"
                        class="btn btn-sm btn-icon bg-light"
                    >
                        <i class="ti ti-settings"></i>
                    </Link>
                </div>
            </div>
        </div>
        <!-- /Sidebar Header -->

        <!-- Sidebar Inner -->
        <simplebar id="scrollbar" class="sidebar-inner" ref="scrollbar">
            <div class="sidebar-inner slimscroll flex-fill">
                <div id="sidebar-menu" class="sidebar-menu">
                    <VerticalSidebar></VerticalSidebar>
                </div>
            </div>
        </simplebar>
        <!-- /Sidebar Inner -->
    </div>
    <TwoColSidebar></TwoColSidebar>
    <HorizontalHeader></HorizontalHeader>
    <PosLoader></PosLoader>
</template>

<script setup>
import { ref, watch, onMounted, onBeforeUnmount } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import TwoColSidebar from '@/layouts/two-col-sidebar.vue';
import HorizontalHeader from '@/layouts/horizontal-header.vue';
import PosLoader from '@/layouts/pos-loader.vue';
import VerticalSidebar from '@/layouts/vertical-sidebar.vue';
import simplebar from 'simplebar-vue';
import 'simplebar-vue/dist/simplebar.min.css';

const page = usePage();
const scrollbar = ref(null);
const sidebarClass = ref('');

const toggleSidebar = () => {
    const body = document.body;
    body.classList.toggle('mini-sidebar');
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

const checkPosRoute = (path) => {
    return (
        path.startsWith('/pos/pos-1') ||
        path.startsWith('/pos/pos-2') ||
        path.startsWith('/pos/pos-3') ||
        path.startsWith('/pos/pos-4') ||
        path.startsWith('/pos/pos-5')
    );
};

watch(
    () => page.url,
    (newPath) => {
        sidebarClass.value = checkPosRoute(newPath) ? 'd-none' : '';
    },
);

onMounted(() => {
    initMouseoverListener();
    if (checkPosRoute(page.url)) {
        sidebarClass.value = 'd-none';
    }
});

onBeforeUnmount(() => {
    document.removeEventListener('mouseover', handleMouseover);
});
</script>
