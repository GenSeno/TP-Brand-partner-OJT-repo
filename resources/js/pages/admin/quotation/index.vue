<template>
    <Head title="Quotations" />

    <div class="page-header">
        <div class="add-item d-flex">
            <div class="page-title">
                <h4>Quotation List</h4>
                <h6>Manage Your Quotation</h6>
            </div>
        </div>
        <ul class="table-top-head">
            <li>
                <a
                    data-bs-toggle="tooltip"
                    data-bs-placement="top"
                    title="Refresh"
                    @click="refreshPage"
                >
                    <i class="ti ti-refresh"></i>
                </a>
            </li>
            <li>
                <a
                    data-bs-toggle="tooltip"
                    data-bs-placement="top"
                    title="Collapse"
                    id="collapse-header"
                    @click="toggleHeader"
                >
                    <i class="ti ti-chevron-up"></i>
                </a>
            </li>
        </ul>
        <div class="page-btn">
            <ModalLink
                navigate
                :href="route('admin.quotation.create')"
                class="btn btn-added btn-primary"
                #default="{ loading }"
            >
                <loading-text :loading="loading">
                    <vue-feather type="plus-circle" class="me-2"></vue-feather>
                    Add Quotation
                </loading-text>
            </ModalLink>
        </div>
    </div>
    <div class="row">
        <div class="col-xl-2 col-sm-6 col-12 d-flex">
            <div class="dash-count bg-primary">
                <div class="dash-counts">
                    <h4 class="mb-1">{{ counts.new }}</h4>
                    <p class="text-white mb-0">New</p>
                </div>
                <div class="dash-imgs">
                    <i data-feather="trash-2" class="feather-file-plus"></i>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-sm-6 col-12 d-flex">
            <div class="dash-count bg-cyan">
                <div class="dash-counts">
                    <h4 class="mb-1">{{ counts.draft }}</h4>
                    <p class="text-white mb-0">On Going</p>
                </div>
                <div class="dash-imgs">
                    <i data-feather="trash-2" class="feather-file"></i>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-sm-6 col-12 d-flex">
            <div class="dash-count bg-dark">
                <div class="dash-counts">
                    <h4 class="mb-1">{{ counts.completed }}</h4>
                    <p class="text-white mb-0">Completed</p>
                </div>
                <div class="dash-imgs">
                    <i data-feather="trash-2" class="feather-check"></i>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-sm-6 col-12 d-flex">
            <div class="dash-count bg-success">
                <div class="dash-counts">
                    <h4 class="mb-1">{{ counts.sent }}</h4>
                    <p class="text-white mb-0">Sent</p>
                </div>
                <div class="dash-imgs">
                    <i data-feather="trash-2" class="feather-send"></i>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-sm-6 col-12 d-flex">
            <div class="dash-count bg-danger">
                <div class="dash-counts">
                    <h4 class="mb-1">{{ counts.cancelled }}</h4>
                    <p class="text-white mb-0">Deleted</p>
                </div>
                <div class="dash-imgs">
                    <i data-feather="trash-2" class="feather-x"></i>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-sm-6 col-12 d-flex">
            <div class="dash-count bg-secondary-gradient">
                <div class="dash-counts">
                    <h4 class="mb-1">{{ counts.total }}</h4>
                    <p class="text-white mb-0">Total</p>
                </div>
                <div class="dash-imgs">
                    <i data-feather="trash-2" class="feather-hard-drive"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="card table-list-card">
        <div
            class="card-header d-flex align-items-center justify-content-between flex-wrap row-gap-3"
        >
            <div class="d-flex align-items-center gap-2">
                <dt-search
                    v-model="form.filter.search"
                    @search="submitFilters"
                />
                <transition name="fade">
                    <div v-show="selected.length > 0">
                        <div class="d-flex align-items-center gap-2">
                            <div class="vr"></div>
                            <div class="btn-list d-md-flex d-block">
                                <dt-bulk-delete
                                    :ids="selected.map((s) => s.id)"
                                    route-name="admin.quotation.bulk-destroy"
                                    name="quotations"
                                    :emitter-event="deleteEmitterEvent"
                                />
                            </div>
                        </div>
                    </div>
                </transition>
            </div>
            <div
                class="d-flex table-dropdown my-xl-auto right-content align-items-center flex-wrap row-gap-3"
            >
                <select-filter
                    v-model="form.filter.status"
                    :options="customerStatus"
                    name="Status"
                    @change="submitFilters"
                ></select-filter>
            </div>
        </div>

        <div class="card-body p-0">
            <dt-table
                @update:selected="onSelect"
                v-model:sortings="form.sort"
                v-model:perPage="form.per_page"
                v-model:selected="selected"
                :columns="columns"
                :data="quotationTableData"
                :total-records="quotation.total"
                :start-record="quotation.from"
                :end-record="quotation.to"
                :links="quotation.links"
                @change="submitFilters"
            >
                <template #reference="{ row, value }">
                    <div
                        class="d-flex align-items-center"
                        @click="goToShow(row)"
                    >
                        <a href="javascript:void(0);" class="text-primary">{{
                            value
                        }}</a>
                    </div>
                </template>
                <template #full_name="{ row, value }">
                    <div
                        class="d-flex align-items-center"
                        @click="goToShow(row)"
                    >
                        <a
                            href="javascript:void(0);"
                            class="avatar avatar-md bg-light-900 p-1 me-2"
                        >
                            <img
                                class="object-fit-contain rounded-circle"
                                :src="row.avatar_url"
                                @error="setDefaultAvatar($event)"
                                alt="Avatar"
                            />
                        </a>
                        <a href="javascript:void(0);">{{ value }}</a>
                    </div>
                </template>
                <template #overall_total="{ value }">
                    {{ formatCurrency(value, currency) }}
                </template>
                <template #created_at="{ value }">
                    {{ dayjs(value).format(dateFormat) }}
                </template>

                <template #status="{ row }">
                    <span class="badge" :class="statusBg(row.status)">
                        {{ statusLabel(row.status) }}
                    </span>
                </template>
            </dt-table>
        </div>
    </div>
