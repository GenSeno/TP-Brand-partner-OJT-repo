<template>
    <Head :title="`Brand Partner - ${brandPartner.name}`" />

    <div class="page-header">
        <div class="add-item d-flex">
            <div class="page-title">
                <h4>{{ brandPartner.name }}</h4>
                <h6>Brand Partner Details</h6>
            </div>
        </div>
        <div class="page-btn d-flex gap-2">
            <ModalLink
                navigate
                :href="route('admin.brand-partners.edit', brandPartner.id)"
                class="btn btn-outline-primary"
                #default="{ loading }"
            >
                <loading-text :loading="loading">
                    <vue-feather type="edit" class="me-2"></vue-feather>
                    Edit
                </loading-text>
            </ModalLink>
            <Link
                :href="route('admin.brand-partners.index')"
                class="btn btn-secondary"
            >
                <vue-feather type="arrow-left" class="me-2"></vue-feather>
                Back to List
            </Link>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-4">
            <div class="card">
                <div class="card-body text-center">
                    <div class="avatar avatar-xxl mb-3">
                        <img
                            :src="brandPartner.logo_url || '/img/default.png'"
                            :alt="brandPartner.name"
                            class="img-fluid rounded"
                        />
                    </div>
                    <h5>{{ brandPartner.name }}</h5>
                    <p class="text-muted mb-2">{{ brandPartner.email }}</p>
                    <span
                        class="badge"
                        :class="`bg-${getStatusColor(brandPartner.status)}`"
                    >
                        {{ brandPartner.status }}
                    </span>
                    <div class="mt-3">
                        <a
                            :href="`/${brandPartner.slug}`"
                            target="_blank"
                            class="btn btn-outline-primary btn-sm"
                        >
                            <vue-feather
                                type="external-link"
                                class="me-1"
                            ></vue-feather>
                            Visit Store
                        </a>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <button
                            v-if="brandPartner.status === 'pending'"
                            class="btn btn-success"
                            @click="approvePartner"
                        >
                            <vue-feather
                                type="check"
                                class="me-2"
                            ></vue-feather>
                            Approve Partner
                        </button>
                        <button
                            v-if="brandPartner.status === 'active'"
                            class="btn btn-warning"
                            @click="suspendPartner"
                        >
                            <vue-feather
                                type="pause"
                                class="me-2"
                            ></vue-feather>
                            Suspend Partner
                        </button>
                        <button
                            v-if="brandPartner.status === 'suspended'"
                            class="btn btn-success"
                            @click="approvePartner"
                        >
                            <vue-feather type="play" class="me-2"></vue-feather>
                            Reactivate Partner
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Partner Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small">Brand Name</label>
                            <p class="mb-0">{{ brandPartner.name }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small">Slug</label>
                            <p class="mb-0">{{ brandPartner.slug }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small">Email</label>
                            <p class="mb-0">{{ brandPartner.email }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small">Phone</label>
                            <p class="mb-0">{{ brandPartner.phone || '-' }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small"
                                >Contact Person</label
                            >
                            <p class="mb-0">
                                {{ brandPartner.contact_person || '-' }}
                            </p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small">Created</label>
                            <p class="mb-0">
                                {{ formatDate(brandPartner.created_at) }}
                            </p>
                        </div>
                        <div class="col-12 mb-3">
                            <label class="text-muted small">Address</label>
                            <p class="mb-0">
                                {{ brandPartner.address || '-' }}
                            </p>
                        </div>
                        <div class="col-12">
                            <label class="text-muted small">Description</label>
                            <p class="mb-0">
                                {{ brandPartner.description || '-' }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body text-center">
                            <h3 class="mb-1">
                                {{ brandPartner.products_count }}
                            </h3>
                            <p class="text-muted mb-0">Products</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body text-center">
                            <h3 class="mb-1">
                                {{ brandPartner.orders_count }}
                            </h3>
                            <p class="text-muted mb-0">Orders</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body text-center">
                            <h3 class="mb-1">
                                {{ brandPartner.categories_count }}
                            </h3>
                            <p class="text-muted mb-0">Categories</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card" v-if="recentOrders.length > 0">
                <div class="card-header">
                    <h5 class="card-title mb-0">Recent Orders</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Reference</th>
                                    <th>Customer</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr
                                    v-for="order in recentOrders"
                                    :key="order.id"
                                >
                                    <td>{{ order.reference }}</td>
                                    <td>{{ order.customer_name }}</td>
                                    <td>{{ formatCurrency(order.total) }}</td>
                                    <td>
                                        <span
                                            class="badge"
                                            :class="`bg-${getOrderStatusColor(order.status)}`"
                                        >
                                            {{ order.status }}
                                        </span>
                                    </td>
                                    <td>{{ formatDate(order.created_at) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import dayjs from 'dayjs';
import DashboardLayout from '@/layouts/dashboard-layout.vue';

defineOptions({
    layout: DashboardLayout,
});

const props = defineProps({
    brandPartner: Object,
    recentOrders: {
        type: Array,
        default: () => [],
    },
});

const formatDate = (date) => {
    return dayjs(date).format('MMM D, YYYY');
};

const formatCurrency = (amount) => {
    return new Intl.NumberFormat('en-PH', {
        style: 'currency',
        currency: 'PHP',
    }).format(amount / 100);
};

const getStatusColor = (status) => {
    const colors = {
        pending: 'warning',
        active: 'success',
        suspended: 'danger',
    };
    return colors[status] || 'secondary';
};

const getOrderStatusColor = (status) => {
    const colors = {
        pending: 'warning',
        confirmed: 'info',
        completed: 'success',
        cancelled: 'danger',
    };
    return colors[status] || 'secondary';
};

const approvePartner = () => {
    if (confirm('Are you sure you want to approve this brand partner?')) {
        router.post(
            route('admin.brand-partners.approve', props.brandPartner.id),
        );
    }
};

const suspendPartner = () => {
    if (confirm('Are you sure you want to suspend this brand partner?')) {
        router.post(
            route('admin.brand-partners.suspend', props.brandPartner.id),
        );
    }
};
</script>
