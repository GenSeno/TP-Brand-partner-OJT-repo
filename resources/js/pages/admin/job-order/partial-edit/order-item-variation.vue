<template>
    <div class="card shadow-sm">
        <div class="card-body p-2">
            <div class="row align-items-center g-2 mb-2">
                <div class="col-auto">
                    <h6 class="mb-0">
                        {{ line.option }}
                    </h6>
                </div>

                <div class="col-auto ms-lg-auto">
                    <template
                        v-if="
                            [
                                JobOrderStage.NewOrder,
                                JobOrderStage.Artist,
                                JobOrderStage.Approval,
                            ].includes(viewingStage)
                        "
                    >
                        {{ line.quantity }} pcs
                    </template>
                    <template v-else>
                        <b>{{ doneCount }}</b> / {{ line.quantity }} pcs
                    </template>
                </div>

                <div v-if="jobOrder.can_produce && isWorkable" class="col-auto">
                    <template v-if="viewingStage === JobOrderStage.Sewing">
                        <ModalLink
                            navigate
                            :href="
                                route('admin.job-order.assign-sewer', line.id)
                            "
                            class="btn btn-sm btn-soft-secondary me-2"
                            #default="{ loading }"
                            @close="emitter.emit('job-order-produced:updated')"
                        >
                            <loading-text :loading="loading">
                                Assign Sewer
                            </loading-text>
                        </ModalLink>
                        <ModalLink
                            navigate
                            :href="
                                route('admin.job-order.set-produced', {
                                    jobOrder: jobOrder.id,
                                    orderLine: line.id,
                                    stage: viewingStage || undefined,
                                })
                            "
                            class="btn btn-sm btn-primary"
                            #default="{ loading }"
                            @close="emitter.emit('job-order-produced:updated')"
                        >
                            <loading-text :loading="loading">
                                Complete
                            </loading-text>
                        </ModalLink>
                    </template>
                    <template v-else>
                        <ModalLink
                            navigate
                            :href="
                                route('admin.job-order.set-produced', {
                                    jobOrder: jobOrder.id,
                                    orderLine: line.id,
                                    stage: viewingStage || undefined,
                                })
                            "
                            class="btn btn-sm btn-soft-secondary me-2"
                            #default="{ loading }"
                            @close="emitter.emit('job-order-produced:updated')"
                        >
                            <loading-text :loading="loading">
                                Partial
                            </loading-text>
                        </ModalLink>
                        <ModalLink
                            navigate
                            :href="
                                route('admin.job-order.complete-produced', {
                                    jobOrder: jobOrder.id,
                                    orderLine: line.id,
                                    stage: viewingStage || undefined,
                                })
                            "
                            class="btn btn-sm btn-primary"
                            #default="{ loading }"
                            @close="emitter.emit('job-order-produced:updated')"
                        >
                            <loading-text :loading="loading">
                                Complete
                            </loading-text>
                        </ModalLink>
                    </template>
                </div>
            </div>

            <div class="row align-items-start g-2">
                <div class="col">
                    <div v-if="stageProgress.length > 0">
                        <div class="d-flex flex-wrap gap-1">
                            <span
                                v-for="stage in stageProgress"
                                :key="stage.label"
                                class="badge"
                                :class="stageClass(stage)"
                            >
                                {{ stage.label }}: {{ stage.produced }}/{{
                                    stage.received
                                }}
                            </span>
                        </div>
                    </div>
                </div>
                <div v-if="line.assignments" class="col-auto">
                    <div class="d-flex flex-wrap gap-1">
                        <template
                            v-for="assignment in line.assignments"
                            :key="assignment.id"
                        >
                            <span
                                v-if="assignment.state === props.viewingStage"
                                class="badge bg-info-subtle text-info"
                            >
                                {{ assignment.staff.full_name }}:
                                {{ assignment.quantity }} pcs
                            </span>
                        </template>
                    </div>
                </div>

                <div v-if="!isEmpty(namesPerStage)" class="col-12">
                    <div class="border-top p-2 bg-light">
                        <!-- <div
                            class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-2"
                        >
                            <h6 class="mb-0">Names by Production Stage</h6>
                        </div> -->
                        <div v-if="showSearch" class="mb-2">
                            <input
                                v-model="searchQuery"
                                type="text"
                                class="form-control form-control-sm"
                                placeholder="Search names..."
                            />
                        </div>
                        <div class="row g-2">
                            <div
                                v-for="(
                                    production, state
                                ) in filteredNamesPerStage"
                                :key="state"
                                class="col-6"
                            >
                                <p
                                    class="fw-bold fs-sm mb-0"
                                    :class="
                                        ['done', 'completed'].includes(state)
                                            ? 'text-success'
                                            : 'text-muted'
                                    "
                                >
                                    {{ production.label }} [
                                    {{ production.names.length }} pcs ]:
                                </p>
                                <ul class="ms-3" style="list-style-type: disc">
                                    <li
                                        v-for="(item, idx) in production.names"
                                        :key="idx"
                                    >
                                        <span>{{ item }}</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { isEmpty } from 'lodash';
