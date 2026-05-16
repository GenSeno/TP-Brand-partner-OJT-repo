<!-- components/LoginModal.vue -->
<template>
  <div
    class="account-modal-overlay"
    v-if="accountModalOpen"
    @click.self="closeModal"
  >
    <div class="account-modal" :class="{ 'register-mode': showRegister }">
      <div
        class="modal-loading-bar"
        v-if="loginForm.processing || registerForm.processing"
      ></div>
      <button type="button" class="modal-close" @click="closeModal">
        <i class="ri-close-line"></i>
      </button>

      <!-- LOGIN VIEW -->
      <template v-if="!showRegister">
        <!-- Left: Form -->
        <div class="account-card-left">
          <div class="account-card-copy">
            <h2 class="auth-title">Welcome, Runner</h2>
            <p class="auth-subtitle">
              Log in to continue your journey—track events, manage
              registrations, and stay connected with the tribe.
            </p>
          </div>

          <form class="account-form" @submit.prevent="submitLogin">
            <div class="auth-error-banner" v-if="loginForm.hasErrors">
              <i class="ri-alert-line"></i>
              <span>{{
                loginForm.errors.email || 'Please fix the errors below.'
              }}</span>
            </div>

            <div class="auth-field">
              <label class="auth-label">EMAIL ADDRESS</label>
              <input
                type="email"
                class="auth-input"
                :class="{ 'auth-input--error': loginForm.errors.email }"
                placeholder="Email Address"
                v-model="loginForm.email"
                required
              />
              <span class="auth-field-error" v-if="loginForm.errors.email">{{
                loginForm.errors.email
              }}</span>
            </div>

            <div class="auth-field">
              <label class="auth-label">PASSWORD</label>
              <input
                type="password"
                class="auth-input"
                :class="{ 'auth-input--error': loginForm.errors.password }"
                placeholder="Password"
                v-model="loginForm.password"
                required
              />
              <span class="auth-field-error" v-if="loginForm.errors.password">{{
                loginForm.errors.password
              }}</span>
            </div>

            <div class="auth-remember-row">
              <label class="auth-remember">
                <input type="checkbox" v-model="loginForm.remember" />
                <span>Remember Password?</span>
              </label>
              <a href="#" class="auth-forgot">Forgot Password?</a>
            </div>

            <button type="submit" class="auth-btn-primary">LOGIN</button>

            <button
              type="button"
              class="auth-btn-google"
              @click="handleGoogleLogin"
            >
              <img
                src="https://www.svgrepo.com/show/475656/google-color.svg"
                alt="Google"
                class="google-icon"
              />
              LOGIN WITH GOOGLE
            </button>

            <button
              type="button"
              class="auth-btn-facebook"
              @click="handleFacebookLogin"
            >
              <i class="ri-facebook-fill facebook-icon"></i>
              LOGIN WITH FACEBOOK
            </button>

            <p class="auth-switch">
              Don't have an account?
              <button
                type="button"
                class="auth-link-btn"
                @click="showRegister = true"
              >
                SignUp
              </button>
            </p>

            <div @click="guestLogin" class="auth-do-later">
              <a class="auth-do-later-link">Login as Guest</a>
            </div>
          </form>
        </div>

        <!-- Right: Photo -->
        <div class="account-card-right">
          <div class="account-card-img-placeholder"></div>
        </div>
      </template>

      <!-- REGISTER VIEW -->
      <template v-else>
        <!-- Left: Intro + Google -->
        <div class="account-card-left register-left">
          <button
            type="button"
            class="auth-back-btn"
            @click="showRegister = false"
          >
            Back
          </button>

          <div class="account-card-copy">
            <h2 class="auth-title register-title">Start Your Journey</h2>
            <p class="auth-subtitle">
              Run with heart. Move with purpose. Join Tribu Pakaras and conquer
              every trail ahead.
            </p>
          </div>

          <button
            type="button"
            class="auth-btn-google"
            @click="handleGoogleLogin"
          >
            <img
              src="https://www.svgrepo.com/show/475656/google-color.svg"
              alt="Google"
              class="google-icon"
            />
            LOGIN WITH GOOGLE
          </button>

          <button
            type="button"
            class="auth-btn-facebook"
            @click="handleFacebookLogin"
          >
            <i class="ri-facebook-fill facebook-icon"></i>
            LOGIN WITH FACEBOOK
          </button>

          <p class="auth-switch" style="margin-top: 20px">
            Already have an account?
            <button
              type="button"
              class="auth-link-btn"
              @click="showRegister = false"
            >
              Log In
            </button>
          </p>
        </div>

        <!-- Right: Form Fields -->
        <div class="account-card-right register-right">
          <form class="account-form" @submit.prevent="submitRegister">
            <div class="auth-error-banner" v-if="registerForm.hasErrors">
              <i class="ri-alert-line"></i>
              <span>Please correct the errors below.</span>
            </div>

            <div class="auth-field">
              <label class="auth-label">FULL NAME</label>
              <input
                type="text"
                class="auth-input"
                :class="{ 'auth-input--error': registerForm.errors.name }"
                placeholder="Enter Full name"
                v-model="registerForm.name"
                required
              />
              <span class="auth-field-error" v-if="registerForm.errors.name">{{
                registerForm.errors.name
              }}</span>
            </div>

            <div class="auth-field">
              <label class="auth-label">EMAIL ADDRESS</label>
              <input
                type="email"
                class="auth-input"
                :class="{ 'auth-input--error': registerForm.errors.email }"
                placeholder="Enter Email Address"
                v-model="registerForm.email"
                required
              />
              <span class="auth-field-error" v-if="registerForm.errors.email">{{
                registerForm.errors.email
              }}</span>
            </div>

            <div class="auth-field">
              <label class="auth-label">PASSWORD</label>
              <input
                type="password"
                class="auth-input"
                :class="{ 'auth-input--error': registerForm.errors.password }"
                placeholder="Enter Password"
                v-model="registerForm.password"
                required
              />
              <span
                class="auth-field-error"
                v-if="registerForm.errors.password"
                >{{ registerForm.errors.password }}</span
              >
            </div>

            <div class="auth-field">
              <label class="auth-label">CONFIRM PASSWORD</label>
              <input
                type="password"
                class="auth-input"
                :class="{
                  'auth-input--error':
                    registerForm.errors.password_confirmation,
                }"
                placeholder="Confirm Password"
                v-model="registerForm.password_confirmation"
                required
              />
              <span
                class="auth-field-error"
                v-if="registerForm.errors.password_confirmation"
                >{{ registerForm.errors.password_confirmation }}</span
              >
            </div>

            <label class="auth-terms">
              <input type="checkbox" v-model="registerForm.terms" required />
              <span>
                By creating an account, you agree to our
                <a href="#" class="auth-terms-link">Terms &amp; Conditions</a>
                and
                <a href="#" class="auth-terms-link">Privacy Policy</a>.
              </span>
            </label>

            <button type="submit" class="auth-btn-primary">
              CREATE ACCOUNT
            </button>
          </form>
        </div>
      </template>
    </div>
  </div>
