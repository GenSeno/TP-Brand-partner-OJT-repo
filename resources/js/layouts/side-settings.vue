<template>
    <div class="customizer-links" id="setdata">
        <ul class="sticky-sidebar">
            <li class="sidebar-icons">
                <a
                    href="javascript:void(0);"
                    class="navigation-add"
                    data-bs-toggle="tooltip"
                    data-bs-placement="left"
                    data-bs-original-title="Theme"
                    @click="toggleClass()"
                >
                    <vue-feather type="settings" class="settings"></vue-feather>
                </a>
            </li>
        </ul>
    </div>
    <div
        :class="{
            'sidebar-settings': true,
            'nav-toggle': true,
            'show-settings': showPreview,
        }"
        @click="hidePreview"
    >
        <div class="sidebar-content sticky-sidebar-one" id="sect">
            <div class="sidebar-header">
                <div class="sidebar-theme-title">
                    <h5>Theme Customizer</h5>
                    <p>Customize & Preview in Real Time</p>
                </div>
                <div class="close-sidebar-icon d-flex">
                    <a class="sidebar-refresh me-2" href="javascript:void(0);"
                        ><vue-feather type="refresh-ccw"></vue-feather
                    ></a>
                    <a
                        class="sidebar-close"
                        href="javascript:void(0);"
                        @click="closeSidebar"
                        ><vue-feather type="x"></vue-feather
                    ></a>
                </div>
            </div>
            <form action="javascript:;" method="post">
                <div class="sidebar-body">
                    <div class="theme-mode">
                        <div class="theme-head">
                            <h6>Theme Mode</h6>
                            <p>Enjoy Dark & Light modes.</p>
                        </div>
                        <div class="row">
                            <div class="col-xl-6 ere">
                                <div class="layout-wrap">
                                    <div class="d-flex align-items-center">
                                        <div
                                            class="status-toggle d-flex align-items-center me-2"
                                        >
                                            <input
                                                type="radio"
                                                name="theme-mode"
                                                id="light_mode"
                                                class="check color-check stylemode alldata"
                                                value="light_mode"
                                            />
                                            <label
                                                for="light_mode"
                                                class="checktoggles"
                                            >
                                                <img
                                                    src="/img/theme/theme-img-01.jpg"
                                                    alt=""
                                                />
                                                <span class="theme-name"
                                                    >Light Mode</span
                                                >
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-6 ere">
                                <div class="layout-wrap">
                                    <div class="d-flex align-items-center">
                                        <div
                                            class="status-toggle d-flex align-items-center me-2"
                                        >
                                            <input
                                                type="radio"
                                                name="theme-mode"
                                                id="dark_mode"
                                                class="check color-check stylemode alldata"
                                                value="dark_mode"
                                            />
                                            <label
                                                for="dark_mode"
                                                class="checktoggles"
                                            >
                                                <img
                                                    src="/img/theme/theme-img-02.jpg"
                                                    alt=""
                                                />
                                                <span class="theme-name"
                                                    >Dark Mode</span
                                                >
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="theme-mode">
                        <div class="theme-head">
                            <h6>Direction</h6>
                            <p>Select the direction for your app.</p>
                        </div>
                        <div class="row">
                            <div class="col-xl-6 ere">
                                <div class="layout-wrap">
                                    <div class="d-flex align-items-center">
                                        <div
                                            class="status-toggle d-flex align-items-center me-2"
                                        >
                                            <input
                                                type="radio"
                                                name="direction"
                                                id="ltr"
                                                class="check direction"
                                                value="ltr"
                                            />
                                            <label
                                                for="ltr"
                                                class="checktoggles"
                                            >
                                                <a
                                                    href="https://dreamspos.dreamstechnologies.com/vue/template/"
                                                    target="_blank"
                                                >
                                                    <img
                                                        src="/img/theme/theme-img-01.jpg"
                                                        alt=""
                                                    />
                                                    <span class="theme-name"
                                                        >LTR</span
                                                    >
                                                </a>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-6 ere">
                                <div class="layout-wrap">
                                    <div class="d-flex align-items-center">
                                        <div
                                            class="status-toggle d-flex align-items-center me-2"
                                        >
                                            <input
                                                type="radio"
                                                name="direction"
                                                id="rtl"
                                                class="check direction"
                                                value="rtl"
                                            />
                                            <label
                                                for="rtl"
                                                class="checktoggles"
                                            >
                                                <a
                                                    href="https://dreamspos.dreamstechnologies.com/vue/template-rtl/"
                                                    target="_blank"
                                                >
                                                    <img
                                                        src="/img/theme/theme-img-03.jpg"
                                                        alt=""
                                                    />
                                                    <span class="theme-name"
                                                        >RTL</span
                                                    >
                                                </a>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="theme-mode border-0 mb-0">
                        <div class="theme-head">
                            <h6>Layout Mode</h6>
                            <p>Select the primary layout style for your app.</p>
                        </div>
                        <div class="row">
                            <div class="col-xl-6 ere">
                                <div class="layout-wrap">
                                    <div class="d-flex align-items-center">
                                        <div
                                            class="status-toggle d-flex align-items-center me-2"
                                        >
                                            <input
                                                type="radio"
                                                name="layout"
                                                id="default_layout"
                                                class="check layout-mode alldata"
                                                value="default"
                                            />
                                            <label
                                                for="default_layout"
                                                class="checktoggles"
                                            >
                                                <img
                                                    src="/img/theme/theme-img-01.jpg"
                                                    alt=""
                                                />
                                                <span class="theme-name"
                                                    >Default</span
                                                >
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-6 ere">
                                <div class="layout-wrap">
                                    <div class="d-flex align-items-center">
                                        <div
                                            class="status-toggle d-flex align-items-center me-2"
                                        >
                                            <input
                                                type="radio"
                                                name="layout"
                                                id="box_layout"
                                                class="check layout-mode alldata"
                                                value="box"
                                            />
                                            <label
                                                for="box_layout"
                                                class="checktoggles"
                                            >
                                                <img
                                                    src="/img/theme/theme-img-07.jpg"
                                                    alt=""
                                                />
                                                <span class="theme-name"
                                                    >Box</span
                                                >
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-6 ere">
                                <div class="layout-wrap">
                                    <div class="d-flex align-items-center">
                                        <div
                                            class="status-toggle d-flex align-items-center me-2"
                                        >
                                            <input
                                                type="radio"
                                                name="layout"
                                                id="collapse_layout"
                                                class="check layout-mode alldata"
                                                value="collapsed"
                                            />
                                            <label
                                                for="collapse_layout"
                                                class="checktoggles"
                                            >
                                                <img
                                                    src="/img/theme/theme-img-05.jpg"
                                                    alt=""
                                                />
                                                <span class="theme-name"
                                                    >Collapsed</span
                                                >
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-6 ere">
                                <div class="layout-wrap">
                                    <div class="d-flex align-items-center">
                                        <div
                                            class="status-toggle d-flex align-items-center me-2"
                                        >
                                            <input
                                                type="radio"
                                                name="layout"
                                                id="horizontal_layout"
                                                class="check layout-mode alldata"
                                                value="horizontal"
                                            />
                                            <label
                                                for="horizontal_layout"
                                                class="checktoggles"
                                            >
                                                <img
                                                    src="/img/theme/theme-img-06.jpg"
                                                    alt=""
                                                />
                                                <span class="theme-name"
                                                    >Horizontal</span
                                                >
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-6 ere">
                                <div class="layout-wrap">
                                    <div class="d-flex align-items-center">
                                        <div
                                            class="status-toggle d-flex align-items-center me-2"
                                        >
                                            <input
                                                type="radio"
                                                name="layout"
                                                id="modern_layout"
                                                class="check layout-mode alldata"
                                                value="modern"
                                            />
                                            <label
                                                for="modern_layout"
                                                class="checktoggles"
                                            >
                                                <img
                                                    src="/img/theme/theme-img-04.jpg"
                                                    alt=""
                                                />
                                                <span class="theme-name"
                                                    >Modern</span
                                                >
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="theme-mode">
                        <div class="theme-head">
                            <h6>Navigation Colors</h6>
                            <p>Setup the color for the Navigation</p>
                        </div>
                        <div class="row">
                            <div class="col-xl-4 ere">
                                <div class="layout-wrap">
                                    <div class="d-flex align-items-center">
                                        <div
                                            class="status-toggle d-flex align-items-center me-2"
                                        >
                                            <input
                                                type="radio"
                                                name="nav_color"
                                                id="light_color"
                                                class="check nav-color alldata"
                                                value="light"
                                            />
                                            <label
                                                for="light_color"
                                                class="checktoggles"
                                            >
                                                <span class="theme-name"
                                                    >Light</span
                                                >
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-4 ere">
                                <div class="layout-wrap">
                                    <div class="d-flex align-items-center">
                                        <div
                                            class="status-toggle d-flex align-items-center me-2"
                                        >
                                            <input
                                                type="radio"
                                                name="nav_color"
                                                id="grey_color"
                                                class="check nav-color alldata"
                                                value="grey"
                                            />
                                            <label
                                                for="grey_color"
                                                class="checktoggles"
                                            >
                                                <span class="theme-name"
                                                    >Grey</span
                                                >
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-4 ere">
                                <div class="layout-wrap">
                                    <div class="d-flex align-items-center">
                                        <div
                                            class="status-toggle d-flex align-items-center me-2"
                                        >
                                            <input
                                                type="radio"
                                                name="nav_color"
                                                id="dark_color"
                                                class="check nav-color alldata"
                                                value="dark"
                                            />
                                            <label
                                                for="dark_color"
                                                class="checktoggles"
                                            >
                                                <span class="theme-name"
                                                    >Dark</span
                                                >
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="sidebar-footer">
                    <div class="row">
                        <div class="col-xl-6">
                            <div class="footer-preview-btn">
                                <button
                                    @click="resetData"
                                    type="button"
                                    class="btn btn-secondary w-100"
                                >
                                    Reset
                                </button>
                            </div>
                        </div>
                        <div class="col-xl-6">
                            <div class="footer-reset-btn">
                                <a
                                    href="https://themeforest.net/item/dreamspos-pos-inventory-management-admin-dashboard-template/38834413"
                                    target="_blank"
                                    class="btn btn-primary w-100"
                                    >Buy Now</a
                                >
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';

