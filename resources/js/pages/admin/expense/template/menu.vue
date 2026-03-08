<style scoped>
.border-start-primary {
    border-left: 5px solid #fe9f43;
}
</style>

<template>
    <div class="card mb-3">
        <div
            class="card-body d-flex justify-content-between align-items-center flex-wrap"
        >
            <div class="action-more d-flex align-items-center flex-wrap gap-1">
                <!-- Edit Button -->
                <button
                    @click="gotoItems"
                    class="btn btn-sm btn-light"
                    :class="
                        route().current('admin.expense.item') ? 'disabled' : ''
                    "
                >
                    <i data-feather="edit" class="feather-edit px-1"></i>
                    <span class="d-none d-lg-inline ms-1">Edit</span>
                </button>
                <span class="border-end d-none d-lg-inline">&nbsp;</span>
                <!-- PDF/Print Button with Loading -->
                <a
                    class="btn btn-sm btn-light"
                    @click="pdfDownload"
                    :disabled="loading"
                >
                    <loading-text :loading="loading">
                        <i
                            data-feather="printer"
                            class="feather-printer px-1"
                        ></i>
                        <span class="d-none d-lg-inline ms-1">PDF/Print</span>
                    </loading-text>
                </a>
                <span class="border-end d-none d-lg-inline">&nbsp;</span>
                <!-- Dropdown -->
                <div class="dropdown">
                    <button
                        class="btn btn-link m-1 btn-light"
                        type="button"
                        data-bs-toggle="dropdown"
                        aria-expanded="false"
                    >
                        <i class="bi bi-three-dots"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <!-- <li><a class="dropdown-item" href="#"><i data-feather="file-plus" class="feather-file-plus px-1"></i>Create Billing</a></li> -->
                        <li>
                            <a
                                class="dropdown-item"
                                :class="{
                                    disabled: expense.status === 'cancelled',
                                }"
                                :aria-disabled="expense.status === 'cancelled'"
                                @click="setCancelled"
                            >
                                <i data-feather="x" class="feather-x me-1"></i>
                                Cancel
                            </a>
                        </li>
                        <li><hr class="dropdown-divider" /></li>
                        <li class="dropdown-item">Expense Account</li>
                        <li>
                            <ModalLink
                                navigate
                                :href="route('admin.expense_account.create')"
                                class="btn btn-added dropdown-item mt-0"
                                #default="{ loading }"
                            >
                                <loading-text :loading="loading">
                                    <i
                                        data-feather="plus-circle"
                                        class="feather-plus-circle me-1"
                                    ></i>
                                    Add New
                                </loading-text>
                            </ModalLink>
                        </li>
                        <li><hr class="dropdown-divider" /></li>
                    </ul>
                </div>
            </div>
            <!-- Right side buttons -->
            <div class="d-flex gap-2 mt-2 mt-lg-0">
                <a
                    class="btn btn-primary btn-sm"
                    v-if="
                        route().current('admin.expense.item') ||
                        route().current('admin.expense.adjustment')
                    "
                    :class="{ disabled: expense.status === 'completed' }"
                    :aria-disabled="expense.status === 'completed'"
                    @click="submitUpcoming('upcoming')"
                    :disabled="isSaving"
                >
                    <loading-text :loading="isSaving">
                        <i data-feather="save" class="feather-save"></i>
                        <span class="d-none d-lg-inline">&nbsp;Save</span>
                    </loading-text>
                </a>
                <ModalLink
                    v-if="
                        route().current('admin.expense.show', {
                            expense: props.expense.id,
                        }) && props.expense.status === 'upcoming'
                    "
                    navigate
                    :href="route('admin.expense.paid', props.expense.id)"
                    class="btn btn-sm btn-secondary"
                    #default="{ loading }"
                >
                    <loading-text :loading="loading">
                        <i
                            data-feather="check-square"
                            class="feather-check-square"
                        ></i>
                        Mark as Paid
                    </loading-text>
                </ModalLink>
                <a
                    class="btn btn-secondary btn-sm"
                    v-if="
                        route().current('admin.expense.item') ||
                        route().current('admin.expense.adjustment')
                    "
                    @click="submitDraft('draft')"
                    :disabled="isSaving1"
                >
                    <loading-text :loading="isSaving1">
                        <i data-feather="save" class="feather-file"></i>
                        <span class="d-none d-lg-inline"
                            >&nbsp;Save as draft</span
                        >
                    </loading-text>
                </a>
                <a
                    class="btn btn-dark btn-sm"
                    @click="goToPreview"
                    v-if="
                        route().current('admin.expense.item') ||
                        route().current('admin.expense.adjustment')
                    "
                >
                    <i data-feather="eye" class="feather-eye"></i>
                    <span class="d-none d-lg-inline">&nbsp;Preview</span>
                </a>
                <dt-delete
                    :id="expense.id"
                    route-name="admin.expense.destroy"
                    :name="expense.reference"
                    model-name="expense"
                    :emitter-event="deleteEmitterEvent"
                    class="btn btn-sm btn-danger"
                    :class="[
                        expense.status != 'request' &&
                        expense.status != 'draft' &&
                        expense.status != 'cancelled'
                            ? 'disabled'
                            : '',
                    ]"
                    title="Delete"
                >
                    <i data-feather="trash" class="feather-trash px-1"></i>
                    <span class="d-none d-lg-inline">&nbsp;Delete</span>
                </dt-delete>
            </div>
        </div>
    </div>
