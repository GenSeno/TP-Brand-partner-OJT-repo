<template>
    <Head title="Be Our Partner" />

    <div class="partner-page">

        <!-- ===== HERO ===== -->
        <section class="partner-hero">
            <div class="partner-hero-bg">
                <img src="/img/img-partner1.png" alt="">
            </div>
            <div class="partner-hero-overlay"></div>
            <div class="partner-hero-content">
                <h1 class="partner-hero-title">Be Our Partner</h1>
            </div>
        </section>

        <!-- ===== LET'S GROW TOGETHER ===== -->
        <section class="partner-grow">
            <div class="partner-grow-inner">
                <div class="partner-grow-copy">
                    <h2 class="partner-grow-title">Let's grow together!</h2>
                    <p class="partner-grow-text">
                        At TP Ink Lab, we make it easy for apparel and garment
                        businesses to expand without the stress. Whether you're
                        starting a new clothing line or looking to scale your
                        brand, we've got everything you need under one roof.
                    </p>
                    <p class="partner-grow-text">
                        We'll help you build your own eCommerce platform,
                        integrate a secure payment gateway, and handle drop
                        shipping and print-on-demand — so you can focus on
                        creativity while we take care of production, packing,
                        and delivery.
                    </p>
                </div>
                <div class="partner-grow-image">
                    <div class="img-placeholder">
                        <img src="/img/img-partner2.png" alt="Let's grow together">
                    </div>
                </div>
            </div>
        </section>

        <!-- ===== WHY PARTNER WITH US ===== -->
        <section class="partner-why">
            <div class="partner-why-bg">
                <img src="/img/img-partner3.png" alt="">
            </div>
            <div class="partner-why-overlay"></div>
            <div class="partner-why-inner">
                <h2 class="partner-why-title">Why Partner With Us?</h2>
                <div class="partner-why-grid">
                    <div class="why-card">
                        <div class="why-card-icon">
                            <i class="ri-price-tag-3-line"></i>
                        </div>
                        <h3>Your Brand,<br>our platform</h3>
                        <p>Launch your own online store with ease! We'll set up your eCommerce site, integrate secure payments, and make selling simple and seamless.</p>
                    </div>
                    <div class="why-card">
                        <div class="why-card-icon">
                            <i class="ri-truck-line"></i>
                        </div>
                        <h3>We Print, Pack<br>& Ship for You</h3>
                        <p>Sit back and relax — we handle all the printing, packaging, and delivery while you focus on designing and growing your brand.</p>
                    </div>
                    <div class="why-card">
                        <div class="why-card-icon">
                            <i class="ri-home-3-line"></i>
                        </div>
                        <h3>No Inventory,<br>No Hassle!</h3>
                        <p>Say goodbye to storage worries! With our print-on-demand and drop shipping solutions, you sell first — we produce and ship on demand.</p>
                    </div>
                    <div class="why-card">
                        <div class="why-card-icon">
                            <i class="ri-store-2-line"></i>
                        </div>
                        <h3>We Showcase Your<br>Brand in Our Store</h3>
                        <p>Apart from online sales, your products can also shine in our physical shop, where walk-in customers can see, feel, and buy your creations in person.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- ===== RUN STRONGER TOGETHER CTA ===== -->
        <section class="partner-run">
            <div class="partner-run-bg">
                <!-- Replace with: <img src="/img/partner-run-bg.jpg" alt=""> -->
            </div>
            <div class="partner-run-overlay"></div>
            <div class="partner-run-inner">
                <h2 class="partner-run-title">Run stronger together—partner<br>with Tribu Parakas.</h2>
                <p class="partner-run-text">
                    We provide end-to-end race solutions including registration, race kit production, and full event
                    support—built for smooth execution and happy runners.
                </p>
                <a href="#" class="partner-run-btn">START YOUR EVENT WITH US</a>
            </div>
        </section>

        <!-- ===== EASY AS 1 2 3 ===== -->
        <section class="partner-easy">
            <div class="partner-easy-inner">
                <div class="partner-easy-left">
                    <h2 class="partner-easy-title">Doing Business with<br>Tribu Pakaras is as<br>Easy as 1 2 3!</h2>
                    <p class="partner-easy-sub">
                        We make partnerships simple, smooth, and stress-free — so you can focus on what you
                        do best: growing your brand. Here's how easy it is to work with us
                    </p>
                    <div class="partner-easy-image">
                        <div class="img-placeholder tall">
                            <!-- Replace with: <img src="/img/partner-easy.jpg" alt="Easy to work with us"> -->
                        </div>
                    </div>
                </div>
                <div class="partner-easy-right">
                    <div class="easy-step" v-for="step in steps" :key="step.title">
                        <div class="easy-step-content">
                            <h3 class="easy-step-title">{{ step.title }}</h3>
                            <p class="easy-step-text">{{ step.text }}</p>
                        </div>
                        <div class="easy-step-image">
                            <div class="img-placeholder square">
                                <!-- Replace with: <img :src="step.image" :alt="step.title"> -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ===== WHY CHOOSE US ===== -->
        <section class="partner-choose">
            <div class="partner-choose-inner">
                <h2 class="partner-choose-title">Why Choose Us?</h2>
                <p class="partner-choose-sub">
                    High-quality prints, seamless customization, and dependable fulfillment —TP Ink Lab is your one-stop
                    solution for custom apparel. More trust, more value, more reason to choose us.
                </p>
                <div class="partner-choose-grid">
                    <div class="choose-card">
                        <div class="choose-card-icon">
                            <i class="ri-equalizer-line"></i>
                        </div>
                        <h3>Full Customization, No Limits</h3>
                        <p>Every design, color, and detail is fully customizable — giving you total freedom to create apparel that's uniquely yours.</p>
                    </div>
                    <div class="choose-card">
                        <div class="choose-card-icon">
                            <i class="ri-star-line"></i>
                        </div>
                        <h3>Premium Quality, Fade-Free Prints</h3>
                        <p>Using high-end sublimation technology, our prints maintain their color and clarity wash after wash.</p>
                    </div>
                    <div class="choose-card">
                        <div class="choose-card-icon">
                            <i class="ri-bar-chart-line"></i>
                        </div>
                        <h3>Comfort Meets Performance</h3>
                        <p>We use lightweight, breathable, and durable fabrics ideal for sportswear, activewear, and lifestyle apparel.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- ===== BRING IDEAS TO LIFE CTA ===== -->
        <section class="partner-ideas">
            <div class="partner-ideas-bg">
                <!-- Replace with: <img src="/img/partner-ideas-bg.jpg" alt=""> -->
            </div>
            <div class="partner-ideas-overlay"></div>
            <div class="partner-ideas-inner">
                <h2 class="partner-ideas-title">We bring your ideas to life!</h2>
                <p class="partner-ideas-sub">with vibrant, high-quality custom prints made exactly your way.</p>
                <a href="#" class="partner-ideas-btn">GET STARTED TODAY</a>
            </div>
        </section>

        <!-- ===== NEED HELP CTA ===== -->
        <section class="partner-help">
            <div class="partner-help-bg">
                <!-- Replace with: <img src="/img/partner-clothes-bg.jpg" alt=""> -->
            </div>
            <div class="partner-help-card">
                <h2 class="partner-help-title">Need Help For Your Printing Ideas?</h2>
                <p class="partner-help-text">
                    Our team is always happy to assist and provide the support you need. Send us a
                    message today and we'll get right back to you
                </p>
                <a href="#" class="partner-help-btn">MESSAGE NOW</a>
            </div>
        </section>

    </div>
