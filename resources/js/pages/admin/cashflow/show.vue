<template>
    <Head title="Cash Flow" />

    <div class="page-header">
        <div class="add-item d-flex">
            <div class="page-title">
                <h4>{{ group.charAt(0).toUpperCase() + group.slice(1) }}</h4>
                <h6>
                    View transactions for
                    <span>{{
                        group.charAt(0).toUpperCase() + group.slice(1)
                    }}</span>
                </h6>
            </div>
        </div>

        <ul class="table-top-head">
            <li>
                <a data-bs-toggle="tooltip" title="Pdf">
                    <img src="/img/icons/pdf.svg" alt="img" />
                </a>
            </li>
            <li>
                <a data-bs-toggle="tooltip" title="Excel">
                    <img src="/img/icons/excel.svg" alt="img" />
                </a>
            </li>
            <li>
                <a
                    data-bs-toggle="tooltip"
                    title="Refresh"
                    @click="refreshTable"
                >
                    <i class="ti ti-refresh"></i>
                </a>
            </li>
            <li>
                <a
                    data-bs-toggle="tooltip"
                    title="Collapse"
                    @click="toggleHeader"
                >
                    <i class="ti ti-chevron-up"></i>
                </a>
            </li>
        </ul>

        <div class="page-btn d-flex flex-wrap gap-2">
            <!-- Back -->
            <Link
                :href="route('admin.cashflow.index')"
                class="btn btn-added btn-dark"
                view-transition
            >
                <i data-feather="arrow-left" class="feather-arrow-left"></i>
                Back
            </Link>

            <!-- Adjustment -->
            <ModalLink
                navigate
                :href="route('admin.cashflow.adjustment.modal', { group })"
                class="btn btn-added btn-primary uppercase"
            >
                <i data-feather="plus" class="feather-plus"></i>
                Adjustment
            </ModalLink>
        </div>
    </div>

    <!-- Card: Balance + Search -->
    <div class="card table-list-card">
        <div
            class="card-header d-flex align-items-center justify-content-between flex-wrap row-gap-3"
        >
            <!-- Balance -->
            <div
                class="d-flex flex-column mt-2 mt-md-0"
                style="min-width: 150px"
            >
                <span class="fw-bold text-start">Balance:</span>
                <span
                    class="text-white bg-secondary px-3 py-1 rounded shadow-sm mt-1 d-block"
                    style="text-align: right"
                >
                    {{ formatter.format(props.balance) }}
                </span>
            </div>

            <!-- Search -->
            <div class="ms-auto">
                <dt-search
                    v-model="form.filter.search"
                    @search="submitFilters"
                    placeholder="Search transactions..."
                />
            </div>
        </div>

        <!-- Table -->
        <div class="card-body p-0">
            <dt-table
                v-model:sortings="form.sort"
                v-model:perPage="form.per_page"
                v-model:selected="selected"
                :columns="columns"
                :data="tableData"
                :total-records="entries.total"
                :start-record="entries.from"
                :end-record="entries.to"
                :links="entries.links"
                :selectable="false"
                @change="submitFilters"
            >
                <template #date="{ value }">{{
                    dayjs(value).format(dateFormat)
                }}</template>

                <template #cash_out="{ value }" class="text-end">
                    {{ value && value > 0 ? formatter.format(value) : '-' }}
                </template>

                <template #cash_in="{ value }" class="text-end">
                    {{ value && value > 0 ? formatter.format(value) : '-' }}
                </template>

                <!-- <template #running_balance="{ value }" class="text-end">{{ formatter.format(value) }}</template> -->
                <template #posted_at="{ row }">
                    <span v-if="row.type !== 'adjustment' && row.posted_at">
                        {{ dayjs(row.posted_at).format(dateFormat) }}
                    </span>
                    <span
                        v-else-if="row.type === 'adjustment'"
                        class="fst-italic fst-capitalize"
                    >
                        {{
                            row.type.charAt(0).toUpperCase() + row.type.slice(1)
                        }}
                    </span>
                    <span v-else>-</span>
                </template>
                
                <template #action="{ row, value }">
                    <div class="d-flex align-items-center gap-2">
                        <!-- Adjustments → Delete button -->
                        <template v-if="row.type === 'adjustment'">
                            <dt-delete2
                                :record-name="'adjustment'"
                                model-name="adjustment"
                                :url="
                                    route(
                                        'admin.cashflow.destroy.adjustment',
                                        row.id,
                                    )
                                "
                                :class="'btn btn-sm btn-outline-danger uppercase'"
                                :emitter-event="deleteEmitterEvent"
                            >
                                DELETE
                            </dt-delete2>
                        </template>

                        <!-- Payments / Expenses → Post / Unpost -->
                        <template v-else>
                            <ModalLink
                                v-if="!row.posted_at && row.type === 'payment'"
                                navigate
                                :href="
                                    route(
                                        'admin.cashflow.post.payment.modal',
                                        row.id,
                                    )
                                "
                                class="btn btn-sm btn-outline-primary uppercase"
                            >
                                POST PAYMENT
                            </ModalLink>

                            <ModalLink
                                v-else-if="
                                    !row.posted_at && row.type === 'expense'
                                "
                                navigate
                                :href="
                                    route(
                                        'admin.cashflow.post.expense.modal',
                                        row.id,
                                    )
                                "
                                class="btn btn-sm btn-outline-primary uppercase"
                            >
                                POST EXPENSE
                            </ModalLink>

                            <div v-else class="d-flex flex-column gap-1">
                                <button
                                    class="btn btn-sm btn-primary uppercase"
                                    :disabled="row.processing"
                                    @click="unpostTransaction(row)"
                                >
                                    POSTED
                                </button>
                            </div>
                        </template>
                    </div>
                </template>
            </dt-table>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { Head, router, useForm, Link } from '@inertiajs/vue3';
