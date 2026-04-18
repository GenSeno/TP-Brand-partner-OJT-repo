<template>
    <Head :title="`Order Confirmed - ${brandPartner.name}`" />

    <div class="grocery-confirmation-section">
        <div class="grocery-container">
            <!-- Success Order Box -->
            <div class="order-success-section">
                <div class="success-order-box">
                    <div class="success-checkmark">
                        <i class="ri-checkbox-circle-fill"></i>
                    </div>
                    <h2>Thank you for your order!</h2>
                    <p class="success-subtitle">
                        Your order <strong>{{ order.reference }}</strong> has
                        been placed successfully. We've sent a confirmation to
                        <strong>{{ order.customer_email }}</strong
                        >.
                    </p>
                    <Link
                        :href="
                            route(
                                'store.brand-partner.index',
                                brandPartner.slug,
                            )
                        "
                        class="grocery-btn theme-btn"
                    >
                        <i class="ri-store-2-line"></i> Continue Shopping
                    </Link>
                </div>
            </div>

            <!-- Details Cards -->
            <div class="details-grid">
                <!-- Order Details Card -->
                <div class="grocery-card">
                    <div class="card-header">
                        <i class="ri-file-list-3-line"></i>
                        <h5>Order Details</h5>
                        <span class="order-ref-badge">{{
                            order.reference
                        }}</span>
                    </div>
                    <div class="detail-rows">
                        <div class="detail-row">
                            <span class="detail-label">Customer</span>
                            <span class="detail-value">{{
                                order.customer_name
                            }}</span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Email</span>
                            <span class="detail-value">{{
                                order.customer_email
                            }}</span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Phone</span>
                            <span class="detail-value">{{
                                order.customer_phone
                            }}</span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Order Date</span>
                            <span class="detail-value">{{
                                formatDate(order.created_at)
                            }}</span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Shipping Address</span>
                            <span class="detail-value">{{
                                order.shipping_address
                            }}</span>
                        </div>
                    </div>
                </div>

                <!-- Items Ordered Card -->
                <div class="grocery-card">
                    <div class="card-header">
                        <i class="ri-shopping-bag-3-line"></i>
                        <h5>Items Ordered</h5>
                    </div>
                    <div class="ordered-items-list">
                        <div
                            class="ordered-item"
                            v-for="line in order.lines"
                            :key="line.id"
                        >
                            <img
                                :src="
                                    line.product?.image_url ||
                                    '/img/tshirt-placeholder.svg'
                                "
                                :alt="line.product_name"
                                class="ordered-item-img"
                            />
                            <div class="ordered-item-info">
                                <span class="ordered-item-name">{{
                                    line.product_name
                                }}</span>
                                <span
                                    v-if="line.meta?.color || line.meta?.size"
                                    style="
                                        font-size: 11px;
                                        color: #888;
                                        display: block;
                                    "
                                >
                                    <span v-if="line.meta.color">{{
                                        line.meta.color
                                    }}</span>
                                    <span
                                        v-if="line.meta.color && line.meta.size"
                                    >
                                        /
                                    </span>
                                    <span v-if="line.meta.size">{{
                                        line.meta.size
                                    }}</span>
                                </span>
                                <span class="ordered-item-meta">
                                    {{ formatCurrency(line.unit_price) }} x
                                    {{ line.quantity }}
                                </span>
                            </div>
                            <span class="ordered-item-total">{{
                                formatCurrency(line.total)
                            }}</span>
                        </div>
                    </div>

                    <div class="order-totals-section">
                        <div class="totals-row">
                            <span>Subtotal</span>
                            <span>{{ formatCurrency(order.subtotal) }}</span>
                        </div>
                        <div class="totals-row" v-if="order.discount > 0">
                            <span>Discount</span>
                            <span class="discount-amount"
                                >-{{ formatCurrency(order.discount) }}</span
                            >
                        </div>
                        <div class="totals-divider"></div>
                        <div class="totals-row grand-total">
                            <span>Total</span>
                            <span>{{ formatCurrency(order.total) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Order Notes -->
            <div class="grocery-card notes-card" v-if="order.notes">
                <div class="card-header">
                    <i class="ri-sticky-note-line"></i>
                    <h5>Order Notes</h5>
                </div>
                <p class="notes-content">{{ order.notes }}</p>
            </div>

            <!-- Next Steps -->
            <div class="next-steps-box">
                <p>
                    The <strong>{{ brandPartner.name }}</strong> team will
                    contact you to confirm your order and arrange delivery
                    details.
                </p>
            </div>
        </div>
    </div>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3';
import dayjs from 'dayjs';

const props = defineProps({
    brandPartner: Object,
    order: Object,
});

const formatCurrency = (amount) => {
    return new Intl.NumberFormat('en-PH', {
        style: 'currency',
        currency: 'PHP',
    }).format(amount / 100);
};

const formatDate = (date) => {
    return dayjs(date).format('MMMM D, YYYY [at] h:mm A');
};
</script>

<style scoped>
.grocery-confirmation-section {
    min-height: 60vh;
    padding: 30px 0 50px;
    font-family: 'Public Sans', sans-serif;
    background: rgb(var(--grocery-light-bg));
    /* Grocery Theme Color Variables */
    --grocery-theme: 60, 133, 153; /* Main teal/cyan color: rgb(60, 133, 153) */
    --grocery-content: 143, 143, 178; /* Light gray-blue content text */
    --grocery-title: 27, 27, 62; /* Dark blue-gray for titles */
    --grocery-border: 232, 232, 232; /* Light gray borders */
    --grocery-primary: 254, 175, 24; /* Yellow/orange accent */
    --grocery-light-bg: 247, 247, 247; /* Light gray background */
    --grocery-rating: 255, 191, 19; /* Gold/yellow for ratings */
}

.grocery-container {
    max-width: 900px;
    margin: 0 auto;
    padding: 0 16px;
}

/* Success Order Box */
.order-success-section {
    margin-bottom: 30px;
}

.success-order-box {
    text-align: center;
    background: #fff;
    border-radius: 14px;
    padding: 48px 32px 40px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
}

.success-checkmark {
    width: 90px;
    height: 90px;
    background: linear-gradient(135deg, #e8f8ee 0%, #d4f5e0 100%);
    border-radius: 50%;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 20px;
}

.success-checkmark i {
    font-size: 48px;
    color: rgb(var(--grocery-rating));
}

.success-order-box h2 {
    font-size: 26px;
    font-weight: 800;
    color: rgb(var(--grocery-title));
    margin: 0 0 12px;
}

.success-subtitle {
    font-size: 14px;
    color: #777;
    max-width: 460px;
    margin: 0 auto 28px;
    line-height: 1.6;
}

.grocery-btn.theme-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 14px 32px;
    background: rgb(var(--grocery-theme));
    color: #fff;
    border: none;
    border-radius: 12px;
    font-size: 15px;
    font-weight: 700;
    font-family: 'Public Sans', sans-serif;
    text-decoration: none;
    cursor: pointer;
    transition:
        background 0.2s,
        transform 0.15s;
}

.grocery-btn.theme-btn:hover {
    background: #e67a1f;
    color: #fff;
    transform: translateY(-1px);
}

/* Details Grid */
.details-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
    margin-bottom: 20px;
}

/* Grocery Card */
.grocery-card {
    background: #fff;
    border-radius: 14px;
    padding: 24px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
}

.card-header {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 20px;
}

.card-header i {
    font-size: 22px;
    color: rgb(var(--grocery-theme));
}

.card-header h5 {
    font-size: 16px;
    font-weight: 700;
    color: rgb(var(--grocery-title));
    margin: 0;
    flex: 1;
}

.order-ref-badge {
    background: #fff5ec;
    color: rgb(var(--grocery-theme));
    padding: 5px 14px;
    border-radius: 8px;
    font-size: 12px;
    font-weight: 700;
}

/* Detail Rows */
.detail-rows {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.detail-row {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 16px;
}

.detail-label {
    font-size: 13px;
    color: #999;
    flex-shrink: 0;
    min-width: 100px;
}

.detail-value {
    font-size: 13px;
    font-weight: 600;
    color: #333;
    text-align: right;
    word-break: break-word;
}

/* Ordered Items */
.ordered-items-list {
    display: flex;
    flex-direction: column;
    gap: 10px;
    margin-bottom: 18px;
}

.ordered-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 10px 0;
    border-bottom: 1px solid #f5f5f5;
}

.ordered-item:last-child {
    border-bottom: none;
}

.ordered-item-img {
    width: 48px;
    height: 48px;
    object-fit: cover;
    border-radius: 10px;
    flex-shrink: 0;
    background: #f5f5f5;
}

.ordered-item-info {
    flex: 1;
    min-width: 0;
}

.ordered-item-name {
    font-size: 13px;
    font-weight: 700;
    color: rgb(var(--grocery-title));
    display: block;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.ordered-item-meta {
    font-size: 11px;
    color: #bbb;
}

.ordered-item-total {
    font-size: 14px;
    font-weight: 700;
    color: #555;
    flex-shrink: 0;
}

/* Order Totals */
.order-totals-section {
    padding-top: 12px;
    border-top: 1px solid rgb(var(--grocery-border));
}

.totals-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 5px 0;
    font-size: 13px;
    color: #777;
}

.totals-row.grand-total {
    font-size: 18px;
    font-weight: 800;
    color: rgb(var(--grocery-title));
}

.totals-row.grand-total span:last-child {
    color: rgb(var(--grocery-theme));
}

.discount-amount {
    color: rgb(var(--grocery-rating));
    font-weight: 600;
}

.totals-divider {
    height: 1px;
    background: rgb(var(--grocery-border));
    margin: 8px 0;
}

/* Notes Card */
.notes-card {
    margin-bottom: 20px;
}

.notes-content {
    font-size: 14px;
    color: #555;
    line-height: 1.7;
    margin: 0;
}

/* Next Steps */
.next-steps-box {
    text-align: center;
    padding: 20px;
}

.next-steps-box p {
    font-size: 14px;
    color: #777;
    line-height: 1.6;
    margin: 0;
}

/* Responsive */
@media (max-width: 768px) {
    .details-grid {
        grid-template-columns: 1fr;
        gap: 16px;
    }

    .success-order-box {
        padding: 36px 20px 32px;
    }

    .success-order-box h2 {
        font-size: 22px;
    }

    .success-checkmark {
        width: 76px;
        height: 76px;
    }

    .success-checkmark i {
        font-size: 40px;
    }

    .grocery-card {
        padding: 18px;
    }

    .detail-row {
        flex-direction: column;
        gap: 2px;
    }

    .detail-value {
        text-align: left;
    }
}
</style>
