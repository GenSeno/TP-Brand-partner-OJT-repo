<template>
    <div class="sidebar sidebar-horizontal" id="horizontal-menu">
        <div id="sidebar-menu-3" class="sidebar-menu">
            <div class="main-menu">
                <ul class="nav-menu">
                    <template
                        v-for="mainTitle in sideBarData"
                        :key="mainTitle.tittle"
                    >
                        <li class="submenu">
                            <a
                                :class="{
                                    active:
                                        openedSubMenu[0] === mainTitle.tittle,
                                    subdrop:
                                        openedSubMenu[0] === mainTitle.tittle,
                                }"
                                @click="showMenu(mainTitle.tittle)"
                            >
                                <i
                                    class="ti"
                                    :class="'ti-' + mainTitle.icon"
                                ></i>
                                <span>{{ mainTitle.tittle }}</span>
                                <span class="menu-arrow"></span>
                            </a>
                            <ul
                                :class="{
                                    'd-block ':
                                        openedSubMenu[0] === mainTitle.tittle,
                                    'd-none':
                                        openedSubMenu[0] !== mainTitle.tittle,
                                }"
                            >
                                <template
                                    v-for="mainMenus in mainTitle.subRoutes"
                                    :key="mainMenus.tittle"
                                >
                                    <template
                                        v-if="mainMenus.hasSubRoute === false"
                                    >
                                        <li class="submenu">
                                            <Link
                                                :class="{
                                                    active:
                                                        mainMenus.activeRoute ===
                                                        activePath,
                                                }"
                                                v-if="mainMenus.route"
                                                :href="mainMenus.route"
                                            >
                                                <span>{{
                                                    mainMenus.tittle
                                                }}</span>
                                            </Link>
                                        </li>
                                    </template>
                                    <template
                                        v-if="mainMenus.hasSubRoute === true"
                                    >
                                        <li class="submenu">
                                            <a
                                                :class="{
                                                    active:
                                                        activePath ===
                                                        mainMenus.activeRoute,
                                                    subdrop:
                                                        openedSubMenu[1] ===
                                                        mainMenus.tittle,
                                                }"
                                                @click="
                                                    showSubMenu(
                                                        mainMenus.tittle,
                                                    )
                                                "
                                            >
                                                <span>{{
                                                    mainMenus.tittle
                                                }}</span>
                                                <span class="menu-arrow"></span>
                                            </a>
                                            <ul
                                                :class="{
                                                    'd-block':
                                                        openedSubMenu[1] ===
                                                        mainMenus.tittle,
                                                    'd-none':
                                                        openedSubMenu[1] !==
                                                        mainMenus.tittle,
                                                }"
                                            >
                                                <template
                                                    v-for="subDropMenus in mainMenus.subRoutes"
                                                    :key="subDropMenus.tittle"
                                                >
                                                    <template
                                                        v-if="
                                                            !subDropMenus.customSubmenuTwo
                                                        "
                                                    >
                                                        <li>
                                                            <Link
                                                                v-if="
                                                                    subDropMenus.route
                                                                "
                                                                :href="
                                                                    subDropMenus.route
                                                                "
                                                                >{{
                                                                    subDropMenus.tittle
                                                                }}</Link
                                                            >
                                                        </li>
                                                    </template>
                                                    <template
                                                        v-if="
                                                            subDropMenus.customSubmenuTwo
                                                        "
                                                    >
                                                        <li
                                                            class="submenu submenu-two"
                                                        >
                                                            <a
                                                                :class="{
                                                                    subdrop:
                                                                        isSubMenuTwo[1] ===
                                                                        subDropMenus.tittle,
                                                                }"
                                                                @click="
                                                                    showSubTwo(
                                                                        subDropMenus.tittle,
                                                                    )
                                                                "
                                                            >
                                                                {{
                                                                    subDropMenus.tittle
                                                                }}
                                                                <span
                                                                    class="menu-arrow inside-submenu"
                                                                ></span>
                                                            </a>
                                                            <ul
                                                                :class="{
                                                                    'd-block':
                                                                        isSubMenuTwo[1] ===
                                                                        subDropMenus.tittle,
                                                                    'd-none':
                                                                        isSubMenuTwo[1] !==
                                                                        subDropMenus.tittle,
                                                                }"
                                                            >
                                                                <li
                                                                    v-for="subDropMenus2 in subDropMenus.subMenusTwo"
                                                                    :key="
                                                                        subDropMenus2.tittle
                                                                    "
                                                                >
                                                                    <Link
                                                                        v-if="
                                                                            subDropMenus2.route
                                                                        "
                                                                        :href="
                                                                            subDropMenus2.route
                                                                        "
                                                                        >{{
                                                                            subDropMenus2.tittle
                                                                        }}</Link
                                                                    >
                                                                </li>
                                                            </ul>
                                                        </li>
                                                    </template>
                                                </template>
                                            </ul>
                                        </li>
                                    </template>
                                </template>
                            </ul>
                        </li>
                    </template>
                </ul>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import sideBarData from '@/json/horizontal-header.json';
import { Link } from '@inertiajs/vue3';

const openedSubMenu = ref([]);
const isSubMenuTwo = ref([]);
const activePath = ref(''); // Set this based on your routing logic

const showMenu = (title) => {
    if (openedSubMenu.value[0] === title) {
        openedSubMenu.value = [];
    } else {
        openedSubMenu.value = [title];
    }
};

const showSubMenu = (title) => {
    if (openedSubMenu.value[1] === title) {
        openedSubMenu.value = [openedSubMenu.value[0]];
    } else {
        openedSubMenu.value = [openedSubMenu.value[0], title];
    }
};

const showSubTwo = (title) => {
    if (isSubMenuTwo.value[1] === title) {
        isSubMenuTwo.value = [];
    } else {
        isSubMenuTwo.value = [isSubMenuTwo.value[0], title];
    }
};
</script>
