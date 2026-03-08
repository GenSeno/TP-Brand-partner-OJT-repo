<template>
    <ul>
        <template v-for="item in sideBarData" :key="item.title">
            <li v-if="hasAnyPermission(item.permissions)" class="submenu-open">
                <h6 v-if="!!item.title" class="submenu-hdr">
                    {{ item.title }}
                </h6>
                <ul>
                    <template v-for="menu in item.menu" :key="menu.menuValue">
                        <li
                            v-if="
                                !menu.hasSubRoute &&
                                !menu.hasSubRouteTwo &&
                                hasAnyPermission(menu.permissions)
                            "
                            :class="{ active: activeMenus[menu.menuValue] }"
                        >
                            <Link
                                v-if="menu.route"
                                :href="handleRoute(menu.route)"
                                @click="collapseAllSubMenu"
                                view-transition
                            >
                                <i :class="menu.icon" class="fs-16 me-2"></i>
                                <span>{{ menu.menuValue }}</span>
                            </Link>
                        </li>
                        <li
                            v-if="
                                menu.hasSubRoute &&
                                hasAnyPermission(menu.permissions)
                            "
                            class="submenu"
                        >
                            <a
                                href="javascript:void(0);"
                                @click="toggleSubMenus(menu)"
                                :class="{
                                    subdrop: openMenus.has(menu.menuValue),
                                    active: activeMenus[menu.menuValue],
                                }"
                            >
                                <i :class="menu.icon" class="fs-16 me-2"></i>
                                <span>{{ menu.menuValue }}</span>
                                <span class="menu-arrow"></span>
                            </a>
                            <ul
                                :class="
                                    openMenus.has(menu.menuValue)
                                        ? 'd-block'
                                        : 'd-none'
                                "
                            >
                                <li
                                    v-for="(subMenu, index) in menu.subMenus"
                                    :key="index"
                                >
                                    <Link
                                        v-if="subMenu.route"
                                        :href="handleRoute(subMenu.route)"
                                        :class="{
                                            active: activeRoutes[subMenu.route],
                                        }"
                                        view-transition
                                        >{{ subMenu.menuValue }}</Link
                                    >
                                </li>
                            </ul>
                        </li>
                        <li v-if="menu.hasSubRouteTwo" class="submenu">
                            <a
                                href="javascript:void(0);"
                                @click="toggleMenu(menu)"
                                :class="{
                                    subdrop: openMenus.has(menu.menuValue),
                                    active: activeMenus[menu.menuValue],
                                }"
                            >
                                <i :class="menu.icon" class="fs-16 me-2"></i>
                                <span>{{ menu.menuValue }}</span>
                                <span class="menu-arrow"></span>
                            </a>
                            <ul
                                :class="
                                    openMenus.has(menu.menuValue)
                                        ? 'd-block'
                                        : 'd-none'
                                "
                            >
                                <template
                                    v-for="subMenus in menu.subMenus"
                                    :key="subMenus.menuValue"
                                >
                                    <li v-if="!subMenus.customSubmenuTwo">
                                        <Link
                                            v-if="subMenus.route"
                                            :href="handleRoute(subMenus.route)"
                                            :class="{
                                                active: activeRoutes[
                                                    subMenus.route
                                                ],
                                            }"
                                            view-transition
                                            >{{ subMenus.menuValue }}</Link
                                        >
                                    </li>
                                    <li v-else class="submenu submenu-two">
                                        <a
                                            href="javascript:void(0);"
                                            @click="toggleSubmenuOne(subMenus)"
                                            :class="{
                                                subdrop: openSubmenus.has(
                                                    subMenus.menuValue,
                                                ),
                                                active: activeMenus[
                                                    subMenus.menuValue
                                                ],
                                            }"
                                        >
                                            {{ subMenus.menuValue }}
                                            <span
                                                class="menu-arrow inside-submenu"
                                            ></span>
                                        </a>
                                        <ul
                                            :class="
                                                openSubmenus.has(
                                                    subMenus.menuValue,
                                                )
                                                    ? 'd-block'
                                                    : 'd-none'
                                            "
                                        >
                                            <li
                                                v-for="subMenuTwo in subMenus.subMenusTwo"
                                                :key="subMenuTwo.menuValue"
                                            >
                                                <Link
                                                    v-if="subMenuTwo.route"
                                                    :href="
                                                        handleRoute(
                                                            subMenuTwo.route,
                                                        )
                                                    "
                                                    :class="{
                                                        active: activeRoutes[
                                                            subMenuTwo.route
                                                        ],
                                                    }"
                                                    view-transition
                                                    >{{
                                                        subMenuTwo.menuValue
                                                    }}</Link
                                                >
                                            </li>
                                        </ul>
                                    </li>
                                </template>
                            </ul>
                        </li>
                    </template>
                </ul>
            </li>
        </template>
    </ul>
