<template>
    <div class="mt-4 small" :style="set_style">
        <h6 class="fw-bold mb-2">Terms & Conditions</h6>
        <strong>Validity:</strong> Prices valid for {{ validity }} days. By
        paying the down payment, the client agrees to all terms.<br />
        <strong>Artwork:</strong> Production starts once the final artwork is
        approved. Revisions after approval may cause delays and extra charges.
        Slight color variations mayoccur due to screen vs. print differences.<br />
        <strong>Payment:</strong> 50% down payment is required to confirm the
        order. Remaining 50% must be paid before delivery or pick-up.<br />
        <strong>Lead Time:</strong> Standard lead time is 7 working days,
        excluding shipping. Bulk orders may require additional time; an agreed
        deadline will be set before production.<br />
        <strong>Changes & Cancellation:</strong> No changes allowed once
        production has started. Downpayment is non-refundable for cancelled
        orders.<br />
        <strong>Quality:</strong> Issues must bereported within 48 hours of
        receiving the items.<br />
        <strong>Delivery / Shipping:</strong> Full payment required before
        shipping or pick-up. Shipping fees are shouldered by the client. TP Ink
        Lab is not liable for courier delays.<br />
    </div>
    <div class="d-flex align-items-center my-3">
        <div class="flex-grow-1 border-top"></div>
        <div class="px-2 small text-center">
            Thank you for your business, we hope to work with you again!
        </div>
        <div class="flex-grow-1 border-top"></div>
    </div>
    <div class="d-flex justify-content-end align-items-center gap-2">
        <div class="small text-end">Powered by</div>
        <img
            src="/img/tech-hive-logo-black-font.png"
            alt="Techhive"
            style="width: 100px"
            class="mb-2"
        />
    </div>
</template>

<script setup>
const props = defineProps({
    validity: Number,
    style: String,
});

const set_style = props?.style;
</script>