</template>
<script setup>
import { ref, onMounted, onUnmounted, defineEmits } from 'vue';
import { router } from '@inertiajs/vue3';
import * as alert from '@/helpers/alert';
import html2pdf from 'html2pdf.js';
import 'jspdf-autotable';
import { emitter } from '@/composables/eventBus';

const emit = defineEmits(['statusUpdated']);

const props = defineProps({
    expense: Object,
    lines: Object,
    template: String,
    errors: Object,
    auth: Object,
    flash: Object,
    counts: Object,
    refreshSidebar: Function,
});

const submitExpenseLinesEmitterEvent = ref('header:submitExpenseLines');
const saveFinishedEmitterEvent = ref('header:saveFinished');
const isSaving = ref(false);
const isSaving1 = ref(false);
const loading = ref(false);
const deleteEmitterEvent = ref('expense-deleted');

function gotoItems() {
    router.get(route('admin.expense.item', { expense: props?.expense?.id }));
}

function goToPreview() {
    router.get(route('admin.expense.show', { expense: props?.expense?.id }));
}

function setCancelled() {
    router.post(
        route('admin.expense.cancelled', { expense: props.expense.id }),
        {},
        {
            preserveState: false,
        },
    );
}

function pdfDownload() {
    if (loading.value) return; // prevent multiple clicks
    loading.value = true;

    // Build URL via Ziggy
    const url = route('admin.expense.pdf.download', {
        expense: props.expense.id,
    });

    // Trigger download
    const link = document.createElement('a');
    link.href = url;
    link.click();

    // reset loading after short delay
    setTimeout(() => (loading.value = false), 1000);
}

onMounted(() => {
    emitter.on(deleteEmitterEvent.value, () => {
        router.reload({
            preserveState: true,
            preserveScroll: true,
            replace: true,
        });
    });
});

const submitUpcoming = (status) => {
    isSaving.value = true;
    emitter.emit(submitExpenseLinesEmitterEvent.value, status);
};

const submitDraft = (status) => {
    isSaving1.value = true;
    emitter.emit(submitExpenseLinesEmitterEvent.value, status);
};

onMounted(() => {
    emitter.on(saveFinishedEmitterEvent.value, () => {
        isSaving.value = false;
        isSaving1.value = false;
    });
});

onUnmounted(() => {
    emitter.off(saveFinishedEmitterEvent.value);
});
</script>
