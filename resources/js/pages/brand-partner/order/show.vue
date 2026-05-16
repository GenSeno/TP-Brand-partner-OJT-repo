<template>
    <Head :title="`Order ${order.reference}`" />

    <div class="page-header">
        <div class="add-item d-flex">
            <div class="page-title">
                <h4>Order {{ order.reference }}</h4>
                <h6>View order details</h6>
            </div>
        </div>
        <div class="page-btn d-flex gap-2">
            <button
                class="btn btn-added"
                :disabled="processing"
                @click="sendToAdmin"
            >
                <vue-feather type="send" class="me-2"></vue-feather>
                Add New Order
            </button>
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
                                            <div>
                                                <span>{{
                                                    line.product_name
                                                }}</span>
                                                <span
                                                    v-if="line.meta?.pre_order"
                                                    class="badge bg-warning ms-1"
                                                    style="font-size: 10px;"
                                                >Pre-Order</span>
                                                <div
                                                    v-if="
                                                        line.meta?.color ||
                                                        line.meta?.size
                                                    "
                                                    style="
                                                        font-size: 11px;
                                                        color: #888;
                                                        margin-top: 2px;
                                                    "
                                                >
                                                    <span v-if="line.meta.color"
                                                        >Color:
                                                        <strong>{{
                                                            line.meta.color
                                                        }}</strong></span
                                                    >
                                                    <span
                                                        v-if="
                                                            line.meta.color &&
                                                            line.meta.size
                                                        "
                                                    >
                                                        &middot;
                                                    </span>
                                                    <span v-if="line.meta.size"
                                                        >Size:
                                                        <strong>{{
                                                            line.meta.size
                                                        }}</strong></span
                                                    >
                                                </div>
                                            </div>
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
                        <strong>Company:</strong>
                        {{ order.company_name || '—' }}
                    </p>
                    <p class="mb-1">
                        <strong>Name:</strong> {{ order.customer_name }}
                    </p>
                    <p class="mb-1">
                        <strong>Address:</strong>
                        {{ order.address_line1 || '—' }}
                    </p>
                    <p class="mb-1">
                        <strong>Phone:</strong>
                        {{ order.customer_phone || '—' }}
                    </p>
                    <p class="mb-0">
                        <strong>Order Date:</strong>
                        {{ formatDate(order.placed_at || order.created_at) }}
                    </p>
                </div>
            </div>

            <div class="card mt-3">
                <div
                    class="card-header d-flex justify-content-between align-items-center"
                >
                    <h5 class="card-title mb-0">Payment Status</h5>
                    <span
                        class="badge"
                        :class="`bg-${getPaymentStatusColor(order.payment_status)}`"
                    >
                        {{ getPaymentStatusLabel(order.payment_status) }}
                    </span>
                </div>
                <div class="card-body">
                    <div class="d-flex flex-wrap gap-2">
                        <span
                            v-for="opt in paymentStatusOptions"
                            :key="opt.value"
                            class="badge fs-6 px-3 py-2"
                            :class="
                                order.payment_status === opt.value
                                    ? `bg-${getPaymentStatusColor(opt.value)}`
                                    : 'bg-light text-dark'
                            "
                            style="cursor: default"
                        >
                            {{ opt.label }}
                        </span>
                    </div>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header">
                    <h5 class="card-title mb-0">Order Info</h5>
                </div>
                <div class="card-body">
                    <p class="mb-1">
                        <strong>JO Number:</strong> {{ order.jo_number || '—' }}
                    </p>
                    <p class="mb-1">
                        <strong>JO Status:</strong> {{ order.jo_status || '—' }}
                    </p>
                    <p class="mb-0">
                        <strong>Note:</strong> {{ order.notes || '—' }}
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
                        <ModalLink
                            :href="route('brand-partner.orders.edit', order.id)"
                            class="btn btn-secondary"
                        >
                            <vue-feather type="edit" class="me-2"></vue-feather>
                            Edit
                        </ModalLink>
                        <ModalLink
                            :href="
                                route(
                                    'brand-partner.orders.receive-payment',
                                    order.id,
                                )
                            "
                            class="btn btn-info text-white"
                        >
                            <vue-feather
                                type="credit-card"
                                class="me-2"
                            ></vue-feather>
                            Receive Payment
                        </ModalLink>
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

const paymentStatusOptions = [
    { value: 'unpaid', label: 'Unpaid' },
    { value: 'partially_paid', label: 'Partially Paid' },
    { value: 'paid', label: 'Paid' },
    { value: 'cancelled', label: 'Cancelled' },
];

const getPaymentStatusLabel = (status) => {
    return (
        paymentStatusOptions.find((o) => o.value === status)?.label ??
        status ??
        '—'
    );
};

const getPaymentStatusColor = (status) => {
    const colors = {
        unpaid: 'warning',
        partially_paid: 'info',
        paid: 'success',
        cancelled: 'danger',
    };
    return colors[status] || 'secondary';
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

const sendToAdmin = () => {
    if (confirm('Create new Sales Order to TPInkLab')) {
        processing.value = true;
        router.post(
            route('brand-partner.orders.send-to-admin', props.order.id),
            {},
            { onFinish: () => (processing.value = false) },
        );
    }
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
