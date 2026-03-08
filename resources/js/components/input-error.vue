<template>
    <div v-show="message">
        <small class="text-danger" style="white-space: normal">
            {{ message }}
        </small>
    </div>
</template>

<script setup>
defineProps({
    message: String,
});
</script>
