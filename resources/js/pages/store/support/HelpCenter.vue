<template>
  <Head title="Help Center" />

  <div class="support-page">
    <section class="support-hero">
      <div class="support-hero-bg">
        <img src="/img/img-about1.png" alt="Help Center" />
      </div>
      <div class="support-hero-overlay"></div>
      <div class="support-hero-content">
        <div class="breadcrumb-wrapper">
          <Breadcrumb :items="breadcrumbItems" />
        </div>
        <h1 class="support-hero-title">Help Center</h1>
        <p class="support-hero-subtitle">How can we help you today?</p>
      </div>
    </section>

    <section class="support-content">
      <div class="support-inner">
        <div class="help-search">
          <i class="ri-search-line"></i>
          <input v-model="searchQuery" type="text" placeholder="Search for help topics..." class="help-search-input" />
        </div>

        <div class="help-categories">
          <div
            v-for="cat in filteredCategories"
            :key="cat.title"
            class="help-card"
          >
            <div class="help-card-icon">
              <i :class="cat.icon"></i>
            </div>
            <h3 class="help-card-title">{{ cat.title }}</h3>
            <ul class="help-card-links">
              <li v-for="link in cat.links" :key="link.label">
                <Link v-if="link.route" :href="route(link.route)">{{ link.label }}</Link>
                <a v-else-if="link.href" :href="link.href" target="_blank" rel="noopener">{{ link.label }}</a>
                <span v-else>{{ link.label }}</span>
              </li>
            </ul>
          </div>
        </div>

        <div class="help-contact-banner">
          <h3>Can't find what you're looking for?</h3>
          <p>Our support team is ready to assist you.</p>
          <div class="help-contact-actions">
            <Link :href="route('store.brand-partner.support.ticket')" class="help-btn help-btn-primary">
              <i class="ri-ticket-2-line"></i> Submit a Ticket
            </Link>
            <Link :href="route('store.brand-partner.support.live-chat')" class="help-btn help-btn-outline">
              <i class="ri-messenger-line"></i> Live Chat
            </Link>
          </div>
        </div>
      </div>
    </section>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import Breadcrumb from '@/components/breadcrumb/layout-breadcrumb.vue';

defineProps({ brandPartner: Object });

const breadcrumbItems = [{ label: 'Help Center' }];
const searchQuery = ref('');

const categories = [
  {
    title: 'Orders & Shipping',
    icon: 'ri-shopping-bag-3-line',
    links: [
      { label: 'How do I track my order?', route: 'store.brand-partner.faq' },
      { label: 'Shipping Policy', route: 'store.brand-partner.shipping' },
      { label: 'How do I cancel an order?', route: 'store.brand-partner.support.ticket' },
    ],
  },
  {
    title: 'Returns & Refunds',
    icon: 'ri-arrow-go-back-line',
    links: [
      { label: 'Return Policy', route: 'store.brand-partner.refund' },
      { label: 'Start a Return', route: 'store.brand-partner.support.returns' },
      { label: 'Refund Policy', route: 'store.brand-partner.refund' },
    ],
  },
  {
    title: 'Warranty',
    icon: 'ri-shield-check-line',
    links: [
      { label: 'Warranty Policy', route: 'store.brand-partner.warranty' },
      { label: 'File a Warranty Claim', route: 'store.brand-partner.support.warranty-claims' },
    ],
  },
  {
    title: 'Account & Payments',
    icon: 'ri-user-settings-line',
    links: [
      { label: 'Payment Policy', route: 'store.brand-partner.payment' },
      { label: 'Cancellation Policy', route: 'store.brand-partner.cancellation' },
      { label: 'Contact Support', route: 'store.brand-partner.support.ticket' },
    ],
  },
  {
    title: 'Find a Store',
    icon: 'ri-map-pin-2-line',
    links: [
      { label: 'Store Locator', route: 'store.brand-partner.support.store-locator' },
    ],
  },
  {
    title: 'Live Chat',
    icon: 'ri-messenger-line',
    links: [
        { label: 'Chat with us on Facebook Messenger', href: 'https://www.facebook.com/TribuPakarasOutdoor' },
      { label: 'More contact options', route: 'store.brand-partner.support.live-chat' },
    ],
  },
];

