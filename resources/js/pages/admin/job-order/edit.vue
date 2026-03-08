<template>
    <Head title="Edit Job Order" />

    <Modal
        ref="modalRef"
        max-width="7xl"
        :close-button="false"
        :close-explicitly="true"
    >
        <div class="job-order-page">
            <div class="page-header no-close-btn flex-wrap gap-2">
                <h4 class="page-title">
                    Edit Job Order -
                    {{
                        props.states[props.viewingStage]?.label ||
                        props.jobOrder.current_state_data.label
                    }}
                </h4>
                <div class="d-flex gap-2">
                    <submit-btn
                        type="button"
                        class="btn btn-sm btn-icon btn-soft-dark"
                        title="Refresh"
                        @click="refreshPage"
                        :loading="reloading"
                    >
                        <template #loading>
                            <span
                                class="spinner-border spinner-border-sm"
                            ></span>
                        </template>
                        <i class="feather feather-refresh-cw"></i>
                    </submit-btn>
                    <button
                        class="btn btn-sm btn-dark"
                        title="Collapse"
                        @click="sidebarVisible = !sidebarVisible"
                    >
                        <i class="feather feather-sidebar"></i>
                        <span class="d-none d-lg-inline"
                            >{{ sidebarVisible ? ' Hide' : ' Show' }} history
                            and notes</span
                        >
                    </button>
                    <button
                        class="btn btn-sm btn-icon btn-danger"
                        title="Close"
                        @click="closeModal"
                    >
                        <i class="feather feather-x"></i>
                    </button>
                </div>
            </div>

            <div class="position-relative">
                <div
                    ref="pageBodyRef"
                    class="page-body bg-light"
                    style="overflow-y: auto; max-height: 75vh"
                >
                    <BAlert
                        v-model="alert.visible"
                        :variant="alert.variant"
                        dismissible
                    >
                        <div class="d-flex align-items-center">
                            <i
                                class="feather-check-circle flex-shrink-0 me-2"
                            ></i>
                            {{ alert.message }}
                        </div>
                    </BAlert>

                    <div class="row g-3">
                        <div class="col-xl-8">
                            <ProductionDetails
                                v-model:form="form"
                                :editable="!props.isCancelled"
                                :job-order="props.jobOrder"
                                :urgency-flags="props.urgencyFlags"
                                :states="props.states"
                                date-format="MMMM DD, YYYY"
                            />
                        </div>

                        <div class="col-xl-4">
                            <OrderDetails :job-order="props.jobOrder" />
                        </div>

                        <div class="col-lg-12">
                            <OrderItems
                                v-model:form="form"
                                :editable="!props.isCancelled"
                                :fabrics="props.fabrics"
                                :job-order="props.jobOrder"
                                :viewing-stage="props.viewingStage"
                                :image-emitter="lightbox.emitter"
                            />
                        </div>

                        <div
                            v-if="props.materials.length > 0"
                            class="col-lg-12"
                        >
                            <JobInventory
                                :job-order="props.jobOrder"
                                :materials="props.materials"
                                :inventory-emitter="inventoryEmitter"
                            />
                        </div>

                        <div
                            v-if="
                                props.viewingStage === JobOrderStage.Dispatching
                            "
                            class="col-lg-12"
                        >
                            <DispatchingDetails
                                v-model:form="form"
                                :job-order="props.jobOrder"
                            />
                        </div>
                    </div>
                </div>

                <ModalSidebar
                    v-model:modal-ref="modalRef"
                    v-model:show="sidebarVisible"
                    :activities="props.activities"
                    :job-order="props.jobOrder"
                />

                <div class="new-employee-field">
                    <div v-if="props.isCancelled" class="page-footer-buttons">
                        <button
                            type="button"
                            class="btn btn-secondary"
                            @click="closeModal"
                        >
                            Close
                        </button>
                    </div>
                    <form v-else @submit.prevent="submitForm">
                        <div class="page-footer-buttons">
                            <button
                                type="button"
                                class="btn btn-secondary"
                                @click="closeModal"
                            >
                                Cancel
                            </button>
                            <submit-btn
                                v-if="showAssignButton"
                                type="button"
                                class="btn btn-info"
                                :loading="assignProcessing"
                                @click="handleAssignArtist"
                            >
                                {{
                                    isAssignedToMe
                                        ? 'Unassign Me'
                                        : 'Assign to Me'
                                }}
                            </submit-btn>
                            <submit-btn
                                v-if="
                                    hasAnyPermission([
                                        'job-orders:manage-new-tasks',
                                        'job-orders:manage-artist-tasks',
                                        'job-orders:manage-dispatching-tasks',
                                    ])
                                "
                                :loading="form.processing"
                            >
                                Save Changes
                            </submit-btn>
                            <button
                                v-if="props.nextStage"
                                @click="submitStage"
                                type="button"
                                class="btn"
                                :class="
                                    props.canSubmitToNextStage
                                        ? 'btn-info'
                                        : 'btn-soft-dark'
                                "
                                :disabled="!props.canSubmitToNextStage"
                            >
                                Submit JO to {{ props.nextStage }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <VueEasyLightbox
                :visible="lightbox.visible"
                :index="lightbox.index"
                :imgs="lightbox.gallery"
                @hide="lightbox.visible = false"
            >
            </VueEasyLightbox>
        </div>
    </Modal>
</template>
<script setup>
import { getImageUrl } from '@/helpers/media';
import { useAxiosForm } from '@/composables/axiosForm';
import { Head, usePage } from '@inertiajs/vue3';
import axios from 'axios';
import {
    computed,
    nextTick,
    onMounted,
    reactive,
    ref,
    useTemplateRef,
} from 'vue';
import VueEasyLightbox from 'vue-easy-lightbox';
import ProductionDetails from './partial-edit/production-details.vue';
import OrderItems from './partial-edit/order-items.vue';
import { can, hasAnyPermission } from '@/helpers/guard';
import ModalSidebar from './partial-edit/modal-sidebar.vue';
import { emitter } from '@/composables/eventBus';
import OrderDetails from './partial-edit/order-details.vue';
import JobInventory from './partial-edit/job-inventory.vue';
import DispatchingDetails from './partial-edit/dispatching-details.vue';
import { JobOrderStage } from '@/enums/job-order-stage';

const props = defineProps({
    jobOrder: {
        type: Object,
        required: true,
    },
    notes: Array,
    nextStage: String,
    urgencyFlags: Object,
    isCancelled: Boolean,
    states: Object,
    fabrics: Object,
    materials: Object,
    permissions: Object,
    viewingStage: String,
    canSubmitToNextStage: Boolean,
    activities: {
        type: Array,
        default: () => [],
    },
});

const modalRef = useTemplateRef('modalRef');
const pageBodyRef = useTemplateRef('pageBodyRef');

const sidebarVisible = ref(false);
const reloading = ref(false);
const assignProcessing = ref(false);
const alert = reactive({
    visible: false,
    message: '',
    variant: 'success',
});

const form = useAxiosForm({
    urgency_flag: props.jobOrder.urgency_flag || '',
    products: props.jobOrder.products.map((item) => ({
        id: item.id,
        inventory_item_id: item.inventory_item_id || '',
        inventory_item_name: item.inventory_item?.item_name || '',
        notes: item.notes || '',
        file_path: item.file_path || '',
    })),
    deadlines: props.jobOrder.stages
        .map((stage) => ({
            id: stage.id,
            state: stage.state,
            due_at: stage.due_at,
        }))
        .sort((a, b) => {
            const order = Object.keys(props.states);
            return order.indexOf(a.state) - order.indexOf(b.state);
        }),
    dispatching: {
        dispatching_type: props.jobOrder.meta?.dispatching_type || '',
        delivery_method: props.jobOrder.meta?.delivery_method || '',
        reference_number: props.jobOrder.meta?.reference_number || '',
        add_delivery_fee: props.jobOrder.meta?.add_delivery_fee || '',
    },
});

const inventoryEmitter = ref('job-order-inventory:updated');

const lightbox = reactive({
    emitter: 'job-order:preview-image',
    visible: false,
    index: 0,
    gallery: [],
});

const previewImage = (image) => {
    if (image) {
        lightbox.gallery = getImageUrl(image);
        lightbox.visible = true;
    }
};

const currentUserId = computed(() => usePage().props.auth.user.id);

const artistStage = computed(() =>
    props.jobOrder.stages?.find((s) => s.state === JobOrderStage.Artist),
);

const isAssignedToMe = computed(
    () => artistStage.value?.operator_id === currentUserId.value,
);

const showAssignButton = computed(
    () =>
        props.viewingStage === JobOrderStage.Artist &&
        can('job-orders:manage-artist-tasks') &&
        artistStage.value?.started_at &&
        !artistStage.value?.completed_at &&
        (!artistStage.value?.operator_id || isAssignedToMe.value),
);

const handleAssignArtist = async () => {
    assignProcessing.value = true;
    alert.visible = false;
    try {
        const { data } = await axios.post(
            route('admin.job-order.assign-artist', props.jobOrder.id),
        );
        modalRef.value.reload({
            only: ['jobOrder'],
            onFinish: () => {
                alert.message = data.message;
                alert.variant = 'success';
                alert.visible = true;
                pageBodyRef.value.scrollTop = 0;
            },
        });
    } catch (e) {
        alert.message = e.response?.data?.message ?? 'Something went wrong.';
        alert.variant = 'danger';
        alert.visible = true;
        pageBodyRef.value.scrollTop = 0;
    } finally {
        assignProcessing.value = false;
    }
};

const submitForm = () => {
    if (
        !hasAnyPermission([
            'job-orders:manage-new-tasks',
            'job-orders:manage-artist-tasks',
        ])
    )
        return;

    form.put(route('admin.job-order.update', props.jobOrder.id), {
        onStart: () => {
            alert.visible = false;
        },
        onSuccess: ({ data }) => {
            modalRef.value.reload({
                only: ['jobOrder', 'canSubmitToNextStage'],
                onFinish: () => {
                    alert.message =
                        data.message || 'Job Order updated successfully.';
                    alert.variant = 'success';
                    alert.visible = true;
                    pageBodyRef.value.scrollTop = 0;
                },
            });
        },
    });
};

const submitStage = () => {
    if (
        !confirm(
            `Are you sure you want to submit this Job Order to ${props.nextStage} stage?`,
        )
    )
        return;

    form.post(route('admin.job-order.submit-stage', props.jobOrder.id), {
        onSuccess: () => {
            modalRef.value.close();
            emitter.emit('job-order:updated');
        },
    });
};

const closeModal = () => {
    if (
        form.isDirty &&
        !confirm('You have unsaved changes. Are you sure you want to close?')
    ) {
        return;
    }

    modalRef.value.close();
};

const refreshPage = () => {
    reloading.value = true;
    modalRef.value.reload({
        onFinish: () => {
            reloading.value = false;
        },
    });
};

let productionReloadTimer = null;
const reloadProduction = () => {
    if (productionReloadTimer) clearTimeout(productionReloadTimer);
    productionReloadTimer = setTimeout(() => {
        modalRef.value?.reload({
            only: ['jobOrder', 'canSubmitToNextStage', 'activities'],
        });
    }, 100);
};

onMounted(() => {
    emitter.on(lightbox.emitter, (image) => {
        previewImage(image);
    });
    emitter.on(inventoryEmitter.value, async () => {
        await nextTick(() =>
            modalRef.value.reload({
                only: ['jobOrder', 'materials', 'canSubmitToNextStage'],
            }),
        );
    });
    emitter.on('job-order:sewer-assigned', () => {
        modalRef.value.reload({
            only: ['jobOrder'],
        });
    });
    emitter.on('job-order-produced:updated', reloadProduction);
});
</script>
