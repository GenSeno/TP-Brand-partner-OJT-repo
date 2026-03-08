<template>
    <div class="main-wrapper" :class="posWrapperClass">
        <slot />
    </div>
</template>

<script setup>
import { computed, onBeforeUnmount, onMounted, ref, watch } from 'vue';

const posType = ref(1);

const posWrapperClass = computed(() => {
    switch (posType.value) {
        case 1:
            return 'pos-five';
        case 2:
            return 'pos-three pos-four';
        case 3:
            return 'pos-two';
        default:
            return '';
    }
});

const updateBody = () => {
    if (posType.value === 2 || posType.value === 3) {
        document.body.classList.add('pos-page');
    } else {
        document.body.classList.remove('pos-page');
    }
};

watch(posType, updateBody);

onMounted(() => {
    updateBody();
});

onBeforeUnmount(() => {
    document.body.classList.remove('pos-page');
});
</script>