</template>

<script setup>
import { useForm, router } from '@inertiajs/vue3';
import { ref, watch, onUnmounted } from 'vue';

const props = defineProps({
  modelValue: {
    type: Boolean,
    default: false,
  },
  loginRoute: {
    type: String,
    required: true,
  },
  registerRoute: {
    type: String,
    required: true,
  },
});

const emit = defineEmits(['update:modelValue', 'success']);

const accountModalOpen = ref(props.modelValue);
const showRegister = ref(false);

const loginForm = useForm({
  email: '',
  password: '',
  remember: false,
});

const registerForm = useForm({
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
  terms: false,
});

//Function to login as guest for testing purposes
const guestLogin = async () => {
  await axios.post('/guest-login');

  window.location.href = '/';
};

// Sync with v-model
watch(
  () => props.modelValue,
  (newVal) => {
    accountModalOpen.value = newVal;
    if (!newVal) {
      showRegister.value = false;
    }
  },
);

watch(accountModalOpen, (isOpen) => {
  emit('update:modelValue', isOpen);

  if (isOpen) {
    document.body.style.overflow = 'hidden';
    document.body.style.paddingRight = 'var(--scrollbar-width, 0px)';
  } else {
    document.body.style.overflow = '';
    document.body.style.paddingRight = '';
  }
});

const closeModal = () => {
  accountModalOpen.value = false;
  showRegister.value = false;
};

const submitLogin = () => {
  loginForm.post(props.loginRoute, {
    onFinish: () => loginForm.reset('password'),
    onSuccess: () => {
      closeModal();
      emit('success');
    },
  });
};

const submitRegister = () => {
  registerForm.post(props.registerRoute, {
    onFinish: () =>
      registerForm.reset('password', 'password_confirmation', 'terms'),
    onSuccess: () => {
      closeModal();
      emit('success');
    },
  });
};

const handleGoogleLogin = () => {
  window.location.href = 'localhost:8000/auth/google/redirect';
  Console.log('Google Login CLicked');
};

const handleFacebookLogin = () => {
  // Implement Facebook OAuth logic here
  console.log('Facebook login clicked');
};

