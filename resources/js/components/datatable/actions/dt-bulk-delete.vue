<template>
    <a
        data-bs-toggle="modal"
        :data-bs-target="`#delete-records`"
        class="btn btn-icon btn-soft-danger"
        href="javascript:void(0);"
        title="Delete selected"
    >
        <i data-feather="trash-2" class="feather-trash-2"></i>
    </a>

    <div class="modal fade" id="delete-records">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="page-wrapper-new p-0">
                    <div class="p-5 px-3 text-center">
                        <span
                            class="rounded-circle d-inline-flex p-2 bg-danger-transparent mb-2"
                            ><i class="ti ti-trash fs-24 text-danger"></i
                        ></span>
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
                                data-bs-dismiss="modal"
                            >
                                Cancel
                            </button>
                            <Link
                                :href="
                                    route(routeName, {
                                        ids: ids,
                                    })
                                "
                                method="delete"
                                class="btn btn-primary fs-13 fw-medium p-2 px-3"
                                data-bs-dismiss="modal"
                                preserve-state
                                preserve-scroll
                                :replace="true"
                                @success="emitter.emit(emitterEvent, ids)"
                            >
                                Yes Delete
                            </Link>
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
import { Link } from '@inertiajs/vue3';
import { lowerCase } from 'lodash';

defineProps({
    ids: {
        type: Array,
        default: () => [],
    },
    routeName: {
        type: String,
        required: true,
    },
    name: {
        type: String,
        default: 'records',
    },
    emitterEvent: {
        type: String,
        default: 'records-deleted',
    },
});
</script>