</template>

<script setup>
import { Head } from '@inertiajs/vue3';

const steps = [
    {
        title: 'Connect & Collaborate',
        text: "Just reach out to us! We'll talk about your brand, products, and goals — and start planning how to bring your designs to life.",
        image: '/img/partner-step1.jpg',
    },
    {
        title: 'Create & Customize',
        text: 'Send your designs or let our creative team help you develop stunning pieces. We handle everything — from printing to packing!',
        image: '/img/partner-step2.jpg',
    },
    {
        title: 'Launch & Earn',
        text: "Your products go live on your dedicated eCommerce store, ready for your customers to shop. We print, ship, and make sure they're happy while you grow your brand!",
        image: '/img/partner-step3.jpg',
    },
];
</script>

<style scoped>
.partner-page {
    font-family: 'Public Sans', sans-serif;
    color: #252525;
    background: #fff;
}

/* ===== Shared placeholder ===== */
.img-placeholder {
    width: 100%;
    height: 100%;
    background: #ccc;
    min-height: 420px;
    display: block;
}

.img-placeholder.tall {
    min-height: 380px;
}

.img-placeholder.square {
    min-height: 180px;
    width: 260px;
    flex-shrink: 0;
}

/* ===== HERO ===== */
.partner-hero {
    position: relative;
    height: 380px;
    display: flex;
    align-items: flex-end;
    overflow: hidden;

}

