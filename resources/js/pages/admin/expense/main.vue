<template>
    <Head title="Edit Voucher" />

    <div class="row">
        <div class="content">
            <div class="d-flex page-header">
                <!-- COMPANY INFO -->
                <div class="col-md-6 page-title">
                    <h4>Payment Voucher No. {{ expense?.reference }}</h4>
                    <h6>Manage Your Voucher</h6>
                </div>
                <div class="col-md-6 text-end">
                    <h6 class="text-end text-uppercase">
                        Status:
                        <span :class="statusText(expense.status)">{{
                            expense.status
                        }}</span>
                    </h6>
                </div>
            </div>
            <div class="row">
                <!-- LEFT LIST -->
                <div class="col-lg-3">
                    <Sidebar
                        :expense="expense"
                        :template="template"
                        ref="sidebar"
                    />
                </div>

                <!-- MAIN CONTENT -->
                <div class="col-lg-9 col-md-12 col-sm-12">
                    <Menu :expense="expense" :template="template" />
                    <!-- PREVIEW AREA -->
                    <div class="card">
                        <div class="card-body">
                            <PreviewTemplate
                                :expense="expense"
                                v-if="template === 'template'"
                            />
                            <Item
                                :expense="expense"
                                :expense_accounts="expense_accounts"
                                v-if="template === 'item'"
                            />
                        </div>
                    </div>

                    <div>
                        <NotesHistory
                            :post-url="route('admin.expense.note', expense.id)"
                            :activities="activities"
                        />
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- <iframe
    id="pdf-frame"
    :src="route('pdf', { quotation: quotation.id })"
    style="display:none;"
    ></iframe> -->
</template>
<style scoped>
.border-start-primary {
    border-left: 5px solid #fe9f43;
}
</style>
<script setup>
import { Head, router } from '@inertiajs/vue3';
import DashboardLayout from '@/layouts/dashboard-layout.vue';
import { statusText } from '@/helpers/status';
import PreviewTemplate from './template/preview.vue';
import Item from './template/item.vue';
import Sidebar from './template/sidebar.vue';
import Menu from './template/menu.vue';
import { ref, onMounted, onUnmounted } from 'vue';
import { emitter } from '@/composables/eventBus';
import NotesHistory from '@/components/notes-history.vue';

defineOptions({
    layout: DashboardLayout,
});

const props = defineProps({
    expense: Object,
    expense_accounts: Object,
    template: String,
    errors: Object,
    auth: Object,
    flash: Object,
    counts: Object,
    activities: Array,
});

const expense = ref(props.expense);

onMounted(() => {
    emitter.on('expense:updated', (updatedExpense) => {
        expense.value = updatedExpense;
    });

    emitter.on('note:created', () => {
        router.reload({
            only: ['activities'],
        });
    });
});

onUnmounted(() => {
    emitter.off('expense:updated');
});
</script>
