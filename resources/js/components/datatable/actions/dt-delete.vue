<template>
    <a
        href="javascript:void(0);"
        :class="$attrs.class || ''"
        v-bind="$attrs"
        @click="open = true"
    >
        <slot></slot>
    </a>
    <Teleport to="body">
        <div class="dd-overlay" v-if="open" @click.self="open = false">
            <div class="dd-modal">
                <span class="dd-icon-wrap">
                    <i class="ti ti-trash fs-24 text-danger"></i>
                </span>
                <h4 class="dd-title">Delete {{ capitalizeWords(name) }}</h4>
                <p class="dd-desc">
                    Are you sure you want to delete this {{ lowerCase(modelName) }}?
                </p>
                <div class="dd-actions">
                    <button
                        type="button"
                        class="btn btn-secondary fs-13 fw-medium p-2 px-3 shadow-none"
                        @click="open = false"
                    >
                        Cancel
                    </button>
                    <Link
                        :href="route(routeName, id)"
                        method="delete"
                        class="btn btn-primary fs-13 fw-medium p-2 px-3"
                        preserve-state
                        preserve-scroll
                        :replace="true"
                        @click="open = false"
                        @success="emitter.emit(emitterEvent, [id])"
                    >
                        Yes Delete
                    </Link>
                </div>
            </div>
        </div>
    </Teleport>
</template>

<script setup>
import { emitter } from '@/composables/eventBus';
import { capitalizeWords } from '@/helpers/string';
import { Link } from '@inertiajs/vue3';
import { lowerCase } from 'lodash';
import { ref, onMounted, onUnmounted } from 'vue';

const props = defineProps({
    id: { type: Number, required: true },
    routeName: { type: String, required: true },
    name: { type: String, default: 'record' },
    modelName: { type: String, default: 'record' },
    emitterEvent: { type: String, default: 'records-deleted' },
});

const open = ref(false);

function onKeyDown(e) {
    if (e.key === 'Escape') open.value = false;
}

onMounted(() => document.addEventListener('keydown', onKeyDown));
onUnmounted(() => document.removeEventListener('keydown', onKeyDown));

defineOptions({ inheritAttrs: false });
</script>

<style scoped>
.dd-overlay {
    position: fixed;
    inset: 0;
    z-index: 9999;
    background: rgba(0, 0, 0, 0.5);
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 24px;
}

.dd-modal {
    background: #fff;
    border-radius: 16px;
    padding: 40px 32px;
    max-width: 420px;
    width: 100%;
    text-align: center;
    box-shadow: 0 24px 64px rgba(0, 0, 0, 0.2);
}

.dd-icon-wrap {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 56px;
    height: 56px;
    border-radius: 50%;
    background: #fef2f2;
    margin-bottom: 16px;
}

.dd-icon-wrap i {
    font-size: 24px;
    color: #dc2626;
}

.dd-title {
    font-size: 18px;
    font-weight: 700;
    margin: 0 0 8px;
    color: #111;
}

.dd-desc {
    font-size: 14px;
    color: #666;
    margin: 0 0 24px;
    line-height: 1.5;
}

.dd-actions {
    display: flex;
    gap: 12px;
    justify-content: center;
}
</style>
