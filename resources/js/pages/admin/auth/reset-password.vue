<template>
    <form @submit.prevent="submitForm">
        <div class="card">
            <div class="card-body p-5">
                <div class="login-userheading">
                    <h3>Reset Password</h3>
                    <h4>Enter your new password below.</h4>
                </div>

                <div class="mb-3">
                    <label class="form-label required">Email</label>
                    <div class="input-group">
                        <input
                            v-model="form.email"
                            type="email"
                            class="form-control border-end-0"
                            readonly
                        />
                        <span class="input-group-text border-start-0">
                            <i class="ti ti-mail"></i>
                        </span>
                    </div>
                    <input-error :message="form.errors.email" />
                </div>

                <div class="mb-3">
                    <label class="form-label required">New Password</label>
                    <div class="pass-group">
                        <input
                            v-model="form.password"
                            :type="showPassword ? 'text' : 'password'"
                            class="form-control pass-input mt-2"
                            @input="form.clearErrors('password')"
                        />
                        <span @click="toggleShow" class="toggle-password">
                            <i :class="{ 'fas fa-eye': showPassword, 'fas fa-eye-slash': !showPassword }"></i>
                        </span>
                        <input-error :message="form.errors.password" />
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label required">Confirm Password</label>
                    <div class="pass-group">
                        <input
                            v-model="form.password_confirmation"
                            :type="showPassword ? 'text' : 'password'"
                            class="form-control pass-input mt-2"
                            @input="form.clearErrors('password_confirmation')"
                        />
                        <input-error :message="form.errors.password_confirmation" />
                    </div>
                </div>

                <div class="form-login mb-0">
                    <submit-btn :loading="form.processing" class="w-100">
                        Reset Password
                    </submit-btn>
                </div>
            </div>
        </div>
    </form>
</template>

<script setup>
import LoginLayout from '@/layouts/login-layout.vue';
import { useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

defineOptions({ layout: LoginLayout });

const props = defineProps({
    email: String,
    token: String,
});

const showPassword = ref(false);

const form = useForm({
    email: props.email || '',
    password: '',
    password_confirmation: '',
    token: props.token || '',
});

const toggleShow = () => {
    showPassword.value = !showPassword.value;
};

const submitForm = () => {
    form.post(route('admin.password.store'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>
