<template>
    <div class="search-set">
        <div class="search-input">
            <a href="#" class="btn-searchset"
                ><i data-feather="search" class="feather-search"></i
            ></a>
            <input
                v-model="search"
                type="search"
                class="form-control form-control-sm"
                :placeholder="placeholder"
            />
        </div>
    </div>
</template>

<script setup>
import { debounce } from 'lodash';
import { watch } from 'vue';

const emit = defineEmits(['search']);

const search = defineModel({
    type: String,
    required: true,
});

const props = defineProps({
    debounceTime: {
        type: Number,
        default: 500,
    },
    placeholder: {
        type: String,
        default: 'Search',
    },
});

watch(
    search,
    debounce((newValue) => {
        emit('search', newValue);
    }, props.debounceTime),
);
</script>
