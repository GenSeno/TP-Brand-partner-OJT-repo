<template>
    <Head title="Product Option" />

    <div class="page-header">
        <div class="add-item d-flex">
            <div class="page-title">
                <h4>Product Option</h4>
                <h6>Manage your product options</h6>
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
            <Link
                :href="route('admin.product.index')"
                class="btn btn-added btn-dark"
                view-transition
            >
                <vue-feather type="arrow-left" class="me-2"></vue-feather>
                Back to Products
            </Link>
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
                v-model:selected="selected"
                :columns="columns"
                :data="productOptions.data"
                :total-records="productOptions.total"
                :start-record="productOptions.from"
                :end-record="productOptions.to"
                :links="productOptions.links"
                :selectable="false"
                @change="submitFilters"
            >
                <template #name="{ row, value }">
                    <div class="d-flex align-items-center">
                        <ModalLink
                            navigate
                            :href="route('admin.product-option.edit', row.id)"
                        >
                            {{ value }}
                        </ModalLink>
                    </div>
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
                                    route('admin.product-option.edit', value)
                                "
                                class="btn btn-icon btn-outline-light btn-sm me-2"
                                title="Edit"
                            >
                                <i data-feather="edit" class="feather-edit"></i>
                            </ModalLink>
                            <dt-delete2
                                v-if="!row.permanent"
                                :url="
                                    route('admin.product-option.destroy', value)
                                "
                                :record-name="row.name"
                                model-name="product option"
                                :emitter-event="deleteEmitterEvent"
                                class="btn btn-icon btn-danger-light btn-sm me-2"
                                title="Delete"
                            >
                                <i
                                    data-feather="trash-2"
                                    class="feather-trash-2"
                                ></i>
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
import { toggleHeader } from '@/helpers/layout';
import { removeEmptyValues } from '@/helpers/form';
import { emitter } from '@/composables/eventBus';
import * as alert from '@/helpers/alert';
import DashboardLayout from '@/layouts/dashboard-layout.vue';

defineOptions({
    layout: DashboardLayout,
});

const props = defineProps({
    productOptions: Object,
    filter: Object,
    default_per_page: {
        type: Number,
        default: 10,
    },
});

const columns = [
    {
        title: 'Product Option',
        dataIndex: 'name',
        key: 'name',
        sortable: true,
    },
    {
        title: 'Created Date',
        dataIndex: 'created_at',
        key: 'created_at',
        sortable: true,
    },
    {
        title: 'Position',
        dataIndex: 'position',
        key: 'position',
    },
    {
        title: '',
        dataIndex: 'id',
        key: 'action',
    },
];

const deleteEmitterEvent = ref('product-options-deleted');
const dateFormat = ref('DD MMM YYYY');
const form = useForm({
    filter: {
        search: props.filter.search || '',
        autoapply: props.filter.autoapply || '',
    },
    sort: [],
    per_page: props.productOptions.per_page,
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
    ).get(route('admin.product-option.index'), {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    });
};

const refreshPage = () => {
    router.get(
        route('admin.product-option.index'),
        {},
        {
            preserveState: false,
            preserveScroll: true,
            replace: true,
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
        selected.value = selected.value.filter(
            (item) => !data.deleted.includes(item.id),
        );
        alert.showSuccess(
            data.message || 'Product options deleted successfully.',
        );
    });
});
</script>
