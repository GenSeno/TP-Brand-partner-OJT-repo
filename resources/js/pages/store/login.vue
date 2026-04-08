<template>
    <Head :title="showRegister ? 'Sign Up' : 'Login'" />

    <div class="auth-page">
        <!-- Background -->
        <div class="auth-bg">
            <div class="auth-bg-placeholder"></div>
            <!-- Replace with: <img src="/img/auth-bg.jpg" alt=""> -->
        </div>
        <div class="auth-bg-overlay"></div>

        <!-- LOGIN CARD -->
        <div class="auth-card login-card" v-if="!showRegister">
            <!-- Left: Form -->
            <div class="auth-card-left">
                <h2 class="auth-title">Welcome, Runner</h2>
                <p class="auth-subtitle">
                    Log in to continue your journey—track events, manage
                    registrations, and stay connected with the tribe.
                </p>

                <form @submit.prevent="submitLogin">
                    <div class="auth-field">
                        <label class="auth-label">EMAIL ADDRESS</label>
                        <input
                            type="email"
                            class="auth-input"
                            placeholder="Email Address"
                            v-model="loginForm.email"
                            required
                        />
                    </div>

                    <div class="auth-field">
                        <label class="auth-label">PASSWORD</label>
                        <input
                            type="password"
                            class="auth-input"
                            placeholder="Password"
                            v-model="loginForm.password"
                            required
                        />
                    </div>

                    <div class="auth-remember-row">
                        <label class="auth-remember">
                            <input
                                type="checkbox"
                                v-model="loginForm.remember"
                            />
                            <span>Remember Password?</span>
                        </label>
                        <a href="#" class="auth-forgot">Forgot Password?</a>
                    </div>

                    <button type="submit" class="auth-btn-primary">
                        LOGIN
                    </button>

                    <button type="button" class="auth-btn-google">
                        <img
                            src="https://www.svgrepo.com/show/475656/google-color.svg"
                            alt="Google"
                            class="google-icon"
                        />
                        LOGIN WITH GOOGLE
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

                    <div class="auth-do-later">
                        <a href="/" class="auth-do-later-link">Do it Later.</a>
                    </div>
                </form>
            </div>

            <!-- Right: Image -->
            <div class="auth-card-right">
                <div class="auth-card-img-placeholder"></div>
                <!-- Replace with: <img src="/img/login-side.jpg" alt=""> -->
            </div>
        </div>

        <!-- REGISTER CARD -->
        <div class="auth-card register-card" v-else>
            <!-- Left: Info -->
            <div class="auth-card-left register-left">
                <button
                    type="button"
                    class="auth-back"
                    @click="showRegister = false"
                >
                    ← Back
                </button>

                <div class="register-left-content">
                    <h2 class="auth-title register-title">
                        Start Your Journey
                    </h2>
                    <p class="auth-subtitle">
                        Run with heart. Move with purpose. Join Tribu Pakaras
                        and conquer every trail ahead.
                    </p>

                    <button type="button" class="auth-btn-google">
                        <img
                            src="https://www.svgrepo.com/show/475656/google-color.svg"
                            alt="Google"
                            class="google-icon"
                        />
                        LOGIN WITH GOOGLE
                    </button>

                    <p class="auth-switch">
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
            </div>

            <!-- Right: Form -->
            <div class="auth-card-right register-right">
                <form @submit.prevent="submitRegister">
                    <div class="auth-field">
                        <label class="auth-label">FULL NAME</label>
                        <input
                            type="text"
                            class="auth-input"
                            placeholder="Enter Full name"
                            v-model="registerForm.name"
                            required
                        />
                    </div>

                    <div class="auth-field">
                        <label class="auth-label">EMAIL ADDRESS</label>
                        <input
                            type="email"
                            class="auth-input"
                            placeholder="Enter Email Address"
                            v-model="registerForm.email"
                            required
                        />
                    </div>

                    <div class="auth-field">
                        <label class="auth-label">PASSWORD</label>
                        <input
                            type="password"
                            class="auth-input"
                            placeholder="Enter Password"
                            v-model="registerForm.password"
                            required
                        />
                    </div>

                    <div class="auth-field">
                        <label class="auth-label">CONFIRM PASSWORD</label>
                        <input
                            type="password"
                            class="auth-input"
                            placeholder="Confirm Password"
                            v-model="registerForm.password_confirmation"
                            required
                        />
                    </div>

                    <label class="auth-terms">
                        <input
                            type="checkbox"
                            v-model="registerForm.terms"
                            required
                        />
                        <span>
                            By creating an account, you agree to our
                            <a href="#" class="auth-terms-link"
                                >Terms &amp; Conditions</a
                            >
                            and
                            <a href="#" class="auth-terms-link"
                                >Privacy Policy</a
                            >.
                        </span>
                    </label>

                    <button type="submit" class="auth-btn-primary">
                        CREATE ACCOUNT
                    </button>
                </form>
            </div>
        </div>
    </div>
