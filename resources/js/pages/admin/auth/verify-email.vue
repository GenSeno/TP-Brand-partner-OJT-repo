<template>
    <div class="card">
        <div class="card-body p-5 text-center">
            <div class="login-userheading">
                <h3>Verify Your Email</h3>
                <h4>Please verify your email address to continue.</h4>
            </div>

            <div v-if="status" class="alert alert-success mb-3">
                {{ status }}
            </div>

            <p class="text-muted mb-4">
                Before accessing the admin panel, please verify your email address.
                If you didn't receive the email, click the button below.
            </p>

            <div class="form-login mb-0">
                <submit-btn :loading="form.processing" class="w-100" @click="submitForm">
                    Resend Verification Email
                </submit-btn>
            </div>

            <div class="mt-3">
                <Link :href="route('admin.logout')" method="post" as="button" class="text-orange fs-16 fw-medium">
                    Log Out
                </Link>
            </div>
        </div>
    </div>
</template>

<script setup>
import LoginLayout from '@/layouts/login-layout.vue';
import { Link, useForm } from '@inertiajs/vue3';

defineOptions({ layout: LoginLayout });

const props = defineProps({
    status: String,
});

const form = useForm({});

const submitForm = () => {
    form.post(route('verification.send'));
};
</script>