</template>
<style scoped>
.dt-table tbody tr:not([data-is-deletable='true']) input[type='checkbox'] {
    display: none;
}
</style>
<script setup>
import { onMounted, ref, computed, watch } from 'vue';
import { Head, router, useForm } from '@inertiajs/vue3';
import { removeEmptyValues } from '@/helpers/form';
import { toggleHeader } from '@/helpers/layout';
import { emitter } from '@/composables/eventBus';
import DashboardLayout from '@/layouts/dashboard-layout.vue';
import { statusBg, statusLabel } from '@/helpers/status';
import { formatCurrency } from '@/helpers/quote';

defineOptions({
    layout: DashboardLayout,
});

const props = defineProps({
    quotation: Object,
    filter: Object,
    default_per_page: {
        type: Number,
        default: 10,
    },
    errors: Object,
    auth: Object,
    flash: Object,
    counts: Object,
});
const defaultAvatar = '/img/default.png';
const currency = props?.quotation?.data[0]?.currency;
// Ensure every customer has a valid avatar URL
const quotationTableData = computed(() =>
    props.quotation.data.map((c) => {
        const defaultAddress = c.billing_address || {};
        const customer = c.quotable || {};
        return {
            ...c,
            avatar_url: customer.avatar_url || defaultAvatar,
            total: c.total.formatted,
            total_quantity:
                c.lines?.reduce((sum, line) => sum + (line.quantity || 0), 0) ||
                0,
            full_name: `${defaultAddress.first_name} ${defaultAddress.last_name}`,
            company_name: defaultAddress.company_name || '',
            is_deletable:
                c.status == 'request' || 'draft' || 'cancelled' ? true : false,
            'data-is-deletable':
                c.status == 'request' || 'draft' || 'cancelled' ? true : false,
        };
    }),
);

const columns = [
    {
        title: 'Quotation Number',
        dataIndex: 'reference',
        key: 'reference',
        sortable: true,
    },
    {
        title: 'Customer Name',
        dataIndex: 'full_name',
        key: 'full_name',
        sortable: true,
    },
    {
        title: 'Company Name',
        dataIndex: 'company_name',
        key: 'company_name',
        sortable: true,
    },
    { title: 'Quantity', dataIndex: 'total_quantity', key: 'total_quantity' },
    { title: 'Total', dataIndex: 'total', key: 'total' },
    { title: 'Status', dataIndex: 'status', key: 'status', sortable: true },
];

const deleteEmitterEvent = ref('brands-deleted');
const dateFormat = ref('DD MMM YYYY');
const customerStatus = ref({
    request: 'New',
    draft: 'On Going',
    completed: 'Completed',
    cancelled: 'Deleted',
    sent: 'Sent',
});

const form = useForm({
    filter: {
        search: props.filter.search || '',
        enabled: props.filter.enabled || '',
    },
    sort: [],
    per_page: props.quotation.per_page,
});
const selected = ref([]);

const submitFilters = () => {
    form.transform((data) =>
        removeEmptyValues({
            ...data,
            sort: data.sort.join(','),
            per_page:
                data.per_page === props.default_per_page ? '' : data.per_page,
        }),
    ).get(route('admin.quotation.index'), {
        preserveState: true,
        replace: true,
    });
};

const refreshPage = () => {
    router.get(
        route('admin.quotation.index'),
        {},
        { preserveState: false, preserveScroll: true, replace: true },
    );
};

function setDefaultAvatar(event) {
    event.target.src = defaultAvatar;
}

onMounted(() => {
    emitter.on(deleteEmitterEvent.value, (ids) => {
        selected.value = selected.value.filter(
            (item) => !ids.includes(item.id),
        );
    });
});

const goToShow = (row) => {
    router.get(route('admin.quotation.show', { quotation: row.id }));
};

const onSelect = (rows) => {
    selected.value = rows.filter((row) => row.is_deletable);
};

watch(
    selected,
    (rows) => {
        const filtered = rows.filter((row) => row.is_deletable);

        // 🔒 Guard: only update if different
        if (filtered.length !== rows.length) {
            selected.value = filtered;
        }
    },
    { deep: true },
);
</script>
