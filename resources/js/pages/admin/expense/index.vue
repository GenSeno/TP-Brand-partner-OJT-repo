<template>
    <Head title="Expense" />

    <div class="page-header">
        <div class="add-item d-flex">
            <div class="page-title">
                <h4>Expenses</h4>
                <h6>Manage your Payment Voucher</h6>
            </div>
        </div>
        <ul class="table-top-head">
            <li>
                <a data-bs-toggle="tooltip" data-bs-placement="top" title="Pdf">
                    <img src="/img/icons/pdf.svg" alt="img" />
                </a>
            </li>
            <li>
                <a
                    data-bs-toggle="tooltip"
                    data-bs-placement="top"
                    title="Excel"
                >
                    <img src="/img/icons/excel.svg" alt="img" />
                </a>
            </li>
            <li>
                <a
                    data-bs-toggle="tooltip"
                    data-bs-placement="top"
                    title="Refresh"
                    @click="refreshPage"
                >
                    <i class="ti ti-refresh"></i>
                </a>
            </li>
            <li>
                <a
                    data-bs-toggle="tooltip"
                    data-bs-placement="top"
                    title="Collapse"
                    id="collapse-header"
                    @click="toggleHeader"
                >
                    <i class="ti ti-chevron-up"></i>
                </a>
            </li>
        </ul>
        <div class="page-btn d-flex flex-wrap gap-2">
            <Link
                :href="route('admin.expense_account.index')"
                class="btn btn-added btn-dark"
            >
                <vue-feather type="settings" class="me-2"></vue-feather>
                Expense Accounts
            </Link>
            <ModalLink
                navigate
                :href="route('admin.expense.create')"
                class="btn btn-added"
                #default="{ loading }"
            >
                <loading-text :loading="loading">
                    <vue-feather type="plus-circle" class="me-2"></vue-feather>
                    Add New Voucher
                </loading-text>
            </ModalLink>
        </div>
    </div>
    <div class="row">
        <div class="col-xl-3 col-sm-6 col-12 d-flex">
            <div class="dash-count bg-success">
                <div class="dash-counts">
                    <h4 class="mb-1">
                        {{ formatter.format(counts.paid / 100) }}
                    </h4>
                    <p class="text-white mb-0">Total Paid For this Month</p>
                </div>
                <div class="dash-imgs">
                    <i data-feather="trash-2" class="feather-check-circle"></i>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6 col-12 d-flex">
            <div class="dash-count bg-primary">
                <div class="dash-counts">
                    <h4 class="mb-1">
                        {{ formatter.format(counts.upcoming / 100) }}
                    </h4>
                    <p class="text-white mb-0">Total Upcoming Payment</p>
                </div>
                <div class="dash-imgs">
                    <i data-feather="trash-2" class="feather-clock"></i>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6 col-12 d-flex">
            <div class="dash-count bg-cyan">
                <div class="dash-counts">
                    <h4 class="mb-1">
                        {{ formatter.format(counts.expenses / 100) }}
                    </h4>
                    <p class="text-white mb-0">Total Expenses For This Month</p>
                </div>
                <div class="dash-imgs">
                    <i data-feather="trash-2" class="feather-dollar-sign"></i>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6 col-12 d-flex">
            <div class="dash-count bg-dark">
                <div class="dash-counts">
                    <h4 class="mb-1">{{ counts.total }}</h4>
                    <p class="text-white mb-0">Total Voucher</p>
                </div>
                <div class="dash-imgs">
                    <i data-feather="trash-2" class="feather-file-text"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="card table-list-card">
        <div
            class="card-header d-flex align-items-center justify-content-between flex-wrap row-gap-3"
        >
            <div class="d-flex align-items-center gap-2">
                <dt-search
                    v-model="form.filter.search"
                    @search="submitFilters"
                />
            </div>
            <div
                class="d-flex table-dropdown my-xl-auto right-content align-items-center flex-wrap row-gap-3"
            >
                <select-filter
                    v-model="form.filter.status"
                    :options="expenseStatus"
                    name="Status"
                    @change="submitFilters"
                ></select-filter>
            </div>
        </div>

        <div class="card-body p-0">
            <dt-table
                v-model:sortings="form.sort"
                v-model:perPage="form.per_page"
                v-model:selected="selected"
                :columns="columns"
                :data="expenseTableData"
                :total-records="expense.total"
                :start-record="expense.from"
                :end-record="expense.to"
                :links="expense.links"
                :selectable="false"
                @change="submitFilters"
            >
                <template #supplier="{ row, value }">
                    <div class="d-flex align-items-center">
                        <div class="avatar avatar-md bg-light-900 p-1 me-2">
                            <img
                                class="object-fit-contain rounded-circle"
                                :src="row.avatar_url"
                                @error="setDefaultAvatar($event)"
                                alt="Avatar"
                            />
                        </div>
                        <span>{{ value }}</span>
                    </div>
                </template>
                <template #expense_date="{ value }">
                    {{ dayjs(value).format(dateFormat) }}
                </template>
                <template #payment_date="{ value }">
                    {{
                        value && value !== '-'
                            ? dayjs(value).format(dateFormat)
                            : '-'
                    }}
                </template>
                <template #reference="{ row, value }">
                    <a href="#" @click="goToShow(row)" class="text-primary">{{
                        value
                    }}</a>
                </template>
                <template #status="{ row, value }">
                    <span class="badge" :class="statusBg(row.status)">
                        {{ row.status }}
                    </span>
                </template>
            </dt-table>
        </div>
    </div>
