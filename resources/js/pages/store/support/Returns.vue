<template>
  <Head title="Returns Portal" />

  <div class="support-page">
    <section class="support-hero">
      <div class="support-hero-bg">
        <img src="/img/img-about1.png" alt="Returns Portal" />
      </div>
      <div class="support-hero-overlay"></div>
      <div class="support-hero-content">
        <div class="breadcrumb-wrapper">
          <Breadcrumb :items="breadcrumbItems" />
        </div>
        <h1 class="support-hero-title">Returns Portal</h1>
        <p class="support-hero-subtitle">Request a return or exchange for your order</p>
      </div>
    </section>

    <section class="support-content">
      <div class="support-inner">
        <div class="returns-policy-summary">
          <h3><i class="ri-information-line"></i> Return Policy Summary</h3>
          <ul>
            <li>Returns must be initiated within <strong>30 days</strong> of delivery.</li>
            <li>Items must be unused, unworn, and have original tags attached.</li>
            <li>Final sale and personalized items are not eligible for returns.</li>
          </ul>
          <Link :href="route('store.brand-partner.refund')" class="policy-link">Read Full Refund Policy <i class="ri-arrow-right-s-line"></i></Link>
        </div>

        <div v-if="submitted" class="ticket-success">
          <div class="ticket-success-icon"><i class="ri-check-line"></i></div>
          <h3>Return Request Submitted!</h3>
          <p>We've received your return request. Our team will review it and send you return instructions via email within 1-2 business days.</p>
          <Link :href="route('store.brand-partner.support.help')" class="help-btn help-btn-primary">Back to Help Center</Link>
        </div>

        <form v-else class="ticket-form" @submit.prevent="submitReturn">
          <div class="ticket-form-grid">
            <div class="form-group">
              <label class="form-label">Full Name <span class="required">*</span></label>
              <input v-model="form.name" type="text" class="form-input" :class="{ 'input-error': form.errors.name }" placeholder="Your full name" required />
              <span v-if="form.errors.name" class="error-text">{{ form.errors.name }}</span>
            </div>
            <div class="form-group">
              <label class="form-label">Email <span class="required">*</span></label>
              <input v-model="form.email" type="email" class="form-input" :class="{ 'input-error': form.errors.email }" placeholder="Your email address" required />
              <span v-if="form.errors.email" class="error-text">{{ form.errors.email }}</span>
            </div>
          </div>

          <div class="ticket-form-grid">
            <div class="form-group">
              <label class="form-label">Order Number <span class="required">*</span></label>
              <input v-model="form.order_number" type="text" class="form-input" :class="{ 'input-error': form.errors.order_number }" placeholder="e.g. ORD-00001" required />
              <span v-if="form.errors.order_number" class="error-text">{{ form.errors.order_number }}</span>
            </div>
            <div class="form-group">
              <label class="form-label">Reason for Return <span class="required">*</span></label>
              <select v-model="form.reason" class="form-input" :class="{ 'input-error': form.errors.reason }" required>
                <option value="">Select a reason</option>
                <option value="wrong_size">Wrong Size</option>
                <option value="defective">Defective / Damaged</option>
                <option value="wrong_item">Wrong Item Received</option>
                <option value="not_as_described">Not as Described</option>
                <option value="changed_mind">Changed My Mind</option>
                <option value="other">Other</option>
              </select>
              <span v-if="form.errors.reason" class="error-text">{{ form.errors.reason }}</span>
            </div>
          </div>

          <div class="form-group">
            <label class="form-label">Item(s) to Return <span class="required">*</span></label>
            <input v-model="form.items" type="text" class="form-input" :class="{ 'input-error': form.errors.items }" placeholder="List the product name(s) and variant(s)" required />
            <span v-if="form.errors.items" class="error-text">{{ form.errors.items }}</span>
          </div>

          <div class="form-group">
            <label class="form-label">Additional Details</label>
            <textarea v-model="form.details" class="form-textarea" placeholder="Any additional information about your return..." rows="4"></textarea>
          </div>

          <button type="submit" class="submit-btn" :disabled="form.processing">
            <i v-if="form.processing" class="ri-loader-4-line spin"></i>
            <span v-else><i class="ri-arrow-go-back-line"></i></span>
            {{ form.processing ? 'Submitting...' : 'Submit Return Request' }}
          </button>
        </form>
      </div>
    </section>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import Breadcrumb from '@/components/breadcrumb/layout-breadcrumb.vue';

