<template>
    <Modal ref="modalRef" max-width="sm" v-slot="{ close }">
        <div class="page-header">
            <h4>Complete Production</h4>
        </div>

        <div class="page-body">
            <p class="text-muted mb-1">
                You are about to complete production for:
            </p>
            <h6 class="mb-3">
                {{ props.orderLine.option }}
            </h6>

            <div class="mb-3">
                <label class="form-label">Production State</label>
                <input
                    v-if="props.viewingStage || stateOptions.length <= 1"
                    type="text"
                    class="form-control"
                    :value="currentStageLabel"
                    disabled
                    readonly
                    placeholder="Select state"
                />
                <vue-select
                    v-else
                    v-model="form.data.state"
                    :options="stateOptions"
                    :reduce="(option) => option.value"
                    label="label"
                    placeholder="Select state"
                    :is-clearable="false"
                />
                <input-error :message="form.errors.state" />
            </div>

            <div v-if="form.data.state" class="card bg-light border mb-3">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Remaining Quantity:</span>
                        <strong
                            >{{ remainingQuantity }}
                            {{ props.orderLine.purchasable.uom_code }}</strong
                        >
                    </div>
                    <div
                        v-if="availableNames.length > 0"
                        class="d-flex justify-content-between"
                    >
                        <span class="text-muted">Remaining Names:</span>
                        <strong>{{ availableNames.length }} items</strong>
                    </div>
                </div>
            </div>

            <div class="alert alert-warning border border-warning mb-0">
                <div class="d-flex align-items-start">
                    <div class="me-2">
                        <i class="feather-alert-triangle flex-shrink-0"></i>
                    </div>
                    <div class="text-warning w-100">
                        <strong>Warning:</strong> This will mark all remaining
                        items as complete for the selected state. This action
                        cannot be undone.
                    </div>
                </div>
            </div>
        </div>

        <div class="new-employee-field">
            <form @submit.prevent="submitForm">
                <div class="page-footer-buttons">
                    <button
                        type="button"
                        class="btn btn-secondary"
                        @click="close()"
                    >
                        Cancel
                    </button>
                    <submit-btn
                        :loading="form.processing"
                        :disabled="!form.data.state"
                    >
                        Confirm & Complete
                    </submit-btn>
                </div>
            </form>
        </div>
    </Modal>
</template>

<script setup>
import { useAxiosForm } from '@/composables/axiosForm';
import { computed, useTemplateRef, watch } from 'vue';
import { emitter } from '@/composables/eventBus';

const props = defineProps({
    jobOrder: {
        type: Object,
        required: true,
    },
    orderLine: {
        type: Object,
        required: true,
    },
    productions: {
        type: Object,
        required: true,
    },
    viewingStage: {
        type: String,
        default: null,
    },
});

const modalRef = useTemplateRef('modalRef');

const form = useAxiosForm({
    order_line_id: props.orderLine.id,
    state: '',
    quantity: '',
    meta: {
        names: [],
    },
});

const stateOptions = computed(() => {
    return Object.entries(props.productions)
        .filter(([, production]) => {
            return production.balance > 0;
        })
        .map(([value, production]) => ({
            value,
            label: production.label,
        }));
});

const currentStageLabel = computed(() => {
    if (props.viewingStage && props.productions[props.viewingStage]) {
        return props.productions[props.viewingStage].label;
    }
    if (stateOptions.value.length > 0) {
        return stateOptions.value[0].label;
    }
    return '';
});

const remainingQuantity = computed(() => {
    if (!form.data.state) return 0;
    return props.productions[form.data.state]?.balance || 0;
});

const producedNames = computed(() => {
    const names = new Set();
    if (!form.data.state) return names;

    const currentProduction = props.productions[form.data.state];
    if (!currentProduction) return names;

    currentProduction.items.forEach((item) => {
        item.meta?.names?.forEach((name) => names.add(name));
    });

    return names;
});

const producedNamesInPreviousStages = computed(() => {
    const names = new Set();
    if (!form.data.state) return names;

    // Get all stage keys in order
    const stageKeys = Object.keys(props.productions);
    const currentStateIndex = stageKeys.indexOf(form.data.state);

    // If this is the first stage, no previous stages to check
    if (currentStateIndex <= 0) return names;

    // Get all names produced in previous stages
    for (let i = 0; i < currentStateIndex; i++) {
        const stageKey = stageKeys[i];
        const production = props.productions[stageKey];

        production.items.forEach((item) => {
            item.meta?.names?.forEach((name) => names.add(name));
        });
    }

    return names;
});

const availableNames = computed(() => {
    const allNames = props.orderLine.meta?.names || [];

    // For first stage: exclude names already produced in current stage
    if (Object.keys(props.productions).indexOf(form.data.state) === 0) {
        return allNames.filter((name) => !producedNames.value.has(name));
    }

    // For subsequent stages: only include names produced in previous stages but not in current stage
    return allNames.filter(
        (name) =>
            producedNamesInPreviousStages.value.has(name) &&
            !producedNames.value.has(name),
    );
});

const submitForm = () => {
    form.data.quantity = remainingQuantity.value;
    form.data.meta.names = availableNames.value;

    form.post(route('admin.job-order.update-produced', props.jobOrder.id), {
        onSuccess: () => {
            modalRef.value.close();
            setTimeout(() => {
                emitter.emit('job-order-produced:updated');
            }, 300);
        },
    });
};

// Set default state based on viewing stage or first option
watch(
    stateOptions,
    (options) => {
        if (form.data.state) return;

        if (props.viewingStage && props.productions[props.viewingStage]) {
            form.data.state = props.viewingStage;
        } else if (options.length > 0) {
            form.data.state = options[0].value;
        }
    },
    { immediate: true },
);
</script>
