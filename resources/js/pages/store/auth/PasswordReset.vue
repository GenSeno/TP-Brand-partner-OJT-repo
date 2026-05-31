<template>
  <Head title="Reset Password" />

  <div class="reset-page">
    <div class="reset-card">
      <h2>Reset Password</h2>
      <p>Enter your new password below.</p>

      <form @submit.prevent="submit">
        <div class="form-group">
          <label>New Password</label>
          <input
            v-model="form.password"
            type="password"
            class="grocery-input"
            placeholder="Enter new password"
            required
          />
          <span class="error-text" v-if="form.errors.password">
            {{ form.errors.password }}
          </span>
        </div>

        <div class="form-group">
          <label>Confirm Password</label>
          <input
            v-model="form.password_confirmation"
            type="password"
            class="grocery-input"
            placeholder="Confirm new password"
            required
          />
        </div>

        <button type="submit" class="reset-btn" :disabled="form.processing">
          {{ form.processing ? 'Resetting...' : 'Reset Password' }}
        </button>
      </form>
    </div>
  </div>
</template>

<script setup>
import { Head, useForm } from '@inertiajs/vue3';

const props = defineProps({
  token: String,
  email: String,
});

// Debug — remove after testing
console.log('Token:', props.token);
console.log('Email:', props.email);

const form = useForm({
  token: props.token,
  email: '',
  password: '',
  password_confirmation: '',
});

const submit = () => {
  form.post(route('password.update'));
};
</script>

<style scoped>
.reset-page {
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  background: #f7f7f7;
  font-family: 'Public Sans', sans-serif;
}

.reset-card {
  background: #fff;
  padding: 40px;
  border-radius: 16px;
  width: 100%;
  max-width: 440px;
  box-shadow: 0 4px 24px rgba(0, 0, 0, 0.08);
}

h2 {
  font-size: 24px;
  font-weight: 800;
  color: #111;
  margin: 0 0 8px;
}

p {
  color: #666;
  font-size: 14px;
  margin-bottom: 24px;
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: 6px;
  margin-bottom: 16px;
}

.form-group label {
  font-size: 13px;
  font-weight: 700;
  color: #555;
}

.grocery-input {
  padding: 12px 14px;
  border: 1.5px solid #e8e8e8;
  border-radius: 10px;
  font-size: 14px;
  width: 100%;
  box-sizing: border-box;
  outline: none;
  font-family: 'Public Sans', sans-serif;
}

.grocery-input:focus {
  border-color: #ff9505;
  outline: none;
}

.error-text {
  font-size: 12px;
  color: #ff4757;
}

.reset-btn {
  width: 100%;
  padding: 14px;
  background: #ff9505;
  color: #fff;
  border: none;
  border-radius: 10px;
  font-size: 15px;
  font-weight: 700;
  cursor: pointer;
  margin-top: 8px;
  transition: background 0.2s;
}

.reset-btn:hover {
  background: #e08500;
}
.reset-btn:disabled {
  background: #ddd;
  cursor: not-allowed;
}
</style>
