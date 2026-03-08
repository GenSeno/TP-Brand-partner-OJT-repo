<template>
    <div class="card mb-3">
        <div
            class="card-body d-flex justify-content-between align-items-center flex-wrap"
        >
            <div class="d-flex align-items-center">
                <!-- Edit Button -->
                <div class="border-end pe-1 me-1">
                    <button
                        @click="goToProducts"
                        class="btn btn-sm btn-light-ghost"
                        :class="
                            route().current('admin.quotation.item')
                                ? 'disabled'
                                : ''
                        "
                    >
                        <i data-feather="edit" class="feather-edit px-1"></i>
                        <span class="d-none d-lg-inline ml-1">Edit</span>
                    </button>
                </div>

                <!-- Send Email -->
                <!-- Share -->
                <div class="border-end pe-1 me-1">
                    <a
                        href="#"
                        class="btn btn-sm btn-light-ghost"
                        @click="sendEmail"
                        :class="[
                            quotation.status != 'completed' &&
                            quotation.status != 'sent'
                                ? 'disabled'
                                : '',
                            isSending ? 'disabled' : '',
                        ]"
                        :disabled="isSending"
                    >
                        <loading-text :loading="isSending">
                            <i data-feather="mail" class="feather-mail"></i>
                            <span class="d-none d-lg-inline ms-2">
                                {{
                                    quotation.status === 'sent'
                                        ? 'Resend Email'
                                        : 'Send Email'
                                }}
                            </span>
                        </loading-text>
                    </a>
                </div>

                <!-- Share -->
                <div class="border-end pe-1 me-1">
                    <a
                        href="#"
                        class="btn btn-sm btn-light-ghost"
                        @click="shareQuotation"
                        :disabled="sharing"
                    >
                        <loading-text :loading="sharing">
                            <i
                                data-feather="share-2"
                                class="feather-share-2"
                            ></i>
                            <span class="d-none d-lg-inline ms-2">Share</span>
                        </loading-text>
                    </a>
                </div>

                <!-- PDF/Print Button with Loading -->
                <div class="border-end pe-1 me-1">
                    <a
                        class="btn btn-sm btn-light-ghost"
                        @click="pdfDownload"
                        :disabled="loading"
                    >
                        <loading-text :loading="loading">
                            <i
                                data-feather="printer"
                                class="feather-printer"
                            ></i>
                            <span class="d-none d-lg-inline ms-2"
                                >PDF/Print</span
                            >
                        </loading-text>
                    </a>
                </div>

                <!-- Dropdown -->
                <div class="dropdown">
                    <button
                        class="btn btn-sm btn-icon"
                        type="button"
                        data-bs-toggle="dropdown"
                        aria-expanded="false"
                    >
                        <i class="bi bi-three-dots"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <a
                                class="dropdown-item"
                                :class="{
                                    disabled:
                                        quotation.order_id ||
                                        creatingOrder ||
                                        quotation.status === 'cancelled',
                                }"
                                @click="createSalesOrder"
                            >
                                <i
                                    data-feather="file-plus"
                                    class="feather-file-plus me-2"
                                ></i>
                                {{
                                    quotation.order_id
                                        ? 'SO Already Created'
                                        : 'Create SO'
                                }}
                            </a>
                        </li>
                        <li><hr class="dropdown-divider" /></li>
                        <li>
                            <a
                                class="dropdown-item"
                                :class="{
                                    disabled: quotation.status === 'cancelled',
                                }"
                                :aria-disabled="
                                    quotation.status === 'cancelled'
                                "
                                @click="setCancelled"
                            >
                                <i data-feather="x" class="feather-x me-2"></i>
                                Cancel
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
            <!-- Right side buttons -->
            <div class="d-flex gap-2 mt-2 mt-lg-0">
                <a
                    class="btn btn-sm btn-primary"
                    v-if="route().current('admin.quotation.item')"
                    @click="setCompleted"
                    :disabled="saving"
                >
                    <loading-text :loading="saving">
                        <i data-feather="save" class="feather-save"></i>
                        <span class="d-none d-lg-inline ms-2">Save</span>
                    </loading-text>
                </a>
                <a
                    class="btn btn-dark btn-sm"
                    @click="goToPreview"
                    v-if="route().current('admin.quotation.item')"
                >
                    <i data-feather="eye" class="feather-eye"></i>
                    <span class="d-none d-lg-inline ms-2">Preview</span>
                </a>
                <a
                    class="btn btn-sm btn-danger"
                    :class="{
                        disabled: quotation.status === 'cancelled',
                    }"
                    @click="setCancelled"
                >
                    <i data-feather="x" class="feather-x"></i>
                    <span class="d-none d-lg-inline ms-2">Cancel</span>
                </a>
                <a class="btn btn-sm btn-secondary" @click="goBack">
                    <i data-feather="arrow-left" class="feather-arrow-left"></i>
                    <span class="d-none d-lg-inline ms-2">Back</span>
                </a>
            </div>
        </div>
    </div>
