<template>
    <Head :title="`Order Confirmed - ${brandPartner.name}`" />

    <div class="confirmation-page">

        <!-- ===== HERO SECTION ===== -->
        <section class="hero-section">
            <div class="hero-bg">
                <img src="/img/confirmation_bg.png" alt="" class="hero-img" />
                <div class="hero-overlay"></div>
            </div>

            <div class="hero-content">
                <p class="hero-eyebrow">ORDER #{{ order.reference }}</p>
                <h1 class="hero-title">
                    <span class="hero-line1">THANK YOU</span>
                    <span class="hero-line2">FOR YOUR ENQUIRY</span>
                </h1>
                <p class="hero-subtitle">
                    Your action means a lot to us-whether you place an order, signed up, or joined our community. Welcome to
                    <strong>{{ brandPartner.name }}</strong>, where every step
                    forward is fueled by passion, grit, and purpose.
                </p>
                <div class="hero-actions">
                    <Link
                        :href="route('store.brand-partner.index', brandPartner.slug)"
                        class="hero-btn hero-btn--ghost"
                    >
                        CONTINUE EXPLORING
                    </Link>
                    <a href="#order-details" class="hero-btn hero-btn--ghost">
                        VIEW YOUR ORDER
                    </a>
                </div>
            </div>

            <!-- Scroll indicator -->
            <div class="scroll-indicator">
                <span></span>
            </div>
        </section>

        <!-- ===== ORDER DETAILS SECTION ===== -->
        <section class="details-section" id="order-details">
            <div class="details-container">

                <!-- Confirmation banner -->
                <div class="confirmation-banner">
                    <div class="banner-check">
                        <i class="ri-checkbox-circle-fill"></i>
                    </div>
                    <div class="banner-text">
                        <h2>Order Placed Successfully</h2>
                        <p>
                            We've sent a confirmation to
                            <strong>{{ order.customer_email }}</strong>. The
                            <strong>{{ brandPartner.name }}</strong> team will
                            contact you to confirm and arrange delivery.
                        </p>
                    </div>
                    <span class="banner-ref">{{ order.reference }}</span>
                </div>

                <!-- Cards grid -->
                <div class="cards-grid">

                    <!-- Order Info Card -->
                    <div class="info-card">
                        <div class="card-head">
                            <i class="ri-file-list-3-line"></i>
                            <h3>Order Details</h3>
                        </div>
                        <ul class="info-list">
                            <li>
                                <span class="info-label">Customer</span>
                                <span class="info-value">{{ order.customer_name }}</span>
                            </li>
                            <li>
                                <span class="info-label">Email</span>
                                <span class="info-value">{{ order.customer_email }}</span>
                            </li>
                            <li>
                                <span class="info-label">Phone</span>
                                <span class="info-value">{{ order.customer_phone }}</span>
                            </li>
                            <li>
                                <span class="info-label">Order Date</span>
                                <span class="info-value">{{ formatDate(order.created_at) }}</span>
                            </li>
                            <li>
                                <span class="info-label">Shipping Address</span>
                                <span class="info-value">{{ order.shipping_address }}</span>
                            </li>
                        </ul>
                    </div>

                    <!-- Items Card -->
                    <div class="info-card">
                        <div class="card-head">
                            <i class="ri-shopping-bag-3-line"></i>
                            <h3>Items Ordered</h3>
                        </div>

                        <div class="items-list">
                            <div
                                class="item-row"
                                v-for="line in order.lines"
                                :key="line.id"
                            >
                                <img
                                    :src="line.product?.image_url || '/img/tshirt-placeholder.svg'"
                                    :alt="line.product_name"
                                    class="item-img"
                                />
                                <div class="item-info">
                                    <span class="item-name">{{ line.product_name }}</span>
                                    <span
                                        v-if="line.meta?.color || line.meta?.size"
                                        class="item-variants"
                                    >
                                        <span v-if="line.meta.color">{{ line.meta.color }}</span>
                                        <span v-if="line.meta.color && line.meta.size"> · </span>
                                        <span v-if="line.meta.size">{{ line.meta.size }}</span>
                                    </span>
                                    <span class="item-meta">
                                        {{ formatCurrency(line.unit_price) }} × {{ line.quantity }}
                                    </span>
                                </div>
                                <span class="item-total">{{ formatCurrency(line.total) }}</span>
                            </div>
                        </div>

                        <div class="totals">
                            <div class="totals-row">
                                <span>Subtotal</span>
                                <span>{{ formatCurrency(order.sub_total) }}</span>
                            </div>
                            <div class="totals-divider"></div>
                            <div class="totals-row grand">
                                <span>Total</span>
                                <span>{{ formatCurrency(order.total) }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Notes -->
                <div class="notes-card" v-if="order.notes">
                    <div class="card-head">
                        <i class="ri-sticky-note-line"></i>
                        <h3>Order Notes</h3>
                    </div>
                    <p class="notes-body">{{ order.notes }}</p>
                </div>

                <!-- Back to shop -->
                <div class="back-row">
                    <Link
                        :href="route('store.brand-partner.index', brandPartner.slug)"
                        class="back-btn"
                    >
                        <i class="ri-arrow-left-line"></i>
                        Back to Shop
                    </Link>
                </div>

            </div>
        </section>
    </div>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3';
import dayjs from 'dayjs';

const props = defineProps({
    brandPartner: Object,
    order: Object,
});

const formatCurrency = (amount) =>
    new Intl.NumberFormat('en-PH', {
        style: 'currency',
        currency: 'PHP',
    }).format(amount / 100);

const formatDate = (date) =>
    dayjs(date).format('MMMM D, YYYY [at] h:mm A');
</script>

<style scoped>
/* ─── Reset / Base ───────────────────────────────────── */
.confirmation-page {
    font-family: 'Public Sans', sans-serif;
    background: #f5f4f0;
    min-height: 100vh;
}

/* ─── HERO ───────────────────────────────────────────── */
.hero-section {
    position: relative;
    width: 100%;
    height: 100vh;
    min-height: 560px;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
}

.hero-bg {
    position: absolute;
    inset: 0;
    z-index: 0;
}

.hero-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    object-position: center 40%;
    display: block;
}

