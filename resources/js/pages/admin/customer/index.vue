<template>
    <Head title="Customer" />

    <div class="page-header">
        <div class="add-item d-flex">
            <div class="page-title">
                <h4>Customer</h4>
                <h6>Manage your customers</h6>
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
        <div class="page-btn">
            <ModalLink
                navigate
                :href="route('admin.customer.create')"
                class="btn btn-added btn-primary"
                #default="{ loading }"
            >
                <loading-text :loading="loading">
                    <vue-feather type="plus-circle" class="me-2"></vue-feather>
                    Add New Customer
                </loading-text>
            </ModalLink>
        </div>
    </div>
    <div class="row">
        <div class="col-xl-3 col-sm-6 col-12 d-flex">
            <div class="dash-count bg-primary">
                <div class="dash-counts">
                    <h4 class="mb-1">{{ counts.active }}</h4>
                    <p class="text-white mb-0">Active Customers</p>
                </div>
                <div class="dash-imgs">
                    <i data-feather="trash-2" class="feather-user-check"></i>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6 col-12 d-flex">
            <div class="dash-count bg-cyan">
                <div class="dash-counts">
                    <h4 class="mb-1">{{ counts.inactive }}</h4>
                    <p class="text-white mb-0">Inactive Customers</p>
                </div>
                <div class="dash-imgs">
                    <i data-feather="trash-2" class="feather-user-x"></i>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6 col-12 d-flex">
            <div class="dash-count bg-dark">
                <div class="dash-counts">
                    <h4 class="mb-1">{{ counts.total }}</h4>
                    <p class="text-white mb-0">Total Customers</p>
                </div>
                <div class="dash-imgs">
                    <i data-feather="trash-2" class="feather-users"></i>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6 col-12 d-flex">
            <div class="dash-count bg-success">
                <div class="dash-counts">
                    <h4 class="mb-1">0</h4>
                    <p class="text-white mb-0">Others</p>
                </div>
                <div class="dash-imgs">
                    <i data-feather="trash-2" class="feather-users"></i>
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
                                    route-name="admin.customer.bulk-destroy"
                                    name="customers"
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
                    v-model="form.filter.enabled"
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
                :data="customerTableData"
                :total-records="customers.total"
                :start-record="customers.from"
                :end-record="customers.to"
                :links="customers.links"
                @change="submitFilters"
            >
                <template #full_name="{ row, value }">
                    <div class="d-flex align-items-center">
                        <div class="avatar avatar-md bg-light-900 p-1 me-2">
                            <img
                                class="object-fit-contain rounded-circle"
                                :src="row.avatar_url"
                                @error="setDefaultAvatar($event)"
                                alt="Avatar"
                            />
                        </div>
                        <ModalLink
                            navigate
                            :href="route('admin.customer.edit', row.id)"
                        >
                            {{ value }}
                        </ModalLink>
                    </div>
                </template>

                <template #created_at="{ value }">
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
                        <!-- <label class="form-check-label ms-2" for="statusSwitch">
                        {{ value ? 'Active' : 'Inactive' }}
                    </label> -->
                    </div>
                </template>

                <template #action="{ row, value }">
                    <div class="action-table-data">
                        <div class="edit-delete-action">
                            <ModalLink
                                navigate
                                :href="route('admin.customer.edit', value)"
                                class="btn btn-icon btn-outline-light btn-sm me-2"
                                title="View"
                            >
                                <i data-feather="edit" class="feather-eye"></i>
                            </ModalLink>
                            <ModalLink
                                navigate
                                :href="route('admin.customer.edit', value)"
                                class="btn btn-icon btn-outline-light btn-sm me-2"
                                title="Edit"
                            >
                                <i data-feather="edit" class="feather-edit"></i>
                            </ModalLink>
                            <dt-delete
                                :id="value"
                                route-name="admin.customer.destroy"
                                :name="row.name"
                                model-name="customer"
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

defineOptions({
    layout: DashboardLayout,
});

const props = defineProps({
    customers: Object,
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

// Ensure every customer has a valid avatar URL
const customerTableData = computed(() =>
    props.customers.data.map((c) => {
        const defaultAddress = c.addresses?.find((a) => a.default) || {};
        return {
            ...c,
            avatar_url: c.avatar_url || defaultAvatar,
            email: defaultAddress.email || '',
            phone: defaultAddress.phone || '',
            province: defaultAddress.province || '',
            is_deletable: c.is_deletable,
            'data-is-deletable': c.is_deletable, //
        };
    }),
);

const columns = [
    { title: 'Customer Name', dataIndex: 'full_name', key: 'full_name' },
    { title: 'Company Name', dataIndex: 'company_name', key: 'company_name' },
    { title: 'Email', dataIndex: 'email', key: 'email' },
    { title: 'Phone', dataIndex: 'phone', key: 'phone' },
    { title: 'Province', dataIndex: 'province', key: 'province' },
    { title: 'Status', dataIndex: 'enabled', key: 'enabled' },
    { title: '', dataIndex: 'id', key: 'action' },
];

const deleteEmitterEvent = ref('customer-deleted');
const dateFormat = ref('DD MMM YYYY');
const customerStatus = ref({ 1: 'Active', 0: 'Inactive' });

const form = useForm({
    filter: {
        search: props.filter.search || '',
        enabled: props.filter.enabled || '',
    },
    sort: [],
    per_page: props.customers.per_page,
});
const selected = ref([]);
const statusToggleEnabled = ref(true);

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
    ).get(route('admin.customer.index'), {
        preserveState: true,
        replace: true,
    });
};

const refreshPage = () => {
    router.get(
        route('admin.customer.index'),
        {},
        { preserveState: false, preserveScroll: true, replace: true },
    );
};

const toggleStatus = (customer) => {
    router.post(
        route('admin.customer.toggle-status', { customer: customer.id }),
        {},
        {
            preserveState: true,
            replace: true,
            onStart: () => {
                statusToggleEnabled.value = false;
            },
            onSuccess: () => {
                statusToggleEnabled.value = true;
            },
        },
    );
};

// Fallback function for missing avatars
function setDefaultAvatar(event) {
    event.target.src = defaultAvatar;
}

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

onMounted(() => {
    emitter.on(deleteEmitterEvent.value, (data) => {
        router.reload({
            preserveState: true,
            preserveScroll: true,
            replace: true,
        });
        selected.value = selected.value.filter(
            (item) => !data.deleted.includes(item.id),
        );
        alert.showSuccess(data.message || 'Customer deleted successfully.');
    });
});

const onSelect = (rows) => {
    selected.value = rows.filter((row) => row.is_deletable);
};
</script>
