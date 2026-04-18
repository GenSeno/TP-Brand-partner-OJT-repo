<template>
    <form @submit.prevent="submitForm">
        <div class="card">
            <div class="card-body p-5">
                <div class="login-userheading">
                    <h3>Brand Partner Sign In</h3>
                    <h4>
                        Access your partner dashboard using your email and
                        password.
                    </h4>
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
                <div class="mb-3">
                    <label class="form-label required">Password</label>
                    <div class="pass-group">
                        <input
                            v-model="form.password"
                            :type="showPassword ? 'text' : 'password'"
                            class="form-control pass-input mt-2"
                            @input="form.clearErrors('password')"
                        />
                        <span
                            @click="toggleShow"
                            class="toggle-password"
                            :title="buttonLabel"
                        >
                            <i
                                :class="{
                                    'fas fa-eye': showPassword,
                                    'fas fa-eye-slash': !showPassword,
                                }"
                            ></i>
                        </span>
                        <input-error :message="form.errors.password" />
                    </div>
                </div>
                <div class="form-login authentication-check">
                    <div class="row">
                        <div
                            class="col-12 d-flex align-items-center justify-content-between"
                        >
                            <div class="custom-control custom-checkbox">
                                <label
                                    class="checkboxs ps-4 mb-0 pb-0 line-height-1 fs-16 text-gray-6"
                                >
                                    <input
                                        type="checkbox"
                                        class="form-control"
                                    />
                                    <span class="checkmarks"></span>Remember me
                                </label>
                            </div>
                            <div v-if="canResetPassword" class="text-end">
                                <Link
                                    :href="
                                        route('brand-partner.password.request')
                                    "
                                    class="text-orange fs-16 fw-medium"
                                    >Forgot Password?</Link
                                >
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-login mb-0">
                    <submit-btn :loading="form.processing" class="w-100">
                        Sign In
                    </submit-btn>
                </div>
            </div>
        </div>
    </form>
</template>
<script setup>
import LoginLayout from '@/layouts/login-layout.vue';
import { Link, useForm } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

defineOptions({
    layout: LoginLayout,
});

defineProps({
    canResetPassword: Boolean,
    status: String,
});

const showPassword = ref(false);

const form = useForm({
    email: 'pakaras@tpinklab.com',
    password: 'password',
    remember: false,
});

const buttonLabel = computed(() => {
    return showPassword.value ? 'Hide' : 'Show';
});

const toggleShow = () => {
    showPassword.value = !showPassword.value;
};

const submitForm = () => {
    form.post(route('admin.login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>
