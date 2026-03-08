<template>
    <nav aria-label="Page navigation" class="pagination-style-4">
        <ul class="pagination mb-0 flex-wrap justify-content-center">
            <li
                v-for="(item, index) in links"
                :key="index"
                class="page-item"
                :class="{ disabled: !item.url, active: item.active }"
            >
                <Link
                    v-if="item.url"
                    class="page-link"
                    :class="{
                        'text-primary':
                            (index === 0 || index === links.length - 1) &&
                            !!item.url,
                    }"
                    :href="item.url"
                    preserve-state
                    preserve-scroll
                    ><span v-html="item.label"></span
                ></Link>
                <span v-else class="page-link" v-html="item.label"></span>
            </li>
        </ul>
    </nav>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    links: {
        type: Array,
        required: true,
    },
});
</script>
