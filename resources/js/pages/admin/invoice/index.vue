<template>
    <Head title="Billing Statements" />

    <div class="page-header">
        <div class="add-item d-flex">
            <div class="page-title">
                <h4>Billing Statements</h4>
                <h6>Manage your billing statement</h6>
            </div>
        </div>
        <ul class="table-top-head">
            <li>
                <a
                    data-bs-toggle="tooltip"
                    data-bs-placement="top"
                    title="Refresh"
                    @click="refreshPage"
                    ><i class="ti ti-refresh"></i
                ></a>
            </li>
            <li>
                <a
                    data-bs-toggle="tooltip"
                    data-bs-placement="top"
                    title="Collapse"
                    id="collapse-header"
                    @click="toggleHeader"
                    ><i class="ti ti-chevron-up"></i
                ></a>
            </li>
        </ul>
    </div>

    <div class="row">
        <div
            v-for="card in cards"
            :key="card.key"
            class="col-xl-3 col-sm-6 col-12 d-flex"
        >
            <div class="dash-count" :class="`${card.color}`">
                <div class="dash-counts">
                    <h4>{{ card.amount }}</h4>
                    <p class="mb-0">{{ card.label }}</p>
                </div>
                <div class="dash-imgs">
                    <vue-feather :type="card.icon"></vue-feather>
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
                    placeholder="Search Billing #, SO #, Customer"
                    @search="submitFilters"
                />
            </div>
            <div
                class="d-flex table-dropdown my-xl-auto right-content align-items-center flex-wrap row-gap-3"
            >
                <select-filter
                    v-model="form.filter.status"
                    :options="statusOptions"
                    name="Status"
                    @change="submitFilters"
                />
            </div>
        </div>
        <div class="card-body p-0">
            <dt-table
                v-model:sortings="form.sort"
                v-model:perPage="form.per_page"
                :columns="columns"
                :data="props.invoices.data"
                :total-records="props.invoices.total"
                :start-record="props.invoices.from"
                :end-record="props.invoices.to"
                :links="props.invoices.links"
                :selectable="false"
                @change="submitFilters"
            >
                <template #reference="{ row, value }">
                    <Link
                        :href="route('admin.billing.show', row.id)"
                        class="link-primary fw-medium"
                    >
                        {{ value }}
                    </Link>
                </template>
                <template #sales_order="{ value }">
                    <span v-if="value">{{ value }}</span>
                    <span v-else class="text-muted">—</span>
                </template>
                <template #customer="{ row }">
                    <div class="d-flex align-items-center">
                        <a
                            v-if="_customer(row)"
                            href="javascript:void(0);"
                            class="avatar avatar-md bg-light-900 p-1 me-2"
                        >
                            <img
                                class="object-fit-contain"
                                :src="_customer(row)?.avatar_url"
                                alt="img"
                            />
                        </a>
                        <ModalLink
                            navigate
                            :href="
                                route(
                                    'admin.billing.customer',
                                    _customer(row)?.id,
                                )
                            "
                            class="link-primary"
                            title="Customer"
                        >
                            <p class="mb-0">
                                {{ _customer(row)?.full_name || '—' }}
                            </p>
                            <small
                                v-if="_customer(row)?.company_name"
                                class="text-muted"
                            >
                                {{ _customer(row)?.company_name }}
                            </small>
                        </ModalLink>
                    </div>
                </template>
                <template #invoiced_at="{ value }">
                    {{ value ? dayjs(value).format(dateFormat) : '—' }}
                </template>
                <template #due_at="{ value }">
                    {{ value ? dayjs(value).format(dateFormat) : '—' }}
                </template>
                <template #aging="{ value, row }">
                    <span
                        v-if="value && row.status !== 'paid'"
                        :class="agingClass(value)"
                    >
                        {{ agingDays(value) }}
                    </span>
                    <span v-else class="text-muted">—</span>
                </template>
                <template #total="{ value }">
                    {{ value.formatted }}
                </template>
                <template #amount_due="{ value }">
                    {{ value.formatted }}
                </template>
                <template #status="{ value }">
                    <InvoiceStatus :status="value" />
                </template>
                <template #job_order_no="{ value }">
                    <span v-if="value">{{ value }}</span>
                    <span v-else class="text-muted">—</span>
                </template>
                <template #job_order_status="{ value, row }">
                    <JobOrderState
                        v-if="value"
                        :state="value"
                        :label="row.job_order.current_general_state"
                    />
                    <span v-else class="text-muted">—</span>
                </template>
            </dt-table>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import dayjs from 'dayjs';