.hero-overlay {
    position: absolute;
    inset: 0;
    background: linear-gradient(
        to bottom,
        rgba(0, 0, 0, 0.28) 0%,
        rgba(0, 0, 0, 0.42) 60%,
        rgba(0, 0, 0, 0.65) 100%
    );
}

.hero-content {
    position: relative;
    z-index: 2;
    text-align: center;
    padding: 0 24px;
    max-width: 700px;
}

.hero-eyebrow {
    font-size: 12px;
    font-weight: 700;
    letter-spacing: 0.22em;
    color: rgba(255, 255, 255, 0.65);
    text-transform: uppercase;
    margin: 0 0 18px;
}

.hero-title {
    margin: 0 0 20px;
    line-height: 1;
}

.hero-line1,
.hero-line2 {
    display: block;
    font-family: 'Poppins', 'Arial', sans-serif;
    text-transform: uppercase;
    color: #fff;
    letter-spacing: 0.04em;
}

.hero-line1 {
    font-size: clamp(52px, 5vw, 96px);
    font-weight: 900;
}

.hero-line2 {
    font-size: clamp(22px, 4.5vw, 42px);
    font-weight: 700;
    letter-spacing: 0.18em;
    opacity: 0.95;
}

.hero-subtitle {
    font-size: 14px;
    color: rgba(255, 255, 255, 0.80);
    line-height: 1.7;
    max-width: 440px;
    margin: 0 auto 36px;
}

.hero-subtitle strong {
    color: #fff;
}

.hero-actions {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 16px;
    flex-wrap: wrap;
}

.hero-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 13px 28px;
    font-size: 11px;
    font-weight: 800;
    letter-spacing: 0.18em;
    text-transform: uppercase;
    text-decoration: none;
    cursor: pointer;
    transition: background 0.2s, color 0.2s, border-color 0.2s;
}

.hero-btn--ghost {
    background: transparent;
    border: 1.5px solid rgba(255, 255, 255, 0.80);
    color: #fff;
}

.hero-btn--ghost:hover {
    background: #FF9505;
    color: #ffffff;
}

/* Scroll indicator */
.scroll-indicator {
    position: absolute;
    bottom: 28px;
    left: 50%;
    transform: translateX(-50%);
    z-index: 2;
}

.scroll-indicator span {
    display: block;
    width: 1px;
    height: 52px;
    background: rgba(255, 255, 255, 0.5);
    margin: 0 auto;
    animation: scrollPulse 1.8s ease-in-out infinite;
    transform-origin: top center;
}

@keyframes scrollPulse {
    0%   { transform: scaleY(0); opacity: 0; }
    40%  { opacity: 1; }
    100% { transform: scaleY(1); opacity: 0; }
}

/* ─── DETAILS SECTION ────────────────────────────────── */
.details-section {
    background: #f5f4f0;
    padding: 60px 0 80px;
}

.details-container {
    max-width: 960px;
    margin: 0 auto;
    padding: 0 24px;
}

/* Confirmation banner */
.confirmation-banner {
    display: flex;
    align-items: center;
    gap: 18px;
    background: #fff;
    border-left: 4px solid #2d6a4f;
    border-radius: 10px;
    padding: 22px 24px;
    margin-bottom: 32px;
    box-shadow: 0 2px 12px rgba(0,0,0,0.05);
}