</template>

<script setup>
import { computed, reactive, watch } from 'vue';
import { usePage, Link } from '@inertiajs/vue3';
import side_bar_data from '@/json/sidebar.json';
import { useMenuState } from '@/composables/menuState';
import { hasAnyPermission } from '@/helpers/guard';

// Use composable for persistent state
const { openMenus, openSubmenus, setMenuOpen, setSubmenuOpen, clearAll } =
    useMenuState();

const page = usePage();
const currentRoute = computed(() => page.url);

const sideBarData = side_bar_data;

// Reactive objects for active states (better performance than function calls)
const activeMenus = reactive({});
const activeRoutes = reactive({});

// Update active states
const updateActiveStates = () => {
    // Clear previous states
    Object.keys(activeMenus).forEach((key) => delete activeMenus[key]);
    Object.keys(activeRoutes).forEach((key) => delete activeRoutes[key]);

    sideBarData.forEach((item) => {
        item.menu.forEach((menu) => {
            // Check if menu is active
            if (
                menu.active_routes?.some((activeRoute) =>
                    route().current(activeRoute),
                )
            ) {
                activeMenus[menu.menuValue] = true;
            }

            // Check route-level activation
            if (menu.route && route().current(menu.route)) {
                activeRoutes[menu.route] = true;
            }

            // Check submenus
            if (menu.subMenus) {
                menu.subMenus.forEach((submenu) => {
                    if (submenu.route && route().current(submenu.route)) {
                        activeRoutes[submenu.route] = true;
                    }

                    if (
                        submenu.active_routes?.some((r) => route().current(r))
                    ) {
                        activeMenus[submenu.menuValue] = true;
                    }

                    // Check nested submenus
                    if (submenu.subMenusTwo) {
                        submenu.subMenusTwo.forEach((subMenuTwo) => {
                            if (
                                subMenuTwo.route &&
                                route().current(subMenuTwo.route)
                            ) {
                                activeRoutes[subMenuTwo.route] = true;
                            }
                        });
                    }
                });
            }
        });
    });
};

const toggleSubMenus = (menu) => {
    const id = menu.menuValue;

    if (openMenus.value.has(id)) {
        openMenus.value.delete(id);
    } else {
        openMenus.value.add(id);
    }

    // Nudge Vue to notice the Set changed
    openMenus.value = new Set(openMenus.value);
};

const toggleMenu = (menu) => {
    let next = new Set(openMenus.value);

    if (next.has(menu.menuValue)) {
        next.delete(menu.menuValue);
    } else {
        next = new Set([menu.menuValue]);
    }

    openMenus.value = next;
};

const toggleSubmenuOne = (submenu) => {
    setSubmenuOpen(
        submenu.menuValue,
        !openSubmenus.value.has(submenu.menuValue),
    );
};

const collapseAllSubMenu = () => {
    clearAll();
};

const handleRoute = (routeName) => {
    if (routeName && !['#', 'javascript:void(0);'].includes(routeName)) {
        return route(routeName);
    }
    return '#';
};

// Auto-expand active menus on route change
watch(
    currentRoute,
    () => {
        updateActiveStates();

        sideBarData.forEach((item) => {
            item.menu.forEach((menu) => {
                if (activeMenus[menu.menuValue]) {
                    setMenuOpen(menu.menuValue, true);

                    // Check nested submenus
                    if (menu.subMenus) {
                        menu.subMenus.forEach((submenu) => {
                            if (activeMenus[submenu.menuValue]) {
                                setSubmenuOpen(submenu.menuValue, true);
                            }
                        });
                    }
                }
            });
        });
    },
    { immediate: true },
);
</script>