const showPreview = ref(false);

const theme = ref(localStorage.getItem('theme') || 'light');
const sidebarTheme = ref(localStorage.getItem('sidebarTheme') || 'light');
const color = ref(localStorage.getItem('color') || 'primary');
const layout = ref(localStorage.getItem('layout') || 'default');
const topbar = ref(localStorage.getItem('topbar') || 'white');
const topbarcolor = ref(localStorage.getItem('topbarcolor') || 'white');
const size = ref(localStorage.getItem('size') || 'default');
const width = ref(localStorage.getItem('width') || 'fluid');

const toggleClass = () => {
    showPreview.value = !showPreview.value;
};

const hidePreview = (event) => {
    if (event.target.classList.contains('sidebar-settings')) {
        showPreview.value = false;
    }
};

const closeSidebar = () => {
    showPreview.value = false;
};

const applyThemeSettings = () => {
    document.documentElement.setAttribute('data-theme', theme.value);
    document.documentElement.setAttribute('data-sidebar', sidebarTheme.value);
    document.documentElement.setAttribute('data-color', color.value);
    document.documentElement.setAttribute('data-layout', layout.value);
    document.documentElement.setAttribute('data-topbar', topbar.value);
    document.documentElement.setAttribute(
        'data-topbarcolor',
        topbarcolor.value,
    );
    document.documentElement.setAttribute('data-size', size.value);
    document.documentElement.setAttribute('data-width', width.value);
};

