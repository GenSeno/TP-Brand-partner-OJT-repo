<template>
    <Head title="View Sales Order" />

    <div class="row">
        <div class="content">
            <div class="d-flex page-header">
                <!-- ORDER INFO -->
                <div class="col-md-6 page-title">
                    <h4>Sales Order No. {{ order.reference }}</h4>
                    <h6>Manage Your Sales Order</h6>
                </div>
                <div class="col-md-6 text-end">
                    <h6 class="text-end text-uppercase">
                        Status: <OrderStatus :status="order.status" />
                    </h6>
                </div>
            </div>
            <div class="row">
                <!-- LEFT LIST -->
                <div class="col-lg-3">
                    <Sidebar :order="order" ref="sidebar" />
                </div>

                <!-- MAIN CONTENT -->
                <div class="col-lg-9 col-md-12 col-sm-12">
                    <!-- TOP ACTIONS BAR -->
                    <div class="card mb-3">
                        <div
                            class="card-body d-flex justify-content-between align-items-center flex-wrap"
                        >
                            <div class="d-flex align-items-center">
                                <div class="border-end pe-1 me-1">
                                    <button
                                        @click="isEdit = !isEdit"
                                        class="btn btn-sm btn-light-ghost"
                                        :class="{ active: isEdit }"
                                    >
                                        <i
                                            data-feather="edit"
                                            class="feather-edit me-1"
                                        ></i>
                                        Edit
                                    </button>
                                </div>

                                <div class="border-end pe-1 me-1">
                                    <a
                                        href="#"
                                        class="btn btn-sm btn-light-ghost"
                                        :class="{
                                            disabled:
                                                order.billing_summary
                                                    ?.amount_billed?.value > 0,
                                        }"
                                        :title="
                                            order.billing_summary?.amount_billed
                                                ?.value > 0
                                                ? 'Cannot print SO: order has already been billed'
                                                : ''
                                        "
                                    >
                                        <i
                                            data-feather="printer"
                                            class="feather-printer me-1"
                                        ></i>
                                        PDF/Print
                                    </a>
                                </div>

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
                                        <li
                                            v-if="
                                                order.billing_summary
                                                    .amount_unbilled.value > 0
                                            "
                                        >
                                            <ModalLink
                                                navigate
                                                :href="
                                                    route(
                                                        'admin.order.billing.create',
                                                        order.id,
                                                    )
                                                "
                                                class="dropdown-item"
                                            >
                                                <i
                                                    data-feather="file-text"
                                                    class="feather-file-text px-1"
                                                ></i>
                                                New Billing
                                            </ModalLink>
                                        </li>
                                        <li>
                                            <button
                                                type="button"
                                                class="dropdown-item"
                                                @click="createJobOrder"
                                                :disabled="!canCreateJobOrder"
                                            >
                                                <i
                                                    data-feather="file-plus"
                                                    class="feather-file-plus px-1"
                                                ></i>
                                                Create Job Order
                                            </button>
                                        </li>
                                    </ul>
                                </div>
                            </div>

                            <!-- Right side buttons -->
                            <div class="d-flex gap-2 mt-2 mt-lg-0">
                                <a
                                    v-show="isEdit"
                                    class="btn btn-sm btn-primary"
                                    @click="showReasonModal('save')"
                                >
                                    <i
                                        data-feather="save"
                                        class="feather-save"
                                    ></i>
                                    <span class="d-none d-lg-inline ms-2"
                                        >Save</span
                                    >
                                </a>
                                <a
                                    v-show="isEdit"
                                    class="btn btn-dark btn-sm"
                                    @click="isEdit = false"
                                >
                                    <i
                                        data-feather="eye"
                                        class="feather-eye"
                                    ></i>
                                    <span class="d-none d-lg-inline ms-2"
                                        >Preview</span
                                    >
                                </a>
                                <a
                                    class="btn btn-sm btn-danger"
                                    :class="{
                                        disabled: order.status === 'cancelled',
                                    }"
                                    @click="showReasonModal('cancel')"
                                >
                                    <i data-feather="x" class="feather-x"></i>
                                    <span class="d-none d-lg-inline ms-2"
                                        >Cancel</span
                                    >
                                </a>
                                <a
                                    class="btn btn-sm btn-secondary"
                                    @click="goBack"
                                >
                                    <i
                                        data-feather="arrow-left"
                                        class="feather-arrow-left"
                                    ></i>
                                    <span class="d-none d-lg-inline ms-2"
                                        >Back</span
                                    >
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Reason Modal -->
                    <div
                        class="modal fade"
                        id="reasonModal"
                        tabindex="-1"
                        aria-labelledby="reasonModalLabel"
                        aria-hidden="true"
                        ref="reasonModalRef"
                    >
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5
                                        class="modal-title"
                                        id="reasonModalLabel"
                                    >
                                        {{
                                            reasonAction === 'cancel'
                                                ? 'Cancel Order'
                                                : 'Save Changes'
                                        }}
                                    </h5>
                                    <button
                                        type="button"
                                        class="btn-close"
                                        data-bs-dismiss="modal"
                                        aria-label="Close"
                                    ></button>
                                </div>
                                <div class="modal-body">
                                    <p class="text-muted">
                                        {{
                                            reasonAction === 'cancel'
                                                ? 'Please provide a reason for cancelling this order.'
                                                : 'Please provide a reason for altering the details.'
                                        }}
                                    </p>
                                    <textarea
                                        v-model="reasonText"
                                        class="form-control"
                                        rows="3"
                                        placeholder="Enter reason..."
                                    ></textarea>
                                </div>
                                <div class="modal-footer">
                                    <button
                                        type="button"
                                        class="btn btn-secondary btn-sm"
                                        data-bs-dismiss="modal"
                                    >
                                        Close
                                    </button>
                                    <button
                                        type="button"
                                        class="btn btn-sm"
                                        :class="
                                            reasonAction === 'cancel'
                                                ? 'btn-danger'
                                                : 'btn-primary'
                                        "
                                        @click="confirmReason"
                                        :disabled="
                                            !reasonText.trim() || reasonLoading
                                        "
                                    >
                                        <i
                                            v-if="reasonLoading"
                                            class="spinner-border spinner-border-sm me-1"
                                        ></i>
                                        {{
                                            reasonAction === 'cancel'
                                                ? 'Confirm Cancel'
                                                : 'Confirm Save'
                                        }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- PREVIEW AREA -->
                    <div class="card">
                        <div class="card-body">
                            <Preview
                                v-model:is-edit="isEdit"
                                v-model:form="form"
                                :order="order"
                            />
                        </div>
                    </div>

                    <div>
                        <NotesHistory
                            :post-url="route('admin.order.note', order.id)"
                            :activities="activities"
                        />
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { Head, router, useForm } from '@inertiajs/vue3';
import DashboardLayout from '@/layouts/dashboard-layout.vue';
import OrderStatus from '@/components/order/order-status.vue';
import Preview from './preview.vue';
import Sidebar from './sidebar.vue';
import dayjs from 'dayjs';
import { onMounted, nextTick, ref, watch } from 'vue';
import { Modal } from 'bootstrap';
import { emitter } from '@/composables/eventBus';
import axios from 'axios';
import * as alert from '@/helpers/alert';
import NotesHistory from '@/components/notes-history.vue';

defineOptions({
    layout: DashboardLayout,
});

const props = defineProps({
    order: Object,
    canCreateJobOrder: Boolean,
    activities: Array,
});

const sidebar = ref(null);
const dateFormat = 'MMM DD, YYYY';
const isEdit = ref(false);
const form = useForm({
    placed_at: props.order.placed_at || '',
    expected_delivery: props.order.expected_delivery || '',
    notes: props.order.notes || '',
});

// Reason modal state
const reasonModalRef = ref(null);
const reasonText = ref('');
const reasonAction = ref('');
const reasonLoading = ref(false);
let bsModal = null;

const showReasonModal = (action) => {
    reasonAction.value = action;
    reasonText.value = '';
    reasonLoading.value = false;
    nextTick(() => {
        if (!bsModal) {
            bsModal = new Modal(reasonModalRef.value);
        }
        bsModal.show();
    });
};

const confirmReason = () => {
    if (!reasonText.value.trim()) return;
    reasonLoading.value = true;

    if (reasonAction.value === 'cancel') {
        cancelOrder();
    } else {
        submitForm();
    }
};

function cancelOrder() {
    axios
        .post(route('admin.order.cancel', props.order.id), {
            reason: reasonText.value,
        })
        .then(({ data }) => {
            bsModal.hide();
            alert.showSuccess(data.message || 'Order cancelled successfully.');
            router.reload();
        })
        .catch((error) => {
            alert.showError(
                error.response?.data?.message || 'Failed to cancel order.',
            );
        })
        .finally(() => {
            reasonLoading.value = false;
        });
}

function goBack() {
    router.get(route('admin.order.index'));
}

function createJobOrder() {
    axios
        .post(route('admin.order.job-order.create', props.order.id))
        .then(({ data }) => {
            alert.showSuccess(
                data.message || 'Job Order created successfully.',
            );
        })
        .catch((error) => {
            alert.showError(
                error.response.data.message ||
                    'Failed to create Job Order from Sales Order.',
            );
        });
}

const submitForm = () => {
    form.transform((data) => ({
        ...data,
        reason: reasonText.value,
    })).put(route('admin.order.update', props.order.id), {
        onSuccess: () => {
            form.defaults({
                placed_at: props.order.placed_at || '',
                expected_delivery: props.order.expected_delivery || '',
                notes: props.order.notes || '',
            });
        },
        onFinish: () => {
            isEdit.value = false;
            reasonLoading.value = false;
            if (bsModal) bsModal.hide();
        },
        preserveState: true,
        preserveScroll: true,
    });
};

watch(isEdit, (value) => {
    if (!value) {
        form.reset();
    }
});

onMounted(() => {
    emitter.on('order:address-updated', () => {
        router.reload({
            onFinish: () => {
                isEdit.value = false;
            },
        });
    });

    emitter.on('note:created', () => {
        router.reload({
            only: ['activities'],
        });
    });

    emitter.on('order:item-updated', () => {
        router.reload({ preserveScroll: true });
    });

    emitter.on('order:item-deleted', () => {
        router.reload({
            only: ['activities'],
        });
    });
});
</script>