.partner-hero-bg {
    position: absolute;
    inset: 0;
    background: #444;
    /* Replace: background-image: url('/img/partner-hero.jpg'); background-size: cover; background-position: center; */
}

.partner-hero-bg img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
}

.partner-hero-overlay {
    position: absolute;
    inset: 0;
    background: rgba(0,0,0,0.45);
    z-index: 1;
}

.partner-hero-content {
    position: relative;
    z-index: 2;
    padding: 0 80px 48px;
}

.partner-hero-title {
    font-size: 48px;
    font-weight: 900;
    color: #fff;
    margin: 0;
    font-family: 'Public Sans', sans-serif;
}

/* ===== GROW TOGETHER ===== */
.partner-grow {
    padding: 80px 0;
    background: #fff;
}

.partner-grow-inner {
    max-width: 1300px;
    margin: 0 auto;
    padding: 0 80px;
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 80px;
    align-items: center;
}

.partner-grow-title {
    font-size: 40px;
    font-weight: 900;
    color: #252525;
    margin: 0 0 28px;
    font-family: 'Public Sans', sans-serif;
}

.partner-grow-text {
    font-size: 15px;
    color: #555;
    line-height: 1.8;
    margin: 0 0 18px;
}

.partner-grow-image {
    overflow: hidden;
}

/* ===== WHY PARTNER ===== */
.partner-why {
    position: relative;
    padding: 80px 0 100px;
    overflow: hidden;
}

.partner-why-bg {
    position: absolute;
    inset: 0;
    background: #2a2a2a;
    /* Replace: background-image: url('/img/partner-why-bg.jpg'); background-size: cover; background-position: center; */
}

.partner-why-bg img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
}

.partner-why-overlay {
    position: absolute;
    inset: 0;
    background: rgba(0,0,0,0.72);
    z-index: 1;
}

.partner-why-inner {
    position: relative;
    z-index: 2;
    max-width: 1300px;
    margin: 0 auto;
    padding: 0 80px;
}

.partner-why-title {
    font-size: 40px;
    font-weight: 900;
    color: #fff;
    text-align: center;
    margin: 0 0 52px;
    font-family: 'Public Sans', sans-serif;
}

.partner-why-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 20px;
}

.why-card {
    background: rgba(255,255,255,0.08);
    backdrop-filter: blur(6px);
    border: 1px solid rgba(255,255,255,0.12);
    padding: 36px 28px;
    display: flex;
    flex-direction: column;
    gap: 20px;
    transition: background 0.3s;
}

.why-card:hover {
    background: rgba(255,255,255,0.13);
}

.why-card-icon {
    width: 58px;
    height: 58px;
    border-radius: 50%;
    border: 1px solid rgba(255,255,255,0.3);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    color: #fff;
    flex-shrink: 0;
}

.why-card h3 {
    font-size: 18px;
    font-weight: 800;
    color: #fff;
    margin: 0;
    line-height: 1.3;
    font-family: 'Public Sans', sans-serif;
}

.why-card p {
    font-size: 14px;
    color: rgba(255,255,255,0.75);
    line-height: 1.75;
    margin: 0;
}

/* ===== RUN STRONGER CTA ===== */
.partner-run {
    position: relative;
    padding: 100px 24px;
    text-align: center;
    overflow: hidden;
}

.partner-run-bg {
    position: absolute;
    inset: 0;
    background: #c0440a;
    /* Replace: background-image: url('/img/partner-run-bg.jpg'); background-size: cover; background-position: center; */
}

.partner-run-bg img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
}

