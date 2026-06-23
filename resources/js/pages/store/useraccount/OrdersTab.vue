<template>
    <div>
        <div v-if="!selectedOrder">
            <h2 class="account-page-title">My Orders</h2>
            <p class="account-page-subtitle">
                Manage your orders, track shipments, and view order history
            </p>

            <!-- Tabs -->
            <div class="orders-tabs">
                <button
                    :class="{ active: orderTab === 'all' }"
                    @click="orderTab = 'all'"
                >
                    All
                </button>
                <button
                    :class="{ active: orderTab === 'shipping' }"
                    @click="orderTab = 'shipping'"
                >
                    On Shipping
                </button>
                <button
                    :class="{ active: orderTab === 'arrived' }"
                    @click="orderTab = 'arrived'"
                >
                    Arrived
                </button>
                <button
                    :class="{
                        active: orderTab === 'cancelled',
                    }"
                    @click="orderTab = 'cancelled'"
                >
                    Cancelled
                </button>
            </div>

            <!-- List -->
            <div class="orders-list">
                <div
                    v-for="order in filteredOrders"
                    :key="order.id"
                    class="order-card"
                >
                    <div class="order-card-header">
                        <span class="font-bold"
                            >Order NO. {{ order.reference }}</span
                        >
                        <span class="font-bold">{{
                            formatDateShort(order.created_at)
                        }}</span>
                    </div>
                    <div class="order-card-body">
                        <div
                            v-for="line in order.lines"
                            :key="line.id"
                            class="order-line-item"
                        >
                            <div class="line-img-wrap">
                                <img
                                    :src="
                                        line.product?.images?.[0]?.url ||
                                        '/img/tshirt-placeholder.svg'
                                    "
                                    alt=""
                                />
                            </div>
                            <div class="line-details">
                                <div class="line-title">
                                    {{ line.product_name }}
                                </div>
                                <div class="line-variants">
                                    <div v-if="line.meta?.size">
                                        Size: {{ line.meta.size }}
                                    </div>
                                    <div v-if="line.meta?.color">
                                        Color: {{ line.meta.color }}
                                    </div>
                                    <div>
                                        Quantity: {{ line.quantity }}
                                    </div>
                                </div>
                            </div>
                            <div class="line-price-col">
                                <div class="price-current text-red">
                                    PHP
                                    {{
                                        (line.unit_price / 100).toFixed(2)
                                    }}
                                </div>
                                <div
                                    v-if="
                                        line.product?.compare_price >
                                        line.unit_price
                                    "
                                    class="price-old text-strike"
                                >
                                    PHP
                                    {{
                                        (
                                            line.product.compare_price / 100
                                        ).toFixed(2)
                                    }}
                                </div>
                            </div>
                            <div class="status-col">
                                <div
                                    class="status-badge"
                                    :class="getStatusBadgeClass(order.status)"
                                >
                                    <span class="status-dot"></span>
                                    {{ getStatusLabel(order.status) }}
                                </div>
                                <div
                                    class="status-text mt-2 text-muted"
                                    style="font-size: 11px"
                                >
                                    <span v-if="order.status === 'completed'"
                                        >Delivered on
                                        {{ formatDateLong(order.updated_at) }}</span
                                    >
                                    <span v-else-if="order.status === 'pending'"
                                        >Order is being prepared</span
                                    >
                                    <span v-else-if="order.status === 'confirmed'"
                                        >Order has been confirmed</span
                                    >
                                    <span v-else-if="order.status === 'cancelled'"
                                        >Order has been cancelled</span
                                    >
                                    <span v-else
                                        >Estimated Delivery: Pending</span
                                    >
                                </div>
                            </div>
                        </div>

                        <div class="order-card-footer">
                            <button
                                class="btn-cancel-order"
                                v-if="
                                    ['pending', 'confirmed'].includes(
                                        order.status,
                                    )
                                "
                                @click="cancelOrder(order)"
                            >
                                CANCEL ORDER
                            </button>
                            <button
                                class="btn-view-details"
                                @click="viewDetails(order)"
                            >
                                VIEW DETAILS
                            </button>
                        </div>
                    </div>
                </div>
                <div
                    v-if="filteredOrders.length === 0"
                    class="no-orders py-4 text-center text-muted"
                >
                    <p>No orders found in this category.</p>
                </div>
            </div>
        </div>

        <!-- ============================================== -->
        <!-- ORDER DETAILS VIEW                             -->
        <!-- ============================================== -->
        <div v-else class="order-details-view">
            <div
                class="d-flex justify-content-between align-items-start mb-4"
            >
                <div>
                    <h2 class="account-page-title">My Orders</h2>
                    <p class="account-page-subtitle">
                        Manage your orders, track shipments, and view order
                        history
                    </p>
                </div>
                <button
                    @click="backToList"
                    class="btn-back mt-2"
                    style="
                        background: none;
                        border: none;
                        color: #1b5e38;
                        font-weight: bold;
                        cursor: pointer;
                    "
                >
                    &larr; Back
                </button>
            </div>

            <div class="order-details-header">
                <div class="detail-h-item" style="width: 25%">
                    <label>ORDER DATE</label>
                    <div>
                        {{ formatDateShort(selectedOrder.created_at) }}
                    </div>
                </div>
                <div class="detail-h-item" style="width: 25%">
                    <label>PAYMENT STATUS</label>
                    <div>
                        {{ selectedOrder.payment_status || 'Paid' }}
                    </div>
                </div>
                <div class="detail-h-item" style="width: 25%">
                    <label>FULFILLMENT STATUS</label>
                    <div>
                        {{ getStatusLabel(selectedOrder.status) }}
                    </div>
                </div>
                <div class="detail-h-item" style="width: 25%">
                    <label>ORDER NO.</label>
                    <div>Order NO. {{ selectedOrder.reference }}</div>
                </div>
            </div>

            <div class="order-table-container">
                <table class="order-table">
                    <thead>
                        <tr>
                            <th style="width: 50%">PRODUCT</th>
                            <th style="width: 20%">PRICE</th>
                            <th style="width: 15%">QUANTITY</th>
                            <th
                                style="
                                    width: 15%;
                                    text-align: right;
                                "
                            >
                                TOTAL
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="line in selectedOrder.lines"
                            :key="line.id"
                        >
                            <td>
                                <div class="d-flex" style="gap: 16px">
                                    <div
                                        style="
                                            width: 80px;
                                            height: 80px;
                                            background: #f5f5f5;
                                            display: flex;
                                            align-items: center;
                                            justify-content: center;
                                        "
                                    >
                                        <img
                                            :src="
                                                line.product?.images?.[0]
                                                    ?.url ||
                                                '/img/tshirt-placeholder.svg'
                                            "
                                            style="
                                                max-width: 100%;
                                                max-height: 100%;
                                                object-fit: contain;
                                            "
                                            alt=""
                                        />
                                    </div>
                                    <div>
                                        <div
                                            class="font-bold"
                                            style="
                                                font-size: 14px;
                                                margin-bottom: 4px;
                                                color: #333;
                                            "
                                        >
                                            {{ line.product_name }}
                                        </div>
                                        <div
                                            class="text-muted"
                                            style="
                                                font-size: 12px;
                                                line-height: 1.6;
                                            "
                                        >
                                            <div
                                                v-if="
                                                    line.product?.category
                                                "
                                            >
                                                Garment:
                                                {{ line.product.category.name }}
                                            </div>
                                            <div v-if="line.meta?.color">
                                                Color: {{ line.meta.color }}
                                            </div>
                                            <div v-if="line.meta?.size">
                                                Size: {{ line.meta.size }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td
                                style="
                                    vertical-align: top;
                                    padding-top: 24px;
                                    color: #555;
                                "
                            >
                                PHP
                                {{ (line.unit_price / 100).toFixed(2) }}
                            </td>
                            <td
                                style="
                                    vertical-align: top;
                                    padding-top: 24px;
                                    color: #555;
                                "
                            >
                                {{ line.quantity }}
                            </td>
                            <td
                                style="
                                    vertical-align: top;
                                    padding-top: 24px;
                                    color: #555;
                                    text-align: right;
                                "
                            >
                                PHP {{ (line.total / 100).toFixed(2) }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="order-summary-box">
                <div class="summary-line">
                    <span>Subtotal</span>
                    <span
                        >PHP
                        {{
                            (selectedOrder.sub_total / 100).toFixed(2)
                        }}</span
                    >
                </div>
                <div class="summary-line">
                    <span>Shipping</span>
                    <span
                        >PHP
                        {{
                            (
                                (selectedOrder.tax_total || 0) / 100
                            ).toFixed(2)
                        }}</span
                    >
                </div>
                <div class="summary-line summary-total">
                    <span class="font-bold">Total</span>
                    <span
                        class="font-bold font-lg"
                        style="font-size: 18px"
                        >PHP
                        {{ (selectedOrder.total / 100).toFixed(2) }}</span
                    >
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { router } from '@inertiajs/vue3';
import { formatDateShort, formatDateLong } from '../../../utils/dateHelpers';

const props = defineProps({
    orders: {
        type: Array,
        default: () => [],
    },
});

const orderTab = ref('all');
const selectedOrder = ref(null);

const filteredOrders = computed(() => {
    if (!props.orders) return [];
    if (orderTab.value === 'all') return props.orders;
    if (orderTab.value === 'shipping')
        return props.orders.filter((o) =>
            ['sent_to_tpinklab', 'confirmed'].includes(o.status),
        );
    if (orderTab.value === 'arrived')
        return props.orders.filter((o) => o.status === 'completed');
    if (orderTab.value === 'cancelled')
        return props.orders.filter((o) => o.status === 'cancelled');
    return props.orders;
});

const getStatusBadgeClass = (status) => {
    if (status === 'completed') return 'badge-delivered';
    if (status === 'cancelled') return 'badge-processing';
    if (status === 'sent_to_tpinklab') return 'badge-shipping';
    if (status === 'confirmed') return 'badge-confirmed';
    return 'badge-processing';
};

const getStatusLabel = (status) => {
    if (status === 'completed') return 'Delivered';
    if (status === 'cancelled') return 'Cancelled';
    if (status === 'sent_to_tpinklab') return 'Shipping';
    if (status === 'confirmed') return 'Confirmed';
    return 'PROCESSING';
};

const viewDetails = (order) => {
    selectedOrder.value = order;
};

const backToList = () => {
    selectedOrder.value = null;
};

const cancelOrder = (order) => {
    if (confirm('Are you sure you want to cancel this order?')) {
        router.patch(
            route('store.brand-partner.order.cancel', {
                reference: order.reference,
            }),
            {},
            {
                preserveScroll: true,
            },
        );
    }
};
</script>

<style scoped>
/* Orders Tab Defaults */
.orders-tabs {
    display: flex;
    border-bottom: 1px solid #eee;
    margin-bottom: 24px;
}
.orders-tabs button {
    flex: 1;
    background: none;
    border: none;
    color: #888;
    padding: 12px 16px;
    font-size: 12px;
    font-weight: 500;
    cursor: pointer;
    border-bottom: 2px solid transparent;
    transition: all 0.2s;
}
.orders-tabs button.active {
    color: #333;
    border-bottom-color: #333;
    font-weight: 700;
}

.orders-list {
    display: flex;
    flex-direction: column;
    gap: 24px;
}

.order-card {
    border: 1px solid #eee;
}
.order-card-header {
    background: #e9e9e9;
    padding: 14px 20px;
    display: flex;
    justify-content: space-between;
    font-size: 12px;
    color: #555;
    text-transform: uppercase;
}
.font-bold {
    font-weight: 800;
}
.order-card-body {
    padding: 24px;
}

.order-line-item {
    display: flex;
    justify-content: space-between;
    gap: 24px;
    padding-bottom: 24px;
    margin-bottom: 24px;
    border-bottom: 1px dashed #eee;
}
.order-line-item:last-child {
    border-bottom: none;
    padding-bottom: 0;
    margin-bottom: 0;
}

.line-img-wrap {
    width: 80px;
    height: 80px;
    background: #f5f5f5;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}
.line-img-wrap img {
    max-width: 90%;
    max-height: 90%;
    object-fit: contain;
}

.line-details {
    flex: 1;
}
.line-title {
    font-weight: 800;
    font-size: 13px;
    margin-bottom: 6px;
    color: #333;
}
.line-variants {
    font-size: 12px;
    color: #777;
    line-height: 1.6;
}

.line-price-col {
    width: 120px;
    text-align: left;
}
.price-current {
    font-size: 12px;
    font-weight: 800;
    padding-top: 2px;
}
.text-red {
    color: #ff5e5e;
}
.price-old {
    font-size: 12px;
    color: #aaa;
    margin-top: 4px;
}
.text-strike {
    text-decoration: line-through;
}

.status-col {
    width: 200px;
    text-align: right;
    display: flex;
    flex-direction: column;
    align-items: flex-start;
}
.status-badge {
    display: inline-flex;
    align-items: center;
    padding: 6px 14px;
    border-radius: 50px;
    font-size: 11px;
    font-weight: 800;
    text-transform: uppercase;
}
.status-dot {
    width: 6px;
    height: 6px;
    border-radius: 50%;
    margin-right: 8px;
}
.badge-delivered {
    background: #c8e6c9;
    color: #1b5e38;
}
.badge-delivered .status-dot {
    background: #1b5e38;
}
.badge-shipping {
    background: #ffe0b2;
    color: #e65100;
}
.badge-shipping .status-dot {
    background: #e65100;
}
.badge-processing {
    background: #c1c1c1;
    color: #000000;
}
.badge-processing .status-dot {
    background: #fe0000;
}
.badge-confirmed {
    background: #bbdefb;
    color: #1565c0;
}
.badge-confirmed .status-dot {
    background: #1565c0;
}

.order-card-footer {
    display: flex;
    justify-content: flex-end;
    gap: 12px;
    margin-top: 20px;
}
.btn-view-details {
    background: #0b6630;
    color: #fff;
    border: none;
    padding: 10px 24px;
    font-size: 11px;
    font-weight: 800;
    cursor: pointer;
    border-radius: 2px;
    transition: background 0.2s;
}
.btn-view-details:hover {
    background: #084822;
}
.btn-cancel-order {
    background: transparent;
    color: #ff5e5e;
    border: 1px solid #ff5e5e;
    padding: 10px 24px;
    font-size: 11px;
    font-weight: 800;
    cursor: pointer;
    border-radius: 2px;
    transition: all 0.2s;
}
.btn-cancel-order:hover {
    background: #ff5e5e;
    color: #fff;
}

/* Order Details Layout */
.order-details-header {
    background: #e9e9e9;
    display: flex;
    padding: 24px;
    margin-bottom: 24px;
}
.detail-h-item label {
    font-size: 10px;
    font-weight: 800;
    color: #555;
    margin-bottom: 8px;
    display: block;
}
.detail-h-item div {
    font-size: 13px;
    color: #333;
    font-weight: 600;
}

.order-table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 24px;
}
.order-table th {
    text-align: left;
    padding: 12px 12px 12px 0;
    font-size: 10px;
    font-weight: 800;
    color: #888;
    border-bottom: 1px solid #eee;
}
.order-table td {
    padding: 24px 12px 24px 0;
    border-bottom: 1px solid #f9f9f9;
}

.order-summary-box {
    width: 380px;
    margin-left: auto;
    border-top: 1px solid #eee;
    padding-top: 24px;
}
.summary-line {
    display: flex;
    justify-content: space-between;
    margin-bottom: 16px;
    font-size: 13px;
    color: #555;
    font-weight: 500;
}
.summary-total {
    border-top: 1px solid #eee;
    padding-top: 16px;
    color: #333;
}

@media (max-width: 768px) {
    .order-line-item {
        flex-direction: column;
        gap: 12px;
    }
    .line-price-col,
    .status-col {
        width: 100%;
        text-align: left;
    }
    .order-details-header {
        flex-direction: column;
        gap: 16px;
    }
    .detail-h-item {
        width: 100% !important;
    }
    .order-table-container {
        overflow-x: auto;
    }
    .order-table th,
    .order-table td {
        min-width: 100px;
    }
    .order-summary-box {
        width: 100%;
    }
}
</style>
