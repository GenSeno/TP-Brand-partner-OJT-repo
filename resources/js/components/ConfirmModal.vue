<template>
    <div v-if="modelValue">
        <div
            class="modal fade show d-block"
            tabindex="-1"
            role="dialog"
            style="background: rgba(0, 0, 0, 0.4)"
        >
            <div
                class="modal-dialog modal-sm modal-dialog-centered"
                role="document"
            >
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ title }}</h5>
                        <button
                            type="button"
                            class="btn-close"
                            @click="onCancel"
                            aria-label="Close"
                        ></button>
                    </div>
                    <div class="modal-body">
                        <p class="mb-0">{{ message }}</p>
                    </div>
                    <div class="modal-footer">
                        <button
                            type="button"
                            class="btn btn-secondary btn-sm"
                            @click="onCancel"
                        >
                            {{ cancelText }}
                        </button>
                        <button
                            type="button"
                            class="btn btn-primary btn-sm"
                            @click="onConfirm"
                        >
                            {{ okText }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
const props = defineProps({
    modelValue: { type: Boolean, default: false },
    title: { type: String, default: 'Confirm' },
    message: { type: String, default: 'Are you sure?' },
    okText: { type: String, default: 'Yes' },
    cancelText: { type: String, default: 'Cancel' },
});

const emit = defineEmits(['update:modelValue', 'confirm', 'cancel']);

function onConfirm() {
    emit('confirm');
    emit('update:modelValue', false);
}

function onCancel() {
    emit('cancel');
    emit('update:modelValue', false);
}
</script>

<style scoped></style>
