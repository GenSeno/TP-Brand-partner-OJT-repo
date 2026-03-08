<template>
    <Head title="Job Order" />

    <div class="page-header">
        <div class="add-item d-flex">
            <div class="page-title">
                <h4>Job Orders</h4>
                <h6>Manage your job orders</h6>
            </div>
        </div>
        <ul class="table-top-head">
            <li>
                <a data-bs-toggle="tooltip" data-bs-placement="top" title="Pdf"
                    ><img src="/img/icons/pdf.svg" alt="img"
                /></a>
            </li>
            <li>
                <a
                    data-bs-toggle="tooltip"
                    data-bs-placement="top"
                    title="Excel"
                    ><img src="/img/icons/excel.svg" alt="img"
                /></a>
            </li>
            <li>
                <a
                    data-bs-toggle="tooltip"
                    data-bs-placement="top"
                    title="Refresh"
                    @click="refreshPage"
                    ><i class="ti ti-refresh"></i
                ></a>
            </li>
            <li>
                <a
                    data-bs-toggle="tooltip"
                    data-bs-placement="top"
                    title="Collapse"
                    id="collapse-header"
                    @click="toggleHeader"
                    ><i class="ti ti-chevron-up"></i
                ></a>
            </li>
        </ul>
    </div>

    <StageTabs
        v-if="Object.keys(states).length > 1"
        :states="states"
        v-model:form="form"
        @update:form="submitFilters"
    />

    <UrgencyCards :total="total" />

    <div class="card table-list-card">
        <div
            class="card-header d-flex align-items-center justify-content-between flex-wrap row-gap-3"
        >
            <div class="d-flex align-items-center gap-2">
                <dt-search
                    v-model="form.filter.search"
                    @search="submitFilters"
                />
            </div>
            <div
                class="d-flex table-dropdown my-xl-auto right-content align-items-center flex-wrap row-gap-3"
            >
                <select-filter
                    v-model="form.misc.progress"
                    :options="progressOptions"
                    name="Status"
                    :with-all="false"
                    @change="submitFilters"
                ></select-filter>
                <select-filter
                    v-model="form.filter.urgency_flag"
                    :options="props.urgencyFlags"
                    name="Urgency Status"
                    @change="submitFilters"
                ></select-filter>
            </div>
        </div>
        <div class="card-body p-0">
            <dt-table
                v-model:sortings="form.sort"
                v-model:perPage="form.per_page"
                :columns="columns"
                :data="jobOrders.data"
                :total-records="jobOrders.total"
                :start-record="jobOrders.from"
                :end-record="jobOrders.to"
                :links="jobOrders.links"
                :selectable="false"
                @change="submitFilters"
            >
                <template #urgency_flag="{ value }">
                    <UrgentStatus :urgency-flag="value" />
                </template>
                <template #customer="{ value }">
                    {{ value.orderable.full_name }}
                </template>
                <template #reference="{ row, value }">
                    <ModalLink
                        navigate
                        :href="
                            route('admin.job-order.edit', {
                                job_order: row.id,
                                stage: form.filter.stage || undefined,
                            })
                        "
                        class="link-primary"
                        title="Edit"
                    >
                        {{ value }}
                    </ModalLink>
                </template>
                <template #total_quantity="{ value }">
                    <div class="text-center">
                        {{ value }}
                    </div>
                </template>
                <template #current_state_data="{ row, value }">
                    <template
                        v-if="form.filter.stage === JobOrderStage.Printing"
                    >
                        {{ getPrintingOperator(row) }}
                    </template>
                    <template
                        v-else-if="
                            form.filter.stage === JobOrderStage.HeatPress
                        "
                    >
                        {{ getHeatPressOperator(row) }}
                    </template>
                    <template
                        v-else-if="form.filter.stage === JobOrderStage.Artist"
                    >
                        {{ getArtistOperator(row) }}
                    </template>
                    <template
                        v-else-if="form.filter.stage === JobOrderStage.Sewing"
                    >
                        {{ getSewingOperator(row) }}
                    </template>
                    <template v-else>
                        <div class="d-flex flex-wrap gap-1">
                            <template v-if="getActiveStages(row).length > 0">
                                <span
                                    v-for="stage in getActiveStages(row)"
                                    :key="stage.state"
                                    :class="`badge shadow-none badge-xs badge-soft-${states[stage.state]?.color || 'secondary'}`"
                                >
                                    <i
                                        :class="`feather ${states[stage.state]?.icon}`"
                                    ></i>
                                    {{
                                        states[stage.state]?.label ||
                                        stage.state
                                    }}
                                </span>
                            </template>
                            <span
                                v-else
                                :class="`badge shadow-none badge-xs badge-soft-${value.color}`"
                            >
                                <i :class="`feather ${value.icon}`"></i>
                                {{ value.label }}
                            </span>
                        </div>
                    </template>
                </template>
                <template #ordered_at="{ value }">
                    {{ dayjs(value).format(dateFormat) }}
                </template>
                <template #due_at="{ row }">
                    {{ deadline[row.id]?.date }}
                </template>
                <template #lead_time="{ row }">
                    <span v-if="isString(deadline[row.id]?.left)">
                        {{ deadline[row.id]?.left }}
                    </span>
                    <span
                        v-else
                        :class="{
                            'text-danger': deadline[row.id]?.left <= 0,
                        }"
                    >
                        {{ deadline[row.id]?.left }} day(s)
                    </span>
                </template>
            </dt-table>
        </div>
    </div>
</template>

