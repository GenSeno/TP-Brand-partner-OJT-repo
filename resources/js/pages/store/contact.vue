<template>
    <Head title="Contact Us" />

    <div class="contact-page">

        <!-- Hero -->
        <section class="contact-hero">
            <div class="contact-hero-bg">
                <img src="/img/img-contact.png" alt="">
            </div>
            <div class="contact-hero-overlay"></div>
            <div class="contact-hero-content">
                <h1 class="contact-hero-title">Contact us</h1>
            </div>
        </section>

        <!-- Contact Info Cards -->
        <section class="contact-info-section">
            <div class="contact-inner">
                <div class="contact-info-grid">
                    <div class="contact-info-card">
                        <div class="contact-info-icon">
                            <i class="ri-map-pin-line"></i>
                        </div>
                        <p class="contact-info-label">ADDRESS</p>
                        <p class="contact-info-value">Davao City, Philippines</p>
                    </div>
                    <div class="contact-info-card">
                        <div class="contact-info-icon">
                            <i class="ri-mail-line"></i>
                        </div>
                        <p class="contact-info-label">EMAIL ADDRESS</p>
                        <p class="contact-info-value">tribupakarasph@gmail.com</p>
                    </div>
                    <div class="contact-info-card">
                        <div class="contact-info-icon">
                            <i class="ri-phone-line"></i>
                        </div>
                        <p class="contact-info-label">CONTACT NUMBER</p>
                        <p class="contact-info-value">0906 486 1393</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Contact Form -->
        <section class="contact-form-section">
            <div class="contact-inner">
                <h2 class="contact-form-title">Send Us a Message</h2>
                <p class="contact-form-subtitle">
                    Have something specific in mind? Fill out the form below and we'll get back to you as soon as possible.
                </p>
                <div class="contact-form">
                    <input type="text" class="contact-input" placeholder="Full Name" />
                    <input type="email" class="contact-input" placeholder="Email Address" />
                    <input type="text" class="contact-input" placeholder="Subject" />
                    <textarea class="contact-textarea" placeholder="Message" rows="6"></textarea>
                    <div class="contact-submit-wrap">
                        <button class="contact-submit-btn">SEND US MESSAGE</button>
                    </div>
                </div>
            </div>
        </section>
    </div>
</template>

<script setup>
import { Head } from '@inertiajs/vue3';
</script>

<style scoped>
.contact-page {
    font-family: 'Public Sans', sans-serif;
    background: #fff;
    min-height: 100vh;
}

/* Hero */
.contact-hero {
    position: relative;
    height: 450px;
    display: flex;
    align-items: flex-end;
    overflow: hidden;
}

.contact-hero-bg {
    position: absolute;
    inset: 0;
    background: #3a5c3a;
}

.contact-hero-bg img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
}

.contact-hero-overlay {
    position: absolute;
    inset: 0;
    background: rgba(0,0,0,0.4);
    z-index: 1;
}

.contact-hero-content {
    position: relative;
    z-index: 2;
    padding: 0 60px 36px;
}

.contact-hero-title {
    font-size: 64px;
    font-weight: 800;
    text-align: left;
    color: #ffffff;
    letter-spacing: -1px;
    line-height: 1.2;
    font-family: 'Poppins', sans-serif;
    display: block;
    width: 100%;
}

/* Inner container */
.contact-inner {
    max-width: 900px;
    margin: 0 auto;
    padding: 0 24px;
}

/* Info Cards */
.contact-info-section {
    padding: 60px 0 40px;
}

.contact-info-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 20px;
}

.contact-info-card {
    background: #f7f7f7;
    padding: 32px 20px;
    text-align: center;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 8px;
}

.contact-info-icon {
    width: 52px;
    height: 52px;
    border-radius: 50%;
    border: 1.5px solid #e84b0f;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 4px;
}

.contact-info-icon i {
    font-size: 20px;
    color: #e84b0f;
}

.contact-info-label {
    font-size: 11px;
    font-weight: 700;
    color: #888;
    letter-spacing: 1px;
    margin: 0;
    text-transform: uppercase;
}

.contact-info-value {
    font-size: 13px;
    color: #333;
    margin: 0;
    font-weight: 500;
}

/* Form */
.contact-form-section {
    padding: 50px 0 70px;
    text-align: center;
}

.contact-form-title {
    font-size: 28px;
    font-weight: 800;
    color: #111;
    margin: 0 0 10px;
    font-family: 'Public Sans', sans-serif;
}

.contact-form-subtitle {
    font-size: 13px;
    color: #666;
    margin: 0 0 36px;
    line-height: 1.6;
}

.contact-form {
    display: flex;
    flex-direction: column;
    gap: 14px;
    text-align: left;
}

.contact-input,
.contact-textarea {
    width: 100%;
    padding: 14px 16px;
    border: 1.5px solid #e0e0e0;
    background: #fff;
    font-size: 13px;
    font-family: 'Public Sans', sans-serif;
    color: #333;
    outline: none;
    border-radius: 0;
    transition: border-color 0.2s;
    resize: vertical;
}

.contact-input:focus,
.contact-textarea:focus {
    border-color: #111;
}

.contact-input::placeholder,
.contact-textarea::placeholder {
    color: #aaa;
}

.contact-submit-wrap {
    display: flex;
    justify-content: center;
    margin-top: 8px;
}

.contact-submit-btn {
    background: #1a5c3a;
    color: #fff;
    border: none;
    padding: 16px 48px;
    font-size: 13px;
    font-weight: 800;
    letter-spacing: 2px;
    text-transform: uppercase;
    cursor: pointer;
    font-family: 'Public Sans', sans-serif;
    transition: all 0.25s;
}

.contact-submit-btn:hover {
    background: #144d30;
    transform: translateY(-1px);
}


</style>