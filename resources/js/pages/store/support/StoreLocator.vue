<template>
  <Head title="Store Locator" />

  <div class="support-page">
    <section class="support-hero">
      <div class="support-hero-bg">
        <img src="/img/img-about1.png" alt="Store Locator" />
      </div>
      <div class="support-hero-overlay"></div>
      <div class="support-hero-content">
        <div class="breadcrumb-wrapper">
          <Breadcrumb :items="breadcrumbItems" />
        </div>
        <h1 class="support-hero-title">Store Locator</h1>
        <p class="support-hero-subtitle">Find us in Davao City</p>
      </div>
    </section>

    <section class="support-content">
      <div class="support-inner">
        <div class="store-info-grid">
          <div class="store-main">
            <div class="store-card">
              <div class="store-card-header">
                <i class="ri-store-2-line"></i>
                <h3>Tribu Pakaras Store</h3>
              </div>
              <div class="store-details">
                <div class="store-detail-row">
                  <i class="ri-map-pin-line"></i>
                  <div>
                    <p class="detail-label">Address</p>
                    <p class="detail-value">21 Virgo Gsis Matina, Davao City, Philippines, 8000</p>
                  </div>
                </div>
                <div class="store-detail-row">
                  <i class="ri-phone-line"></i>
                  <div>
                    <p class="detail-label">Phone</p>
                    <p class="detail-value">0906 486 1393</p>
                  </div>
                </div>
                <div class="store-detail-row">
                  <i class="ri-mail-line"></i>
                  <div>
                    <p class="detail-label">Email</p>
                    <p class="detail-value">tribupakarasph@gmail.com</p>
                  </div>
                </div>
                <div class="store-detail-row">
                  <i class="ri-time-line"></i>
                  <div>
                    <p class="detail-label">Business Hours</p>
                    <p class="detail-value">Monday – Saturday, 9:00 AM – 6:00 PM</p>
                  </div>
                </div>
              </div>
            </div>

            <div class="store-card">
              <div class="store-card-header">
                <i class="ri-road-map-line"></i>
                <h3>How to Get Here</h3>
              </div>
              <div class="store-directions">
                <p>Our store is located in Davao City. For exact directions and our current pop-up locations, please contact us directly or message us on Facebook Messenger.</p>
                <a href="https://www.facebook.com/TribuPakarasOutdoor" target="_blank" rel="noopener" class="directions-btn">
                  <svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor" style="flex-shrink:0">
                    <path d="M12 2C6.36 2 2 6.13 2 11.7c0 2.91 1.2 5.42 3.15 7.14V22l2.91-1.6c.83.23 1.7.35 2.6.35h.34c5.44-.04 9.8-4.36 9.8-9.8C21.6 6.13 17.64 2 12 2zm1.07 12.86l-2.54-2.72-4.96 2.72 5.46-5.8 2.6 2.72 4.9-2.72-5.46 5.8z"/>
                  </svg>
                  Ask for Directions on Messenger
                </a>
              </div>
            </div>
          </div>

          <div class="store-sidebar">
            <div class="store-card">
              <div class="store-card-header">
                <i class="ri-question-line"></i>
                <h3>Need Help?</h3>
              </div>
              <div class="store-sidebar-links">
                <Link :href="route('store.brand-partner.support.live-chat')" class="sidebar-link">
                  <i class="ri-messenger-line"></i>
                  <span>Chat with us</span>
                </Link>
                <Link :href="route('store.brand-partner.support.ticket')" class="sidebar-link">
                  <i class="ri-ticket-2-line"></i>
                  <span>Submit a ticket</span>
                </Link>
                <Link :href="route('store.brand-partner.contact')" class="sidebar-link">
                  <i class="ri-mail-line"></i>
                  <span>Contact page</span>
                </Link>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3';
import Breadcrumb from '@/components/breadcrumb/layout-breadcrumb.vue';

defineProps({ brandPartner: Object });

const breadcrumbItems = [
  { label: 'Help Center', link: route('store.brand-partner.support.help') },
  { label: 'Store Locator' },
];
</script>

<style scoped>
.support-page {
  font-family: 'Public Sans', sans-serif;
  background: #fff;
  min-height: 100vh;
}

