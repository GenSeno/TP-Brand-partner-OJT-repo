<template>
    <div class="main-wrapper">
        <div class="account-content">
            <div class="login-wrapper login-new">
                <div class="row w-100">
                    <div class="col-lg-5 mx-auto">
                        <div class="login-content user-login">
                            <div class="login-logo">
                                <img src="/img/logo/logo_pakaras_white.png" alt="img"/>
                                <span
                                    to="/dashboard"
                                    class="login-logo logo-white"
                                >
                                    <img src="/img/logo-white.svg" alt="Img" />
                                </span>
                            </div>
                            <slot />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { onMounted, onUnmounted } from 'vue';

onMounted(() => {
    document.body.classList.add('account-page', 'bg-white');
});

onUnmounted(() => {
    document.body.classList.remove('account-page', 'bg-white');
});
</script>
