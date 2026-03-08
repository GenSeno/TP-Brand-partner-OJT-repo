<template>
    <Modal ref="modalRef" max-width="4xl" v-slot="{ close }">
        <div class="page-header">
            <h4>Partial Production</h4>
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
                                    <th>State</th>
                                    <th>Date</th>
                                    <th>Produced</th>
                                    <th>
                                        {{
                                            props.viewingStage ===
                                            JobOrderStage.Sewing
                                                ? 'Sewer'
                                                : 'Description'
                                        }}
                                    </th>
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
                                    <td>{{ item.stateLabel }}</td>
                                    <td>
                                        {{
                                            dayjs(item.created_at).format(
                                                'DD MMM YYYY HH:mm',
                                            )
                                        }}
                                    </td>
                                    <td>{{ item.quantity }}</td>
                                    <td>
                                        <template
                                            v-if="
                                                props.viewingStage ===
                                                JobOrderStage.Sewing
                                            "
                                        >
                                            {{ item.meta?.sewer || '-' }}
                                        </template>
                                        <template v-else>
                                            <div v-if="item.meta" class="mb-0">
                                                <div
                                                    v-for="(
                                                        properties, key
                                                    ) in item.meta"
                                                    :key="key"
                                                >
                                                    <ul class="mb-0 ps-3">
                                                        <li
                                                            v-for="(
                                                                value, index
                                                            ) in properties"
                                                            :key="index"
                                                        >
                                                            {{ value }}
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </template>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Right Column: Production Form -->
                <div class="col-xl-4">
                    <h6 class="mb-3">Add Production</h6>

                    <div class="mb-3">
                        <label class="form-label">Production State</label>
                        <input
                            v-if="
                                props.viewingStage || stateOptions.length <= 1
                            "
                            type="text"
                            class="form-control"
                            :value="currentStageLabel"
                            disabled
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

                    <!-- <div
                        v-if="props.viewingStage === JobOrderStage.Sewing"
                        class="mb-3"
                    >
                        <label class="form-label required">Sewer</label>
                        <vue-select
                            v-model="form.data.meta.sewer"
                            :options="props.sewers || []"
                            :reduce="(option) => option.full_name"
                            label="full_name"
                            placeholder="Select sewer"
                            :is-clearable="false"
                            :class="{ 'is-invalid': form.errors['meta.sewer'] }"
                        />
                        <input-error :message="form.errors['meta.sewer']" />
                    </div> -->

                    <div v-if="availableNames.length > 0" class="mb-3">
                        <label class="form-label"
                            >Select Names ({{ selectedNamesCount }}/{{
                                form.data.quantity || 0
                            }})</label
                        >
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
import { JobOrderStage } from '@/enums/job-order-stage';

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
    state: '',
    quantity: '',
    meta: {
        sewer: '',
        names: [],
    },
});

const stateOptions = computed(() => {
    const hasNames = props.orderLine.meta?.names?.length > 0;

    return Object.entries(props.productions)
        .filter(([, production]) => {
            if (hasNames) return true; // Show all stages if has names
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
