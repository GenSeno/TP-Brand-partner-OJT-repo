<template>
  <Head title="Live Chat" />

  <div class="support-page">
    <section class="support-hero">
      <div class="support-hero-bg">
        <img src="/img/img-about1.png" alt="Live Chat" />
      </div>
      <div class="support-hero-overlay"></div>
      <div class="support-hero-content">
        <div class="breadcrumb-wrapper">
          <Breadcrumb :items="breadcrumbItems" />
        </div>
        <h1 class="support-hero-title">Live Chat</h1>
        <p class="support-hero-subtitle">Chat with us in real time</p>
      </div>
    </section>

    <section class="support-content">
      <div class="support-inner">
        <div class="chat-card">
          <div class="chat-icon-wrap">
            <i class="ri-messenger-line"></i>
          </div>
          <h2 class="chat-title">Chat with us on Facebook Messenger</h2>
          <p class="chat-description">
            Our support team is available on Facebook Messenger. Click the button below to start a conversation with <strong>Tribu Pakaras</strong>.
          </p>
          <a
            href="https://www.facebook.com/TribuPakarasOutdoor"
            target="_blank"
            rel="noopener"
            class="messenger-btn"
          >
            <svg class="messenger-icon" viewBox="0 0 24 24" width="20" height="20" fill="currentColor">
              <path d="M12 2C6.36 2 2 6.13 2 11.7c0 2.91 1.2 5.42 3.15 7.14V22l2.91-1.6c.83.23 1.7.35 2.6.35h.34c5.44-.04 9.8-4.36 9.8-9.8C21.6 6.13 17.64 2 12 2zm1.07 12.86l-2.54-2.72-4.96 2.72 5.46-5.8 2.6 2.72 4.9-2.72-5.46 5.8z"/>
            </svg>
            Open Messenger
          </a>
        </div>

        <div class="chat-hours">
          <h3>Support Hours</h3>
          <p>Monday – Saturday: 9:00 AM – 6:00 PM (PHT)</p>
          <p>Sunday: Closed</p>
        </div>

        <div class="chat-alternatives">
          <h3>Other Ways to Reach Us</h3>
          <div class="alt-grid">
            <Link :href="route('store.brand-partner.support.ticket')" class="alt-card">
              <i class="ri-ticket-2-line"></i>
              <span>Submit a Ticket</span>
            </Link>
            <Link :href="route('store.brand-partner.contact')" class="alt-card">
              <i class="ri-mail-line"></i>
              <span>Email Us</span>
            </Link>
            <Link :href="route('store.brand-partner.support.help')" class="alt-card">
              <i class="ri-question-line"></i>
              <span>Help Center</span>
            </Link>
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
  { label: 'Live Chat' },
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
  max-width: 700px;
  margin: 0 auto;
  padding: 0 24px;
  display: flex;
  flex-direction: column;
  gap: 32px;
}

/* Main Chat Card */
.chat-card {
  text-align: center;
  background: #f9f9f9;
  padding: 48px 32px;
  border-radius: 12px;
}

.chat-icon-wrap {
  width: 80px;
  height: 80px;
  border-radius: 50%;
  background: linear-gradient(135deg, #0078ff, #00c6ff);
  display: inline-flex;
  align-items: center;
  justify-content: center;
  margin-bottom: 20px;
}

.chat-icon-wrap i {
  font-size: 36px;
  color: #fff;
}

.chat-title {
  font-size: 22px;
  font-weight: 700;
  color: #111;
  margin: 0 0 12px;
}

.chat-description {
  font-size: 14px;
  color: #666;
  line-height: 1.7;
  margin: 0 0 24px;
  max-width: 500px;
  margin-left: auto;
  margin-right: auto;
}

.messenger-btn {
  display: inline-flex;
  align-items: center;
  gap: 10px;
  padding: 14px 32px;
  background: #0078ff;
  color: #fff;
  text-decoration: none;
  border-radius: 8px;
  font-size: 15px;
  font-weight: 700;
  transition: background 0.2s;
}

.messenger-btn:hover {
  background: #0062cc;
}

.messenger-icon {
  flex-shrink: 0;
}

/* Hours */
.chat-hours {
  text-align: center;
  padding: 24px;
  background: #f5f5f5;
  border-radius: 8px;
}

.chat-hours h3 {
  font-size: 16px;
  font-weight: 700;
  color: #111;
  margin: 0 0 8px;
}

.chat-hours p {
  font-size: 14px;
  color: #666;
  margin: 0;
  line-height: 1.6;
}

/* Alternatives */
.chat-alternatives h3 {
  font-size: 18px;
  font-weight: 700;
  color: #111;
  margin: 0 0 16px;
  text-align: center;
}

.alt-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 16px;
}

.alt-card {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 10px;
  padding: 24px 16px;
  background: #f9f9f9;
  border-radius: 8px;
  text-decoration: none;
  transition: all 0.2s;
}

.alt-card:hover {
  background: #fff3e0;
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
}

.alt-card i {
  font-size: 24px;
  color: #ff9505;
}

.alt-card span {
  font-size: 13px;
  font-weight: 600;
  color: #333;
  text-align: center;
}

@media (max-width: 575px) {
  .support-hero-title {
    font-size: 32px;
  }
  .alt-grid {
    grid-template-columns: 1fr;
  }
}
</style>
