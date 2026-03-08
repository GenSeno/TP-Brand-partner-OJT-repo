<template>
    <Modal ref="modalRef" max-width="sm" v-slot="{ close }">
        <div class="page-header">
            <h4>Assign Sewer</h4>
        </div>

        <form @submit.prevent="submitForm">
            <div class="page-body new-employee-field">
                <div class="row g-1">
                    <div class="col-8">
                        <label class="form-label">Sewer</label>
                    </div>
                    <div class="col-4">
                        <label class="form-label">Qty Assign</label>
                    </div>
                </div>
                <div
                    v-for="(assignment, index) in form.data.assignments"
                    :key="index"
                    class="mb-2"
                >
                    <div class="row g-1">
                        <div class="col-8">
                            <vue-select
                                v-model="assignment.sewer_id"
                                :options="sewers"
                                :reduce="(option) => option.value"
                                label="label"
                                placeholder="Select sewer"
                            />
                        </div>
                        <div class="col-4 d-flex align-items-center">
                            <input
                                type="number"
                                class="form-control"
                                v-model.number="assignment.quantity"
                                min="1"
                                placeholder="Quantity"
                            />
                            <div
                                v-show="form.data.assignments.length > 1"
                                class="ms-2"
                            >
                                <button
                                    type="button"
                                    class="btn btn-danger btn-icon btn-sm"
                                    @click="removeSewer(index)"
                                >
                                    <i class="feather feather-x"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <input-error
                        :message="form.errors[`assignments.${index}.sewer_id`]"
                    />
                </div>
                <div class="row g-1">
                    <div class="col">
                        <p class="mb-0">
                            Assigned: {{ remainingQuantity }} / {{ total }} pcs
                        </p>
                    </div>
                    <div v-if="remainingQuantity > 0" class="col-auto ms-auto">
                        <button
                            type="button"
                            class="btn btn-link"
                            @click="addSewer"
                        >
                            <i class="feather feather-plus me-1"></i> Add
                        </button>
                    </div>
                </div>
            </div>

            <div class="page-footer-buttons">
                <button
                    type="button"
                    class="btn btn-secondary"
                    @click="close()"
                >
                    Cancel
                </button>
                <submit-btn :loading="form.processing">
                    Confirm & Complete
                </submit-btn>
            </div>
        </form>
    </Modal>
</template>

<script setup>
import { useAxiosForm } from '@/composables/axiosForm';
import { computed, onMounted, useTemplateRef } from 'vue';
import { emitter } from '@/composables/eventBus';

const props = defineProps({
    orderLine: {
        type: Object,
        required: true,
    },
    sewers: {
        type: Object,
        required: true,
    },
    currentAssignments: {
        type: Array,
        required: true,
    },
    total: {
        type: Number,
        required: true,
    },
});

const modalRef = useTemplateRef('modalRef');

const form = useAxiosForm({
    assignments: [],
});

const remainingQuantity = computed(() => {
    const assignedQuantity = form.data.assignments.reduce(
        (total, assignment) => total + (assignment.quantity || 0),
        0,
    );
    return props.total - assignedQuantity;
});

const addSewer = () => {
    form.data.assignments.push({
        sewer_id: '',
        quantity: 1,
    });
};

const removeSewer = (index) => {
    form.data.assignments.splice(index, 1);
};

const submitForm = () => {
    form.post(route('admin.job-order.assign-sewer.store', props.orderLine.id), {
        onSuccess: () => {
            emitter.emit('job-order:sewer-assigned');
            modalRef.value.close();
        },
    });
};

onMounted(() => {
    if (props.currentAssignments.length === 0) {
        addSewer();
    } else {
        form.data.assignments = props.currentAssignments.map((assignment) => ({
            sewer_id: assignment.staff_id,
            quantity: assignment.quantity,
        }));
    }
});
</script>
