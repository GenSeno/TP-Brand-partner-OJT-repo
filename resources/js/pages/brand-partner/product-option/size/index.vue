<template>
    <Head title="Sizes" />

    <div class="page-header">
        <div class="add-item d-flex">
            <div class="page-title">
                <h4>Sizes</h4>
                <h6>Manage your product sizes</h6>
            </div>
        </div>
        <div class="page-btn d-flex gap-2">
            <Link
                :href="route('brand-partner.products.index')"
                class="btn btn-secondary"
            >
                <vue-feather type="arrow-left" class="me-2"></vue-feather>
                Back to Products
            </Link>
            <Link
                :href="route('brand-partner.product-options.collections.index')"
                class="btn btn-outline-primary"
            >
                <vue-feather type="layers" class="me-2"></vue-feather>
                Collections
            </Link>
            <Link
                :href="route('brand-partner.categories.index')"
                class="btn btn-outline-primary"
            >
                <vue-feather type="grid" class="me-2"></vue-feather>
                Categories
            </Link>
            <ModalLink
                navigate
                :href="route('brand-partner.product-options.sizes.create')"
                class="btn btn-added"
                #default="{ loading }"
            >
                <loading-text :loading="loading">
                    <vue-feather type="plus-circle" class="me-2"></vue-feather>
                    Add Size
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
        </div>

        <div class="card-body p-0">
            <dt-table
                v-model:sortings="form.sort"
                v-model:perPage="form.per_page"
                :columns="columns"
                :data="sizes.data"
                :total-records="sizes.total"
                :start-record="sizes.from"
                :end-record="sizes.to"
                :links="sizes.links"
                @change="submitFilters"
            >
                <template #action="{ row, value }">
                    <div class="action-table-data">
                        <div class="edit-delete-action">
                            <ModalLink
                                navigate
                                :href="
                                    route(
                                        'brand-partner.product-options.sizes.edit',
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
                                route-name="brand-partner.product-options.sizes.destroy"
                                :name="row.name"
                                model-name="size"
                                class="btn btn-icon btn-danger-light btn-sm"
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
import { Head, Link, useForm } from '@inertiajs/vue3';
import { removeEmptyValues } from '@/helpers/form';

const props = defineProps({
    sizes: Object,
    filter: Object,
});

const columns = [
    { title: 'Name', dataIndex: 'name', key: 'name', sortable: true },
    {
        title: 'Created At',
        dataIndex: 'created_at',
        key: 'created_at',
        sortable: true,
    },
    { title: '', dataIndex: 'id', key: 'action' },
];

const form = useForm({
    filter: {
        search: props.filter?.search || '',
    },
    sort: [],
    per_page: props.sizes?.per_page || 10,
});

const submitFilters = () => {
    form.transform((data) =>
        removeEmptyValues({
            ...data,
            sort: data.sort.join(','),
            per_page: data.per_page === 10 ? '' : data.per_page,
        }),
    ).get(route('brand-partner.product-options.sizes.index'), {
        preserveState: true,
        replace: true,
    });
};
</script>