<script setup>
import { computed, ref, watch } from 'vue';
import dayjs from 'dayjs';
import { Head, router, useForm } from '@inertiajs/vue3';
import { toggleHeader } from '@/helpers/layout';
import { removeEmptyValues } from '@/helpers/form';
import UrgentStatus from '@/components/job-order/urgent-status.vue';
import DashboardLayout from '@/layouts/dashboard-layout.vue';
import UrgencyCards from './partial-index/urgency-cards.vue';
import StageTabs from './partial-index/stage-tabs.vue';
import { isString } from 'lodash';
import { JobOrderStage } from '@/enums/job-order-stage';

defineOptions({
    layout: DashboardLayout,
});

const props = defineProps({
    jobOrders: Object,
    filter: Object,
    misc: Object,
    urgencyFlags: Object,
    states: Object,
    total: Object,
});

const form = useForm({
    filter: {
        search: props.filter.search || '',
        urgency_flag: props.filter.urgency_flag || '',
        stage: props.filter.stage || '',
    },
    misc: {
        progress: props.misc.progress || 'pending',
    },
    sort: [],
    per_page: props.jobOrders.per_page,
});

const columns = computed(() =>
    [
        {
            title: 'Urgency Status',
            dataIndex: 'urgency_flag',
            key: 'urgency_flag',
        },
        {
            title: 'Customer',
            dataIndex: 'order',
            key: 'customer',
        },
        {
            title: 'JO No.',
            dataIndex: 'reference',
            key: 'reference',
            sortable: true,
        },
        {
            title: 'Quantity',
            dataIndex: 'total_quantity',
            key: 'total_quantity',
            sortable: true,
        },
        {
            title: 'JO Date',
            dataIndex: 'ordered_at',
            key: 'ordered_at',
            sortable: true,
        },
        ![JobOrderStage.Packing, JobOrderStage.Dispatching].includes(
            form.filter.stage,
        )
            ? {
                  title:
                      form.filter.stage === JobOrderStage.Printing
                          ? 'Printer Operator'
                          : form.filter.stage === JobOrderStage.Artist
                            ? 'Artist'
                            : form.filter.stage === JobOrderStage.HeatPress
                              ? 'Heat Press Operator'
                              : form.filter.stage === JobOrderStage.Sewing
                                ? 'Sewer'
                                : 'Stage',
                  dataIndex: 'current_state_data',
                  key: 'current_state_data',
              }
            : null,
        {
            title: 'Deadline',
            dataIndex: 'due_at',
            key: 'due_at',
            sortable: true,
        },
        {
            title: 'Days to Deadline',
            dataIndex: 'lead_time',
            key: 'lead_time',
        },
    ].filter(Boolean),
);

const progressOptions = computed(() => {
    const options = {
        pending: 'Pending',
        cancelled: 'Cancelled',
    };
    if (form.filter.stage !== JobOrderStage.NewOrder) {
        options.completed = 'Completed';
    }
    return options;
});

const dateFormat = ref('DD MMM YYYY');

const deadline = computed(() => {
    const map = {};
    for (const row of props.jobOrders.data) {
        // Default values
        let date = '-';
        let left = '-';

        // Early return for cancelled jobs
        if (row.cancelled_at) {
            map[row.id] = { date, left: 'Cancelled' };
            continue;
        }

        let dueDate;
        let stage;
        if ([JobOrderStage.NewOrder, ''].includes(form.filter.stage)) {
            dueDate = row.due_at ? dayjs(row.due_at) : null;
        } else {
            stage = (row.stages || []).find(
                (s) => s.state === form.filter.stage,
            );
            dueDate = stage?.due_at ? dayjs(stage.due_at) : null;
        }

        if (dueDate) {
            date = dueDate.format(dateFormat.value);
            if (row.current_state === 'completed' || stage?.completed_at) {
                left = 'Completed';
            } else {
                left = dueDate.diff(dayjs(), 'day');
            }
        }

        map[row.id] = { date, left };
    }
    return map;
});

const getActiveStages = (row) => {
    return (row.stages || []).filter((s) => s.started_at && !s.completed_at);
};

const getPrintingOperator = (row) => {
    const stage = (row.stages || []).find(
        (s) => s.state === JobOrderStage.Printing,
    );
    return stage?.operator?.full_name || '-';
};

const getHeatPressOperator = (row) => {
    const stage = (row.stages || []).find(
        (s) => s.state === JobOrderStage.HeatPress,
    );
    return stage?.operator?.full_name || '-';
};

const getArtistOperator = (row) => {
    const stage = (row.stages || []).find(
        (s) => s.state === JobOrderStage.Artist,
    );
    return stage?.operator?.full_name || 'Not yet assigned';
};

const getSewingOperator = (row) => {
    const stage = (row.stages || []).find(
        (s) => s.state === JobOrderStage.Sewing,
    );
    return stage?.operator?.full_name || '-';
};

const isSubmittedToNextStep = (row) => {
    const currentStage = form.filter.stage;
    if (!currentStage) return false;
    const stage = (row.stages || []).find((s) => s.state === currentStage);
    return !!stage?.completed_at;
};

const submitFilters = () => {
    form.transform((data) =>
        removeEmptyValues({
            ...data,
            sort: data.sort.join(','),
            per_page:
                data.per_page === props.filter.default_per_page
                    ? ''
                    : data.per_page,
        }),
    ).get(route('admin.job-order.index'), {
        preserveState: true,
        preserveScroll: true,
        replace: true,
        only: ['jobOrders', 'filter', 'states', 'total'],
    });
};

const refreshPage = () => {
    router.get(
        route('admin.job-order.index'),
        {},
        {
            preserveState: false,
            preserveScroll: true,
            replace: true,
        },
    );
};

watch(
    () => form.filter.stage,
    (newStage) => {
        if (newStage === JobOrderStage.NewOrder) {
            form.misc.progress = 'pending';
        }
    },
);
</script>