</template>
<style scoped>
.border-start-primary {
    border-left: 5px solid #fe9f43;
}
</style>
<script setup>
import { ref, defineEmits } from 'vue';
import { router } from '@inertiajs/vue3';
import * as alert from '@/helpers/alert';
import 'jspdf-autotable';
import axios from 'axios';
const emit = defineEmits(['statusUpdated']);

const loading = ref(false);
const sharing = ref(false);
const isSending = ref(false);
const saving = ref(false);
const creatingOrder = ref(false);
const props = defineProps({
    quotation: Object,
    lines: Object,
    template: String,
    errors: Object,
    auth: Object,
    flash: Object,
    counts: Object,
    refreshSidebar: Function,
});

function goToProducts() {
    router.get(
        route('admin.quotation.item', { quotation: props?.quotation?.id }),
    );
}
function goToPreview() {
    router.get(
        route('admin.quotation.show', { quotation: props?.quotation?.id }),
    );
}
function setCompleted() {
    saving.value = true;
    router.post(
        route('admin.quotation.completed', { quotation: props.quotation.id }),
        {},
        {
            onFinish: () => {
                saving.value = false;
                emit('statusUpdated'); // notify parent
            },
        },
    );
}
function goBack() {
    router.get(route('admin.quotation.index'));
}
function createSalesOrder() {
    if (creatingOrder.value || props.quotation.order_id) return;
    if (
        !confirm(
            'Are you sure you want to create a Sales Order from this quotation?',
        )
    )
        return;

    creatingOrder.value = true;
    router.post(
        route('admin.quotation.create-order', {
            quotation: props.quotation.id,
        }),
        {},
        {
            onFinish: () => {
                creatingOrder.value = false;
            },
        },
    );
}
function setCancelled() {
    router.post(
        route('admin.quotation.cancelled', { quotation: props.quotation.id }),
        {},
        {
            onFinish: () => {
                emit('statusUpdated'); // notify parent
            },
        },
    );
}

function pdfDownload() {
    if (loading.value) return; // prevent multiple clicks
    loading.value = true;

    // Build URL via Ziggy
    const url = route('download.quotation', { quotation: props.quotation.id });

    // Trigger download
    const link = document.createElement('a');
    link.href = url;
    link.click();

    // reset loading after short delay
    setTimeout(() => (loading.value = false), 1000);
}

function sendEmail() {
    if (
        isSending.value ||
        !['completed', 'sent'].includes(props?.quotation.status)
    )
        return;

    isSending.value = true;

    router.post(
        route('admin.quotation.send', { quotation: props?.quotation?.id }),
        {},
        {
            onFinish: () => {
                isSending.value = false; // reset loading
            },
            onError: () => {
                isSending.value = false; // reset on error too
            },
        },
    );
}

function shareQuotation() {
    sharing.value = true;
    axios
        .post(route('admin.quotation.share', props.quotation.id))
        .then((res) => {
            navigator.clipboard.writeText(res.data.url);
            sharing.value = false;
            alert.showSuccess('Share link copied!');
        });
}
</script>
