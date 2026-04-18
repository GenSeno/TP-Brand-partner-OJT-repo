<template>
    <ul class="breadcrumb">
        <!-- Home with Icon and Text together -->
        <li class="breadcrumb-item">
            <Link href="/" class="breadcrumb-home">
                <span>Home</span>
            </Link>
        </li>

        <!-- Other Items -->
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
.breadcrumb {
    display: flex;
    flex-wrap: wrap;
    padding: 12px 0;
    margin-bottom: 8px;
    list-style: none;
    background: transparent;
    font-family: 'Public Sans', sans-serif;
    user-select: none;
}

.breadcrumb-item {
    display: flex;
    align-items: center;
    font-size: 14px;
    color: #666;
}

.breadcrumb-item:not(:last-child)::after {
    margin: 0 10px;
    color: #ccc;
}

.breadcrumb-home {
    display: flex;
    align-items: center;
    gap: 6px;
    color: #666;
    transition: color 0.2s;
}

.breadcrumb-item a {
    color: #666;
    text-decoration: none;
    transition: color 0.2s;
}

.breadcrumb-item a:hover {
    color: #ff9505;
    text-decoration: underline;
}
</style>
