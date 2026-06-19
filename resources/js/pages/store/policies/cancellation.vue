<template>
  <Head title="Cancellation Policy" />
  <PolicyLayout title="Cancellation Policy" last-updated="June 16, 2026">
    <section>
      <h2>1. Order Cancellation</h2>
      <p>
        You may cancel your order within 24 hours of placing it, provided it has not yet been
        shipped. To cancel, please contact us immediately at tribupakarasph@gmail.com.
      </p>

      <h2>2. Cancellation After Shipping</h2>
      <p>
        Once an order has been shipped, it cannot be canceled. Please refer to our
        <Link :href="route('store.brand-partner.refund')">Refund and Return Policy</Link> for
        returning items after delivery.
      </p>

      <h2>3. Cancellation by Us</h2>
      <p>
        We reserve the right to cancel orders in cases of suspected fraud, inaccurate pricing, or
        stock unavailability. You will be notified and fully refunded.
      </p>

      <h2>4. Refunds for Canceled Orders</h2>
      <p>
        Refunds for canceled orders will be processed to the original payment method within 5-10
        business days.
      </p>
    </section>
  </PolicyLayout>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3';
import PolicyLayout from './PolicyLayout.vue';
</script>
