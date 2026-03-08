<template>
    <Head title="Expense Accounts" />

    <div class="page-header">
        <div class="add-item d-flex">
            <div class="page-title">
                <h4>Expense Account List</h4>
                <h6>Manage your Expense Accounts</h6>
            </div>
        </div>
        <ul class="table-top-head">
            <li>
                <a data-bs-toggle="tooltip" data-bs-placement="top" title="Pdf">
                    <img src="/img/icons/pdf.svg" alt="img" />
                </a>
            </li>
            <li>
                <a
                    data-bs-toggle="tooltip"
                    data-bs-placement="top"
                    title="Excel"
                >
                    <img src="/img/icons/excel.svg" alt="img" />
                </a>
            </li>
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
        <div class="page-btn d-flex flex-wrap gap-2">
            <Link
                :href="route('admin.expense.index')"
                class="btn btn-added btn-dark"
                view-transition
            >
                <vue-feather type="arrow-left" class="me-2"></vue-feather>
                Back to Expenses
            </Link>
            <ModalLink
                navigate
                :href="route('admin.expense_account.create')"
                class="btn btn-added"
                #default="{ loading }"
            >
                <loading-text :loading="loading">
                    <vue-feather type="plus-circle" class="me-2"></vue-feather>
                    Add New Expense Account
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
                />
            </div>
            <div
                class="d-flex table-dropdown my-xl-auto right-content align-items-center flex-wrap row-gap-3"
            >
                <select-filter
                    v-model="form.filter.enabled"
                    :options="accountStatus"
                    name="Status"
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
                :data="accountTableData"
                :total-records="account.total"
                :start-record="account.from"
                :end-record="account.to"
                :links="account.links"
                :selectable="false"
                @change="submitFilters"
            >
                <template #expense_date="{ value }">
                    {{ dayjs(value).format(dateFormat) }}
                </template>

                <template #enabled="{ row, value }">
                    <div
                        class="form-check form-check-inline form-switch me-0 d-flex align-items-center"
                    >
                        <input
                            class="form-check-input"
                            type="checkbox"
                            role="switch"
                            :checked="value"
                            :disabled="!statusToggleEnabled"
                            @change="toggleStatus(row)"
                            id="statusSwitch"
                        />
                    </div>
                </template>
                <template #action="{ row, value }">
                    <div class="action-table-data">
                        <div class="edit-delete-action">
                            <ModalLink
                                navigate
                                :href="
                                    route('admin.expense_account.edit', value)
                                "
                                class="btn btn-icon btn-outline-light btn-sm me-2"
                                title="Edit"
                            >
                                <i data-feather="edit" class="feather-edit"></i>
                            </ModalLink>
                            <dt-delete
                                :id="value"
                                route-name="admin.expense_account.destroy"
                                :name="row.name"
                                model-name="expense_account"
                                :emitter-event="deleteEmitterEvent"
                                class="btn btn-icon btn-danger-light btn-sm me-2"
                                :class="{
                                    'disabled opacity-50 pointer-events-none':
                                        !row.is_deletable,
                                }"
                                title="Delete"
                            >
                                <i
                                    data-feather="trash-2"
                                    class="feather-trash-2"
                                ></i>
                            </dt-delete>
                        </div>
                    </div>
                </template>
            </dt-table>
        </div>
    </div>
</template>
<script setup>
import { ref, computed, onMounted } from 'vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { removeEmptyValues } from '@/helpers/form';
import * as alert from '@/helpers/alert';
import { emitter } from '@/composables/eventBus';
import DashboardLayout from '@/layouts/dashboard-layout.vue';
import dayjs from 'dayjs';

defineOptions({
    layout: DashboardLayout,
});

const props = defineProps({
    account: Object,
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
const deleteEmitterEvent = ref('expenseaccount:deleted');
const accountTableData = computed(() =>
    props.account.data.map((c) => {
        return {
            ...c,
            is_deletable: c.is_deletable,
        };
    }),
);

const columns = [
    { title: 'Name', dataIndex: 'name', key: 'name', sortable: true },
    { title: 'Description', dataIndex: 'description', key: 'description' },
    { title: 'Status', dataIndex: 'enabled', key: 'enabled' },
    { title: '', dataIndex: 'id', key: 'action' },
];

const dateFormat = ref('DD MMM YYYY');
const accountStatus = ref({ 1: 'Active', 0: 'Inactive' });

const form = useForm({
    filter: {
        search: props.filter.search || '',
        enabled: props.filter.enabled || '',
    },
    sort: [],
    per_page: props.account.per_page,
});
const selected = ref([]);
const statusToggleEnabled = ref(true);
const submitFilters = () => {
    form.transform((data) =>
        removeEmptyValues({
            ...data,
            sort: data.sort.join(','),
            per_page:
                data.per_page === props.default_per_page ? '' : data.per_page,
        }),
    ).get(route('admin.expense_account.index'), {
        preserveState: true,
        replace: true,
    });
};

const toggleStatus = (account) => {
    router.post(
        route('admin.expense_account.toggle-status', { account: account.id }),
        {},
        {
            preserveState: true,
            replace: true,
            onStart: () => {
                statusToggleEnabled.value = 0;
            },
            onSuccess: () => {
                statusToggleEnabled.value = 1;
            },
        },
    );
};

onMounted(() => {
    emitter.on(deleteEmitterEvent.value, (data) => {
        router.reload({
            preserveState: true,
            preserveScroll: true,
            replace: true,
        });
    });
});
</script>
