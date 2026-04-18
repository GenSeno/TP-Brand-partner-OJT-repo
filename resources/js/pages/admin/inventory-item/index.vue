<template>
    <Head title="Raw Materials" />

    <div class="page-header">
        <div class="add-item d-flex">
            <div class="page-title">
                <h4>Raw Materials</h4>
                <h6>Manage your inventory items</h6>
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
        <div class="page-btn">
            <ModalLink
                navigate
                :href="route('admin.inventory-item.create')"
                class="btn btn-added btn-primary"
                #default="{ loading }"
            >
                <loading-text :loading="loading">
                    <vue-feather type="plus-circle" class="me-2"></vue-feather>
                    Add New Item
                </loading-text>
            </ModalLink>
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
                    placeholder="Search Item Name, Type"
                />
            </div>
            <div
                class="d-flex table-dropdown my-xl-auto right-content align-items-center flex-wrap row-gap-3"
            >
                <select-filter
                    v-model="form.filter.enabled"
                    :options="inventoryStatus"
                    name="Status"
                    @change="submitFilters"
                ></select-filter>
                <select-filter
                    v-model="form.filter.type"
                    :options="props.inventoryTypes"
                    name="Type"
                    @change="submitFilters"
                ></select-filter>
            </div>
        </div>
        <div class="card-body p-0">
            <dt-table
                v-model:sortings="form.sort"
                v-model:perPage="form.per_page"
                v-model:selected="selected"
                :columns="columns"
                :data="props.inventoryItems.data"
                :total-records="props.inventoryItems.total"
                :start-record="props.inventoryItems.from"
                :end-record="props.inventoryItems.to"
                :links="props.inventoryItems.links"
                @change="submitFilters"
                :selectable="false"
            >
                <template #type="{ value }">
                    <span class="badge shadow-none badge-xs badge-soft-info">{{
                        value
                    }}</span>
                </template>
                <template #item_name="{ row, value }">
                    <ModalLink
                        navigate
                        :href="route('admin.inventory-item.edit', row.id)"
                    >
                        {{ value }}
                    </ModalLink>
                </template>
                <template #current_stock="{ row, value }">
                    <span
                        :class="{
                            'text-danger fw-bold': value <= row.reorder_qty,
                            'text-warning fw-bold':
                                value > row.reorder_qty &&
                                value <= row.reorder_qty + 5,
                        }"
                    >
                        {{ simplifyFloat(value) }}
                        {{ row.unit_measure?.code }}
                    </span>
                </template>
                <template #created_at="{ value }">
                    {{ dayjs(value).format(dateFormat) }}
                </template>
                <template #action="{ row, value }">
                    <div class="action-table-data">
                        <div class="edit-delete-action">
                            <ModalLink
                                navigate
                                :href="
                                    route(
                                        'admin.inventory-item.adjust-stock',
                                        value,
                                    )
                                "
                                class="btn btn-icon btn-primary-light btn-sm me-2"
                                title="Adjust Stock"
                            >
                                <i class="ti ti-adjustments"></i>
                            </ModalLink>
                            <ModalLink
                                navigate
                                :href="
                                    route('admin.inventory-item.edit', value)
                                "
                                class="btn btn-icon btn-outline-light btn-sm me-2"
                                title="Edit"
                            >
                                <i data-feather="edit" class="feather-edit"></i>
                            </ModalLink>
                            <dt-delete2
                                :record-name="row.item_name"
                                model-name="inventory item"
                                :url="
                                    route('admin.inventory-item.destroy', value)
                                "
                                class="btn btn-icon btn-danger-light btn-sm me-2"
                                title="Delete"
                                :emitter-event="deleteEmitterEvent"
                                :disabled="row.movements_count > 0"
                            >
                                <i
                                    data-feather="trash-2"
                                    class="feather-trash-2"
                                ></i>
                            </dt-delete2>
                            <ModalLink
                                navigate
                                :href="
                                    route('admin.inventory-item.history', value)
                                "
                                class="btn btn-icon btn-secondary-light btn-sm me-2"
                                title="Movement History"
                            >
                                <i class="ti ti-history"></i>
                            </ModalLink>
                        </div>
                    </div>
                </template>
            </dt-table>
        </div>
    </div>
</template>

<script setup>
import { onMounted, ref } from 'vue';
import { Head, router, useForm } from '@inertiajs/vue3';
import { removeEmptyValues } from '@/helpers/form';
import { toggleHeader } from '@/helpers/layout';
import { emitter } from '@/composables/eventBus';
import * as alert from '@/helpers/alert';
import dayjs from 'dayjs';
import DashboardLayout from '@/layouts/dashboard-layout.vue';
import { simplifyFloat } from '@/helpers/number';

defineOptions({
    layout: DashboardLayout,
});

const props = defineProps({
    inventoryItems: Object,
    inventoryTypes: Object,
    filter: Object,
});

const deleteEmitterEvent = 'inventory-item:deleted';
const selected = ref([]);

const columns = [
    {
        title: 'Type',
        dataIndex: 'type',
        key: 'type',
        sortable: true,
    },
    {
        title: 'Item Name',
        dataIndex: 'item_name',
        key: 'item_name',
        sortable: true,
    },
    {
        title: 'Current Stock',
        dataIndex: 'current_stock',
        key: 'current_stock',
        sortable: true,
    },
    {
        title: 'Created',
        dataIndex: 'created_at',
        key: 'created_at',
        sortable: true,
    },
    {
        title: 'Reorder Qty',
        dataIndex: 'reorder_qty',
        key: 'reorder_qty',
        sortable: true,
    },
    {
        title: '',
        dataIndex: 'id',
        key: 'action',
    },
];

const dateFormat = 'DD MMM YYYY';

const inventoryStatus = ref({
    1: 'Active',
    0: 'Inactive',
});

const form = useForm({
    filter: {
        search: props.filter?.search || '',
        type: props.filter?.type || '',
        enabled: props.filter?.enabled || '',
    },
    sort: [],
    per_page: props.inventoryItems.per_page,
});

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
    ).get(route('admin.inventory-item.index'), {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    });
};

const refreshPage = () => {
    router.get(
        route('admin.inventory-item.index'),
        {},
        {
            preserveState: false,
            preserveScroll: true,
            replace: true,
        },
    );
};

onMounted(() => {
    emitter.on('inventory-item:created', () => {
        router.reload({
            preserveState: true,
            preserveScroll: true,
            replace: true,
        });
    });

    emitter.on('inventory-item:updated', () => {
        router.reload({
            preserveState: true,
            preserveScroll: true,
            replace: true,
        });
    });

    emitter.on('inventory-item:adjusted', () => {
        router.reload({
            preserveState: true,
            preserveScroll: true,
            replace: true,
        });
    });

    emitter.on(deleteEmitterEvent, (data) => {
        router.reload({
            preserveState: true,
            preserveScroll: true,
            replace: true,
        });
        selected.value = selected.value.filter(
            (item) => !data.deleted.includes(item.id),
        );
        alert.showSuccess(data.message || 'Item deleted successfully.');
    });
});
</script>
