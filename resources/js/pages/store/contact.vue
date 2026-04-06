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
                <p class="contact-breadcrumb">Home › Contact Us</p>
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

        <!-- Footer -->
        <footer class="contact-footer">
            <div class="contact-footer-inner">
                <div class="footer-brand">
                    <div class="footer-logo">
                        <!-- Replace with: <img src="/img/logo-white.png" alt="Tribu Pakaras"> -->
                        <span class="footer-logo-text">✦ TRIBU<br>PAKARAS</span>
                    </div>
                    <p class="footer-tagline">Pakaras /pa-kah-ras/<br><em>adjective</em></p>
                    <p class="footer-tagline-desc">
                        Showing fearlessness and determination without thinking or caring about the
                        probable consequences of their action.
                    </p>
                </div>

                <div class="footer-col">
                    <h5 class="footer-col-title">Sitemap</h5>
                    <ul>
                        <li><a href="#">Shop</a></li>
                        <li><a href="#">Collections</a></li>
                        <li><a href="#">Header 1</a></li>
                        <li><a href="#">Header 2</a></li>
                        <li><a href="#">Header 3</a></li>
                    </ul>
                </div>

                <div class="footer-col">
                    <h5 class="footer-col-title">Account</h5>
                    <ul>
                        <li><a href="#">Wishlist</a></li>
                        <li><a href="#">Cart</a></li>
                    </ul>
                </div>

                <div class="footer-col">
                    <h5 class="footer-col-title">Contact Information</h5>
                    <ul class="footer-contact-list">
                        <li><i class="ri-map-pin-line"></i> Davao City, Philippines</li>
                        <li><i class="ri-mail-line"></i> tribupakarasph@gmail.com</li>
                        <li><i class="ri-phone-line"></i> 0906 486 1393</li>
                    </ul>
                    <h5 class="footer-col-title mt-3">Social</h5>
                    <div class="footer-socials">
                        <a href="#"><i class="ri-facebook-fill"></i></a>
                        <a href="#"><i class="ri-tiktok-fill"></i></a>
                        <a href="#"><i class="ri-youtube-fill"></i></a>
                        <a href="#"><i class="ri-instagram-line"></i></a>
                    </div>
                </div>

                <div class="footer-col footer-newsletter">
                    <h5 class="footer-col-title">Be among the first to experience it.</h5>
                    <p>From rugged trails to everyday runs, we've got you covered.</p>
                    <div class="footer-subscribe">
                        <input type="email" placeholder="Enter Address" />
                        <button>SUBSCRIBE</button>
                    </div>
                </div>
            </div>
            <div class="footer-bottom">
                <p>All right reserved 2026.</p>
            </div>
        </footer>

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
    height: 300px;
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

.contact-breadcrumb {
    font-size: 12px;
    color: rgba(255,255,255,0.6);
    margin: 0 0 8px;
}

.contact-hero-title {
    font-size: 38px;
    font-weight: 900;
    color: #fff;
    margin: 0;
    font-family: 'Public Sans', sans-serif;
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

/* Footer */
.contact-footer {
    background: #111;
    padding: 60px 0 0;
}

.contact-footer-inner {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 40px;
    display: grid;
    grid-template-columns: 2fr 1fr 1fr 1.5fr 1.5fr;
    gap: 40px;
}

.footer-logo-text {
    font-size: 18px;
    font-weight: 900;
    color: #fff;
    line-height: 1.2;
    display: block;
    margin-bottom: 16px;
}

.footer-tagline {
    font-size: 12px;
    color: rgba(255,255,255,0.5);
    margin: 0 0 6px;
    line-height: 1.5;
}

.footer-tagline-desc {
    font-size: 12px;
    color: rgba(255,255,255,0.4);
    line-height: 1.6;
    margin: 0;
}

.footer-col-title {
    font-size: 13px;
    font-weight: 700;
    color: #fff;
    margin: 0 0 16px;
    font-family: 'Public Sans', sans-serif;
}

.footer-col ul {
    list-style: none;
    padding: 0;
    margin: 0;
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.footer-col ul li a {
    font-size: 13px;
    color: rgba(255,255,255,0.5);
    text-decoration: none;
    transition: color 0.2s;
}

.footer-col ul li a:hover {
    color: #fff;
}

.footer-contact-list {
    list-style: none;
    padding: 0;
    margin: 0;
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.footer-contact-list li {
    font-size: 12px;
    color: rgba(255,255,255,0.5);
    display: flex;
    align-items: center;
    gap: 8px;
}

.footer-contact-list li i {
    color: #e84b0f;
    font-size: 14px;
}

.mt-3 {
    margin-top: 24px !important;
}

.footer-socials {
    display: flex;
    gap: 12px;
}

.footer-socials a {
    width: 36px;
    height: 36px;
    border: 1px solid rgba(255,255,255,0.2);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: rgba(255,255,255,0.6);
    font-size: 16px;
    text-decoration: none;
    transition: all 0.2s;
}

.footer-socials a:hover {
    border-color: #e84b0f;
    color: #e84b0f;
}

.footer-newsletter p {
    font-size: 12px;
    color: rgba(255,255,255,0.5);
    margin: 0 0 14px;
    line-height: 1.6;
}

.footer-subscribe {
    display: flex;
    gap: 0;
}

.footer-subscribe input {
    flex: 1;
    padding: 10px 14px;
    border: 1px solid rgba(255,255,255,0.15);
    background: rgba(255,255,255,0.05);
    color: #fff;
    font-size: 12px;
    outline: none;
    font-family: 'Public Sans', sans-serif;
}

.footer-subscribe input::placeholder {
    color: rgba(255,255,255,0.3);
}

.footer-subscribe button {
    background: #e84b0f;
    color: #fff;
    border: none;
    padding: 10px 16px;
    font-size: 11px;
    font-weight: 700;
    letter-spacing: 1px;
    cursor: pointer;
    font-family: 'Public Sans', sans-serif;
    white-space: nowrap;
    transition: background 0.2s;
}

.footer-subscribe button:hover {
    background: #d04010;
}

.footer-bottom {
    margin-top: 48px;
    border-top: 1px solid rgba(255,255,255,0.08);
    padding: 18px 40px;
    text-align: center;
}

.footer-bottom p {
    font-size: 12px;
    color: rgba(255,255,255,0.3);
    margin: 0;
}

/* Responsive */
@media (max-width: 991px) {
    .contact-footer-inner {
        grid-template-columns: 1fr 1fr;
        gap: 32px;
    }
}

@media (max-width: 768px) {
    .contact-hero-content {
        padding: 0 24px 28px;
    }

    .contact-hero-title {
        font-size: 28px;
    }

    .contact-info-grid {
        grid-template-columns: 1fr;
    }

    .contact-footer-inner {
        grid-template-columns: 1fr;
        padding: 0 24px;
    }
}
</style>