<template>
  <div class="policy-page-wrapper">
    <div class="policy-container">
      <div class="policy-header">
        <h1 class="policy-title">{{ title }}</h1>
        <p v-if="lastUpdated" class="policy-date">Last Updated: {{ lastUpdated }}</p>
      </div>
      <div class="policy-divider"></div>
      <div class="policy-body">
        <slot />
      </div>
    </div>
  </div>
</template>

<script setup>
defineProps({
  title: { type: String, required: true },
  lastUpdated: { type: String, default: null },
});
</script>

<style scoped>
.policy-page-wrapper {
  min-height: 60vh;
  padding: 120px 24px 60px;
  background: #f5f5f5;
}

.policy-container {
  max-width: 800px;
  margin: 0 auto;
  background: #fff;
  border-radius: 12px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.06);
  padding: 40px 48px;
}

.policy-header {
  margin-bottom: 4px;
}

.policy-title {
  font-size: 26px;
  font-weight: 700;
  color: #111;
  margin: 0 0 6px;
  line-height: 1.3;
  letter-spacing: -0.01em;
}

.policy-date {
  font-size: 13px;
  color: #888;
  margin: 0;
}

.policy-divider {
  height: 1px;
  background: #e8e8e8;
  margin: 20px 0 28px;
}

.policy-body {
  color: #444;
  font-size: 15px;
  line-height: 1.75;
}

.policy-body section {
  margin: 0;
}

.policy-body h2 {
  font-size: 17px;
  font-weight: 600;
  color: #111;
  margin: 28px 0 10px;
}

.policy-body h2:first-child {
  margin-top: 0;
}

.policy-body h3 {
  font-size: 15px;
  font-weight: 600;
  color: #222;
  margin: 20px 0 8px;
}

.policy-body p {
  margin: 0 0 12px;
}

.policy-body ul {
  padding-left: 20px;
  margin: 0 0 14px;
  list-style: disc;
}

.policy-body li {
  margin-bottom: 4px;
}

.policy-body a {
  color: #ff9505;
  text-decoration: none;
  font-weight: 500;
}

.policy-body a:hover {
  text-decoration: underline;
}

.policy-body hr {
  border: none;
  border-top: 1px solid #eee;
  margin: 24px 0;
}

@media (max-width: 768px) {
  .policy-page-wrapper {
    padding: 100px 16px 40px;
  }

  .policy-container {
    padding: 24px 20px;
    border-radius: 8px;
  }

  .policy-title {
    font-size: 20px;
  }

  .policy-body {
    font-size: 14px;
  }

  .policy-body h2 {
    font-size: 15px;
  }
}
</style>
