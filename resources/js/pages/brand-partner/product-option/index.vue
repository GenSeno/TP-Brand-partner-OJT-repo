<template>
    <Head title="Product Options" />

    <div class="page-header">
        <div class="add-item d-flex">
            <div class="page-title">
                <h4>Product Options</h4>
                <h6>Manage your product options</h6>
            </div>
        </div>
        <div class="page-btn d-flex gap-2">
            <Link
                :href="route('brand-partner.products.index')"
                class="btn btn-added btn-dark"
            >
                <vue-feather type="arrow-left" class="me-2"></vue-feather>
                Back to Products
            </Link>
            <ModalLink
                navigate
                :href="route('brand-partner.product-options.create')"
                class="btn btn-added"
                #default="{ loading }"
            >
                <loading-text :loading="loading">
                    <vue-feather type="plus-circle" class="me-2"></vue-feather>
                    Add Product Option
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
                :data="productOptions.data"
                :total-records="productOptions.total"
                :start-record="productOptions.from"
                :end-record="productOptions.to"
                :links="productOptions.links"
                @change="submitFilters"
            >
                <template #name="{ row, value }">
                    <ModalLink
                        navigate
                        :href="
                            route('brand-partner.product-options.edit', row.id)
                        "
                    >
                        {{ value }}
                    </ModalLink>
                </template>

                <template #created_at="{ value }">
                    {{ dayjs(value).format('DD MMM YYYY') }}
                </template>

                <template #action="{ row, value }">
                    <div class="action-table-data">
                        <div class="edit-delete-action">
                            <ModalLink
                                navigate
                                :href="
                                    route(
                                        'brand-partner.product-options.edit',
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
                            <dt-delete2
                                :url="
                                    route(
                                        'brand-partner.product-options.destroy',
                                        value,
                                    )
                                "
                                :record-name="row.name"
                                model-name="product option"
                                :emitter-event="deleteEmitterEvent"
                                class="btn btn-icon btn-danger-light btn-sm"
                                title="Delete"
                            >
                                <vue-feather
                                    type="trash-2"
                                    class="feather-14"
                                ></vue-feather>
                            </dt-delete2>
                        </div>
                    </div>
                </template>
            </dt-table>
        </div>
    </div>
</template>

<script setup>
import { onMounted, ref } from 'vue';
import dayjs from 'dayjs';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { removeEmptyValues } from '@/helpers/form';
import { emitter } from '@/composables/eventBus';
import * as alert from '@/helpers/alert';

const props = defineProps({
    productOptions: Object,
    filter: Object,
    default_per_page: {
        type: Number,
        default: 10,
    },
});

const columns = [
    { title: 'Product Option', dataIndex: 'name', key: 'name', sortable: true },
    {
        title: 'Created Date',
        dataIndex: 'created_at',
        key: 'created_at',
        sortable: true,
    },
    { title: 'Position', dataIndex: 'position', key: 'position' },
    { title: '', dataIndex: 'id', key: 'action' },
];

const deleteEmitterEvent = ref('product-options-deleted');

const form = useForm({
    filter: {
        search: props.filter?.search || '',
    },
    sort: [],
    per_page: props.productOptions.per_page,
});

const submitFilters = () => {
    form.transform((data) =>
        removeEmptyValues({
            ...data,
            sort: data.sort.join(','),
            per_page:
                data.per_page === props.default_per_page ? '' : data.per_page,
        }),
    ).get(route('brand-partner.product-options.index'), {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    });
};

onMounted(() => {
    emitter.on(deleteEmitterEvent.value, (data) => {
        router.reload({
            preserveState: true,
            preserveScroll: true,
            replace: true,
        });
        alert.showSuccess(
            data.message || 'Product option deleted successfully.',
        );
    });
});
</script>
