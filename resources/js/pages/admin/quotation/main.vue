<style scoped>
.border-start-primary {
    border-left: 5px solid #fe9f43;
}
</style>

<template>
    <Head title="Edit Quotation" />

    <div class="row">
        <div class="content">
            <div class="d-flex page-header">
                <!-- COMPANY INFO -->
                <div class="col-md-6 page-title">
                    <h4>Quotation No. {{ quotation.reference }}</h4>
                    <h6>Manage Your Quotation</h6>
                </div>
                <div class="col-md-6 text-end">
                    <h6 class="text-end text-uppercase">
                        Status:
                        <span :class="statusText(quotation.status)"
                            ><span
                                v-if="
                                    quotation.status == 'request' ||
                                    quotation.status == 'draft'
                                "
                                >{{ quotation.status }} / </span
                            >{{ statusLabel(quotation.status) }}</span
                        >
                    </h6>
                </div>
            </div>
            <div class="row">
                <!-- LEFT LIST -->
                <div class="col-lg-3">
                    <Sidebar
                        :quotation="quotation"
                        :template="template"
                        ref="sidebar"
                    />
                </div>

                <!-- MAIN CONTENT -->
                <div class="col-lg-9 col-md-12 col-sm-12">
                    <Menu
                        :quotation="quotation"
                        :lines="lines"
                        :template="template"
                        @statusUpdated="handleStatusUpdated"
                    />

                    <!-- PREVIEW AREA -->
                    <div class="card">
                        <div class="card-body">
                            <PreviewTemplate
                                :quotation="quotation"
                                :lines="lines"
                                v-if="template === 'template'"
                            />
                            <Products
                                v-if="template === 'item'"
                                :quotation="quotation"
                                :lines="lines"
                            />
                        </div>
                    </div>

                    <div>
                        <NotesHistory
                            :post-url="
                                route('admin.quotation.note', quotation.id)
                            "
                            :activities="activities"
                        />
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { Head, router } from '@inertiajs/vue3';
import DashboardLayout from '@/layouts/dashboard-layout.vue';
import { statusText, statusLabel } from '@/helpers/status';
import PreviewTemplate from './template/preview.vue';
import Products from './template/item.vue';
import Sidebar from './template/sidebar.vue';
import Menu from './template/menu.vue';
import { onMounted, ref } from 'vue';
import NotesHistory from '@/components/notes-history.vue';
import { emitter } from '@/composables/eventBus';

defineOptions({
    layout: DashboardLayout,
});

const props = defineProps({
    quotation: Object,
    lines: Object,
    template: String,
    errors: Object,
    auth: Object,
    flash: Object,
    counts: Object,
    activities: Array,
});

const sidebar = ref(null);

const handleStatusUpdated = () => {
    if (sidebar.value) sidebar.value.fetchQuotations(); // refresh sidebar
};

onMounted(() => {
    emitter.on('note:created', () => {
        router.reload({
            only: ['activities'],
        });
    });
});
</script>
