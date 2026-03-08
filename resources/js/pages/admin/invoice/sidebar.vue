<template>
    <div class="card">
        <div class="card-body">
            <!-- Dropdown filter -->
            <div class="relative inline-block w-56 mb-2">
                <div class="dropdown">
                    Billing List
                    <button
                        class="w-full border rounded bg-white float-end"
                        type="button"
                        data-bs-toggle="dropdown"
                        data-bs-auto-close="outside"
                        aria-expanded="false"
                    >
                        <i
                            data-feather="chevron-down"
                            class="feather-chevron-down mt-1"
                        ></i>
                    </button>

                    <ul class="dropdown-menu dropdown-menu-end form-control">
                        <li>
                            <label
                                v-for="status in statuses"
                                :key="status.value"
                                class="d-block py-1 cursor-pointer"
                            >
                                <input
                                    type="checkbox"
                                    :value="status.value"
                                    v-model="selected"
                                    @change="fetchInvoices"
                                />
                                {{ status.label }}
                            </label>
                        </li>
                        <hr />
                        <li>
                            <div class="d-flex justify-content-end gap-2 px-2">
                                <button
                                    type="button"
                                    class="btn btn-xs btn-primary"
                                    @click.stop="selectAllAndFetch"
                                >
                                    Select all
                                </button>
                                <button
                                    type="button"
                                    class="btn btn-xs btn-secondary"
                                    @click.stop="clearAllAndFetch"
                                >
                                    Clear
                                </button>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>

            <hr />

            <!-- Billing List -->
            <div class="row relative">
                <div class="col-12">
                    <!-- Loading / Empty states -->
                    <div v-if="loading" class="d-flex justify-content-center">
                        <i class="spinner-border spinner-border-sm me-1"></i
                        >Loading...
                    </div>
                    <div
                        v-else-if="invoices.length === 0"
                        class="alert bg-light"
                    >
                        No billing records found.
                    </div>

                    <!-- Billing Items -->
                    <div v-else class="list-group">
                        <div
                            v-for="item in paginatedInvoices"
                            :key="item.id"
                            class="list-group-item d-flex flex-column justify-content-center cursor-pointer"
                            :class="
                                item.id === invoice.id
                                    ? 'border-start-primary'
                                    : 'border-transparent'
                            "
                            @click="selectInvoice(item)"
                        >
                            <a href="#">
                                <div class="row">
                                    <label class="col-md-8 mb-0 fw-bold fs-12">
                                        {{
                                            item.order?.orderable?.full_name ||
                                            'N/A'
                                        }}
                                    </label>
                                    <label
                                        class="col-md-4 mb-0 fw-bold fs-10 text-end text-muted"
                                    >
                                        {{ item.amount_due?.formatted }}
                                    </label>
                                </div>
                                <div class="row mt-1">
                                    <div class="col-md-6">
                                        <small class="fs-10">{{
                                            item.reference
                                        }}</small>
                                    </div>
                                    <div class="col-md-6 text-end text-muted">
                                        <small class="fs-10">
                                            <i
                                                data-feather="calendar"
                                                class="feather-calendar mt-1"
                                            ></i>
                                            {{
                                                dayjs(item.created_at).format(
                                                    dateFormat,
                                                )
                                            }}
                                        </small>
                                    </div>
                                </div>
                                <div class="row mt-1">
                                    <div class="col-md-12">
                                        <small class="fs-10 text-capitalize">
                                            <InvoiceStatus
                                                :status="item.status"
                                            />
                                        </small>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>

                    <!-- Pagination Controls -->
                    <nav class="mt-2">
                        <ul class="pagination justify-content-center mb-0">
                            <li
                                class="page-item"
                                :class="{ disabled: currentPage === 1 }"
                            >
                                <a
                                    class="page-link btn btn-xs"
                                    href="#"
                                    @click.prevent="prevPage"
                                    >Prev</a
                                >
                            </li>
                            <li
                                class="page-item"
                                :class="{
                                    disabled: currentPage === totalPages,
                                }"
                            >
                                <a
                                    class="page-link btn btn-xs"
                                    href="#"
                                    @click.prevent="nextPage"
                                    >Next</a
                                >
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.border-start-primary {
    border-left: 5px solid #fe9f43;
}
</style>

<script setup>
import { ref, computed, onMounted, onBeforeUnmount, defineExpose } from 'vue';
import { router } from '@inertiajs/vue3';
import axios from 'axios';
import dayjs from 'dayjs';
import InvoiceStatus from '@/components/invoice/invoice-status.vue';
import { emitter } from '@/composables/eventBus';

const loading = ref(false);
const invoices = ref([]);
const selected = ref([
    'draft',
    'unpaid',
    'partially-paid',
    'paid',
    'overdue',
    'cancelled',
]);
const currentPage = ref(1);
const perPage = ref(20);

const props = defineProps({
    invoice: Object,
});
const dateFormat = ref('MMM. DD, YYYY');

const statuses = [
    { label: 'Draft', value: 'draft' },
    { label: 'Unpaid', value: 'unpaid' },
    { label: 'Partially Paid', value: 'partially-paid' },
    { label: 'Paid', value: 'paid' },
    { label: 'Overdue', value: 'overdue' },
    { label: 'Cancelled', value: 'cancelled' },
];

const selectAllAndFetch = () => {
    selected.value = statuses.map((s) => s.value);
    fetchInvoices();
};

const clearAllAndFetch = () => {
    selected.value = [];
    fetchInvoices();
};

// computed pagination
const totalPages = computed(() =>
    Math.ceil(invoices.value.length / perPage.value),
);
const paginatedInvoices = computed(() => {
    const start = (currentPage.value - 1) * perPage.value;
    return invoices.value.slice(start, start + perPage.value);
});

// fetch invoices
const fetchInvoices = async () => {
    loading.value = true;
    try {
        const response = await axios.get(
            route('admin.billing.status', { status: selected.value }),
        );
        invoices.value = response.data;
        currentPage.value = 1;
    } catch (error) {
        console.error('Error fetching invoices:', error);
    } finally {
        loading.value = false;
    }
};

// select invoice
const selectInvoice = (item) => {
    router.get(route('admin.billing.show', { billing: item.id }));
};

// pagination methods
const prevPage = () => {
    if (currentPage.value > 1) currentPage.value--;
};
const nextPage = () => {
    if (currentPage.value < totalPages.value) currentPage.value++;
};

/* Close dropdown when clicking outside */
const open = ref(false);
const handleClickOutside = (e) => {
    if (!e.target.closest('.relative')) open.value = false;
};

onMounted(() => {
    fetchInvoices();
    emitter.on('note:created', () => {
        fetchInvoices();
    });
    document.addEventListener('click', handleClickOutside);
});

onBeforeUnmount(() => {
    emitter.off('note:created');
    document.removeEventListener('click', handleClickOutside);
});

defineExpose({
    fetchInvoices,
});
</script>