import dayjs from 'dayjs';
import DashboardLayout from '@/layouts/dashboard-layout.vue';
import { removeEmptyValues } from '@/helpers/form';
import * as alert from '@/helpers/alert';
import { emitter } from '@/composables/eventBus';
import axios from 'axios';

defineOptions({ layout: DashboardLayout });

const props = defineProps({
    group: String,
    entries: Object,
    filter: Object,
    balance: Number,
    default_per_page: { type: Number, default: 10 },
});
const deleteEmitterEvent = ref('adjustment-deleted');
// Compute table data + running balance (posted only)
const tableData = computed(() => {
    let cumulative = 0;
    return props.entries.data.map((row) => {
        // Include adjustments even if posted_at is null
        if (row.posted_at || row.type === 'adjustment') {
            cumulative += (row.cash_in || 0) - (row.cash_out || 0);
        }

        return {
            ...row,
            cash_in: row.cash_in,
            cash_out: row.cash_out,
            action: row.type,
            posted_by: row.posted_by,
            processing: false,
            running_balance: cumulative,
        };
    });
});

// Columns
const columns = [
    { title: 'Date', dataIndex: 'date', key: 'date', sortable: true },
    {
        title: 'Transaction Ref',
        dataIndex: 'reference',
        key: 'reference',
        sortable: true,
    },
    { title: 'Cash Out', dataIndex: 'cash_out', key: 'cash_out' },
    { title: 'Cash In', dataIndex: 'cash_in', key: 'cash_in' },
    // { title: 'Running Balance', dataIndex: 'running_balance', key: 'running_balance' },
    { title: 'Action', dataIndex: 'action', key: 'action' },
    {
        title: 'Date Posted',
        dataIndex: 'posted_at',
        key: 'posted_at',
        sortable: true,
    },
];

const dateFormat = ref('DD MMM YYYY');
const selected = ref([]);

const form = useForm({
    filter: { search: props.filter?.search || '' },
    sort: [],
    per_page: props.entries.per_page || 10,
});

const formatter = new Intl.NumberFormat('en-US', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2,
});

// Filters
const submitFilters = () => {
    form.transform((data) =>
        removeEmptyValues({
            ...data,
            sort: data.sort.join(','),
            per_page:
                data.per_page === props.default_per_page ? '' : data.per_page,
        }),
    ).get(route('admin.cashflow.show', { group: props.group }), {
        preserveState: true,
        replace: true,
    });
};

// Refresh table manually
const refreshTable = () => {
    router.get(
        route('admin.cashflow.show', { group: props.group }),
        {},
        { preserveState: true },
    );
};

// UNPOST transaction
const unpostTransaction = async (row) => {
    try {
        row.processing = true;

        const routeName =
            row.type === 'payment'
                ? 'admin.cashflow.unpost.payment'
                : 'admin.cashflow.unpost.expense';

        await axios.post(route(routeName, row.id));

        // Mark as unposted
        row.posted_at = null;

        alert.showSuccess('Transaction unposted successfully');

        // Emit event to refresh table & recalc running balance
        emitter.emit('cashflow:updated');
    } catch (error) {
        alert.showError(
            error.response?.data?.message || 'Failed to unpost transaction',
        );
    } finally {
        row.processing = false;
    }
};

onMounted(() => {
    emitter.on(deleteEmitterEvent.value, (data) => {
        router.reload({
            preserveState: true,
            preserveScroll: true,
            replace: true,
        });
        alert.showSuccess(data.message || 'Adjustment deleted successfully.');
        emitter.emit('cashflow:updated');
    });
});

// Listen for global table refresh
emitter.on('cashflow:updated', refreshTable);
</script>