import { computed, ref } from 'vue';
import { emitter } from '@/composables/eventBus';
import { JobOrderStage } from '@/enums/job-order-stage';

const props = defineProps({
    jobOrder: {
        type: Object,
        required: true,
    },
    line: {
        type: Object,
        required: true,
    },
    viewingStage: {
        type: String,
        default: null,
    },
});

const searchQuery = ref('');
const showSearch = ref(false);

const stageClass = (stage) => {
    if (stage.received > 0 && stage.produced >= stage.received) {
        return 'bg-success-subtle text-success';
    }
    if (props.viewingStage === stage.state) {
        return 'bg-soft-info';
    }
    if (stage.produced > 0) {
        return 'bg-soft-primary';
    }
    return 'bg-secondary-subtle text-secondary';
};

const doneCount = computed(() => {
    const productionsByState = props.line.productions_by_state;

    if (isEmpty(productionsByState)) {
        return 0;
    }

    if (props.viewingStage && productionsByState[props.viewingStage]) {
        return productionsByState[props.viewingStage].quantity;
    }

    const states = Object.values(productionsByState);
    let furthest = 0;
    for (const state of states) {
        if (state.quantity > 0) {
            furthest = state.quantity;
        }
    }

    return furthest;
});

const isWorkable = computed(() => {
    const productionsByState = props.line.productions_by_state;
    if (isEmpty(productionsByState)) {
        // If no productions yet, only allow working if we're in the printing stage
        return props.viewingStage === JobOrderStage.Printing;
    }

    // If viewing specific stage, check if it's workable (i.e. received items but not fully produced)
    if (props.viewingStage && productionsByState[props.viewingStage]) {
        const stage = productionsByState[props.viewingStage];
        return stage.received > 0 && stage.quantity < stage.received;
    }

    // If viewing overall, allow working if last stage has received items but not fully produced
    const states = Object.values(productionsByState);
    const lastState = states[states.length - 1];

    return lastState?.received > 0 && lastState?.quantity < lastState?.received;
});

const stageProgress = computed(() => {
    const productionsByState = props.line.productions_by_state;
    if (isEmpty(productionsByState)) {
        return [];
    }

    return Object.entries(productionsByState).map(([key, stage]) => ({
        state: key,
        label: stage.label,
        produced: stage.quantity,
        received: stage.received,
    }));
});

const namesPerStage = computed(() => {
    const names = props.line.meta?.names;
    if (isEmpty(names)) {
        return [];
    }

    const productionsByState = props.line.productions_by_state;

    // No productions yet - show all names under one group
    if (isEmpty(productionsByState)) {
        return {
            pending: {
                label: 'Names',
                names: names,
                quantity: props.line.quantity,
            },
        };
    }

    const processedNames = new Set();
    const result = {};

    // Process each production state
    for (const [state, production] of Object.entries(productionsByState)) {
        const producedNames = new Set(production.meta?.names || []);
        const remainingNames = names.filter(
            (name) => !processedNames.has(name) && !producedNames.has(name),
        );

        if (remainingNames.length > 0) {
            result[state] = {
                label: production.label,
                names: remainingNames,
                quantity: remainingNames.length,
            };
            remainingNames.forEach((name) => processedNames.add(name));
        }
    }

    // Check for completed names
    const completedNames = names.filter((name) => !processedNames.has(name));
    if (completedNames.length > 0) {
        result.done = {
            label: 'Done',
            names: completedNames,
            quantity: completedNames.length,
        };
    }

    // All productions completed but no remaining names
    if (isEmpty(result)) {
        return {
            completed: {
                label: 'Completed',
                names: names,
                quantity: props.line.quantity,
            },
        };
    }

    return result;
});

const filteredNamesPerStage = computed(() => {
    if (!searchQuery.value.trim()) {
        return namesPerStage.value;
    }

    const query = searchQuery.value.toLowerCase();
    const filtered = {};

    for (const [state, production] of Object.entries(namesPerStage.value)) {
        const matchedNames = production.names.filter((name) =>
            name.toLowerCase().includes(query),
        );

        if (matchedNames.length > 0) {
            filtered[state] = {
                ...production,
                names: matchedNames,
            };
        }
    }

    return filtered;
});
</script>
