<template>
    <MainLayout>
        <LayoutHeader></LayoutHeader>
        <LayoutSidebar></LayoutSidebar>
        <div class="page-wrapper">
            <div class="content">
                <slot />
            </div>
        </div>
    </MainLayout>
</template>

<script setup>
import MainLayout from '@/layouts/main-layout.vue';
import LayoutHeader from '@/layouts/layouts-header.vue';
import LayoutSidebar from '@/layouts/layouts-sidebar.vue';
import { useToast } from 'bootstrap-vue-next';
import { usePage } from '@inertiajs/vue3';
import * as alert from '@/helpers/alert';
import { onMounted, watch } from 'vue';
import { emitter } from '@/composables/eventBus';

const page = usePage();
const toast = useToast();

watch(
    () => page.props.flash,
    (flash) => {
        if (flash.success) {
            alert.showSuccess(flash.success);
        }
        if (flash.error) {
            alert.showError(flash.error);
        }
        if (flash.info) {
            alert.showInfo(flash.info);
        }
        if (flash.warning) {
            alert.showWarning(flash.warning);
        }
    },
);

onMounted(() => {
    emitter.on('toast:show', ({ type, message }) => {
        toast.create({
            variant: type,
            solid: true,
            modelValue: 4000,
            appendToast: true,
            body: message,
            progressProps: {
                variant: type,
            },
            isStatus: true,
        });
    });
});
</script>
