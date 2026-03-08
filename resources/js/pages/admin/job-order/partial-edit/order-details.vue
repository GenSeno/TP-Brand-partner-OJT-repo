<template>
    <div class="card mb-0 h-100">
        <div class="card-body">
            <div class="mb-3 border-bottom pb-2">
                <h5>Customer</h5>
            </div>

            <div class="mb-3">
                <div class="d-flex align-items-center">
                    <span class="avatar avatar-lg me-2">
                        <img
                            class="object-fit-contain rounded-circle border"
                            :src="
                                jobOrder.order.orderable.avatar_url ||
                                fallbackUserImage
                            "
                            alt="Avatar"
                        />
                    </span>
                    <div class="d-flex flex-column">
                        <ModalLink
                            v-if="can('job-orders:manage-new-tasks')"
                            navigate
                            :href="
                                route(
                                    'admin.customer.edit',
                                    jobOrder.order.orderable.id,
                                )
                            "
                        >
                            {{ jobOrder.order.orderable.full_name }}
                        </ModalLink>
                        <p v-else class="text-dark mb-0">
                            {{ jobOrder.order.orderable.full_name }}
                        </p>
                        <small>{{
                            jobOrder.order.orderable.company_name
                        }}</small>
                    </div>
                </div>
            </div>

            <div class="mb-0">
                <h6 class="mb-2">Sales Order Remarks</h6>
                <div class="border-start bg-light p-2">
                    <p>
                        {{ jobOrder.order.notes || 'No remarks available.' }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { fallbackUserImage } from '@/helpers/media';
import { can } from '@/helpers/guard';

defineProps({
    jobOrder: {
        type: Object,
        required: true,
    },
});
</script>
