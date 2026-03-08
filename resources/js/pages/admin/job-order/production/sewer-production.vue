<template>
    <Modal ref="modalRef" max-width="4xl" v-slot="{ close }">
        <div class="page-header">
            <h4>
                Complete Production -
                {{ JobOrderStageOptions.sewing.label }}
            </h4>
        </div>

        <div class="page-body">
            <div class="row">
                <!-- Left Column: Production History -->
                <div class="col-xl-8">
                    <div
                        class="d-flex justify-content-between align-items-center mb-3"
                    >
                        <h6 class="mb-0">Production History</h6>
                        <div class="d-flex align-items-center gap-3">
                            <div class="form-check form-switch mb-0">
                                <input
                                    v-model="showAllProductions"
                                    class="form-check-input"
                                    type="checkbox"
                                    id="showAllProductions"
                                />
                                <label
                                    class="form-check-label"
                                    for="showAllProductions"
                                >
                                    Show All
                                </label>
                            </div>
                            <span class="fw-bold"
                                >Balance: {{ currentProductionBalance }}</span
                            >
                        </div>
                    </div>

                    <div
                        class="table-responsive mb-3"
                        style="max-height: 400px"
                    >
                        <table
                            class="table table-sm table-bordered text-center mb-0"
                        >
                            <thead style="position: sticky; top: 0; z-index: 1">
                                <tr>
                                    <th>Date</th>
                                    <th>Sewer</th>
                                    <th>Qty Assigned</th>
                                    <th>Qty Produced</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-if="!filteredProductions.length">
                                    <td colspan="4">No records found.</td>
                                </tr>
                                <tr
                                    v-for="item in filteredProductions"
                                    :key="item.id"
                                >
                                    <td>
                                        {{
                                            dayjs(item.created_at).format(
                                                'DD MMM YYYY HH:mm',
                                            )
                                        }}
                                    </td>
                                    <td>
                                        {{
                                            item.staff?.full_name ||
                                            '-not assigned-'
                                        }}
                                    </td>
                                    <td>
                                        {{
                                            _currentSewer(item.staff_id)
                                                ?.quantity || '-'
                                        }}
                                    </td>
                                    <td>{{ item.quantity }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Right Column: Production Form -->
                <div class="col-xl-4">
                    <h6 class="mb-3">Add Production</h6>

                    <div class="mb-3">
                        <label class="form-label">Sewer Name</label>
                        <vue-select
                            v-model="form.data.staff_id"
                            :options="sewers"
                            :get-option-label="(sewer) => sewer.staff.full_name"
                            :get-option-value="(sewer) => sewer.staff.id"
                            label="sewer"
                            placeholder="Select Sewer"
                        />
                        <input-error :message="form.errors.staff_id" />
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Quantity</label>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">{{
                                dayjs().format('DD MMM YYYY')
                            }}</span>
                            <input-text
                                v-model="form.data.quantity"
                                type="number"
                                placeholder="Enter quantity"
                            />
                            <span class="input-group-text">{{
                                props.orderLine.purchasable.uom_code
                            }}</span>
                        </div>
                        <input-error :message="form.errors.quantity" />
                    </div>

                    <div v-if="availableNames.length > 0" class="mb-3">
                        <label class="form-label">
                            Select Names ({{ selectedNamesCount }}/{{
                                form.data.quantity || 0
                            }})
                        </label>
                        <input
                            v-model="nameSearchQuery"
                            type="text"
                            class="form-control form-control-sm mb-2"
                            placeholder="Search names..."
                        />
                        <div
                            class="border rounded p-2"
                            style="max-height: 250px; overflow-y: auto"
                        >
                            <div
                                v-if="filteredAvailableNames.length === 0"
                                class="text-muted text-center py-2"
                            >
                                No names found
                            </div>
                            <div
                                v-for="(name, index) in filteredAvailableNames"
                                :key="index"
                                class="form-check"
                            >
                                <input
                                    v-model="form.data.meta.names"
                                    class="form-check-input"
                                    type="checkbox"
                                    :value="name"
                                    :id="`name-${index}`"
                                    :disabled="
                                        !form.data.meta.names.includes(name) &&
                                        selectedNamesCount >= form.data.quantity
                                    "
                                />
                                <label
                                    class="form-check-label"
                                    :for="`name-${index}`"
                                >
                                    {{ name }}
                                </label>
                            </div>
                        </div>
                        <input-error :message="form.errors['meta.names']" />
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
                    <submit-btn :loading="form.processing">
                        Save Changes
                    </submit-btn>
                </div>
            </form>
        </div>
    </Modal>
</template>

<script setup>
import { useAxiosForm } from '@/composables/axiosForm';
import { computed, ref, useTemplateRef, watch } from 'vue';
import dayjs from 'dayjs';
import { emitter } from '@/composables/eventBus';
import { JobOrderStage, JobOrderStageOptions } from '@/enums/job-order-stage';

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
    sewers: {
        type: Array,
        default: () => [],
    },
});

const modalRef = useTemplateRef('modalRef');
const showAllProductions = ref(false);
const nameSearchQuery = ref('');

const form = useAxiosForm({
    order_line_id: props.orderLine.id,
    state: JobOrderStage.Sewing,
    staff_id: '',
    quantity: '',
    meta: {
        names: [],
    },
});

const _currentSewer = (staff_id) => {
    return props.sewers.find((s) => s.staff.id === staff_id);
};

const allProductions = computed(() => {
    const items = [];
    for (const [state, production] of Object.entries(props.productions)) {
        production.items.forEach((item) => {
            items.push({
                ...item,
                state,
                stateLabel: production.label,
            });
        });
    }
    return items.sort(
        (a, b) => new Date(b.created_at) - new Date(a.created_at),
    );
});

const filteredProductions = computed(() => {
    if (showAllProductions.value) {
        return allProductions.value;
    }
    return allProductions.value.filter(
        (item) => item.state === form.data.state,
    );
});

const currentProductionBalance = computed(() => {
    const productionBalance = props.productions[form.data.state]?.balance || 0;
    if (!form.data.staff_id) return productionBalance;

    const sewerBalance = _currentSewer(form.data.staff_id)?.quantity || 0;
    return Math.min(productionBalance, sewerBalance);
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

const filteredAvailableNames = computed(() => {
    if (!nameSearchQuery.value.trim()) {
        return availableNames.value;
    }

    const query = nameSearchQuery.value.toLowerCase();
    return availableNames.value.filter((name) =>
        name.toLowerCase().includes(query),
    );
});

const selectedNamesCount = computed(() => {
    return form.data.meta.names.length;
});

const submitForm = () => {
    form.post(route('admin.job-order.update-produced', props.jobOrder.id), {
        onSuccess: () => {
            modalRef.value.close();
            setTimeout(() => {
                emitter.emit('job-order-produced:updated');
            }, 300);
        },
    });
};

// Clear selected names when quantity changes
watch(
    () => form.data.quantity,
    (newQuantity) => {
        const qty = parseInt(newQuantity) || 0;
        if (form.data.meta.names.length > qty) {
            form.data.meta.names = form.data.meta.names.slice(0, qty);
        }
    },
);
</script>
