<template>
    <MainLayout>
        <BrandPartnerHeader></BrandPartnerHeader>
        <BrandPartnerSidebar></BrandPartnerSidebar>
        <div class="page-wrapper">
            <div class="content">
                <slot />
            </div>
        </div>
    </MainLayout>
</template>

<script setup>
import MainLayout from '@/layouts/main-layout.vue';
import BrandPartnerHeader from '@/layouts/brand-partner-header.vue';
import BrandPartnerSidebar from '@/layouts/brand-partner-sidebar.vue';
import { usePage } from '@inertiajs/vue3';
import * as alert from '@/helpers/alert';
import { watch } from 'vue';

const page = usePage();

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
</script>
