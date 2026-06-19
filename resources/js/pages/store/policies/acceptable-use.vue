<template>
  <Head title="Acceptable Use Policy" />
  <PolicyLayout title="Acceptable Use Policy" last-updated="June 16, 2026">
    <section>
      <h2>1. Prohibited Activities</h2>
      <p>
        You agree not to use our website or services for any unlawful, harmful, or abusive
        purposes, including but not limited to:
      </p>
      <ul>
        <li>Violating any applicable laws or regulations</li>
        <li>Impersonating any person or entity</li>
        <li>Attempting to gain unauthorized access to our systems</li>
        <li>Uploading malicious code or interfering with site operations</li>
        <li>Engaging in fraudulent or deceptive activities</li>
        <li>Harassing, threatening, or abusing others</li>
      </ul>

      <h2>2. User Content</h2>
      <p>
        Any content you submit (reviews, comments, etc.) must not infringe on third-party rights or
        contain offensive material. We reserve the right to remove content at our discretion.
      </p>

      <h2>3. Account Security</h2>
      <p>
        You are responsible for maintaining the security of your account. Notify us immediately of
        any unauthorized use.
      </p>

      <h2>4. Enforcement</h2>
      <p>
        Violation of this policy may result in account suspension, termination, and legal action
        where applicable.
      </p>
    </section>
  </PolicyLayout>
</template>

<script setup>
import { Head } from '@inertiajs/vue3';
import PolicyLayout from './PolicyLayout.vue';
</script>
