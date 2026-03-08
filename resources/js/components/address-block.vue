<template>
    <address class="address-block">
        <p class="text-black fw-bold">
            {{ props.address.company_name || props.address.full_name }}
        </p>
        <p v-if="props.address.company_name">
            {{ props.address.full_name }}
        </p>
        <p>
            {{ merge(props.address.line1, props.address.line2) }}
        </p>
        <p>
            {{ merge(props.address.barangay, props.address.city) }}
        </p>
        <p>
            {{
                merge(
                    props.address.province,
                    props.address.postcode,
                    props.address.country?.name,
                )
            }}
        </p>
        <p>
            <span v-if="props.address.email"
                >Email:
                <a :href="'mailto:' + props.address.email">{{
                    props.address.email
                }}</a></span
            >
            <span
                v-if="props.address.email && props.address.phone"
                class="separator"
                >·</span
            >
            <span v-if="props.address.phone"
                >Phone:
                <a :href="'tel:' + props.address.phone">{{
                    props.address.phone
                }}</a>
            </span>
        </p>
    </address>
</template>

<script setup>
const props = defineProps({
    address: {
        type: Object,
        required: true,
    },
});

const merge = (...parts) => {
    return parts.filter(Boolean).join(', ');
};
</script>
