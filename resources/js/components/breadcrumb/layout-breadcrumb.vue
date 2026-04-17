<template>
    <ul class="breadcrumb">
        <!-- Home -->
        <li class="breadcrumb-item">
            <Link href="/">
                <i class="pi pi-home"></i>
            </Link>
        </li>

        <!-- Items -->
        <li
            v-for="(item, index) in items"
            :key="index"
            class="breadcrumb-item"
            :class="{ active: index === items.length - 1 }"
        >
            <Link
                v-if="item.link && index !== items.length - 1"
                :href="item.link"
            >
                {{ item.label }}
            </Link>

            <span v-else>
                {{ item.label }}
            </span>
        </li>
    </ul>
</template>

<script>
import { Link } from '@inertiajs/vue3';

export default {
    components: { Link },

    props: {
        items: {
            type: Array,
            default: () => [],
        },
    },
};
</script>

<style scoped>
li:hover {
    color: #f39c12 !important;
    text-decoration: underline;
}
</style>
