import { ref } from 'vue';

const openMenus = ref(new Set());
const openSubmenus = ref(new Set());

export function useMenuState() {
    const toggleMenu = (menuId, isOpen) => {
        isOpen ? openMenus.value.add(menuId) : openMenus.value.delete(menuId);
        // Trigger reactivity for Set updates
        openMenus.value = new Set(openMenus.value);
    };

    const setMenuOpen = (menuId, isOpen) => {
        if (isOpen) {
            openMenus.value.add(menuId);
        } else {
            openMenus.value.delete(menuId);
        }
        openMenus.value = new Set(openMenus.value);
    };

    const toggleSubmenu = (submenuId, isOpen) => {
        isOpen
            ? openSubmenus.value.add(submenuId)
            : openSubmenus.value.delete(submenuId);
        // Trigger reactivity for Set updates
        openSubmenus.value = new Set(openSubmenus.value);
    };

    const isMenuOpen = (menuId) => openMenus.value.has(menuId);

    const isSubmenuOpen = (submenuId) => openSubmenus.value.has(submenuId);

    const clearAll = () => {
        openMenus.value = new Set();
        openSubmenus.value = new Set();
    };

    return {
        openMenus,
        openSubmenus,
        toggleMenu,
        setMenuOpen,
        toggleSubmenu,
        isMenuOpen,
        isSubmenuOpen,
        clearAll,
    };
}
