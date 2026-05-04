<template>
    <div class="header">
        <div class="main-header">
            <div class="header-left active">
                <Link
                    :href="route('brand-partner.dashboard')"
                    class="logo logo-normal"
                >
                    <img src="/img/logo/logo_pakaras_white.png" alt="Logo" />
                </Link>
                <Link
                    :href="route('brand-partner.dashboard')"
                    class="logo logo-white"
                >
                    <img src="/img/logo-white.svg" alt="Logo" />
                </Link>
                <Link
                    :href="route('brand-partner.dashboard')"
                    class="logo-small"
                >
                    <img src="/img/logo-small.png" alt="Logo" />
                </Link>
                <a
                    id="toggle_btn"
                    href="javascript:void(0);"
                    @click="toggleSidebar"
                >
                    <vue-feather
                        type="chevrons-left"
                        class="feather-16"
                    ></vue-feather>
                </a>
            </div>

            <a
                id="mobile_btn"
                class="mobile_btn"
                href="javascript:void(0);"
                @click="toggleMobileSidebar"
            >
                <span class="bar-icon">
                    <span></span>
                    <span></span>
                    <span></span>
                </span>
            </a>

            <ul class="nav user-menu">
                <div class="nav nav-menu">
                    <li class="nav-item nav-item-box">
                        <a
                            href="javascript:void(0);"
                            id="btnFullscreen"
                            @click="toggleFullscreen"
                        >
                            <i class="ti ti-maximize"></i>
                        </a>
                    </li>

                    <li
                        class="nav-item dropdown has-arrow main-drop profile-nav"
                        :class="{ show: dropdownOpen }"
                    >
                        <a
                            href="javascript:void(0);"
                            class="nav-link userset"
                            @click="toggleDropdown"
                        >
                            <span class="user-info p-0">
                                <span class="user-letter">
                                    <img
                                        :src="
                                            brandPartner?.logo_url ||
                                            '/img/default.png'
                                        "
                                        alt="Logo"
                                        class="img-fluid border"
                                    />
                                </span>
                                <span class="user-detail ms-2">
                                    <span class="user-name">{{
                                        brandPartner?.name || 'Brand Partner'
                                    }}</span>
                                    <span class="user-role">{{
                                        brandPartner?.role || 'Partner'
                                    }}</span>
                                </span>
                            </span>
                        </a>
                        <div
                            class="dropdown-menu dropdown-menu-end menu-drop-user"
                            :class="{ show: dropdownOpen }"
                        >
                            <Link
                                class="dropdown-item"
                                :href="route('brand-partner.settings.index')"
                            >
                                <vue-feather
                                    type="settings"
                                    class="feather-13 me-1"
                                ></vue-feather>
                                Settings
                            </Link>
                            <Link
                                :href="route('brand-partner.logout')"
                                method="post"
                                as="button"
                                class="dropdown-item logout"
                            >
                                <vue-feather
                                    type="log-out"
                                    class="feather-13 me-1"
                                ></vue-feather>
                                Logout
                            </Link>
                        </div>
                    </li>
                </div>
            </ul>
        </div>
    </div>
</template>

<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import { computed, ref, onMounted, onUnmounted } from 'vue';

const page = usePage();
const brandPartner = computed(() => page.props.auth?.brand_partner);

const dropdownOpen = ref(false);

const toggleSidebar = () => {
    document.body.classList.toggle('mini-sidebar');
    const toggle = document.getElementById('toggle_btn');
    if (toggle) {
        toggle.classList.toggle('active');
    }
};

const toggleMobileSidebar = () => {
    document.querySelector('.main-wrapper').classList.toggle('slide-nav');
    document.querySelector('.sidebar-overlay').classList.toggle('opened');
};

const toggleFullscreen = () => {
    if (!document.fullscreenElement) {
        document.documentElement.requestFullscreen();
    } else {
        document.exitFullscreen();
    }
};

const toggleDropdown = () => {
    dropdownOpen.value = !dropdownOpen.value;
};

// Close dropdown when clicking outside
const handleClickOutside = (event) => {
    const dropdown = document.querySelector('.main-drop');
    if (dropdown && !dropdown.contains(event.target)) {
        dropdownOpen.value = false;
    }
};

onMounted(() => {
    document.addEventListener('click', handleClickOutside);
});

onUnmounted(() => {
    document.removeEventListener('click', handleClickOutside);
});
</script>

<style scoped>
:deep(.menu-drop-user) {
    transform: none !important;
    margin-top: 0.25rem !important;
    position: absolute !important;
    top: 100% !important;
    right: 0 !important;
    margin-left: auto;
}
.nav-menu {
    align-items: center;
    margin-left: auto;
}
</style>
