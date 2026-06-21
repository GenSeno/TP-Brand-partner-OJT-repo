<template>
  <div v-if="!dismissed" class="cookie-consent-overlay">
    <div class="cookie-consent-banner">
      <div class="cookie-content">
        <div class="cookie-icon">
          <i class="ri-shield-check-line"></i>
        </div>
        <div class="cookie-text">
          <h4>We value your privacy</h4>
          <p>
            We use cookies to enhance your browsing experience, serve personalized ads or content,
            and analyze our traffic. By clicking "Accept All", you consent to our use of cookies.
            See our
            <Link :href="cookiePolicyUrl" class="cookie-link">Cookie Policy</Link> for details.
          </p>
        </div>
      </div>
      <div class="cookie-actions">
        <button class="cookie-btn cookie-btn-secondary" @click="acceptNecessary">
          Accept Necessary
        </button>
        <button class="cookie-btn cookie-btn-primary" @click="acceptAll">
          Accept All
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import { usePage } from '@inertiajs/vue3';

const page = usePage();
const brandPartner = computed(() => page.props.brandPartner);
const cookiePolicyUrl = computed(() =>
  brandPartner.value
    ? route('store.brand-partner.cookies', brandPartner.value.slug)
    : '/cookies'
);

const COOKIE_CONSENT_KEY = 'tpink_cookie_consent';

const dismissed = ref(localStorage.getItem(COOKIE_CONSENT_KEY) !== null);

const acceptAll = () => {
  localStorage.setItem(COOKIE_CONSENT_KEY, 'all');
  dismissed.value = true;
};

const acceptNecessary = () => {
  localStorage.setItem(COOKIE_CONSENT_KEY, 'necessary');
  dismissed.value = true;
};
</script>

<style scoped>
.cookie-consent-overlay {
  position: fixed;
  bottom: 0;
  left: 0;
  right: 0;
  z-index: 999999;
  padding: 16px;
  pointer-events: none;
}

.cookie-consent-banner {
  max-width: 800px;
  margin: 0 auto;
  background: #1a1a1a;
  color: #e0e0e0;
  border-radius: 16px;
  padding: 24px 28px;
  box-shadow: 0 -4px 24px rgba(0, 0, 0, 0.3);
  display: flex;
  align-items: center;
  gap: 24px;
  pointer-events: auto;
  border: 1px solid rgba(255, 255, 255, 0.06);
}

.cookie-content {
  display: flex;
  align-items: flex-start;
  gap: 16px;
  flex: 1;
  min-width: 0;
}

.cookie-icon {
  flex-shrink: 0;
  width: 40px;
  height: 40px;
  background: rgba(255, 149, 5, 0.15);
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 20px;
  color: #ff9505;
}

.cookie-text h4 {
  font-size: 14px;
  font-weight: 700;
  color: #fff;
  margin: 0 0 6px;
  font-family: 'Poppins', 'Public Sans', sans-serif;
}

.cookie-text p {
  font-size: 12px;
  line-height: 1.6;
  color: #aaa;
  margin: 0;
}

.cookie-link {
  color: #ff9505;
  text-decoration: none;
  font-weight: 600;
}

.cookie-link:hover {
  text-decoration: underline;
}

.cookie-actions {
  display: flex;
  gap: 10px;
  flex-shrink: 0;
}

.cookie-btn {
  padding: 10px 20px;
  border-radius: 8px;
  font-size: 12px;
  font-weight: 700;
  font-family: 'Public Sans', sans-serif;
  cursor: pointer;
  border: none;
  transition: all 0.2s;
  white-space: nowrap;
}

.cookie-btn-primary {
  background: #ff9505;
  color: #fff;
}

.cookie-btn-primary:hover {
  background: #e08500;
}

.cookie-btn-secondary {
  background: rgba(255, 255, 255, 0.08);
  color: #ccc;
  border: 1px solid rgba(255, 255, 255, 0.1);
}

.cookie-btn-secondary:hover {
  background: rgba(255, 255, 255, 0.14);
  color: #fff;
}

@media (max-width: 768px) {
  .cookie-consent-banner {
    flex-direction: column;
    padding: 20px;
    gap: 16px;
  }

  .cookie-content {
    flex-direction: column;
    align-items: center;
    text-align: center;
  }

  .cookie-actions {
    width: 100%;
  }

  .cookie-btn {
    flex: 1;
    text-align: center;
    font-size: 11px;
    padding: 10px 14px;
  }
}
</style>
