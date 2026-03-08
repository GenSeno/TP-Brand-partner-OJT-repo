<template>
    <button
        type="button"
        :class="btnClass"
        @click="openModal"
        title="Delete selected"
    >
        <i data-feather="trash-2" class="feather-trash-2"></i>
    </button>

    <div ref="modalRef" class="modal fade">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="page-wrapper-new p-0">
                    <div class="p-5 px-3 text-center">
                        <span
                            class="rounded-circle d-inline-flex p-2 bg-danger-transparent mb-2"
                        >
                            <i class="ti ti-trash fs-24 text-danger"></i>
                        </span>
                        <h4 class="fs-20 fw-bold mb-2 mt-1">
                            Delete {{ capitalizeWords(name) }}
                        </h4>
                        <p class="mb-0 fs-16">
                            Are you sure you want to delete these
                            {{ lowerCase(name) }}?
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
                                class="btn btn-danger fs-13 fw-medium p-2 px-3"
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
import { lowerCase } from 'lodash';
import { ref, onMounted, useTemplateRef } from 'vue';
import { Modal } from 'bootstrap';
import axios from 'axios';
import * as alert from '@/helpers/alert';

defineOptions({
    inheritAttrs: false,
});

const props = defineProps({
    ids: {
        type: Array,
        default: () => [],
    },
    name: {
        type: String,
        default: 'records',
    },
    emitterEvent: {
        type: String,
        default: 'records-deleted',
    },
    routeName: {
        type: String,
        required: true,
    },
    btnClass: {
        type: String,
        default: '',
    },
});

const modalRef = useTemplateRef('modalRef');
const modal = ref(null);
const deleting = ref(false);

function openModal() {
    modal.value?.show();
}

function closeModal() {
    modal.value?.hide();
}

async function handleDestroy() {
    if (!props.ids.length) {
        alert.showError(`No ${lowerCase(props.name)} selected.`);
        return;
    }

    deleting.value = true;
    try {
        const { data } = await axios.delete(route(props.routeName), {
            data: { ids: props.ids },
        });

        emitter.emit(props.emitterEvent, data);
        modal.value.hide();
    } catch (error) {
        alert.showError(
            error.response?.data?.message ||
                `Failed to delete the ${lowerCase(props.name)}.`,
        );
    } finally {
        deleting.value = false;
    }
}

onMounted(() => {
    modal.value = Modal.getOrCreateInstance(modalRef.value);
});
</script>
