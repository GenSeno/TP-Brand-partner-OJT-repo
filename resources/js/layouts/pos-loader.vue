<template>
    <div>
        <div v-if="isLoading" id="global-loader">
            <div class="whirly-loader"></div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';

const isLoading = ref(true);

onMounted(() => {
    setTimeout(() => {
        setTimeout(() => {
            isLoading.value = false;
        }, 100);
    }, 500);
});
</script>
