<template>
    <div class="settings-sidebar" id="sidebar2">
        <div class="sidebar-inner slimscroll">
            <div id="sidebar-menu5" class="sidebar-menu">
                <h4 class="fw-bold fs-18 mb-2 pb-2">Settings</h4>
                <ul>
                    <li class="submenu-open">
                        <ul>
                            <li
                                v-for="menu in state.Settings"
                                :key="menu.title"
                                class="submenu"
                            >
                                <a
                                    href="javascript:void(0);"
                                    @click="toggleSubMenu(menu)"
                                    :class="{
                                        subdrop: menu.expanded,
                                        active: isActiveMenu(menu),
                                    }"
                                >
                                    <vue-feather
                                        :type="menu.icon"
                                        class="fs-18"
                                    ></vue-feather>
                                    <span>{{ menu.title }}</span>
                                    <span class="menu-arrow"></span>
                                </a>
                                <ul
                                    :class="{
                                        'd-block': menu.expanded,
                                        'd-none': !menu.expanded,
                                    }"
                                >
                                    <li
                                        v-for="subMenu in menu.subMenu"
                                        :key="subMenu.routes"
                                    >
                                        <Link
                                            :href="subMenu.routes"
                                            class="Link"
                                            :class="{
                                                active: isActive(
                                                    subMenu.routes,
                                                ),
                                            }"
                                        >
                                            {{ subMenu.title }}
                                        </Link>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</template>

<script setup>
import { reactive } from 'vue';
import { usePage } from '@inertiajs/vue3';
import Settings from '@/json/settings.json';

const state = reactive({
    Settings: Settings.map((menu) => ({
        ...menu,
        expanded: menu.expanded || false,
    })),
});

const page = usePage();

const toggleSubMenu = (menu) => {
    menu.expanded = !menu.expanded;
};

const isActive = (route) => {
    return page.url === route;
};

const isActiveMenu = (menu) => {
    return menu.subMenu.some((subMenu) => page.url === subMenu.routes);
};
</script>
