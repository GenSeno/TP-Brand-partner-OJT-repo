<template>
    <form @submit.prevent="submitForm">
        <div class="card">
            <div class="card-body p-5">
                <div class="login-userheading">
                    <h3>Forgot Password?</h3>
                    <h4>Enter your email and we'll send you a reset link.</h4>
                </div>

                <div v-if="status" class="alert alert-success mb-3">
                    {{ status }}
                </div>

                <div class="mb-3">
                    <label class="form-label required">Email</label>
                    <div class="input-group">
                        <input
                            v-model="form.email"
                            type="email"
                            class="form-control border-end-0"
                            @input="form.clearErrors('email')"
                        />
                        <span class="input-group-text border-start-0">
                            <i class="ti ti-mail"></i>
                        </span>
                    </div>
                    <input-error :message="form.errors.email" />
                </div>

                <div class="form-login mb-0">
                    <submit-btn :loading="form.processing" class="w-100">
                        Send Reset Link
                    </submit-btn>
                </div>

                <div class="mt-3 text-center">
                    <Link :href="route('brand-partner.login')" class="text-orange fs-16 fw-medium">
                        Back to Sign In
                    </Link>
                </div>
            </div>
        </div>
    </form>
</template>

<script setup>
import LoginLayout from '@/layouts/login-layout.vue';
import { Link, useForm } from '@inertiajs/vue3';

defineOptions({ layout: LoginLayout });

const props = defineProps({
    status: String,
});

const form = useForm({
    email: '',
});

const submitForm = () => {
    form.post(route('brand-partner.password.email'));
};
</script>
