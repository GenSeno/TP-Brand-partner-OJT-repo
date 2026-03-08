<template>
    <Head title="Dashboard" />

    <div
        class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-2"
    >
        <div class="mb-3">
            <h1 class="mb-1">
                Welcome, {{ $page.props.auth.user.first_name }}
            </h1>
            <p v-if="stats.orders.new_today > 0" class="fw-medium">
                You have
                <span class="text-primary fw-bold">{{
                    formatNumber(stats.orders.new_today)
                }}</span>
                Orders, Today
            </p>
            <p v-else class="fw-medium text-muted">
                No new orders today. Time to focus on pending tasks!
            </p>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row">
        <div class="col-xl-3 col-sm-6 col-12 d-flex">
            <div class="card bg-primary sale-widget flex-fill">
                <div class="card-body d-flex align-items-center">
                    <span class="sale-icon bg-white text-primary">
                        <i class="ti ti-users fs-24"></i>
                    </span>
                    <div class="ms-2">
                        <p class="text-white mb-1">Total Customers</p>
                        <div
                            class="d-inline-flex align-items-center flex-wrap gap-2"
                        >
                            <h4 class="text-white">
                                {{ formatNumber(stats.customers.total) }}
                            </h4>
                            <span
                                v-show="stats.customers.new_today > 0"
                                class="badge badge-soft-primary"
                                ><i class="ti ti-arrow-up me-1"></i>+{{
                                    formatNumber(stats.customers.new_today)
                                }}</span
                            >
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6 col-12 d-flex">
            <div class="card bg-secondary sale-widget flex-fill">
                <div class="card-body d-flex align-items-center">
                    <span class="sale-icon bg-white text-secondary">
                        <i class="ti ti-clock-pause fs-24"></i>
                    </span>
                    <div class="ms-2">
                        <p class="text-white mb-1">Pending Orders</p>
                        <div
                            class="d-inline-flex align-items-center flex-wrap gap-2"
                        >
                            <h4 class="text-white">
                                {{ formatNumber(stats.orders.pending) }}
                            </h4>
                            <span
                                v-if="stats.orders.pending > 0"
                                class="badge badge-soft-success"
                                ><i class="ti ti-arrow-up me-1"></i>+{{
                                    formatNumber(stats.orders.pending)
                                }}</span
                            >
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6 col-12 d-flex">
            <div class="card bg-teal sale-widget flex-fill">
                <div class="card-body d-flex align-items-center">
                    <span class="sale-icon bg-white text-teal">
                        <i class="ti ti-box fs-24"></i>
                    </span>
                    <div class="ms-2">
                        <p class="text-white mb-1">Total Products</p>
                        <div
                            class="d-inline-flex align-items-center flex-wrap gap-2"
                        >
                            <h4 class="text-white">
                                {{ formatNumber(stats.products.total) }}
                            </h4>
                            <span
                                v-if="stats.products.new_today > 0"
                                class="badge badge-soft-success"
                                ><i class="ti ti-arrow-up me-1"></i>+{{
                                    formatNumber(stats.products.new_today)
                                }}</span
                            >
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6 col-12 d-flex">
            <div class="card bg-info sale-widget flex-fill">
                <div class="card-body d-flex align-items-center">
                    <span class="sale-icon bg-white text-info">
                        <i class="ti ti-briefcase fs-24"></i>
                    </span>
                    <div class="ms-2">
                        <p class="text-white mb-1">Active Job Orders</p>
                        <div
                            class="d-inline-flex align-items-center flex-wrap gap-2"
                        >
                            <h4 class="text-white">
                                {{ formatNumber(stats.job_orders.total) }}
                            </h4>
                            <span
                                v-if="stats.job_orders.new_today > 0"
                                class="badge badge-soft-success"
                                ><i class="ti ti-arrow-up me-1"></i>+{{
                                    formatNumber(stats.job_orders.new_today)
                                }}</span
                            >
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Recent Orders -->
        <div class="col-xl-7 col-sm-12 col-12 d-flex">
            <div class="card flex-fill">
                <div
                    class="card-header d-flex justify-content-between align-items-center"
                >
                    <h4 class="card-title mb-0">Recent Orders</h4>
                    <Link href="#" class="btn btn-sm btn-primary">
                        View All
                    </Link>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive dataview">
                        <table class="table dashboard-recent-products">
                            <thead class="thead-light">
                                <tr>
                                    <th>Reference</th>
                                    <th>Customer</th>
                                    <th>Status</th>
                                    <th>Total</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr
                                    v-for="order in recentOrders"
                                    :key="order.id"
                                >
                                    <td>
                                        <Link href="#" class="text-primary">
                                            {{ order.reference }}
                                        </Link>
                                    </td>
                                    <td>
                                        {{
                                            order.orderable?.full_name ||
                                            'Guest'
                                        }}
                                    </td>
                                    <td>
                                        <span
                                            class="badge"
                                            :class="`badge-${getStatusColor(order.status)}`"
                                        >
                                            {{ order.status }}
                                        </span>
                                    </td>
                                    <td>
                                        {{ order.total.formatted }}
                                    </td>
                                    <td>
                                        {{
                                            dayjs(order.placed_at).format(
                                                'MMM DD, YYYY',
                                            )
                                        }}
                                    </td>
                                </tr>
                                <tr v-if="recentOrders.length === 0">
                                    <td colspan="5" class="text-center">
                                        No recent orders
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Urgent Job Orders -->
        <div class="col-xl-5 col-sm-12 col-12 d-flex">
            <div class="card flex-fill">
                <div
                    class="card-header d-flex justify-content-between align-items-center"
                >
                    <h5 class="card-title mb-0">Urgent Job Orders</h5>
                    <Link
                        :href="route('admin.job-order.index')"
                        class="btn btn-sm btn-danger"
                    >
                        View All
                    </Link>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive dataview">
                        <table class="table dashboard-recent-products">
                            <thead class="thead-light">
                                <tr>
                                    <th>Reference</th>
                                    <th>Urgency</th>
                                    <th>Due Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr
                                    v-for="jobOrder in urgentJobOrders"
                                    :key="jobOrder.id"
                                >
                                    <td>
                                        <Link
                                            :href="
                                                route(
                                                    'admin.job-order.edit',
                                                    jobOrder.id,
                                                )
                                            "
                                            class="text-primary"
                                        >
                                            {{ jobOrder.reference }}
                                        </Link>
                                    </td>
                                    <td>
                                        <UrgentStatus
                                            :urgency-flag="
                                                jobOrder.urgency_flag
                                            "
                                        />
                                    </td>
                                    <td>
                                        {{
                                            dayjs(jobOrder.due_at).format(
                                                'MMM DD, YYYY',
                                            )
                                        }}
                                    </td>
                                </tr>
                                <tr v-if="urgentJobOrders.length === 0">
                                    <td colspan="3" class="text-center">
                                        No urgent job orders
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
import DashboardLayout from '@/layouts/dashboard-layout.vue';
import { Head, Link } from '@inertiajs/vue3';
import dayjs from 'dayjs';
import { formatNumber } from '@/helpers/number';
import UrgentStatus from '@/components/job-order/urgent-status.vue';

defineOptions({
    layout: DashboardLayout,
});

defineProps({
    stats: Object,
    recentOrders: Array,
    urgentJobOrders: Array,
});

const getStatusColor = (status) => {
    const colors = {
        'awaiting-payment': 'warning',
        'verifying-payment': 'info',
        'payment-offline': 'secondary',
        'payment-received': 'success',
        dispatched: 'primary',
    };
    return colors[status] || 'secondary';
};
</script>
