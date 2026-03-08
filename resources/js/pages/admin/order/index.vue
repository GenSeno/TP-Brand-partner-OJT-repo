<template>
    <Head title="Sales Orders" />

    <div class="page-header">
        <div class="add-item d-flex">
            <div class="page-title">
                <h4>Sales Orders</h4>
                <h6>Manage sales orders</h6>
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
        <div class="page-btn">
            <ModalLink
                navigate
                :href="route('admin.order.create')"
                class="btn btn-added btn-primary"
                #default="{ loading }"
            >
                <loading-text :loading="loading">
                    <vue-feather type="plus-circle" class="me-2"></vue-feather>
                    Add Sales Order
                </loading-text>
            </ModalLink>
        </div>
    </div>

    <div class="row">
        <div
            v-for="card in cards"
            :key="card.key"
            class="col-xl-3 col-sm-6 col-12 d-flex"
        >
            <div class="dash-count" :class="`${card.color}`">
                <div class="dash-counts">
                    <h4>{{ card.count }}</h4>
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
                    @search="submitFilters"
                />
            </div>
        </div>
        <div class="card-body p-0">
            <dt-table
                v-model:sortings="form.sort"
                v-model:perPage="form.per_page"
                v-model:selected="selected"
                :columns="columns"
                :data="props.orders.data"
                :total-records="props.orders.total"
                :start-record="props.orders.from"
                :end-record="props.orders.to"
                :links="props.orders.links"
                :selectable="false"
                @change="submitFilters"
            >
                <template #reference="{ row, value }">
                    <Link
                        :href="route('admin.order.show', row.id)"
                        class="link-primary"
                    >
                        {{ value }}
                    </Link>
                </template>
                <template #orderable="{ value }">
                    <div class="d-flex align-items-center">
                        <a
                            href="javascript:void(0);"
                            class="avatar avatar-md bg-light-900 p-1 me-2"
                        >
                            <img
                                class="object-fit-contain"
                                :src="value.avatar_url"
                                alt="img"
                            />
                        </a>
                        <div>
                            <p class="mb-0">{{ value.full_name }}</p>
                            <small
                                v-if="!!value.company_name"
                                class="text-muted"
                            >
                                {{ value.company_name }}
                            </small>
                        </div>
                    </div>
                </template>
                <template #lines_sum_quantity="{ value }">
                    {{ value || '-' }}
                </template>
                <template #created_at="{ value }">{{
                    dayjs(value).format(dateFormat)
                }}</template>
                <template #so_status="{ value }">
                    <span
                        v-if="value === null"
                        class="badge shadow-none badge-xs bg-warning"
                        >Pending</span
                    >
                    <ModalLink
                        v-else-if="can('job-orders')"
                        navigate
                        :href="route('admin.job-order.edit', value.id)"
                        class="link-primary"
                        title="View Job Order"
                    >
                        {{ value.reference }}
                    </ModalLink>
                    <span v-else class="text-muted">{{ value.reference }}</span>
                </template>
                <template #total="{ row }">
                    {{ row.total?.formatted }}
                </template>
                <template #billed_amount="{ row }">
                    {{ row.billing_summary?.amount_billed?.formatted }}
                </template>
                <template #balance_amount="{ row }">
                    {{ row.billing_summary?.amount_balance?.formatted }}
                </template>
                <template #payment_status="{ value }">
                    <PaymentStatus :status="value" />
                </template>
            </dt-table>
        </div>
    </div>
</template>

<script setup>
import { computed, ref } from 'vue';
import { Head, router, useForm, Link } from '@inertiajs/vue3';
import { removeEmptyValues } from '@/helpers/form';
import dayjs from 'dayjs';
import DashboardLayout from '@/layouts/dashboard-layout.vue';
import { toggleHeader } from '@/helpers/layout';
import PaymentStatus from '@/components/payment-status.vue';
import { can } from '@/helpers/guard';

defineOptions({ layout: DashboardLayout });

const props = defineProps({
    orders: Object,
    filter: Object,
    counts: Object,
});

const selected = ref([]);

const columns = [
    {
        title: 'Sales Order No.',
        dataIndex: 'reference',
        key: 'reference',
        sortable: true,
    },
    { title: 'Customer Name', dataIndex: 'orderable', key: 'orderable' },
    {
        title: 'Total Quantity',
        dataIndex: 'lines_sum_quantity',
        key: 'lines_sum_quantity',
    },
    { title: 'Total Amount', dataIndex: 'total', key: 'total' },
    {
        title: 'Billed Amount',
        dataIndex: 'billing_summary',
        key: 'billed_amount',
    },
    { title: 'Balance', dataIndex: 'billing_summary', key: 'balance_amount' },
    {
        title: 'Created Date',
        dataIndex: 'created_at',
        key: 'created_at',
        sortable: true,
    },
    { title: 'SO Status', dataIndex: 'job_order', key: 'so_status' },
    { title: 'Payment Status', dataIndex: 'status', key: 'payment_status' },
];

const dateFormat = 'DD MMM YYYY';

const form = useForm({
    filter: {
        search: props.filter?.search || '',
    },
    sort: [],
    per_page: props.orders.per_page,
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
    ).get(route('admin.order.index'), {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    });
};

const refreshPage = () =>
    router.get(
        route('admin.order.index'),
        {},
        { preserveState: false, preserveScroll: true, replace: true },
    );

const cards = computed(() => [
    {
        key: 'pending',
        label: 'Pending Payment',
        count: props.counts.pending || 0,
        color: 'bg-warning-gradient',
        icon: 'clock',
    },
    {
        key: 'unpaid',
        label: 'Unpaid',
        count: props.counts.unpaid || 0,
        color: 'bg-danger-gradient',
        icon: 'credit-card',
    },
    {
        key: 'partially_paid',
        label: 'Partially Paid',
        count: props.counts.partially_paid || 0,
        color: 'bg-info-gradient',
        icon: 'dollar-sign',
    },
    {
        key: 'completed',
        label: 'Completed',
        count: props.counts.completed || 0,
        color: 'bg-success-gradient',
        icon: 'check-circle',
    },
]);
</script>
