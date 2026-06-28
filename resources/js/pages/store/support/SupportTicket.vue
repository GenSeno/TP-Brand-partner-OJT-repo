<template>
  <Head title="Submit a Support Ticket" />

  <div class="support-page">
    <section class="support-hero">
      <div class="support-hero-bg">
        <img src="/img/img-about1.png" alt="Support Ticket" />
      </div>
      <div class="support-hero-overlay"></div>
      <div class="support-hero-content">
        <div class="breadcrumb-wrapper">
          <Breadcrumb :items="breadcrumbItems" />
        </div>
        <h1 class="support-hero-title">Support Ticket</h1>
        <p class="support-hero-subtitle">Submit a request and we'll get back to you</p>
      </div>
    </section>

    <section class="support-content">
      <div class="support-inner">
        <div v-if="submitted" class="ticket-success">
          <div class="ticket-success-icon"><i class="ri-check-line"></i></div>
          <h3>Ticket Submitted!</h3>
          <p>Thank you for reaching out. We'll get back to you within 24-48 hours.</p>
          <Link :href="route('store.brand-partner.support.help')" class="help-btn help-btn-primary">Back to Help Center</Link>
        </div>

        <form v-else class="ticket-form" @submit.prevent="submitTicket">
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
              <label class="form-label">Order Number</label>
              <input v-model="form.order_number" type="text" class="form-input" placeholder="e.g. ORD-00001 (if applicable)" />
            </div>
            <div class="form-group">
              <label class="form-label">Category <span class="required">*</span></label>
              <select v-model="form.category" class="form-input" :class="{ 'input-error': form.errors.category }" required>
                <option value="">Select a category</option>
                <option value="order_issue">Order Issue</option>
                <option value="shipping">Shipping Problem</option>
                <option value="return">Return / Exchange</option>
                <option value="warranty">Warranty Claim</option>
                <option value="product">Product Question</option>
                <option value="payment">Payment Issue</option>
                <option value="other">Other</option>
              </select>
              <span v-if="form.errors.category" class="error-text">{{ form.errors.category }}</span>
            </div>
          </div>

          <div class="form-group">
            <label class="form-label">Subject <span class="required">*</span></label>
            <input v-model="form.subject" type="text" class="form-input" :class="{ 'input-error': form.errors.subject }" placeholder="Brief summary of your issue" required />
            <span v-if="form.errors.subject" class="error-text">{{ form.errors.subject }}</span>
          </div>

          <div class="form-group">
            <label class="form-label">Message <span class="required">*</span></label>
            <textarea v-model="form.message" class="form-textarea" :class="{ 'input-error': form.errors.message }" placeholder="Describe your issue in detail..." rows="6" required></textarea>
            <span v-if="form.errors.message" class="error-text">{{ form.errors.message }}</span>
          </div>

          <div class="form-group">
            <label class="form-label">Attachments (optional)</label>
            <input type="file" class="form-input" multiple @change="handleFiles" />
            <p class="form-hint">Max 5MB per file. Accepted: JPG, PNG, PDF</p>
          </div>

          <button type="submit" class="submit-btn" :disabled="form.processing">
            <i v-if="form.processing" class="ri-loader-4-line spin"></i>
            <span v-else><i class="ri-send-plane-line"></i></span>
            {{ form.processing ? 'Submitting...' : 'Submit Ticket' }}
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
  { label: 'Support Ticket' },
];

const submitted = ref(false);

const form = useForm({
  name: '',
  email: '',
  order_number: '',
  category: '',
  subject: '',
  message: '',
  attachments: [],
});

const handleFiles = (e) => {
  form.attachments = Array.from(e.target.files);
};

const submitTicket = () => {
  form.post(route('store.brand-partner.support.ticket.submit'), {
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
  min-height: 120px;
}

.form-hint {
  font-size: 12px;
  color: #999;
  margin: 0;
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
