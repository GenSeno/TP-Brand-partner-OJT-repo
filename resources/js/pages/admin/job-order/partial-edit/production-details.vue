<template>
    <div class="card mb-0 h-100">
        <div class="card-body">
            <div class="mb-3 border-bottom pb-2">
                <h5>Job Details - #{{ jobOrder.reference }}</h5>
            </div>
            <div class="row g-2">
                <div class="col-xl-6">
                    <div class="row g-1">
                        <div class="col d-flex">
                            <div>
                                <i class="feather feather-calendar me-2"></i>
                                JO Date
                            </div>
                            <span class="ms-auto">:</span>
                        </div>
                        <div class="col-6">
                            {{ dayjs(jobOrder.ordered_at).format(dateFormat) }}
                        </div>
                    </div>
                    <div class="row g-1">
                        <div class="col d-flex">
                            <div>
                                <i
                                    class="feather feather-shopping-bag me-2"
                                ></i>
                                SO No.
                            </div>
                            <span class="ms-auto">:</span>
                        </div>
                        <div class="col-6">
                            {{ jobOrder.order.reference }}
                        </div>
                    </div>
                    <div class="row align-items-center g-1">
                        <div class="col d-flex">
                            <div>
                                <i
                                    class="feather feather-alert-circle me-2"
                                ></i>
                                Urgency
                            </div>
                            <span class="ms-auto">:</span>
                        </div>
                        <div class="col-6">
                            <dropdown-select
                                v-if="
                                    can('job-orders:manage-urgency') && editable
                                "
                                v-model="form.data.urgency_flag"
                                :options="urgencyFlags"
                                placeholder="Select urgency"
                            >
                                <template #trigger="{ selectedValue }">
                                    <UrgentStatus
                                        as="a"
                                        href="javascript:void(0);"
                                        class="dropdown-toggle"
                                        :urgency-flag="selectedValue"
                                    />
                                </template>
                            </dropdown-select>
                            <div v-else>
                                <UrgentStatus
                                    :urgency-flag="jobOrder.urgency_flag"
                                />
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-6">
                    <div class="row g-1">
                        <div class="col d-flex">
                            <div>
                                <i class="feather feather-truck me-2"></i>
                                Delivery Date
                            </div>
                            <span class="ms-auto">:</span>
                        </div>
                        <div class="col-6">
                            {{
                                dayjs(jobOrder.estimated_delivery).format(
                                    dateFormat,
                                )
                            }}
                        </div>
                    </div>
                    <div class="row g-1">
                        <div class="col d-flex">
                            <div>
                                <i class="feather feather-clock me-2"></i>
                                Due Date
                            </div>
                            <span class="ms-auto">:</span>
                        </div>
                        <div class="col-6">
                            {{ dayjs(jobOrder.due_at).format(dateFormat) }}
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-3 mb-0">
                <h6 class="mb-2">Production Deadline</h6>
                <ProductionDeadlines
                    v-model:form="form"
                    :editable="editable"
                    :job-order="jobOrder"
                    :states="states"
                />
            </div>
        </div>
    </div>
</template>

<script setup>
import dayjs from 'dayjs';
import UrgentStatus from '@/components/job-order/urgent-status.vue';
import { can } from '@/helpers/guard';
import ProductionDeadlines from './production-deadlines.vue';

const form = defineModel('form');

defineProps({
    jobOrder: Object,
    editable: Boolean,
    urgencyFlags: Object,
    dateFormat: String,
    states: Object,
});
</script>
