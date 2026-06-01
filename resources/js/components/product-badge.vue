<template>
  <div class="product-badges">
    <span
      class="badge sale-badge"
      v-if="product.compare_price && product.compare_price > product.price"
      >SALE</span
    >
    <span class="badge new-badge" v-else-if="isNew(product.created_at)"
      >NEW
    </span>
  </div>
</template>

<script setup>
function isNew(createdAt) {
  const dateCreated = new Date(createdAt);
  const now = new Date();

  const diffTime = now - dateCreated;
  const msPerDay = 1000 * 60 * 60 * 24;
  const diffDays = diffTime / msPerDay;

  return diffDays <= 7;
}
</script>