import InvoiceStatus from '@/components/invoice/invoice-status.vue';
import JobOrderState from '@/components/job-order-state.vue';
import { toggleHeader } from '@/helpers/layout';
import { removeEmptyValues } from '@/helpers/form';
import DashboardLayout from '@/layouts/dashboard-layout.vue';

defineOptions({
    layout: DashboardLayout,
});

const props = defineProps({
    invoices: Object,
    stats: Object,
    statusOptions: Object,
    filter: Object,
});

const columns = [
    {
        title: 'Date',
        dataIndex: 'invoiced_at',
        key: 'invoiced_at',
        sortable: true,
    },
    {
        title: 'Billing No.',
        dataIndex: 'reference',
        key: 'reference',
        sortable: true,
    },
    {
        title: 'SO No.',
        dataIndex: 'order.reference',
        key: 'sales_order',
    },
    {
        title: 'Customer',
        dataIndex: 'customer',
        key: 'customer',
    },
    {
        title: 'Due Date',
        dataIndex: 'due_at',
        key: 'due_at',
        sortable: true,
    },
    {
        title: 'Amount Due',
        dataIndex: 'amount_due',
        key: 'amount_due',
        sortable: true,
        align: 'end',
    },
    {
        title: 'Bal. Amount',
        dataIndex: 'summary.amount_balance.formatted',
        key: 'summary.amount_balance.formatted',
        sortable: true,
        align: 'end',
    },
    {
        title: 'Aging',
        dataIndex: 'due_at',
        key: 'aging',
    },
    {
        title: 'Status',
        dataIndex: 'status',
        key: 'status',
    },
    {
        title: 'JO No.',
        dataIndex: 'job_order.reference',
        key: 'job_order_no',
    },
    {
        title: 'JO Status',
        dataIndex: 'job_order.current_state',
        key: 'job_order_status',
    },
];

const dateFormat = ref('DD MMM YYYY');
const form = useForm({
    filter: {
        search: props.filter.search || '',
        status: props.filter.status || '',
    },
    sort: ['-reference'],
    per_page: props.invoices.per_page,
});

const submitFilters = () => {
    form.transform((data) =>
        removeEmptyValues({
            ...data,
            sort: data.sort.join(','),
            per_page:
                data.per_page === props.filter.default_per_page
                    ? ''
                    : data.per_page,
        }),
    ).get(route('admin.billing.index'), {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    });
};

const refreshPage = () =>
    router.get(
        route('admin.billing.index'),
        {},
        { preserveState: false, preserveScroll: true, replace: true },
    );

const cards = computed(() => [
    {
        key: 'sales',
        label: 'Sales (This Month)',
        amount: props.stats.sales_this_month.formatted,
        color: 'bg-primary',
        icon: 'trending-up',
    },
    {
        key: 'paid',
        label: 'Paid (This Month)',
        amount: props.stats.paid_this_month.formatted,
        color: 'bg-success-gradient',
        icon: 'check-circle',
    },
    {
        key: 'unpaid',
        label: 'Unpaid / Partially Paid',
        amount: props.stats.unpaid_partially_paid.formatted,
        color: 'bg-warning-gradient',
        icon: 'clock',
    },
    {
        key: 'overdue',
        label: 'Overdue',
        amount: props.stats.overdue.formatted,
        color: 'bg-danger-gradient',
        icon: 'alert-circle',
    },
]);

const agingDays = (dueAt) => {
    const days = dayjs()
        .startOf('day')
        .diff(dayjs(dueAt).startOf('day'), 'day');
    if (days > 0) return `${days} day${days > 1 ? 's' : ''} overdue`;
    if (days === 0) return 'Due today';
    return `${Math.abs(days)} day${Math.abs(days) > 1 ? 's' : ''} left`;
};

const agingClass = (dueAt) => {
    const days = dayjs()
        .startOf('day')
        .diff(dayjs(dueAt).startOf('day'), 'day');
    if (days > 0) return 'text-danger fw-medium';
    if (days === 0) return 'text-warning fw-medium';
    return 'text-success';
};

const _customer = (invoice) => {
    return invoice.order?.orderable;
};
</script>
