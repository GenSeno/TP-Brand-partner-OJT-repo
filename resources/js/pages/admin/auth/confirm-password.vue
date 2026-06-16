<template>
    <form @submit.prevent="submitForm">
        <div class="card">
            <div class="card-body p-5">
                <div class="login-userheading">
                    <h3>Confirm Password</h3>
                    <h4>Please confirm your password before continuing.</h4>
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
                        <span @click="toggleShow" class="toggle-password">
                            <i :class="{ 'fas fa-eye': showPassword, 'fas fa-eye-slash': !showPassword }"></i>
                        </span>
                        <input-error :message="form.errors.password" />
                    </div>
                </div>

                <div class="form-login mb-0">
                    <submit-btn :loading="form.processing" class="w-100">
                        Confirm
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

const showPassword = ref(false);

const form = useForm({
    password: '',
});

const toggleShow = () => {
    showPassword.value = !showPassword.value;
};

const submitForm = () => {
    form.post(route('admin.password.confirm'), {
        onFinish: () => form.reset('password'),
    });
};
</script>
