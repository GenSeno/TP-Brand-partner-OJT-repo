<template>
    <div class="card">
        <div class="card-body p-1">
            <ul class="nav nav-fill nav-justified text-nowrap" role="tablist">
                <li class="nav-item" role="presentation">
                    <button
                        class="nav-link"
                        :class="{ active: form.filter.stage === '' }"
                        role="tab"
                        @click="setCurrentStage('')"
                    >
                        <p class="position-relative mb-1">
                            <i class="feather feather-grid"></i>
                        </p>
                        <p class="mb-0 text-break">All</p>
                    </button>
                </li>
                <li
                    v-for="(state, key) in states"
                    :key="key"
                    class="nav-item"
                    role="presentation"
                >
                    <button
                        class="nav-link"
                        :class="{ active: form.filter.stage === key }"
                        role="tab"
                        @click="setCurrentStage(key)"
                    >
                        <p class="position-relative mb-1">
                            <i :class="`feather ${state.icon}`"></i>
                            <span
                                v-show="state.count > 0"
                                class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-primary"
                            >
                                {{ state.count }}
                            </span>
                        </p>
                        <p class="mb-0 text-break">{{ state.label }}</p>
                    </button>
                </li>
            </ul>
        </div>
    </div>
</template>

<script setup>
defineProps({
    states: {
        type: Object,
        required: true,
    },
});

const form = defineModel('form');

const emit = defineEmits(['update:form']);

const setCurrentStage = (state) => {
    form.value.filter.stage = state;
    emit('update:form', form.value);
};
</script>
