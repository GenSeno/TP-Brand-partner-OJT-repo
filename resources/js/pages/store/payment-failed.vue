<template>
    <Head title="Payment Failed" />

    <div class="failed-page">
        <section class="hero-section">
            <div class="hero-bg">
                <img src="/img/confirmation_bg.png" alt="" class="hero-img" />
                <div class="hero-overlay"></div>
            </div>

            <div class="hero-content">
                <p class="hero-eyebrow">ORDER #{{ order.reference }}</p>
                <h1 class="hero-title">
                    <span class="hero-line1">PAYMENT</span>
                    <span class="hero-line2">FAILED</span>
                </h1>
                <p class="hero-subtitle">
                    Something went wrong with your payment. Please try again or
                    contact support if the problem persists.
                </p>
                <div class="hero-actions">
                    <Link
                        :href="route('store.brand-partner.checkout')"
                        class="hero-btn hero-btn--primary"
                    >
                        TRY AGAIN
                    </Link>
                    <Link
                        :href="route('store.brand-partner.index', brandPartner.slug)"
                        class="hero-btn hero-btn--ghost"
                    >
                        BACK TO SHOP
                    </Link>
                </div>
            </div>

            <div class="scroll-indicator">
                <span></span>
            </div>
        </section>
    </div>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { usePage } from '@inertiajs/vue3';

const props = defineProps({
    order: Object,
});

const brandPartner = usePage().props.brandPartner;
</script>

<style scoped>
.failed-page {
    font-family: 'Public Sans', sans-serif;
    background: #f5f4f0;
    min-height: 100vh;
}

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

.hero-btn--primary {
    background: #e84b0f;
    border: 1.5px solid #e84b0f;
    color: #fff;
}

.hero-btn--primary:hover {
    background: #c93d0a;
    border-color: #c93d0a;
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

@media (max-width: 768px) {
    .hero-line1 { font-size: 52px; }
    .hero-line2 { font-size: 22px; }
    .hero-actions { flex-direction: column; gap: 12px; }
    .hero-btn { width: 100%; max-width: 280px; }
}
</style>