const filteredCategories = computed(() => {
  if (!searchQuery.value.trim()) return categories;
  const q = searchQuery.value.toLowerCase();
  return categories.filter(
    (cat) =>
      cat.title.toLowerCase().includes(q) ||
      cat.links.some((l) => l.label.toLowerCase().includes(q)),
  );
});
</script>

<style scoped>
.support-page {
  font-family: 'Public Sans', sans-serif;
  background: #fff;
  min-height: 100vh;
}

.support-hero {
  position: relative;
  height: 380px;
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
  max-width: 1100px;
  margin: 0 auto;
  padding: 0 24px;
}

/* Search */
.help-search {
  display: flex;
  align-items: center;
  gap: 12px;
  background: #f5f5f5;
  border: 1px solid #e0e0e0;
  padding: 14px 20px;
  margin-bottom: 40px;
}

.help-search i {
  font-size: 18px;
  color: #999;
}

.help-search-input {
  flex: 1;
  border: none;
  background: transparent;
  font-size: 15px;
  outline: none;
  font-family: 'Public Sans', sans-serif;
  color: #333;
}

.help-search-input::placeholder {
  color: #aaa;
}

/* Cards Grid */
.help-categories {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 24px;
  margin-bottom: 50px;
}

.help-card {
  background: #f9f9f9;
  padding: 28px 24px;
  border-radius: 10px;
  transition: box-shadow 0.2s, transform 0.2s;
}

.help-card:hover {
  box-shadow: 0 4px 16px rgba(0, 0, 0, 0.08);
  transform: translateY(-2px);
}

.help-card-icon {
  width: 48px;
  height: 48px;
  border-radius: 50%;
  background: #fff3e0;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-bottom: 14px;
}

.help-card-icon i {
  font-size: 20px;
  color: #ff9505;
}

.help-card-title {
  font-size: 16px;
  font-weight: 700;
  color: #111;
  margin: 0 0 12px;
}

.help-card-links {
  list-style: none;
  padding: 0;
  margin: 0;
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.help-card-links li a,
.help-card-links li span {
  font-size: 13px;
  color: #555;
  text-decoration: none;
  transition: color 0.2s;
  cursor: pointer;
}

.help-card-links li a:hover {
  color: #ff9505;
}

/* Contact Banner */
.help-contact-banner {
  text-align: center;
  background: #f5f5f5;
  padding: 48px 32px;
  border-radius: 10px;
}

.help-contact-banner h3 {
  font-size: 22px;
  font-weight: 700;
  color: #111;
  margin: 0 0 8px;
}

.help-contact-banner p {
  font-size: 14px;
  color: #777;
  margin: 0 0 24px;
}

.help-contact-actions {
  display: flex;
  gap: 12px;
  justify-content: center;
}

.help-btn {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 12px 24px;
  font-size: 13px;
  font-weight: 700;
  text-decoration: none;
  border-radius: 8px;
  transition: all 0.2s;
  cursor: pointer;
}

.help-btn-primary {
  background: #ff9505;
  color: #fff;
  border: 2px solid #ff9505;
}

.help-btn-primary:hover {
  background: #e88600;
  border-color: #e88600;
}

.help-btn-outline {
  background: transparent;
  color: #198754;
  border: 2px solid #198754;
}

.help-btn-outline:hover {
  background: #198754;
  color: #fff;
}

@media (max-width: 991px) {
  .help-categories {
    grid-template-columns: repeat(2, 1fr);
  }
}

@media (max-width: 575px) {
  .support-hero-title {
    font-size: 32px;
  }

  .help-categories {
    grid-template-columns: 1fr;
  }

  .help-contact-actions {
    flex-direction: column;
    align-items: center;
  }
}
</style>