// Cleanup on unmount
onUnmounted(() => {
  document.body.style.overflow = '';
  document.body.style.paddingRight = '';
});
</script>

<style scoped>
/* Copy all the account modal styles from store-layout.vue here */
/* ============================================
   ACCOUNT MODAL STYLES
   ============================================ */
.account-modal-overlay {
  position: fixed;
  inset: 0;
  background: rgba(0, 0, 0, 0.75);
  z-index: 2000;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 24px;
  backdrop-filter: blur(4px);
  overflow-y: auto;
  -webkit-overflow-scrolling: touch;
}

.account-modal {
  width: min(100%, 960px);
  max-height: 92vh;
  overflow: hidden;
  border-radius: 28px;
  background: #fff;
  box-shadow: 0 40px 100px rgba(0, 0, 0, 0.35);
  display: grid;
  grid-template-columns: 1fr 1fr;
  position: relative;
  margin: auto;
}

.account-modal.register-mode {
  grid-template-columns: 1fr 1fr;
}

.modal-loading-bar {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  height: 3px;
  background: #ff9505;
  z-index: 20;
  animation: modalLoading 1.2s ease-in-out infinite;
  transform-origin: left;
  border-radius: 28px 28px 0 0;
}

@keyframes modalLoading {
  0% {
    transform: scaleX(0);
    opacity: 0.8;
  }
  50% {
    transform: scaleX(0.6);
    opacity: 1;
  }
  100% {
    transform: scaleX(1);
    opacity: 0;
  }
}

.modal-close {
  position: absolute;
  top: 18px;
  right: 18px;
  width: 38px;
  height: 38px;
  border-radius: 50%;
  border: none;
  background: rgba(255, 255, 255, 0.92);
  color: #222;
  font-size: 20px;
  cursor: pointer;
  display: grid;
  place-items: center;
  z-index: 10;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
  transition: background 0.2s;
}

.modal-close:hover {
  background: #fff;
}

/* LEFT PANEL — Login Form */
.account-card-left {
  padding: 48px 44px;
  display: flex;
  flex-direction: column;
  justify-content: center;
  background: #fff;
  overflow-y: auto;
  max-height: 92vh;
}

.account-card-copy {
  margin-bottom: 28px;
}

.auth-title {
  font-size: clamp(1.8rem, 2.4vw, 2.8rem);
  font-weight: 900;
  line-height: 1.1;
  margin: 0 0 14px;
  color: #111;
  font-family: 'Poppins', 'Public Sans', sans-serif;
}

.register-title {
  font-size: clamp(1.6rem, 2.2vw, 2.4rem);
}

.auth-subtitle {
  color: #666;
  font-size: 14px;
  line-height: 1.75;
  margin: 0;
}

.account-form {
  display: grid;
  gap: 16px;
}

.auth-field {
  display: grid;
  gap: 7px;
}

.auth-field-error {
  font-size: 11px;
  color: #e74c3c;
  font-weight: 600;
  line-height: 1.3;
}

.auth-input--error {
  border-color: #e74c3c;
  background: #fef5f5;
}

.auth-input--error:focus {
  border-color: #e74c3c;
  background: #fef5f5;
}

.auth-error-banner {
  display: flex;
  align-items: center;
  gap: 8px;
  background: #fef5f5;
  border: 1px solid #f5c6cb;
  border-radius: 10px;
  padding: 12px 14px;
  font-size: 13px;
  color: #c0392b;
  font-weight: 600;
  line-height: 1.4;
}

.auth-error-banner i {
  font-size: 18px;
  flex-shrink: 0;
}

.auth-label {
  font-size: 11px;
  font-weight: 700;
  letter-spacing: 0.1em;
  text-transform: uppercase;
  color: #444;
}

.auth-input {
  width: 100%;
  height: 50px;
  padding: 0 18px;
  border: 1.5px solid #e0e0e0;
  border-radius: 12px;
  background: #f9f9f9;
  font-size: 14px;
  color: #222;
  font-family: 'Public Sans', sans-serif;
  transition:
    border-color 0.2s,
    background 0.2s;
}

.auth-input:focus {
  outline: none;
  border-color: rgb(var(--grocery-theme));
  background: #fff;
}

.auth-remember-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 16px;
  flex-wrap: wrap;
}

.auth-remember {
  display: flex;
  align-items: center;
  gap: 8px;
  color: #555;
  font-size: 13px;
}

.auth-remember input {
  width: 15px;
  height: 15px;
  accent-color: rgb(var(--grocery-theme));
}

.auth-forgot {
  color: #f15a24;
  text-decoration: none;
  font-size: 13px;
  font-weight: 600;
}

