<template>
    <Head title="Product List" />

    <div class="page-header">
        <div class="add-item d-flex">
            <div class="page-title">
                <h4>Product List</h4>
                <h6>Manage your products</h6>
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
        <div class="page-btn d-flex flex-wrap gap-2">
            <Link
                :href="route('admin.product-option.index')"
                class="btn btn-added btn-dark"
                view-transition
            >
                <vue-feather type="settings" class="me-2"></vue-feather>
                Product Options
            </Link>
            <ModalLink
                navigate
                :href="route('admin.product.create')"
                class="btn btn-added btn-primary"
                #default="{ loading }"
            >
                <loading-text :loading="loading">
                    <vue-feather type="plus-circle" class="me-2"></vue-feather>
                    Add New Product
                </loading-text>
            </ModalLink>
        </div>
    </div>
    <!-- /product list -->
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
                        <div
                            class="btn-list d-md-flex d-block border-start ps-2"
                        >
                            <dt-bulk-delete2
                                :ids="selected.map((s) => s.id)"
                                model="product"
                                name="products"
                                :emitter-event="deleteEmitterEvent"
                            />
                        </div>
                    </div>
                </transition>
            </div>
            <div
                class="d-flex table-dropdown my-xl-auto right-content align-items-center flex-wrap row-gap-3"
            >
                <select-filter
                    v-model="form.filter.status"
                    :options="props.statuses"
                    name="Status"
                    @change="submitFilters"
                ></select-filter>
                <select-filter
                    v-model="form.filter.category"
                    :options="props.categories"
                    name="Category"
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
                :data="props.products.data"
                :total-records="props.products.total"
                :start-record="props.products.from"
                :end-record="props.products.to"
                :links="props.products.links"
                @change="submitFilters"
            >
                <template #category="{ value }">
                    {{ value?.name }}
                </template>
                <template #name="{ row, value }">
                    <div class="d-flex align-items-center">
                        <a
                            href="javascript:void(0);"
                            class="avatar avatar-md bg-light-900 p-1 me-2"
                            @click="previewImage(row.image)"
                        >
                            <img
                                class="object-fit-contain"
                                :src="getImagePreview(row.image)"
                                alt="img"
                            />
                        </a>
                        <ModalLink
                            navigate
                            :href="route('admin.product.edit', row.id)"
                        >
                            {{ value }}
                        </ModalLink>
                    </div>
                </template>
                <template #status="{ row, value }">
                    <span
                        v-if="value === ProductStatus.DRAFT"
                        class="badge table-badge bg-warning fw-medium fs-10"
                        >{{ value }}</span
                    >
                    <div
                        v-else
                        class="form-check form-check-inline form-switch me-0"
                    >
                        <input
                            class="form-check-input"
                            type="checkbox"
                            role="switch"
                            :checked="value === ProductStatus.PUBLISHED"
                            :disabled="!statusToggleEnabled"
                            @click.prevent="toggleStatus(row.id)"
                        />
                    </div>
                </template>
                <template #action="{ row, value }">
                    <div class="action-table-data">
                        <div class="edit-delete-action">
                            <ModalLink
                                navigate
                                :href="route('admin.product.edit', row.id)"
                                class="btn btn-icon btn-outline-light btn-sm me-2"
                                title="Edit"
                            >
                                <i data-feather="edit" class="feather-edit"></i>
                            </ModalLink>
                            <dt-delete2
                                :record-name="row.name"
                                model-name="product"
                                :url="route('admin.product.destroy', value)"
                                class="btn btn-icon btn-danger-light btn-sm me-2"
                                title="Delete"
                                :emitter-event="deleteEmitterEvent"
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

    <vue-easy-lightbox
        :visible="lightbox.visible"
        :index="lightbox.index"
        :imgs="lightbox.gallery"
        @hide="lightbox.visible = false"
    >
    </vue-easy-lightbox>
</template>
<script setup>
import { onMounted, ref, reactive } from 'vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { getImagePreview, getImageUrl } from '@/helpers/media';
import { ProductStatus } from '@/enums/product-status';
import { removeEmptyValues } from '@/helpers/form';
import { toggleHeader } from '@/helpers/layout';
import { emitter } from '@/composables/eventBus';
import * as alert from '@/helpers/alert';
import axios from 'axios';
import VueEasyLightbox from 'vue-easy-lightbox';
import DashboardLayout from '@/layouts/dashboard-layout.vue';

defineOptions({
    layout: DashboardLayout,
});

const props = defineProps({
    products: Object,
    statuses: Object,
    categories: Object,
    filter: Object,
    default_per_page: {
        type: Number,
        default: 10,
    },
});

const columns = [
    {
        title: 'Category',
        dataIndex: 'category',
        key: 'category',
    },
    {
        title: 'Product Name',
        dataIndex: 'name',
        key: 'name',
        sortable: true,
    },
    {
        title: 'Status',
        dataIndex: 'status',
        key: 'status',
    },
    {
        title: '',
        dataIndex: 'id',
        key: 'action',
    },
];

const deleteEmitterEvent = ref('products-deleted');
const form = useForm({
    filter: {
        search: props.filter?.search || '',
        status: props.filter?.status || '',
        category: props.filter?.category || '',
    },
    sort: [],
    per_page: props.products.per_page,
});
const selected = ref([]);
const statusToggleEnabled = ref(true);
const lightbox = reactive({
    visible: false,
    index: 0,
    gallery: [],
});
const previewImage = (image) => {
    if (image) {
        lightbox.gallery = getImageUrl(image);
        lightbox.visible = true;
    }
};

const submitFilters = () => {
    form.transform((data) =>
        removeEmptyValues({
            ...data,
            sort: data.sort.join(','),
            per_page:
                data.per_page === props.default_per_page ? '' : data.per_page,
        }),
    ).get(route('admin.product.index'), {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    });
};

const refreshPage = () => {
    router.get(
        route('admin.product.index'),
        {},
        {
            preserveState: false,
            preserveScroll: true,
            replace: true,
        },
    );
};

const toggleStatus = (id) => {
    statusToggleEnabled.value = false;
    axios
        .patch(route('admin.product.toggle-status', id))
        .then(() => {
            router.reload({
                preserveState: true,
                preserveScroll: true,
                replace: true,
                only: ['products'],
                onSuccess: () => {
                    statusToggleEnabled.value = true;
                },
            });
        })
        .catch((error) => {
            statusToggleEnabled.value = true;
            alert.showError(
                error.response?.data?.message || 'Failed to update status',
            );
        });
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
        alert.showSuccess(data.message || 'Products deleted successfully.');
    });
});
</script>
