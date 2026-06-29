<template>
  <Head :title="policy.title" />
  <PolicyLayout :title="policy.title" :last-updated="policy.lastUpdatedDate">
    <section v-html="policy.content"></section>
  </PolicyLayout>
</template>

<script setup>
import { Head } from '@inertiajs/vue3';
import PolicyLayout from './policies/PolicyLayout.vue';

defineProps({
  policy: {
    type: Object,
    required: true,
  },
});
</script>
