import { ref } from 'vue';
import { router } from '@inertiajs/vue3';

export const globalWishlistedIds = ref([]);
let initialized = false;

// Listen for restore events (back/forward navigation)
let isRestoring = false;
router.on('restore', () => {
    isRestoring = true;
    setTimeout(() => {
        isRestoring = false;
    }, 100);
});

// Sync from page props
export const initWishlist = (incomingIds) => {
    if (!incomingIds) return;
    
    // Only accept incoming IDs if we are NOT restoring from history cache
    // OR if this is the very first initialization
    if (!isRestoring || !initialized) {
        globalWishlistedIds.value = [...incomingIds];
        initialized = true;
    }
};

export const toggleGlobalWishlist = (productId) => {
    const idx = globalWishlistedIds.value.indexOf(productId);
    if (idx > -1) {
        globalWishlistedIds.value.splice(idx, 1);
        return 'remove';
    } else {
        globalWishlistedIds.value.push(productId);
        return 'add';
    }
};

export const revertGlobalWishlist = (productId, action) => {
    const idx = globalWishlistedIds.value.indexOf(productId);
    if (action === 'add' && idx > -1) {
        globalWishlistedIds.value.splice(idx, 1);
    } else if (action === 'remove' && idx === -1) {
        globalWishlistedIds.value.push(productId);
    }
};
