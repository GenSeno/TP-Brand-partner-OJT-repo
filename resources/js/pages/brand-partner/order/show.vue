<template>
    <Head :title="`Order ${order.reference}`" />

    <div class="page-header">
        <div class="add-item d-flex">
            <div class="page-title">
                <h4>Order {{ order.reference }}</h4>
                <h6>View order details</h6>
            </div>
        </div>
        <div class="page-btn">
            <Link
                :href="route('brand-partner.orders.index')"
                class="btn btn-secondary"
            >
                <vue-feather type="arrow-left" class="me-2"></vue-feather>
                Back to Orders
            </Link>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div
                    class="card-header d-flex justify-content-between align-items-center"
                >
                    <h5 class="card-title mb-0">Order Items</h5>
                    <span
                        class="badge"
                        :class="`bg-${getStatusColor(order.status)}`"
                    >
                        {{ order.status }}
                    </span>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th class="text-center">Qty</th>
                                    <th class="text-end">Unit Price</th>
                                    <th class="text-end">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="line in order.lines" :key="line.id">
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img
                                                v-if="line.product?.images?.[0]"
                                                :src="
                                                    line.product.images[0].url
                                                "
                                                :alt="line.product_name"
                                                class="img-thumbnail me-2"
                                                style="
                                                    width: 50px;
                                                    height: 50px;
                                                    object-fit: cover;
                                                "
                                            />
                                            <span>{{ line.product_name }}</span>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        {{ line.quantity }}
                                    </td>
                                    <td class="text-end">
                                        {{ formatCurrency(line.unit_price) }}
                                    </td>
                                    <td class="text-end">
                                        {{ formatCurrency(line.total) }}
                                    </td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="3" class="text-end">
                                        <strong>Subtotal</strong>
                                    </td>
                                    <td class="text-end">
                                        {{ formatCurrency(order.sub_total) }}
                                    </td>
                                </tr>
                                <tr v-if="order.tax_total > 0">
                                    <td colspan="3" class="text-end">
                                        <strong>Tax</strong>
                                    </td>
                                    <td class="text-end">
                                        {{ formatCurrency(order.tax_total) }}
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="3" class="text-end">
                                        <strong>Total</strong>
                                    </td>
                                    <td class="text-end">
                                        <strong>{{
                                            formatCurrency(order.total)
                                        }}</strong>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            <div v-if="order.notes" class="card mt-3">
                <div class="card-header">
                    <h5 class="card-title mb-0">Notes</h5>
                </div>
                <div class="card-body">
                    <p class="mb-0">{{ order.notes }}</p>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Customer Information</h5>
                </div>
                <div class="card-body">
                    <p class="mb-1">
                        <strong>Name:</strong> {{ order.customer_name }}
                    </p>
                    <p class="mb-1">
                        <strong>Email:</strong> {{ order.customer_email }}
                    </p>
                    <p v-if="order.customer_phone" class="mb-1">
                        <strong>Phone:</strong> {{ order.customer_phone }}
                    </p>
                    <p class="mb-0">
                        <strong>Order Date:</strong>
                        {{ formatDate(order.created_at) }}
                    </p>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header">
                    <h5 class="card-title mb-0">Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <button
                            v-if="order.status === 'pending'"
                            class="btn btn-primary"
                            @click="confirmOrder"
                            :disabled="processing"
                        >
                            <vue-feather
                                type="check"
                                class="me-2"
                            ></vue-feather>
                            Confirm Order
                        </button>
                        <button
                            v-if="order.status === 'confirmed'"
                            class="btn btn-success"
                            @click="completeOrder"
                            :disabled="processing"
                        >
                            <vue-feather
                                type="check-circle"
                                class="me-2"
                            ></vue-feather>
                            Mark as Completed
                        </button>
                        <button
                            v-if="
                                order.status !== 'completed' &&
                                order.status !== 'cancelled'
                            "
                            class="btn btn-danger"
                            @click="cancelOrder"
                            :disabled="processing"
                        >
                            <vue-feather
                                type="x-circle"
                                class="me-2"
                            ></vue-feather>
                            Cancel Order
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import dayjs from 'dayjs';

const props = defineProps({
    order: Object,
});

const processing = ref(false);

const formatCurrency = (amount) => {
    return new Intl.NumberFormat('en-PH', {
        style: 'currency',
        currency: 'PHP',
    }).format(amount / 100);
};

const formatDate = (date) => {
    return dayjs(date).format('MMM D, YYYY h:mm A');
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

const confirmOrder = () => {
    if (confirm('Are you sure you want to confirm this order?')) {
        processing.value = true;
        router.post(
            route('brand-partner.orders.confirm', props.order.id),
            {},
            {
                onFinish: () => (processing.value = false),
            },
        );
    }
};

const completeOrder = () => {
    if (confirm('Are you sure you want to mark this order as completed?')) {
        processing.value = true;
        router.post(
            route('brand-partner.orders.complete', props.order.id),
            {},
            {
                onFinish: () => (processing.value = false),
            },
        );
    }
};

const cancelOrder = () => {
    if (confirm('Are you sure you want to cancel this order?')) {
        processing.value = true;
        router.post(
            route('brand-partner.orders.cancel', props.order.id),
            {},
            {
                onFinish: () => (processing.value = false),
            },
        );
    }
};
</script>
