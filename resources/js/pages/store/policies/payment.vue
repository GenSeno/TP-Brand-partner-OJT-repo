<template>
  <Head title="Payment Policy" />
  <PolicyLayout title="Payment Policy" last-updated="June 16, 2026">
    <section>
      <h2>Our Payment Policy</h2>

      <h3>Accepted Payment Methods</h3>
      <p>We accept the following payment methods:</p>
      <ul>
        <li>Credit/Debit Cards (Visa, Mastercard, JCB)</li>
        <li>GCash</li>
        <li>PayMaya</li>
        <li>Bank Transfers</li>
        <li>Cash on Delivery (select areas)</li>
      </ul>

      <h3>Currency</h3>
      <p>All transactions are processed in Philippine Pesos (PHP).</p>

      <h3>Payment Authorization</h3>
      <p>
        Your payment will be authorized at the time of purchase and captured upon order
        confirmation. For Cash on Delivery, payment is collected upon delivery.
      </p>

      <h3>Invoices</h3>
      <p>
        An invoice will be sent to your email after payment is confirmed. Please retain this for
        your records.
      </p>

      <hr />

      <h2>Third-Party Payment Gateway Terms</h2>
      <p>
        We process payments through secure third-party gateways. By completing a purchase, you also
        agree to the respective gateway's terms and conditions. TP Ink Lab does not store full
        payment card details.
      </p>

      <ul>
        <li>
          <strong>Xendit</strong> &mdash;
          <a href="https://www.xendit.co/terms" target="_blank" rel="noopener"
            >Xendit Terms of Service</a
          >
        </li>
        <li>
          <strong>PayMongo</strong> &mdash;
          <a href="https://paymongo.com/terms" target="_blank" rel="noopener"
            >PayMongo Terms of Service</a
          >
        </li>
      </ul>
    </section>
  </PolicyLayout>
</template>

<script setup>
import { Head } from '@inertiajs/vue3';
import PolicyLayout from './PolicyLayout.vue';
</script>