</template>

<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

defineOptions({
    layout: null,
});

// Add props to receive brandPartner
const props = defineProps({
    brandPartner: Object,
});

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

// Update submitLogin to use brand partner route
const submitLogin = () => {
    loginForm.post(
        route('store.brand-partner.login.submit', props.brandPartner.slug),
        {
            onFinish: () => loginForm.reset('password'),
        },
    );
};

// Update submitRegister to use brand partner route
const submitRegister = () => {
    registerForm.post(
        route('store.brand-partner.register.submit', props.brandPartner.slug),
        {
            onFinish: () =>
                registerForm.reset('password', 'password_confirmation'),
        },
    );
};
</script>

<style scoped>
.auth-page {
    font-family: 'Public Sans', sans-serif;
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
    padding: 40px 16px;
    padding-top: 80px;
}

/* Background */
.auth-bg {
    position: fixed;
    inset: 0;
    z-index: 0;
}

.auth-bg-placeholder {
    width: 100%;
    height: 100%;
    background: #1a1a1a;
}

.auth-bg img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
}

.auth-bg-overlay {
    position: fixed;
    inset: 0;
    background: rgba(0, 0, 0, 0.65);
    z-index: 1;
}

/* Card base */
.auth-card {
    position: relative;
    z-index: 2;
    background: #fff;
    border-radius: 20px;
    overflow: hidden;
    display: grid;
    width: 100%;
    box-shadow: 0 24px 60px rgba(0, 0, 0, 0.35);
}

/* Login card */
.login-card {
    grid-template-columns: 1fr 1fr;
    max-width: 860px;
    min-height: 600px;
}

/* Register card */
.register-card {
    grid-template-columns: 1fr 1fr;
    max-width: 1000px;
    min-height: 560px;
}

/* Left panel */
.auth-card-left {
    padding: 52px 44px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    background: #fff;
}

.register-left {
    justify-content: flex-start;
    padding-top: 40px;
}

.auth-back {
    background: none;
    border: none;
    font-size: 13px;
    font-weight: 600;
    color: #555;
    cursor: pointer;
    padding: 0;
    margin-bottom: 32px;
    font-family: 'Public Sans', sans-serif;
    text-align: left;
    transition: color 0.2s;
}

.auth-back:hover {
    color: #111;
}

.register-left-content {
    flex: 1;
    display: flex;
    flex-direction: column;
    justify-content: center;
}

/* Titles */
.auth-title {
    font-size: 28px;
    font-weight: 900;
    color: #111;
    margin: 0 0 12px;
    text-align: center;
    font-family: 'Public Sans', sans-serif;
}

.register-title {
    text-align: left;
    font-size: 32px;
}

.auth-subtitle {
    font-size: 13px;
    color: #666;
    line-height: 1.7;
    margin: 0 0 28px;
    text-align: center;
}

.register-left .auth-subtitle {
    text-align: left;
}

/* Fields */
.auth-field {
    margin-bottom: 16px;
}

.auth-label {
    display: block;
    font-size: 11px;
    font-weight: 700;
    color: #888;
    letter-spacing: 0.8px;
    margin-bottom: 6px;
    text-transform: uppercase;
}

