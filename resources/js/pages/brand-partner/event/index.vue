<template>
    <Head title="Events" />

    <div class="page-header">
        <div class="add-item d-flex">
            <div class="page-title">
                <h4>Events</h4>
                <h6>Manage your events for product categorization</h6>
            </div>
        </div>
        <div class="page-btn">
            <ModalLink
                navigate
                :href="route('brand-partner.events.create')"
                class="btn btn-added"
                #default="{ loading }"
            >
                <loading-text :loading="loading">
                    <vue-feather type="plus-circle" class="me-2"></vue-feather>
                    Add Event
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
                    v-model="form.filter.status"
                    :options="statusOptions"
                    name="Status"
                    @change="submitFilters"
                ></select-filter>
            </div>
        </div>

        <div class="card-body p-0">
            <dt-table
                v-model:sortings="form.sort"
                v-model:perPage="form.per_page"
                :columns="columns"
                :data="events.data"
                :total-records="events.total"
                :start-record="events.from"
                :end-record="events.to"
                :links="events.links"
                @change="submitFilters"
            >
                <template #status="{ value }">
                    <span class="badge" :class="`bg-${getStatusColor(value)}`">
                        {{ value }}
                    </span>
                </template>

                <template #start_date="{ value }">
                    {{ value ? formatDate(value) : '-' }}
                </template>

                <template #end_date="{ value }">
                    {{ value ? formatDate(value) : '-' }}
                </template>

                <template #enabled="{ row, value }">
                    <div class="form-check form-switch">
                        <input
                            class="form-check-input"
                            type="checkbox"
                            :checked="value"
                            @change="toggleStatus(row)"
                        />
                    </div>
                </template>

                <template #action="{ row, value }">
                    <div class="action-table-data">
                        <div class="edit-delete-action">
                            <ModalLink
                                navigate
                                :href="
                                    route('brand-partner.events.edit', value)
                                "
                                class="btn btn-icon btn-outline-light btn-sm me-2"
                                title="Edit"
                            >
                                <vue-feather
                                    type="edit"
                                    class="feather-14"
                                ></vue-feather>
                            </ModalLink>
                            <dt-delete
                                :id="value"
                                route-name="brand-partner.events.destroy"
                                :name="row.name"
                                model-name="event"
                                class="btn btn-icon btn-danger-light btn-sm"
                                :class="{
                                    'disabled opacity-50':
                                        row.products_count > 0,
                                }"
                                title="Delete"
                            >
                                <vue-feather
                                    type="trash-2"
                                    class="feather-14"
                                ></vue-feather>
                            </dt-delete>
                        </div>
                    </div>
                </template>
            </dt-table>
        </div>
    </div>
</template>

<script setup>
import { Head, router, useForm } from '@inertiajs/vue3';
import dayjs from 'dayjs';
import { removeEmptyValues } from '@/helpers/form';

const props = defineProps({
    events: Object,
    statusOptions: Object,
    filter: Object,
});

const columns = [
    { title: 'Name', dataIndex: 'name', key: 'name', sortable: true },
    { title: 'Status', dataIndex: 'status', key: 'status', sortable: true },
    {
        title: 'Start Date',
        dataIndex: 'start_date',
        key: 'start_date',
        sortable: true,
    },
    {
        title: 'End Date',
        dataIndex: 'end_date',
        key: 'end_date',
        sortable: true,
    },
    { title: 'Products', dataIndex: 'products_count', key: 'products_count' },
    { title: 'Enabled', dataIndex: 'enabled', key: 'enabled' },
    { title: '', dataIndex: 'id', key: 'action' },
];

const form = useForm({
    filter: {
        search: props.filter?.search || '',
        status: props.filter?.status || '',
    },
    sort: [],
    per_page: props.events?.per_page || 10,
});

const submitFilters = () => {
    form.transform((data) =>
        removeEmptyValues({
            ...data,
            sort: data.sort.join(','),
            per_page: data.per_page === 10 ? '' : data.per_page,
        }),
    ).get(route('brand-partner.events.index'), {
        preserveState: true,
        replace: true,
    });
};

const toggleStatus = (event) => {
    router.post(
        route('brand-partner.events.toggle-status', { event: event.id }),
        {},
        { preserveState: true, replace: true },
    );
};

const formatDate = (date) => {
    return dayjs(date).format('MMM D, YYYY');
};

const getStatusColor = (status) => {
    const colors = {
        upcoming: 'info',
        active: 'success',
        ended: 'secondary',
    };
    return colors[status] || 'secondary';
};
</script>