.auth-btn-primary {
  border: none;
  border-radius: 50px;
  height: 52px;
  font-weight: 700;
  font-family: 'Public Sans', sans-serif;
  font-size: 14px;
  letter-spacing: 0.08em;
  cursor: pointer;
  background: #ff9505;
  color: #fff;
  transition:
    background 0.2s,
    transform 0.15s;
}

.auth-btn-primary:hover {
  background: #155d8e;
  transform: translateY(-1px);
}

.auth-btn-google {
  height: 52px;
  border-radius: 50px;
  border: 1.5px solid #e0e0e0;
  background: #fff;
  color: #333;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 12px;
  font-size: 13px;
  font-weight: 600;
  letter-spacing: 0.06em;
  font-family: 'Public Sans', sans-serif;
  cursor: pointer;
  transition:
    border-color 0.2s,
    background 0.2s;
}

.auth-btn-google:hover {
  border-color: #bbb;
  background: #fafafa;
}

.google-icon {
  width: 20px;
  height: 20px;
}

.auth-btn-facebook {
  height: 52px;
  border-radius: 50px;
  border: 1.5px solid #e0e0e0;
  background: #fff;
  color: #333;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 12px;
  font-size: 13px;
  font-weight: 600;
  letter-spacing: 0.06em;
  font-family: 'Public Sans', sans-serif;
  cursor: pointer;
  transition:
    border-color 0.2s,
    background 0.2s;
}

.auth-btn-facebook:hover {
  border-color: #bbb;
  background: #fafafa;
}

.facebook-icon {
  font-size: 20px;
  color: #166fe5 ;
}

.auth-switch {
  font-size: 13px;
  color: #666;
  margin: 0;
  text-align: center;
}

.auth-link-btn {
  border: none;
  padding: 0;
  font-weight: 700;
  color: #f15a24;
  background: none;
  cursor: pointer;
  font-size: 13px;
  font-family: 'Public Sans', sans-serif;
}

.auth-do-later {
  text-align: center;
}

.auth-do-later-link {
  font-size: 13px;
  color: #f15a24;
  text-decoration: none;
  font-weight: 600;
}

.auth-terms {
  display: flex;
  gap: 12px;
  align-items: flex-start;
  color: #555;
  font-size: 13px;
  line-height: 1.6;
}

.auth-terms input {
  margin-top: 3px;
  width: 15px;
  height: 15px;
  flex-shrink: 0;
  accent-color: rgb(var(--grocery-theme));
}

.auth-terms-link {
  color: rgb(var(--grocery-theme));
  text-decoration: none;
  font-weight: 600;
}

/* RIGHT PANEL — Photo (Login) */
.account-card-right {
  position: relative;
  overflow: hidden;
  min-height: 520px;
}

.account-card-img-placeholder {
  width: 100%;
  height: 100%;
  background: url('/img/img-login.png') center/cover no-repeat;
}

/* REGISTER — Right panel becomes the form area */
.register-right {
  background: #f7f8fa;
  padding: 48px 44px;
  display: flex;
  flex-direction: column;
  justify-content: center;
  overflow-y: auto;
  max-height: 92vh;
}

/* REGISTER — Left panel styles */
.register-left {
  padding: 48px 44px;
  display: flex;
  flex-direction: column;
  justify-content: center;
  background: #fff;
  border-right: 1px solid #efefef;
}

.auth-back-btn {
  border: none;
  background: none;
  color: #888;
  font-size: 13px;
  font-weight: 600;
  cursor: pointer;
  padding: 0;
  margin-bottom: 28px;
  font-family: 'Public Sans', sans-serif;
  text-align: left;
  display: flex;
  align-items: center;
  gap: 6px;
  transition: color 0.2s;
}

.auth-back-btn:hover {
  color: #333;
}

/* Responsive */
@media (max-width: 1024px) {
  .account-modal {
    grid-template-columns: 1fr;
    max-height: 92vh;
    overflow-y: auto;
    border-radius: 20px;
  }

  .account-modal.register-mode {
    grid-template-columns: 1fr;
  }

  .account-card-right:not(.register-right) {
    display: none;
  }

  .account-card-left,
  .register-left,
  .register-right {
    padding: 36px 28px;
    min-height: auto;
  }

  .register-left {
    border-right: none;
    border-bottom: 1px solid #efefef;
  }
}

@media (max-width: 680px) {
  .account-modal-overlay {
    padding: 12px;
  }

  .auth-title {
    font-size: 1.9rem;
  }

  .auth-remember-row {
    flex-direction: column;
    align-items: flex-start;
    gap: 10px;
  }
}
</style>
