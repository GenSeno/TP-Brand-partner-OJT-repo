<template>
    <Head title="Dashboard" />

    <div class="page-header">
        <div class="add-item d-flex">
            <div class="page-title">
                <h4>Dashboard</h4>
                <h6>Welcome to your brand partner dashboard</h6>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-3 col-sm-6 col-12 d-flex">
            <div class="dash-count bg-primary">
                <div class="dash-counts">
                    <h4 class="mb-1">{{ stats.total_products }}</h4>
                    <p class="text-white mb-0">Total Products</p>
                </div>
                <div class="dash-imgs">
                    <vue-feather type="box"></vue-feather>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6 col-12 d-flex">
            <div class="dash-count bg-success">
                <div class="dash-counts">
                    <h4 class="mb-1">{{ stats.published_products }}</h4>
                    <p class="text-white mb-0">Published Products</p>
                </div>
                <div class="dash-imgs">
                    <vue-feather type="check-circle"></vue-feather>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6 col-12 d-flex">
            <div class="dash-count bg-info">
                <div class="dash-counts">
                    <h4 class="mb-1">{{ stats.total_orders }}</h4>
                    <p class="text-white mb-0">Total Orders</p>
                </div>
                <div class="dash-imgs">
                    <vue-feather type="shopping-cart"></vue-feather>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6 col-12 d-flex">
            <div class="dash-count bg-warning">
                <div class="dash-counts">
                    <h4 class="mb-1">{{ stats.pending_orders }}</h4>
                    <p class="text-white mb-0">Pending Orders</p>
                </div>
                <div class="dash-imgs">
                    <vue-feather type="clock"></vue-feather>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div
                    class="card-header d-flex justify-content-between align-items-center"
                >
                    <h5 class="card-title mb-0">Recent Orders</h5>
                    <Link
                        :href="route('brand-partner.orders.index')"
                        class="btn btn-sm btn-primary"
                    >
                        View All
                    </Link>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
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
                                    <td>
                                        <Link
                                            :href="
                                                route(
                                                    'brand-partner.orders.show',
                                                    order.id,
                                                )
                                            "
                                            class="text-primary"
                                        >
                                            {{ order.reference }}
                                        </Link>
                                    </td>
                                    <td>{{ order.customer_name }}</td>
                                    <td>{{ formatCurrency(order.total) }}</td>
                                    <td>
                                        <span
                                            class="badge"
                                            :class="`bg-${getStatusColor(order.status)}`"
                                        >
                                            {{ order.status }}
                                        </span>
                                    </td>
                                    <td>{{ formatDate(order.created_at) }}</td>
                                </tr>
                                <tr v-if="recentOrders.length === 0">
                                    <td
                                        colspan="5"
                                        class="text-center text-muted"
                                    >
                                        No orders yet
                                    </td>
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
import { Head, Link } from '@inertiajs/vue3';
import dayjs from 'dayjs';
import BrandPartnerLayout from '@/layouts/brand-partner-layout.vue';

// Explicitly set the layout
defineOptions({ layout: BrandPartnerLayout });

const props = defineProps({
    stats: Object,
    recentOrders: Array,
});

const formatCurrency = (amount) => {
    return new Intl.NumberFormat('en-PH', {
        style: 'currency',
        currency: 'PHP',
    }).format(amount / 100);
};

const formatDate = (date) => {
    return dayjs(date).format('MMM D, YYYY');
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
