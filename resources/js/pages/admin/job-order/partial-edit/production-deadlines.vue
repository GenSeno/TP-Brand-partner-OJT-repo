<template>
    <div v-if="can('job-orders:manage-deadlines')" class="row g-3">
        <div
            v-for="(stage, index) in form.data.deadlines"
            :key="stage.id"
            class="col-lg-6"
        >
            <div class="row g-1">
                <div class="col d-flex pt-1">
                    <div>
                        <i
                            :class="`feather ${states?.[stage.state]?.icon} me-2`"
                        ></i>
                        {{ states?.[stage.state]?.label }}
                    </div>
                    <span class="ms-auto">:</span>
                </div>
                <div class="col-6">
                    <VueDatePicker
                        v-if="editable"
                        v-model="stage.due_at"
                        placeholder="Select Date"
                        :formats="{
                            month: 'MMMM',
                        }"
                        :time-config="{
                            enableTimePicker: false,
                        }"
                        :ui="{
                            input: classMerge([
                                'form-control form-control-sm',
                                {
                                    'is-invalid':
                                        form.errors[
                                            `deadlines.${index}.due_at`
                                        ],
                                },
                            ]),
                        }"
                        @update:model-value="
                            form.clearErrors(`deadlines.${index}.due_at`)
                        "
                        auto-apply
                    />
                    <div v-else>
                        <p class="fw-bold mb-0">
                            {{ dayjs(stage.due_at).format('MMMM D, YYYY') }}
                        </p>
                        <p class="text-muted small mb-0">
                            {{ dateFromNow(stage.due_at) }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div v-else class="row g-3">
        <template v-for="stage in permittedStages" :key="stage.id">
            <div class="col-lg-6">
                <div class="row g-1">
                    <div class="col d-flex">
                        <div>
                            <i
                                :class="`feather ${states?.[stage.state]?.icon} me-2`"
                            ></i>
                            {{ states?.[stage.state]?.label }}
                        </div>
                        <span class="ms-auto">:</span>
                    </div>
                    <div class="col-6">
                        <div v-if="stage.due_at">
                            <p class="fw-bold mb-0">
                                {{ dayjs(stage.due_at).format('MMMM D, YYYY') }}
                            </p>
                            <p class="text-muted small mb-0">
                                {{ dateFromNow(stage.due_at) }}
                            </p>
                        </div>
                        <span v-else class="text-muted">No deadline set</span>
                    </div>
                </div>
            </div>
        </template>
    </div>
</template>

<script setup>
import { computed } from 'vue';
import { VueDatePicker } from '@vuepic/vue-datepicker';
import { classMerge } from '@/helpers/layout';
import { can } from '@/helpers/guard';
import dayjs from 'dayjs';
import { dateFromNow } from '@/helpers/date';

const form = defineModel('form');

const props = defineProps({
    editable: Boolean,
    jobOrder: {
        type: Object,
        required: true,
    },
    states: {
        type: Object,
        required: true,
    },
});

const sortedStages = computed(() => {
    const order = Object.keys(props.states);
    return [...props.jobOrder.stages].sort(
        (a, b) => order.indexOf(a.state) - order.indexOf(b.state),
    );
});

const permittedStages = computed(() => {
    return sortedStages.value.filter((stage) => {
        const permission = props.states?.[stage.state]?.permission;
        return permission && can(permission);
    });
});
</script>
