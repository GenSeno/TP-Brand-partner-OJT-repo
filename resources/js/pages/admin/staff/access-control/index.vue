<template>
    <Head title="Access Control" />

    <div class="page-header">
        <div class="add-item d-flex">
            <div class="page-title">
                <h4>Access Control</h4>
                <h6>Manage your access control settings</h6>
            </div>
        </div>
        <ul class="table-top-head">
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
                :href="route('admin.staff.index')"
                class="btn btn-added btn-dark"
                view-transition
            >
                <vue-feather type="arrow-left" class="me-2"></vue-feather>
                Back to Staff
            </Link>
            <ModalLink
                navigate
                :href="route('admin.access-control.role.create')"
                class="btn btn-added btn-primary"
                #default="{ loading }"
            >
                <loading-text :loading="loading">
                    <vue-feather type="plus-circle" class="me-2"></vue-feather>
                    Add New Role
                </loading-text>
            </ModalLink>
        </div>
    </div>
    <div class="card table-list-card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th></th>
                            <th
                                v-for="role in props.roles"
                                :key="role.id"
                                class="text-center"
                                scope="col"
                            >
                                {{ role.name }}
                            </th>
                        </tr>
                        <tr>
                            <th></th>
                            <th
                                v-for="role in props.roles"
                                :key="role.id"
                                class="text-center pt-0"
                                scope="col"
                            >
                                <div class="action-table-data">
                                    <div class="edit-delete-action gap-2">
                                        <ModalLink
                                            navigate
                                            :href="
                                                route(
                                                    'admin.access-control.role.edit',
                                                    role.id,
                                                )
                                            "
                                            class="btn btn-icon btn-outline-light btn-sm"
                                            title="Edit"
                                        >
                                            <i
                                                data-feather="edit"
                                                class="feather-edit"
                                            ></i>
                                        </ModalLink>
                                        <dt-delete2
                                            v-if="!role.is_used"
                                            :record-name="role.name"
                                            model-name="role"
                                            :url="
                                                route(
                                                    'admin.access-control.role.destroy',
                                                    role.id,
                                                )
                                            "
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
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <template
                            v-for="(group, groupName) in props.permissions"
                            :key="groupName"
                        >
                            <tr
                                v-for="permission in group"
                                :key="permission.name"
                            >
                                <td
                                    class="bg-light py-3"
                                    :class="{
                                        'ps-5': permission.name !== groupName,
                                    }"
                                >
                                    <h6>{{ permission.title }}</h6>
                                    <p class="mb-0">
                                        {{ permission.description }}
                                    </p>
                                </td>
                                <td
                                    v-for="role in props.roles"
                                    :key="role.id + '-' + permission.name"
                                    class="text-center py-3"
                                    :class="{
                                        'bg-light':
                                            permission.name !== groupName &&
                                            !role.permissions.includes(
                                                groupName,
                                            ),
                                    }"
                                >
                                    <div class="form-check-md">
                                        <input
                                            class="form-check-input"
                                            type="checkbox"
                                            :checked="
                                                role.permissions.includes(
                                                    permission.name,
                                                )
                                            "
                                            @change="
                                                togglePermission(
                                                    role.id,
                                                    groupName,
                                                    permission.name,
                                                )
                                            "
                                            :disabled="
                                                permission.name !== groupName &&
                                                !role.permissions.includes(
                                                    groupName,
                                                )
                                            "
                                        />
                                    </div>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>
<script setup>
import { onMounted, ref, watch } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { toggleHeader } from '@/helpers/layout';
import { emitter } from '@/composables/eventBus';
import * as alert from '@/helpers/alert';
import DashboardLayout from '@/layouts/dashboard-layout.vue';
import { useAxiosForm } from '@/composables/axiosForm';
import { debounce } from 'lodash';

defineOptions({
    layout: DashboardLayout,
});

const props = defineProps({
    roles: Object,
    permissions: Object,
});

const deleteEmitterEvent = ref('role-deleted');
const form = useAxiosForm({
    group: [],
});

const togglePermission = (roleId, groupName, permissionName) => {
    const index = form.data.group.findIndex(
        (item) =>
            item.role_id === roleId && item.permission_name === permissionName,
    );
    if (index !== -1) {
        form.data.group.splice(index, 1);
    } else {
        form.data.group.push({
            role_id: roleId,
            group: groupName,
            permission: permissionName,
        });
    }
};

const refreshPage = () => {
    router.get(
        route('admin.staff.index'),
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
        alert.showSuccess(data.message || 'Staff deleted successfully.');
    });
});

watch(
    () => form.data.group,
    debounce((group) => {
        if (group.length === 0) {
            return;
        }
        form.post(route('admin.access-control.toggle-permission'), {
            onSuccess: () => {
                form.data.group = [];
                router.reload({
                    preserveState: true,
                    preserveScroll: true,
                    replace: true,
                    only: ['roles'],
                    onSuccess: () => {
                        alert.showSuccess('Permissions updated successfully.');
                    },
                });
            },
        });
    }, 1000),
    { deep: true },
);
</script>
