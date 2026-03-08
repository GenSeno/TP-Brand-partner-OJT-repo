<template>
    <div
        class="toast-container position-fixed top-0 end-0 p-3"
        style="z-index: 9999"
    >
        <div
            v-for="toast in toasts"
            :key="toast.id"
            :ref="(el) => setToastRef(el, toast.id)"
            class="toast align-items-center border-0"
            :class="getToastClass(toast.type)"
            role="alert"
            aria-live="assertive"
            aria-atomic="true"
        >
            <div class="d-flex">
                <div class="toast-body text-white">
                    <i :class="getIcon(toast.type)" class="me-2"></i>
                    {{ toast.message }}
                </div>
                <button
                    type="button"
                    class="btn-close btn-close-white me-2 m-auto"
                    data-bs-dismiss="toast"
                    aria-label="Close"
                ></button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { Toast } from 'bootstrap';
import { onMounted, ref } from 'vue';
import { emitter } from '@/composables/eventBus';

const toasts = ref([]);
const toastRefs = ref({});

function setToastRef(el, id) {
    if (el) {
        toastRefs.value[id] = el;
    }
}

function getToastClass(type) {
    const classes = {
        success: 'bg-success',
        error: 'bg-danger',
        info: 'bg-info',
        warning: 'bg-warning',
    };
    return classes[type] || 'bg-secondary';
}

function getIcon(type) {
    const icons = {
        success: 'feather feather-check-circle',
        error: 'feather feather-x-circle',
        info: 'feather feather-info',
        warning: 'feather feather-alert-triangle',
    };
    return icons[type] || 'feather feather-bell';
}

function showToast(type, message) {
    const id = Date.now() + Math.random();
    toasts.value.push({ id, type, message });

    // Wait for next tick to ensure DOM is updated
    setTimeout(() => {
        const toastEl = toastRefs.value[id];
        if (toastEl) {
            const bsToast = new Toast(toastEl, {
                autohide: true,
                delay: 4000,
            });

            toastEl.addEventListener('hidden.bs.toast', () => {
                // Remove from array after animation
                toasts.value = toasts.value.filter((t) => t.id !== id);
                delete toastRefs.value[id];
            });

            bsToast.show();
        }
    }, 50);
}

onMounted(() => {
    emitter.on('toast:show', ({ type, message }) => {
        showToast(type, message);
    });
});
</script>

<style scoped>
.toast {
    min-width: 300px;
}
</style>