const setThemeAndSidebarTheme = (
    themeValue,
    sidebarThemeValue,
    colorValue,
    layoutValue,
    topbarValue,
    topbarcolorValue,
    sizeValue,
    widthValue,
) => {
    document.documentElement.setAttribute('data-theme', themeValue);
    document.documentElement.setAttribute('data-sidebar', sidebarThemeValue);
    document.documentElement.setAttribute('data-color', colorValue);
    document.documentElement.setAttribute('data-layout', layoutValue);
    document.documentElement.setAttribute('data-topbar', topbarValue);
    document.documentElement.setAttribute('data-topbarcolor', topbarcolorValue);
    document.documentElement.setAttribute('data-size', sizeValue);
    document.documentElement.setAttribute('data-width', widthValue);

    let layout_mini = 0;
    document.body.classList.remove(
        'mini-sidebar',
        'menu-horizontal',
        'expand-menu',
        'layout-box-mode',
    );

    if (
        layoutValue === 'mini' ||
        sizeValue === 'compact' ||
        widthValue === 'box'
    ) {
        document.body.classList.add('mini-sidebar');
        layout_mini = 1;
    }
    if (
        layoutValue === 'horizontal' ||
        layoutValue === 'horizontal-overlay' ||
        layoutValue === 'horizontal-single'
    ) {
        document.body.classList.add('menu-horizontal');
    }
    if (sizeValue === 'hoverview') {
        document.body.classList.add('expand-menu');
        if (layout_mini === 0) document.body.classList.remove('mini-sidebar');
    }
    if (widthValue === 'box') {
        document.body.classList.add('layout-box-mode');
        document.body.classList.add('mini-sidebar');
        layout_mini = 1;
    }

    // Store settings
    localStorage.setItem('theme', themeValue);
    localStorage.setItem('sidebarTheme', sidebarThemeValue);
    localStorage.setItem('color', colorValue);
    localStorage.setItem('layout', layoutValue);
    localStorage.setItem('topbar', topbarValue);
    localStorage.setItem('topbarcolor', topbarcolorValue);
    localStorage.setItem('size', sizeValue);
    localStorage.setItem('width', widthValue);
};

const handleLayoutChange = (event) => {
    layout.value = event.target.value;
    setThemeAndSidebarTheme(
        theme.value,
        sidebarTheme.value,
        color.value,
        layout.value,
        topbar.value,
        topbarcolor.value,
        size.value,
        width.value,
    );
};

const resetData = () => {
    setThemeAndSidebarTheme(
        'light',
        'light',
        'primary',
        'default',
        'white',
        'white',
        'default',
        'fluid',
    );

    theme.value = 'light';
    sidebarTheme.value = 'light';
    color.value = 'primary';
    layout.value = 'default';
    topbar.value = 'white';
    topbarcolor.value = 'white';
    size.value = 'default';
    width.value = 'fluid';

    document.body.removeAttribute('data-sidebarbg');
    document.body.removeAttribute('data-topbarbg');
    localStorage.removeItem('sidebarBg');
    localStorage.removeItem('topbarbg');
};

onMounted(() => {
    applyThemeSettings();

    // Attach event listeners to radio buttons
    document.querySelectorAll('input[name="LayoutTheme"]').forEach((radio) => {
        radio.addEventListener('change', handleLayoutChange);
    });
});
</script>
