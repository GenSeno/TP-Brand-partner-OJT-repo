<template>
    <Head title="Orders" />

    <div class="page-header">
        <div class="add-item d-flex">
            <div class="page-title">
                <h4>Orders</h4>
                <h6>Manage customer orders</h6>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-3 col-sm-6 col-12 d-flex">
            <div class="dash-count bg-primary">
                <div class="dash-counts">
                    <h4 class="mb-1">{{ counts.total }}</h4>
                    <p class="text-white mb-0">Total Orders</p>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6 col-12 d-flex">
            <div class="dash-count bg-warning">
                <div class="dash-counts">
                    <h4 class="mb-1">{{ counts.pending }}</h4>
                    <p class="text-white mb-0">Pending</p>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6 col-12 d-flex">
            <div class="dash-count bg-info">
                <div class="dash-counts">
                    <h4 class="mb-1">{{ counts.confirmed }}</h4>
                    <p class="text-white mb-0">Confirmed</p>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6 col-12 d-flex">
            <div class="dash-count bg-success">
                <div class="dash-counts">
                    <h4 class="mb-1">{{ counts.completed }}</h4>
                    <p class="text-white mb-0">Completed</p>
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
                    :options="statusOptions"
                    name="Status"
                    @change="submitFilters"
                ></select-filter>
                <Link
                    :href="route('brand-partner.orders.create')"
                    class="btn btn-primary d-flex align-items-center gap-2"
                >
                    <vue-feather type="plus" class="feather-14"></vue-feather>
                    Add Order
                </Link>
            </div>
        </div>

        <div class="card-body p-0">
            <dt-table
                v-model:sortings="form.sort"
                v-model:perPage="form.per_page"
                :columns="columns"
                :data="orders.data"
                :total-records="orders.total"
                :start-record="orders.from"
                :end-record="orders.to"
                :links="orders.links"
                @change="submitFilters"
            >
                <template #total="{ row }">
                    {{ formatCurrency(row.total) }}
                </template>

                <template #has_pre_order="{ value }">
                    <span v-if="value" class="badge bg-warning">Pre-Order</span>
                    <span v-else class="text-muted">—</span>
                </template>

                <template #status="{ value }">
                    <span class="badge" :class="`bg-${getStatusColor(value)}`">
                        {{ value }}
                    </span>
                </template>

                <template #reference="{ row, value }">
                    <Link
                        :href="route('brand-partner.orders.show', row.id)"
                        class="text-primary fw-semibold text-decoration-none"
                    >
                        {{ value }}
                    </Link>
                </template>

                <template #created_at="{ value }">
                    {{ formatDateShort(value) }}
                </template>

                <template #action="{ row, value }">
                    <div class="action-table-data">
                        <div class="edit-delete-action">
                            <Link
                                :href="
                                    route('brand-partner.orders.show', value)
                                "
                                class="btn btn-icon btn-outline-light btn-sm"
                                title="View"
                            >
                                <vue-feather
                                    type="eye"
                                    class="feather-14"
                                ></vue-feather>
                            </Link>
                        </div>
                    </div>
                </template>
            </dt-table>
        </div>
    </div>
</template>

<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import dayjs from 'dayjs';
import { removeEmptyValues } from '@/helpers/form';

const props = defineProps({
    orders: Object,
    counts: Object,
    statusOptions: Object,
    filter: Object,
});

const columns = [
    {
        title: 'Reference',
        dataIndex: 'reference',
        key: 'reference',
        sortable: true,
    },
    {
        title: 'Customer',
        dataIndex: 'customer_name',
        key: 'customer_name',
        sortable: true,
    },
    { title: 'Total', dataIndex: 'total', key: 'total', sortable: true },
    {
        title: 'Pre-Order',
        dataIndex: 'has_pre_order',
        key: 'has_pre_order',
    },
    { title: 'Status', dataIndex: 'status', key: 'status', sortable: true },
    {
        title: 'Date',
        dataIndex: 'created_at',
        key: 'created_at',
        sortable: true,
    },
    { title: '', dataIndex: 'id', key: 'action' },
];

const form = useForm({
    filter: {
        search: props.filter?.search || '',
        status: props.filter?.status || '',
    },
    sort: [],
    per_page: props.orders?.per_page || 10,
});

const submitFilters = () => {
    form.transform((data) =>
        removeEmptyValues({
            ...data,
            sort: data.sort.join(','),
            per_page: data.per_page === 10 ? '' : data.per_page,
        }),
    ).get(route('brand-partner.orders.index'), {
        preserveState: true,
        replace: true,
    });
};

const formatCurrency = (amount) => {
    return new Intl.NumberFormat('en-PH', {
        style: 'currency',
        currency: 'PHP',
    }).format(amount / 100);
};

const formatDate = (date) => {
    return dayjs(date).format('MMM D, YYYY h:mm A');
};

const formatDateShort = (date) => {
    return dayjs(date).format('MMM D');
};

const getStatusColor = (status) => {
    const colors = {
        pending: 'warning',
        confirmed: 'primary',
        completed: 'success',
        cancelled: 'danger',
    };
    return colors[status] || 'secondary';
};
</script>

<style>
.badge.bg-warning {
    white-space: nowrap;
}
</style>