defineProps({ brandPartner: Object });

const breadcrumbItems = [
  { label: 'Help Center', link: route('store.brand-partner.support.help') },
  { label: 'Returns Portal' },
];

const submitted = ref(false);

const form = useForm({
  name: '',
  email: '',
  order_number: '',
  reason: '',
  items: '',
  details: '',
});

const submitReturn = () => {
  form.post(route('store.brand-partner.support.returns.submit'), {
    onSuccess: () => {
      submitted.value = true;
    },
  });
};
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
  max-width: 800px;
  margin: 0 auto;
  padding: 0 24px;
}

/* Policy Summary */
.returns-policy-summary {
  background: #fff8e1;
  border-left: 4px solid #ff9505;
  padding: 20px 24px;
  margin-bottom: 32px;
  border-radius: 0 8px 8px 0;
}

.returns-policy-summary h3 {
  font-size: 15px;
  font-weight: 700;
  color: #111;
  margin: 0 0 10px;
  display: flex;
  align-items: center;
  gap: 8px;
}

.returns-policy-summary ul {
  padding-left: 18px;
  margin: 0 0 12px;
}

.returns-policy-summary li {
  font-size: 13px;
  color: #555;
  margin-bottom: 4px;
}

.policy-link {
  font-size: 13px;
  font-weight: 600;
  color: #ff9505;
  text-decoration: none;
  display: inline-flex;
  align-items: center;
  gap: 4px;
}

.policy-link:hover {
  text-decoration: underline;
}

/* Form */
.ticket-form {
  display: flex;
  flex-direction: column;
  gap: 20px;
}

.ticket-form-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 20px;
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: 6px;
}

.form-label {
  font-size: 13px;
  font-weight: 700;
  color: #333;
}

.required {
  color: #dc2626;
}

.form-input,
.form-textarea {
  padding: 12px 14px;
  border: 1.5px solid #e0e0e0;
  background: #fff;
  font-size: 14px;
  font-family: 'Public Sans', sans-serif;
  color: #333;
  outline: none;
  border-radius: 6px;
  transition: border-color 0.2s;
  width: 100%;
  box-sizing: border-box;
}

.form-input:focus,
.form-textarea:focus {
  border-color: #ff9505;
}

.form-input.input-error,
.form-textarea.input-error {
  border-color: #dc2626;
}

.error-text {
  font-size: 12px;
  color: #dc2626;
}

.form-textarea {
  resize: vertical;
  min-height: 80px;
}

.submit-btn {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  padding: 14px 32px;
  background: #ff9505;
  color: #fff;
  border: none;
  border-radius: 8px;
  font-size: 14px;
  font-weight: 700;
  cursor: pointer;
  font-family: 'Public Sans', sans-serif;
  transition: background 0.2s;
  align-self: flex-start;
}

.submit-btn:hover:not(:disabled) {
  background: #e88600;
}

.submit-btn:disabled {
  background: #ccc;
  cursor: not-allowed;
}

@keyframes spin {
  from { transform: rotate(0deg); }
  to { transform: rotate(360deg); }
}
.spin {
  animation: spin 1s linear infinite;
}

/* Success */
.ticket-success {
  text-align: center;
  padding: 60px 20px;
}

.ticket-success-icon {
  width: 80px;
  height: 80px;
  border-radius: 50%;
  background: #e8f5e9;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  margin-bottom: 20px;
}

.ticket-success-icon i {
  font-size: 36px;
  color: #198754;
}

.ticket-success h3 {
  font-size: 22px;
  font-weight: 700;
  color: #111;
  margin: 0 0 8px;
}

.ticket-success p {
  font-size: 14px;
  color: #777;
  margin: 0 0 24px;
  max-width: 500px;
  margin-left: auto;
  margin-right: auto;
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

@media (max-width: 575px) {
  .support-hero-title {
    font-size: 32px;
  }
  .ticket-form-grid {
    grid-template-columns: 1fr;
  }
}
</style>
