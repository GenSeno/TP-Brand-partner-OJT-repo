<template>
  <Head title="Shipping and Delivery Policy" />
  <PolicyLayout title="Shipping and Delivery Policy" last-updated="June 16, 2026">
    <section>
      <h2>Our Shipping Policy</h2>

      <h3>Processing Time</h3>
      <p>Orders are processed within 1-3 business days after payment confirmation.</p>

      <h3>Delivery Time</h3>
      <p>
        Metro Manila: 2-4 business days<br />
        Provincial Areas: 4-7 business days<br />
        International: 7-14 business days
      </p>

      <h3>Shipping Rates</h3>
      <p>
        Shipping fees are calculated at checkout based on your location and order weight. Free
        shipping is available for orders above a certain threshold as indicated on our site.
      </p>

      <h3>Tracking</h3>
      <p>
        A tracking number will be provided once your order is shipped. You can track your package
        through the carrier's website.
      </p>

      <hr />

      <h2>Third-Party Carrier Terms</h2>
      <p>
        We ship through third-party carriers. Delivery estimates are provided by the carrier and are
        not guaranteed. Claims for lost, damaged, or delayed packages must be filed directly with
        the carrier per their terms.
      </p>

      <ul>
        <li>
          <strong>LBC Express</strong> &mdash;
          <a href="https://www.lbcexpress.com/terms" target="_blank" rel="noopener"
            >LBC Terms of Service</a
          >
        </li>
        <li>
          <strong>J&T Express</strong> &mdash;
          <a href="https://www.jtexpress.ph/terms" target="_blank" rel="noopener"
            >J&T Terms of Service</a
          >
        </li>
        <li>
          <strong>Ninja Van</strong> &mdash;
          <a href="https://www.ninjavan.ph/terms" target="_blank" rel="noopener"
            >Ninja Van Terms</a
          >
        </li>
      </ul>

      <p>
        For shipping-related concerns, please contact us before filing a claim with the carrier so
        we can assist.
      </p>
    </section>
  </PolicyLayout>
</template>

<script setup>
import { Head } from '@inertiajs/vue3';
import PolicyLayout from './PolicyLayout.vue';
</script>