.banner-check {
    flex-shrink: 0;
    font-size: 36px;
    color: #2d6a4f;
    line-height: 1;
}

.banner-text {
    flex: 1;
    min-width: 0;
}

.banner-text h2 {
    font-size: 16px;
    font-weight: 800;
    color: #1a1a1a;
    margin: 0 0 4px;
}

.banner-text p {
    font-size: 13px;
    color: #777;
    line-height: 1.6;
    margin: 0;
}

.banner-text strong {
    color: #333;
}

.banner-ref {
    flex-shrink: 0;
    font-size: 11px;
    font-weight: 800;
    letter-spacing: 0.1em;
    color: #2d6a4f;
    background: #edf7f2;
    padding: 6px 14px;
    border-radius: 6px;
    white-space: nowrap;
}

/* Cards grid */
.cards-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
    margin-bottom: 20px;
}

.info-card,
.notes-card {
    background: #fff;
    border-radius: 10px;
    padding: 26px;
    box-shadow: 0 2px 12px rgba(0,0,0,0.05);
}

.card-head {
    display: flex;
    align-items: center;
    gap: 10px;
    padding-bottom: 16px;
    border-bottom: 1px solid #ececec;
    margin-bottom: 18px;
}

.card-head i {
    font-size: 20px;
    color: #2d6a4f;
}

.card-head h3 {
    font-size: 14px;
    font-weight: 800;
    color: #1a1a1a;
    margin: 0;
    letter-spacing: 0.03em;
    text-transform: uppercase;
}

/* Info list */
.info-list {
    list-style: none;
    padding: 0;
    margin: 0;
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.info-list li {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 12px;
}

.info-label {
    font-size: 12px;
    color: #aaa;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.06em;
    flex-shrink: 0;
    min-width: 110px;
}

.info-value {
    font-size: 13px;
    font-weight: 600;
    color: #333;
    text-align: right;
    word-break: break-word;
}

/* Items list */
.items-list {
    display: flex;
    flex-direction: column;
    gap: 0;
    margin-bottom: 16px;
}

.item-row {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 10px 0;
    border-bottom: 1px solid #f3f3f3;
}

.item-row:last-child {
    border-bottom: none;
}

.item-img {
    width: 46px;
    height: 46px;
    object-fit: cover;
    border-radius: 8px;
    background: #f5f5f5;
    flex-shrink: 0;
}

.item-info {
    flex: 1;
    min-width: 0;
    display: flex;
    flex-direction: column;
    gap: 2px;
}

.item-name {
    font-size: 13px;
    font-weight: 700;
    color: #1a1a1a;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.item-variants {
    font-size: 11px;
    color: #888;
}

.item-meta {
    font-size: 11px;
    color: #bbb;
}

.item-total {
    font-size: 14px;
    font-weight: 700;
    color: #444;
    flex-shrink: 0;
}

/* Totals */
.totals {
    border-top: 1px solid #ececec;
    padding-top: 12px;
}

.totals-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 4px 0;
    font-size: 13px;
    color: #888;
}

.totals-row.discount span:last-child {
    color: #e84b0f;
    font-weight: 600;
}

.totals-divider {
    height: 1px;
    background: #ececec;
    margin: 8px 0;
}

.totals-row.grand {
    font-size: 17px;
    font-weight: 800;
    color: #1a1a1a;
}

.totals-row.grand span:last-child {
    color: #2d6a4f;
}

/* Notes */
.notes-card {
    margin-bottom: 20px;
}

.notes-body {
    font-size: 14px;
    color: #555;
    line-height: 1.7;
    margin: 0;
}

/* Back row */
.back-row {
    text-align: center;
    padding-top: 10px;
}

.back-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    font-size: 12px;
    font-weight: 700;
    letter-spacing: 0.1em;
    text-transform: uppercase;
    color: #555;
    text-decoration: none;
    padding: 12px 24px;
    border: 1.5px solid #d0d0d0;
    border-radius: 6px;
    transition: all 0.2s;
}

.back-btn:hover {
    background: #1a1a1a;
    border-color: #1a1a1a;
    color: #fff;
}

/* ─── Responsive ─────────────────────────────────────── */
@media (max-width: 768px) {
    .cards-grid {
        grid-template-columns: 1fr;
    }

    .confirmation-banner {
        flex-direction: column;
        align-items: flex-start;
        gap: 12px;
    }

    .banner-ref {
        align-self: flex-start;
    }

    .hero-line1 {
        font-size: 52px;
    }

    .hero-line2 {
        font-size: 22px;
    }

    .hero-actions {
        flex-direction: column;
        gap: 12px;
    }

    .hero-btn {
        width: 100%;
        max-width: 280px;
    }
}
</style>