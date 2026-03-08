<template>
    <Head title="Employee" />

    <div class="page-header">
        <div class="add-item d-flex">
            <div class="page-title">
                <h4>Employees</h4>
                <h6>Manage your employees</h6>
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
            <ModalLink
                navigate
                :href="route('admin.employee.create')"
                class="btn btn-added btn-primary"
                #default="{ loading }"
            >
                <loading-text :loading="loading">
                    <vue-feather type="plus-circle" class="me-2"></vue-feather>
                    Add New Employee
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
                :data="employeeList.data"
                :total-records="employeeList.total"
                :start-record="employeeList.from"
                :end-record="employeeList.to"
                :links="employeeList.links"
                :selectable="false"
                @change="submitFilters"
            >
                <template #first_name="{ row }">
                    <div class="d-flex align-items-center">
                        <a
                            href="javascript:void(0);"
                            class="avatar avatar-md bg-light-900 p-1 me-2"
                            @click="previewImage(row.avatar)"
                        >
                            <img
                                class="object-fit-contain"
                                :src="row.avatar_url"
                                alt="img"
                            />
                        </a>
                        <ModalLink
                            navigate
                            :href="route('admin.employee.edit', row.id)"
                        >
                            {{ row.full_name }}
                        </ModalLink>
                    </div>
                </template>
                <template #job_title="{ value }">
                    <job-title-badge :job-title="value" />
                </template>
                <template #status="{ value }">
                    <StatusBadge :status="value" />
                </template>
                <template #created_at="{ value }">
                    {{ dayjs(value).format(dateFormat) }}
                </template>
                <template #action="{ row, value }">
                    <div class="action-table-data">
                        <div class="edit-delete-action">
                            <ModalLink
                                navigate
                                :href="route('admin.employee.edit', value)"
                                class="btn btn-icon btn-outline-light btn-sm me-2"
                                title="Edit"
                            >
                                <i data-feather="edit" class="feather-edit"></i>
                            </ModalLink>
                            <dt-delete2
                                :record-name="row.first_name"
                                model-name="employee"
                                :url="route('admin.employee.destroy', value)"
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
import { ref, reactive, onMounted } from 'vue';
import { Head, router, useForm } from '@inertiajs/vue3';
import { toggleHeader } from '@/helpers/layout';
import DashboardLayout from '@/layouts/dashboard-layout.vue';
import { removeEmptyValues } from '@/helpers/form';
import { emitter } from '@/composables/eventBus';
import * as alert from '@/helpers/alert';
import VueEasyLightbox from 'vue-easy-lightbox';
import StatusBadge from '@/components/user/status-badge.vue';
import JobTitleBadge from '@/components/employee/job-title-badge.vue';
import dayjs from 'dayjs';

defineOptions({
    layout: DashboardLayout,
});

const props = defineProps({
    employeeList: Object,
    statuses: Object,
    filter: Object,
});

const columns = [
    {
        title: 'Employee Name',
        dataIndex: 'first_name',
        key: 'first_name',
        sortable: true,
    },
    {
        title: 'Job Title',
        dataIndex: 'job_title',
        key: 'job_title',
        sortable: true,
    },
    { title: 'Status', dataIndex: 'status', key: 'status', sortable: true },
    {
        title: 'Created Date',
        dataIndex: 'created_at',
        key: 'created_at',
        sortable: true,
    },
    {
        title: '',
        dataIndex: 'id',
        key: 'action',
    },
];

const deleteEmitterEvent = ref('employee-deleted');
const dateFormat = ref('DD MMM YYYY');
const form = useForm({
    filter: {
        search: props.filter?.search || '',
        status: props.filter?.status || '',
    },
    sort: [],
    per_page: props.employeeList.per_page,
});
const selected = ref([]);
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
                data.per_page === props.filter.default_per_page
                    ? ''
                    : data.per_page,
        }),
    ).get(route('admin.employee.index'), {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    });
};

const refreshPage = () => {
    router.get(
        route('admin.employee.index'),
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
        alert.showSuccess(data.message || 'Employee deleted successfully.');
    });
});
</script>
