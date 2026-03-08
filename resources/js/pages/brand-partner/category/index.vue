<template>
    <Head title="Categories" />

    <div class="page-header">
        <div class="add-item d-flex">
            <div class="page-title">
                <h4>Categories</h4>
                <h6>Manage your product categories</h6>
            </div>
        </div>
        <div class="page-btn">
            <ModalLink
                navigate
                :href="route('brand-partner.categories.create')"
                class="btn btn-added"
                #default="{ loading }"
            >
                <loading-text :loading="loading">
                    <vue-feather type="plus-circle" class="me-2"></vue-feather>
                    Add Category
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
                    v-model="form.filter.type"
                    :options="typeOptions"
                    name="Type"
                    @change="submitFilters"
                ></select-filter>
            </div>
        </div>

        <div class="card-body p-0">
            <dt-table
                v-model:sortings="form.sort"
                v-model:perPage="form.per_page"
                :columns="columns"
                :data="categories.data"
                :total-records="categories.total"
                :start-record="categories.from"
                :end-record="categories.to"
                :links="categories.links"
                @change="submitFilters"
            >
                <template #type="{ value }">
                    <span
                        class="badge"
                        :class="value === 'event' ? 'bg-info' : 'bg-primary'"
                    >
                        {{ value }}
                    </span>
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
                                    route(
                                        'brand-partner.categories.edit',
                                        value,
                                    )
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
                                route-name="brand-partner.categories.destroy"
                                :name="row.name"
                                model-name="category"
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
import { removeEmptyValues } from '@/helpers/form';

const props = defineProps({
    categories: Object,
    typeOptions: Object,
    filter: Object,
});

const columns = [
    { title: 'Name', dataIndex: 'name', key: 'name', sortable: true },
    { title: 'Type', dataIndex: 'type', key: 'type', sortable: true },
    { title: 'Products', dataIndex: 'products_count', key: 'products_count' },
    {
        title: 'Position',
        dataIndex: 'position',
        key: 'position',
        sortable: true,
    },
    { title: 'Enabled', dataIndex: 'enabled', key: 'enabled' },
    { title: '', dataIndex: 'id', key: 'action' },
];

const form = useForm({
    filter: {
        search: props.filter?.search || '',
        type: props.filter?.type || '',
    },
    sort: [],
    per_page: props.categories?.per_page || 10,
});

const submitFilters = () => {
    form.transform((data) =>
        removeEmptyValues({
            ...data,
            sort: data.sort.join(','),
            per_page: data.per_page === 10 ? '' : data.per_page,
        }),
    ).get(route('brand-partner.categories.index'), {
        preserveState: true,
        replace: true,
    });
};

const toggleStatus = (category) => {
    router.post(
        route('brand-partner.categories.toggle-status', {
            category: category.id,
        }),
        {},
        { preserveState: true, replace: true },
    );
};
</script>