</template>
<script setup>
import { ref, computed } from 'vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { removeEmptyValues } from '@/helpers/form';
import { toggleHeader } from '@/helpers/layout';
import DashboardLayout from '@/layouts/dashboard-layout.vue';
import dayjs from 'dayjs';
import { statusBg, statusLabel } from '@/helpers/status';

defineOptions({
    layout: DashboardLayout,
});

const props = defineProps({
    expense: Object,
    filter: Object,
    default_per_page: {
        type: Number,
        default: 10,
    },
    errors: Object,
    auth: Object,
    flash: Object,
    counts: Object,
});
// Ensure every customer has a valid avatar URL
const defaultAvatar = '/img/default.png';
const expenseTableData = computed(() =>
    props.expense.data.map((c) => {
        const supplier = c.supplier;
        return {
            ...c,
            avatar_url: c.preview_url || defaultAvatar,
            supplier: supplier.name,
            total_amount: c.total_amount.formatted,
            payment_date: c.payment_date || '-',
            payment_mode: c.payment_mode || '-',
            status: c.status || '',
            // phone: defaultAddress.phone || '',
            // province: defaultAddress.province || '',
            // is_deletable: c.is_deletable,
            // 'data-is-deletable': c.is_deletable //
        };
    }),
);

const columns = [
    {
        title: 'Date',
        dataIndex: 'expense_date',
        key: 'expense_date',
        sortable: true,
    },
    {
        title: 'Voucher',
        dataIndex: 'reference',
        key: 'reference',
        sortable: true,
    },
    {
        title: 'Supplier',
        dataIndex: 'supplier',
        key: 'supplier',
        sortable: true,
    },
    {
        title: 'Payment Date',
        dataIndex: 'payment_date',
        key: 'payment_date',
        sortable: true,
    },
    {
        title: 'Payment Mode',
        dataIndex: 'payment_mode',
        key: 'payment_mode',
        sortable: true,
    },
    { title: 'Amount', dataIndex: 'total_amount', key: 'total_amount' },
    { title: 'Status', dataIndex: 'status', key: 'status' },
];

const dateFormat = ref('DD MMM YYYY');
const expenseStatus = ref({
    draft: 'Draft',
    upcoming: 'Upcoming',
    paid: 'Paid',
    cancelled: 'Cancelled',
});

const form = useForm({
    filter: {
        search: props.filter.search || '',
        status: props.filter.status || '',
    },
    sort: [],
    per_page: props.expense.per_page,
});
const selected = ref([]);

const submitFilters = () => {
    form.transform((data) =>
        removeEmptyValues({
            ...data,
            sort: data.sort.join(','),
            per_page:
                data.per_page === props.default_per_page ? '' : data.per_page,
        }),
    ).get(route('admin.expense.index'), { preserveState: true, replace: true });
};

const goToShow = (row) => {
    router.get(route('admin.expense.show', { expense: row.id }));
};

const formatter = new Intl.NumberFormat('en-US', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2,
});


</script>