.partner-run-overlay {
    position: absolute;
    inset: 0;
    background: rgba(200,65,15,0.82);
    z-index: 1;
}

.partner-run-inner {
    position: relative;
    z-index: 2;
    max-width: 860px;
    margin: 0 auto;
}

.partner-run-title {
    font-size: 42px;
    font-weight: 900;
    color: #fff;
    margin: 0 0 20px;
    line-height: 1.2;
    font-family: 'Public Sans', sans-serif;
}

.partner-run-text {
    font-size: 15px;
    color: rgba(255,255,255,0.85);
    line-height: 1.75;
    margin: 0 0 36px;
    max-width: 640px;
    margin-left: auto;
    margin-right: auto;
}

.partner-run-btn {
    display: inline-block;
    background: #1a5c3a;
    color: #fff;
    padding: 16px 40px;
    font-size: 13px;
    font-weight: 800;
    letter-spacing: 1.5px;
    text-transform: uppercase;
    text-decoration: none;
    transition: background 0.25s, transform 0.25s;
}

.partner-run-btn:hover {
    background: #144d30;
    color: #fff;
    transform: translateY(-2px);
}

/* ===== EASY AS 1 2 3 ===== */
.partner-easy {
    padding: 80px 0;
    background: #fff;
}

.partner-easy-inner {
    max-width: 1300px;
    margin: 0 auto;
    padding: 0 80px;
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 80px;
    align-items: start;
}

.partner-easy-title {
    font-size: 38px;
    font-weight: 900;
    color: #252525;
    margin: 0 0 20px;
    line-height: 1.15;
    font-family: 'Public Sans', sans-serif;
}

.partner-easy-sub {
    font-size: 15px;
    color: #555;
    line-height: 1.75;
    margin: 0 0 32px;
}

.partner-easy-image {
    overflow: hidden;
}

/* Steps */
.partner-easy-right {
    display: flex;
    flex-direction: column;
    gap: 36px;
    padding-top: 8px;
}

.easy-step {
    display: flex;
    align-items: flex-start;
    gap: 28px;
}

.easy-step-content {
    flex: 1;
}

.easy-step-title {
    font-size: 20px;
    font-weight: 800;
    color: #252525;
    margin: 0 0 10px;
    font-family: 'Public Sans', sans-serif;
}

.easy-step-text {
    font-size: 14px;
    color: #666;
    line-height: 1.75;
    margin: 0;
}

.easy-step-image {
    overflow: hidden;
    flex-shrink: 0;
}

.easy-step-image .img-placeholder.square {
    min-height: 160px;
    width: 220px;
}

/* ===== WHY CHOOSE US ===== */
.partner-choose {
    padding: 80px 0 100px;
    background: #f7f7f7;
}

.partner-choose-inner {
    max-width: 1300px;
    margin: 0 auto;
    padding: 0 80px;
    text-align: center;
}

.partner-choose-title {
    font-size: 40px;
    font-weight: 900;
    color: #252525;
    margin: 0 0 16px;
    font-family: 'Public Sans', sans-serif;
}

.partner-choose-sub {
    font-size: 15px;
    color: #666;
    line-height: 1.75;
    margin: 0 auto 52px;
    max-width: 680px;
}

.partner-choose-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 24px;
    text-align: left;
}

.choose-card {
    background: #fff;
    padding: 36px 32px;
    border: 1px solid #eee;
    display: flex;
    flex-direction: column;
    gap: 16px;
    transition: box-shadow 0.25s, transform 0.25s;
}

.choose-card:hover {
    box-shadow: 0 8px 32px rgba(0,0,0,0.08);
    transform: translateY(-4px);
}

.choose-card-icon {
    width: 56px;
    height: 56px;
    border-radius: 50%;
    background: rgba(232,75,15,0.1);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 22px;
    color: #e84b0f;
    flex-shrink: 0;
}

.choose-card h3 {
    font-size: 17px;
    font-weight: 800;
    color: #252525;
    margin: 0;
    font-family: 'Public Sans', sans-serif;
    line-height: 1.3;
}

.choose-card p {
    font-size: 14px;
    color: #666;
    line-height: 1.75;
    margin: 0;
}

