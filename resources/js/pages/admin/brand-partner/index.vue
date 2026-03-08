<template>
    <Head title="Brand Partners" />

    <div class="page-header">
        <div class="add-item d-flex">
            <div class="page-title">
                <h4>Brand Partners</h4>
                <h6>Manage brand partners</h6>
            </div>
        </div>
        <div class="page-btn">
            <ModalLink
                navigate
                :href="route('admin.brand-partners.create')"
                class="btn btn-added"
                #default="{ loading }"
            >
                <loading-text :loading="loading">
                    <vue-feather type="plus-circle" class="me-2"></vue-feather>
                    Add Brand Partner
                </loading-text>
            </ModalLink>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-3 col-sm-6 col-12 d-flex">
            <div class="dash-count bg-primary">
                <div class="dash-counts">
                    <h4 class="mb-1">{{ counts.total }}</h4>
                    <p class="text-white mb-0">Total Partners</p>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6 col-12 d-flex">
            <div class="dash-count bg-warning">
                <div class="dash-counts">
                    <h4 class="mb-1">{{ counts.pending }}</h4>
                    <p class="text-white mb-0">Pending Approval</p>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6 col-12 d-flex">
            <div class="dash-count bg-success">
                <div class="dash-counts">
                    <h4 class="mb-1">{{ counts.active }}</h4>
                    <p class="text-white mb-0">Active</p>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6 col-12 d-flex">
            <div class="dash-count bg-danger">
                <div class="dash-counts">
                    <h4 class="mb-1">{{ counts.suspended }}</h4>
                    <p class="text-white mb-0">Suspended</p>
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
            </div>
        </div>

        <div class="card-body p-0">
            <dt-table
                v-model:sortings="form.sort"
                v-model:perPage="form.per_page"
                :columns="columns"
                :data="brandPartners.data"
                :total-records="brandPartners.total"
                :start-record="brandPartners.from"
                :end-record="brandPartners.to"
                :links="brandPartners.links"
                @change="submitFilters"
            >
                <template #name="{ row, value }">
                    <div class="d-flex align-items-center">
                        <div class="avatar avatar-md bg-light me-2">
                            <img
                                :src="row.logo_url || '/img/default.png'"
                                :alt="value"
                                class="img-fluid rounded"
                            />
                        </div>
                        <div>
                            <span class="d-block">{{ value }}</span>
                            <small class="text-muted">{{ row.email }}</small>
                        </div>
                    </div>
                </template>

                <template #status="{ row, value }">
                    <span class="badge" :class="`bg-${getStatusColor(value)}`">
                        {{ value }}
                    </span>
                </template>

                <template #created_at="{ value }">
                    {{ formatDate(value) }}
                </template>

                <template #action="{ row, value }">
                    <div class="action-table-data">
                        <div class="edit-delete-action">
                            <button
                                v-if="row.status === 'pending'"
                                class="btn btn-icon btn-success-light btn-sm me-2"
                                title="Approve"
                                @click="approvePartner(row)"
                            >
                                <vue-feather
                                    type="check"
                                    class="feather-14"
                                ></vue-feather>
                            </button>
                            <button
                                v-if="row.status === 'active'"
                                class="btn btn-icon btn-warning-light btn-sm me-2"
                                title="Suspend"
                                @click="suspendPartner(row)"
                            >
                                <vue-feather
                                    type="pause"
                                    class="feather-14"
                                ></vue-feather>
                            </button>
                            <button
                                v-if="row.status === 'suspended'"
                                class="btn btn-icon btn-success-light btn-sm me-2"
                                title="Activate"
                                @click="approvePartner(row)"
                            >
                                <vue-feather
                                    type="play"
                                    class="feather-14"
                                ></vue-feather>
                            </button>
                            <ModalLink
                                navigate
                                :href="
                                    route('admin.brand-partners.edit', value)
                                "
                                class="btn btn-icon btn-outline-light btn-sm me-2"
                                title="Edit"
                            >
                                <vue-feather
                                    type="edit"
                                    class="feather-14"
                                ></vue-feather>
                            </ModalLink>
                            <Link
                                :href="
                                    route('admin.brand-partners.show', value)
                                "
                                class="btn btn-icon btn-outline-light btn-sm me-2"
                                title="View"
                            >
                                <vue-feather
                                    type="eye"
                                    class="feather-14"
                                ></vue-feather>
                            </Link>
                            <dt-delete
                                :id="value"
                                route-name="admin.brand-partners.destroy"
                                :name="row.name"
                                model-name="brand partner"
                                class="btn btn-icon btn-danger-light btn-sm"
                                :class="{
                                    'disabled opacity-50': row.orders_count > 0,
                                }"
                                title="Delete"
                            >
                                <vue-feather
                                    type="trash-2"
                                    class="feather-14"
                                ></vue-feather>
                            </dt-delete>
                        </div>
                    </div>
                </template>
            </dt-table>
        </div>
    </div>
</template>

<script setup>
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import dayjs from 'dayjs';
import DashboardLayout from '@/layouts/dashboard-layout.vue';
import { removeEmptyValues } from '@/helpers/form';

defineOptions({
    layout: DashboardLayout,
});

const props = defineProps({
    brandPartners: Object,
    counts: Object,
    statusOptions: Object,
    filter: Object,
});

const columns = [
    { title: 'Brand Partner', dataIndex: 'name', key: 'name', sortable: true },
    { title: 'Products', dataIndex: 'products_count', key: 'products_count' },
    { title: 'Orders', dataIndex: 'orders_count', key: 'orders_count' },
    { title: 'Status', dataIndex: 'status', key: 'status', sortable: true },
    {
        title: 'Created',
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
    per_page: props.brandPartners?.per_page || 10,
});

const submitFilters = () => {
    form.transform((data) =>
        removeEmptyValues({
            ...data,
            sort: data.sort.join(','),
            per_page: data.per_page === 10 ? '' : data.per_page,
        }),
    ).get(route('admin.brand-partners.index'), {
        preserveState: true,
        replace: true,
    });
};

const formatDate = (date) => {
    return dayjs(date).format('MMM D, YYYY');
};

const getStatusColor = (status) => {
    const colors = {
        pending: 'warning',
        active: 'success',
        suspended: 'danger',
    };
    return colors[status] || 'secondary';
};

const approvePartner = (partner) => {
    if (confirm('Are you sure you want to approve this brand partner?')) {
        router.post(route('admin.brand-partners.approve', partner.id));
    }
};

const suspendPartner = (partner) => {
    if (confirm('Are you sure you want to suspend this brand partner?')) {
        router.post(route('admin.brand-partners.suspend', partner.id));
    }
};
</script>