.auth-input {
    width: 100%;
    padding: 13px 16px;
    border: 1.5px solid #e0e0e0;
    border-radius: 10px;
    font-size: 14px;
    font-family: 'Public Sans', sans-serif;
    color: #333;
    background: #fff;
    outline: none;
    transition: border-color 0.2s;
    box-sizing: border-box;
}

.auth-input:focus {
    border-color: #3d8bf0;
    background: #fff;
}

.auth-input::placeholder {
    color: #bbb;
}

/* Remember row */
.auth-remember-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}

.auth-remember {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 13px;
    color: #555;
    cursor: pointer;
}

.auth-remember input {
    accent-color: #3d8bf0;
    width: 15px;
    height: 15px;
}

.auth-forgot {
    font-size: 13px;
    font-weight: 600;
    color: #e84b0f;
    text-decoration: none;
}

.auth-forgot:hover {
    text-decoration: underline;
}

/* Buttons */
.auth-btn-primary {
    width: 100%;
    padding: 14px;
    background: #3d8bf0;
    color: #fff;
    border: none;
    border-radius: 10px;
    font-size: 14px;
    font-weight: 800;
    letter-spacing: 1.5px;
    cursor: pointer;
    font-family: 'Public Sans', sans-serif;
    transition: background 0.25s;
    margin-bottom: 12px;
}

.auth-btn-primary:hover {
    background: #2d7ae0;
}

.auth-btn-google {
    width: 100%;
    padding: 13px;
    background: #fff;
    color: #333;
    border: 1.5px solid #e0e0e0;
    border-radius: 10px;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    font-family: 'Public Sans', sans-serif;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    transition:
        border-color 0.2s,
        box-shadow 0.2s;
    margin-bottom: 20px;
}

.auth-btn-google:hover {
    border-color: #3d8bf0;
    box-shadow: 0 2px 8px rgba(61, 139, 240, 0.12);
}

.google-icon {
    width: 18px;
    height: 18px;
}

/* Switch text */
.auth-switch {
    text-align: center;
    font-size: 13px;
    color: #666;
    margin: 0 0 16px;
}

.register-left .auth-switch {
    text-align: left;
}

.auth-link-btn {
    background: none;
    border: none;
    font-size: 13px;
    font-weight: 700;
    color: #e84b0f;
    cursor: pointer;
    padding: 0;
    font-family: 'Public Sans', sans-serif;
    transition: opacity 0.2s;
}

.auth-link-btn:hover {
    opacity: 0.75;
    text-decoration: underline;
}

/* Do it later */
.auth-do-later {
    text-align: center;
}

.auth-do-later-link {
    font-size: 13px;
    color: #e84b0f;
    font-weight: 600;
    text-decoration: none;
}

.auth-do-later-link:hover {
    text-decoration: underline;
}

/* Terms */
.auth-terms {
    display: flex;
    align-items: flex-start;
    gap: 10px;
    font-size: 13px;
    color: #555;
    line-height: 1.5;
    margin-bottom: 20px;
    cursor: pointer;
}

.auth-terms input {
    accent-color: #3d8bf0;
    width: 16px;
    height: 16px;
    flex-shrink: 0;
    margin-top: 2px;
}

.auth-terms-link {
    color: #3d8bf0;
    text-decoration: none;
    font-weight: 600;
}

.auth-terms-link:hover {
    text-decoration: underline;
}

/* Right panel - login */
.login-card .auth-card-right {
    position: relative;
    overflow: hidden;
}

.auth-card-img-placeholder {
    width: 100%;
    height: 100%;
    background: #3a5c3a;
    min-height: 500px;
}

.auth-card-right img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
}

/* Right panel - register */
.register-right {
    background: #f9f9f9;
    padding: 48px 44px;
    display: flex;
    flex-direction: column;
    justify-content: center;
}

/* Responsive */
@media (max-width: 768px) {
    .login-card,
    .register-card {
        grid-template-columns: 1fr;
    }

    .login-card .auth-card-right {
        display: none;
    }

    .auth-card-left {
        padding: 36px 24px;
    }

    .register-right {
        padding: 36px 24px;
    }

    .register-title {
        font-size: 26px;
    }
}
</style>
