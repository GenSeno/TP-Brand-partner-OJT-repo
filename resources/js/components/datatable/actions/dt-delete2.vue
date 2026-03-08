<template>
    <button
        type="button"
        :class="$attrs.class || ''"
        v-bind="$attrs"
        @click="openModal"
    >
        <slot></slot>
    </button>

    <div ref="modalRef" class="modal fade">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="page-wrapper-new p-0">
                    <div class="p-5 px-3 text-center">
                        <span
                            class="rounded-circle d-inline-flex p-2 bg-danger-transparent mb-2"
                            ><i class="ti ti-trash fs-24 text-danger"></i
                        ></span>
                        <h4 class="fs-20 fw-bold mb-2 mt-1">
                            Delete {{ capitalizeWords(props.recordName) }}
                        </h4>
                        <p class="mb-0 fs-16">
                            Are you sure you want to delete this
                            {{ lowerCase(props.modelName) }}?
                        </p>
                        <div
                            class="modal-footer-btn mt-3 d-flex justify-content-center"
                        >
                            <button
                                type="button"
                                class="btn me-2 btn-secondary fs-13 fw-medium p-2 px-3 shadow-none"
                                @click="closeModal"
                            >
                                Cancel
                            </button>
                            <button
                                class="btn btn-primary fs-13 fw-medium p-2 px-3"
                                :disabled="deleting"
                                @click="handleDestroy"
                            >
                                <loading-text :loading="deleting">
                                    Yes Delete
                                </loading-text>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { emitter } from '@/composables/eventBus';
import { capitalizeWords } from '@/helpers/string';
import axios from 'axios';
import { lowerCase } from 'lodash';
import { ref, onMounted, useTemplateRef } from 'vue';
import { Modal } from 'bootstrap';
import * as alert from '@/helpers/alert';

const props = defineProps({
    url: {
        type: String,
        required: true,
    },
    recordName: {
        type: String,
        default: 'record',
    },
    modelName: {
        type: String,
        default: 'record',
    },
    emitterEvent: {
        type: String,
        default: 'records-deleted',
    },
});

const modalRef = useTemplateRef('modalRef');
const modal = ref(null);
const deleting = ref(false);

function openModal() {
    if (modal.value) {
        modal.value.show();
    }
}

function closeModal() {
    if (modal.value) {
        modal.value.hide();
    }
}

function handleDestroy() {
    deleting.value = true;
    axios
        .delete(props.url)
        .then(({ data }) => {
            emitter.emit(props.emitterEvent, data);
            modal.value.hide();
        })
        .catch((error) => {
            alert.showError(
                error.response?.data?.message ||
                    `Failed to delete the ${lowerCase(props.modelName)}.`,
            );
        })
        .finally(() => {
            deleting.value = false;
        });
}

onMounted(() => {
    modal.value = Modal.getOrCreateInstance(modalRef.value);
});

defineOptions({
    inheritAttrs: false,
});
</script>