/* ===== BRING IDEAS CTA ===== */
.partner-ideas {
    position: relative;
    padding: 120px 24px;
    text-align: center;
    overflow: hidden;
}

.partner-ideas-bg {
    position: absolute;
    inset: 0;
    background: #b84012;
    /* Replace: background-image: url('/img/partner-ideas-bg.jpg'); background-size: cover; background-position: center; */
}

.partner-ideas-bg img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
}

.partner-ideas-overlay {
    position: absolute;
    inset: 0;
    background: rgba(180,60,15,0.85);
    z-index: 1;
}

.partner-ideas-inner {
    position: relative;
    z-index: 2;
    max-width: 700px;
    margin: 0 auto;
}

.partner-ideas-title {
    font-size: 42px;
    font-weight: 900;
    color: #fff;
    margin: 0 0 16px;
    font-family: 'Public Sans', sans-serif;
}

.partner-ideas-sub {
    font-size: 15px;
    color: rgba(255,255,255,0.85);
    line-height: 1.7;
    margin: 0 0 36px;
}

.partner-ideas-btn {
    display: inline-block;
    background: #1a5c3a;
    color: #fff;
    padding: 16px 40px;
    font-size: 12px;
    font-weight: 800;
    letter-spacing: 2px;
    text-transform: uppercase;
    text-decoration: none;
    transition: background 0.25s, transform 0.25s;
}

.partner-ideas-btn:hover {
    background: #144d30;
    color: #fff;
    transform: translateY(-2px);
}

/* ===== NEED HELP ===== */
.partner-help {
    position: relative;
    min-height: 480px;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
}

.partner-help-bg {
    position: absolute;
    inset: 0;
    background: #888;
    /* Replace: background-image: url('/img/partner-clothes-bg.jpg'); background-size: cover; background-position: center; */
}

.partner-help-bg img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
}

.partner-help-card {
    position: relative;
    z-index: 2;
    background: #f5a623;
    padding: 60px 80px;
    text-align: center;
    max-width: 780px;
    width: 90%;
}

.partner-help-title {
    font-size: 38px;
    font-weight: 900;
    color: #111;
    margin: 0 0 16px;
    font-family: 'Public Sans', sans-serif;
    line-height: 1.2;
}

.partner-help-text {
    font-size: 15px;
    color: #333;
    line-height: 1.75;
    margin: 0 0 32px;
    max-width: 520px;
    margin-left: auto;
    margin-right: auto;
}

.partner-help-btn {
    display: inline-block;
    background: #1a5c3a;
    color: #fff;
    padding: 16px 48px;
    font-size: 12px;
    font-weight: 800;
    letter-spacing: 2px;
    text-transform: uppercase;
    text-decoration: none;
    transition: background 0.25s, transform 0.25s;
}

.partner-help-btn:hover {
    background: #144d30;
    color: #fff;
    transform: translateY(-2px);
}

/* ===== RESPONSIVE ===== */
@media (max-width: 1024px) {
    .partner-why-grid {
        grid-template-columns: repeat(2, 1fr);
    }

    .partner-choose-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 768px) {
    .partner-hero-content {
        padding: 0 24px 36px;
    }

    .partner-hero-title {
        font-size: 36px;
    }

    .partner-grow-inner,
    .partner-easy-inner {
        grid-template-columns: 1fr;
        gap: 40px;
        padding: 0 24px;
    }

    .partner-why-inner {
        padding: 0 24px;
    }

    .partner-why-grid {
        grid-template-columns: 1fr;
    }

    .partner-why-title {
        font-size: 30px;
    }

    .partner-run-title,
    .partner-ideas-title {
        font-size: 28px;
    }

    .partner-easy-title {
        font-size: 28px;
    }

    .easy-step {
        flex-direction: column;
    }

    .easy-step-image .img-placeholder.square {
        width: 100%;
        min-height: 200px;
    }

    .partner-choose-inner {
        padding: 0 24px;
    }

    .partner-choose-grid {
        grid-template-columns: 1fr;
    }

    .partner-choose-title {
        font-size: 30px;
    }

    .partner-help-card {
        padding: 40px 24px;
    }

    .partner-help-title {
        font-size: 28px;
    }
}
</style>