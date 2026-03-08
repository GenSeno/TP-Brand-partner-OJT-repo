<template>
    <Head title="Payment Acknowledgement" />

    <div class="page-header">
        <div class="add-item d-flex">
            <div class="page-title">
                <h4>Payment Acknowledgement</h4>
                <h6>Manage your received payments</h6>
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
                    placeholder="Search PA #, Billing #, Customer"
                    @search="submitFilters"
                />
            </div>
        </div>
        <div class="card-body p-0">
            <dt-table
                v-model:sortings="form.sort"
                v-model:perPage="form.per_page"
                :columns="columns"
                :data="props.payments.data"
                :total-records="props.payments.total"
                :start-record="props.payments.from"
                :end-record="props.payments.to"
                :links="props.payments.links"
                :selectable="false"
                @change="submitFilters"
            >
                <template #paid_at="{ value }">
                    {{ value ? dayjs(value).format(dateFormat) : '—' }}
                </template>
                <template #pa_no="{ row, value }">
                    <ModalLink
                        navigate
                        :href="
                            route(
                                'admin.billing.payment.edit',
                                { billing:row.invoice.id, 
                                  payment:row.id
                                },
                            )
                        "
                        class="btn btn-sm btn-light-ghost text-primary"
                    >
                         <!-- PA-{{ String(row.id).padStart(4, '0') }} -->
                        {{ row.internal_reference }} 
                    </ModalLink>
                </template>
                <template #customer="{ row }">
                    <div v-if="row.invoice?.order?.orderable">
                        <p class="mb-0">
                            {{
                                row.invoice.order.orderable.company_name ||
                                row.invoice.order.orderable.full_name ||
                                '—'
                            }}
                        </p>
                        <small
                            v-if="
                                row.invoice.order.orderable.company_name &&
                                row.invoice.order.orderable.full_name
                            "
                            class="text-muted"
                        >
                            {{ row.invoice.order.orderable.full_name }}
                        </small>
                    </div>
                    <span v-else class="text-muted">—</span>
                </template>
                <template #billing_date="{ row }">
                    {{
                        row.invoice?.invoiced_at
                            ? dayjs(row.invoice.invoiced_at).format(
                                  dateFormat,
                              )
                            : '—'
                    }}
                </template>
                <template #billing_no="{ row }">
                    <Link
                        v-if="row.invoice"
                        :href="
                            route('admin.billing.show', row.invoice.id)
                        "
                        class="link-primary fw-medium"
                    >
                        {{ row.invoice.reference }}
                    </Link>
                    <span v-else class="text-muted">—</span>
                </template>
                <template #billing_amount="{ row }">
                    {{ row.invoice?.amount_due?.formatted || '—' }}
                </template>
                <template #amount="{ value }">
                    {{ value?.formatted || '—' }}
                </template>
            </dt-table>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import dayjs from 'dayjs';
import { toggleHeader } from '@/helpers/layout';
import { removeEmptyValues } from '@/helpers/form';
import DashboardLayout from '@/layouts/dashboard-layout.vue';

defineOptions({
    layout: DashboardLayout,
});

const props = defineProps({
    payments: Object,
    filter: Object,
    stats: Object,
});

const columns = [
    {
        title: 'Date',
        dataIndex: 'paid_at',
        key: 'paid_at',
        sortable: true,
    },
    {
        title: 'Payment Acknowledgement No.',
        dataIndex: 'id',
        key: 'pa_no',
    },
    {
        title: 'Company / Customer Name',
        dataIndex: 'customer',
        key: 'customer',
    },
    {
        title: 'Billing Date',
        dataIndex: 'invoice.invoiced_at',
        key: 'billing_date',
    },
    {
        title: 'Billing No.',
        dataIndex: 'invoice.reference',
        key: 'billing_no',
    },
    {
        title: 'Billing Amount',
        dataIndex: 'invoice.amount_due',
        key: 'billing_amount',
        align: 'end',
    },
    {
        title: 'Amount Received',
        dataIndex: 'amount',
        key: 'amount',
        sortable: true,
        align: 'end',
    },
];

const dateFormat = ref('DD MMM YYYY');
const form = useForm({
    filter: {
        search: props.filter.search || '',
    },
    sort: ['-paid_at'],
    per_page: props.payments.per_page,
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
    ).get(route('admin.payment-acknowledgement.index'), {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    });
};

const refreshPage = () =>
    router.get(
        route('admin.payment-acknowledgement.index'),
        {},
        { preserveState: false, preserveScroll: true, replace: true },
    );

const cards = computed(() => [
     {
        key: 'overdue',
        label: 'Receipts Created (This Month)',
        amount: props.stats.acknowledgment,
        color: 'bg-success-gradient',
        icon: 'check-circle',
    },
    {
        key: 'sales',
        label: 'Payments (This Month)',
        amount: props.stats.payments_this_month.formatted,
        color: 'bg-primary',
        icon: 'trending-up',
    },
    {
        key: 'paid',
        label: 'Amount Due (This Month)',
        amount: props.stats.due_this_month.formatted,
        color: 'bg-danger-gradient',
        icon: 'calendar',
    },
    {
        key: 'unpaid',
        label: 'To Pay (This Month)',
        amount: props.stats.topay_this_month.formatted,
        color: 'bg-warning-gradient',
        icon: 'clock',
    },
   
]);
</script>