.support-hero {
  position: relative;
  height: 340px;
  padding-top: 80px;
  display: flex;
  align-items: center;
  justify-content: center;
  text-align: center;
  overflow: hidden;
}

.support-hero-bg {
  position: absolute;
  inset: 0;
}

.support-hero-bg img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.support-hero-overlay {
  position: absolute;
  inset: 0;
  background: rgba(0, 0, 0, 0.55);
  z-index: 1;
}

.support-hero-content {
  position: relative;
  z-index: 2;
  max-width: 600px;
  padding: 0 24px;
}

.support-hero-content :deep(.breadcrumb) {
  justify-content: center;
}
.support-hero-content :deep(.breadcrumb-item),
.support-hero-content :deep(.breadcrumb-item:not(:last-child)::after),
.support-hero-content :deep(.breadcrumb-home),
.support-hero-content :deep(.breadcrumb-item a),
.support-hero-content :deep(.breadcrumb-item.active),
.support-hero-content :deep(.breadcrumb-item span) {
  color: #ffffff;
}
.support-hero-content :deep(.breadcrumb-home:hover),
.support-hero-content :deep(.breadcrumb-item a:hover) {
  color: #ff9505;
  text-decoration: none;
}

.support-hero-title {
  font-size: 48px;
  font-weight: 800;
  color: #fff;
  margin: 12px 0 8px;
  font-family: 'Poppins', sans-serif;
}

.support-hero-subtitle {
  font-size: 16px;
  color: rgba(255, 255, 255, 0.8);
  margin: 0;
}

.support-content {
  padding: 60px 0 80px;
}

.support-inner {
  max-width: 1000px;
  margin: 0 auto;
  padding: 0 24px;
}

/* Layout */
.store-info-grid {
  display: grid;
  grid-template-columns: 2fr 1fr;
  gap: 24px;
  align-items: start;
}

.store-main {
  display: flex;
  flex-direction: column;
  gap: 24px;
}

/* Cards */
.store-card {
  background: #f9f9f9;
  border-radius: 10px;
  padding: 28px 24px;
}

.store-card-header {
  display: flex;
  align-items: center;
  gap: 10px;
  margin-bottom: 20px;
}

.store-card-header i {
  font-size: 22px;
  color: #ff9505;
}

.store-card-header h3 {
  font-size: 17px;
  font-weight: 700;
  color: #111;
  margin: 0;
}

/* Details */
.store-details {
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.store-detail-row {
  display: flex;
  gap: 14px;
  align-items: flex-start;
}

.store-detail-row > i {
  font-size: 18px;
  color: #ff9505;
  margin-top: 2px;
  flex-shrink: 0;
}

.detail-label {
  font-size: 11px;
  font-weight: 700;
  color: #999;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  margin: 0 0 2px;
}

.detail-value {
  font-size: 14px;
  color: #333;
  margin: 0;
  font-weight: 500;
}

/* Directions */
.store-directions p {
  font-size: 14px;
  color: #555;
  line-height: 1.7;
  margin: 0 0 16px;
}

.directions-btn {
  display: inline-flex;
  align-items: center;
  gap: 10px;
  padding: 12px 24px;
  background: #0078ff;
  color: #fff;
  text-decoration: none;
  border-radius: 8px;
  font-size: 13px;
  font-weight: 700;
  transition: background 0.2s;
}

.directions-btn:hover {
  background: #0062cc;
}

/* Sidebar */
.store-sidebar-links {
  display: flex;
  flex-direction: column;
  gap: 10px;
}

.sidebar-link {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 12px 14px;
  background: #fff;
  border: 1px solid #e8e8e8;
  border-radius: 8px;
  text-decoration: none;
  transition: all 0.2s;
}

.sidebar-link:hover {
  border-color: #ff9505;
  background: #fff8e1;
}

.sidebar-link i {
  font-size: 18px;
  color: #ff9505;
}

.sidebar-link span {
  font-size: 13px;
  font-weight: 600;
  color: #333;
}

@media (max-width: 768px) {
  .store-info-grid {
    grid-template-columns: 1fr;
  }
}

@media (max-width: 575px) {
  .support-hero-title {
    font-size: 32px;
  }
}
</style>
