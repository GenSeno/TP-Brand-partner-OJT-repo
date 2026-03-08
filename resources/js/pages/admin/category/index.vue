<template>
    <Head title="Category" />

    <div class="page-header">
        <div class="add-item d-flex">
            <div class="page-title">
                <h4>Category</h4>
                <h6>Manage your categories</h6>
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
                :href="route('admin.category.create')"
                class="btn btn-added btn-primary"
                #default="{ loading }"
            >
                <loading-text :loading="loading">
                    <vue-feather type="plus-circle" class="me-2"></vue-feather>
                    Add New Category
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
                <transition name="fade">
                    <div v-show="selected.length > 0">
                        <div
                            class="btn-list d-md-flex d-block border-start ps-2"
                        >
                            <dt-bulk-delete2
                                :ids="selected.map((s) => s.id)"
                                model="category"
                                name="categories"
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
                    v-model="form.filter.enabled"
                    :options="categoryStatus"
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
                :data="props.categories.data"
                :total-records="props.categories.total"
                :start-record="props.categories.from"
                :end-record="props.categories.to"
                :links="props.categories.links"
                @change="submitFilters"
            >
                <template #name="{ row, value }">
                    <div class="d-flex align-items-center">
                        <a
                            href="javascript:void(0);"
                            class="avatar avatar-md bg-light-900 p-1 me-2"
                            @click="previewImage(row.logo)"
                        >
                            <img
                                class="object-fit-contain"
                                :src="getImagePreview(row.logo)"
                                alt="img"
                            />
                        </a>
                        <ModalLink
                            navigate
                            :href="route('admin.category.edit', row.id)"
                        >
                            {{ value }}
                        </ModalLink>
                    </div>
                </template>
                <template #created_at="{ value }">
                    {{ dayjs(value).format(dateFormat) }}
                </template>
                <template #enabled="{ row, value }">
                    <div class="form-check form-check-inline form-switch me-0">
                        <input
                            class="form-check-input"
                            type="checkbox"
                            role="switch"
                            :checked="value"
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
                                :href="route('admin.category.edit', value)"
                                class="btn btn-icon btn-outline-light btn-sm me-2"
                                title="Edit"
                            >
                                <i data-feather="edit" class="feather-edit"></i>
                            </ModalLink>
                            <dt-delete2
                                :record-name="row.name"
                                model-name="category"
                                :url="route('admin.category.destroy', value)"
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
import dayjs from 'dayjs';
import { Head, router, useForm } from '@inertiajs/vue3';
import { getImagePreview, getImageUrl } from '@/helpers/media';
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
    categories: Object,
    filter: Object,
    default_per_page: {
        type: Number,
        default: 10,
    },
});

const columns = [
    {
        title: 'Category Name',
        dataIndex: 'name',
        key: 'name',
        sortable: true,
    },
    {
        title: 'Slug',
        dataIndex: 'slug',
        key: 'slug',
        sortable: true,
    },
    {
        title: 'Created Date',
        dataIndex: 'created_at',
        key: 'created_at',
        sortable: true,
    },
    {
        title: 'Enabled',
        dataIndex: 'enabled',
        key: 'enabled',
        class: 'text-center',
    },
    {
        title: '',
        dataIndex: 'id',
        key: 'action',
    },
];

const deleteEmitterEvent = ref('categories-deleted');
const dateFormat = ref('DD MMM YYYY');
const categoryStatus = ref({
    true: 'Enabled',
    false: 'Disabled',
});
const form = useForm({
    filter: {
        search: props.filter?.search || '',
        enabled: props.filter?.enabled || '',
    },
    sort: [],
    per_page: props.categories.per_page,
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
    ).get(route('admin.category.index'), {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    });
};

const refreshPage = () => {
    router.get(
        route('admin.category.index'),
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
        .patch(
            route('admin.toggle-field', {
                model: 'category',
                id: id,
                field: 'enabled',
            }),
        )
        .then(() => {
            router.reload({
                preserveState: true,
                preserveScroll: true,
                replace: true,
                only: ['categories'],
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
        alert.showSuccess(data.message || 'Categories deleted successfully.');
    });
});
</script>
