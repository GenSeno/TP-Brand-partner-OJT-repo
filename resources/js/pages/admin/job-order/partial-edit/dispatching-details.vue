<template>
    <div class="card mb-0">
        <div class="card-body">
            <div class="mb-3 border-bottom pb-2">
                <h5>Dispatching Details</h5>
            </div>

            <!-- Billing Status -->
            <div class="border-bottom pb-4 mb-4">
                <h6 class="mb-3">Billing Status</h6>

                <div class="d-flex align-items-center gap-3 mb-3">
                    <div>
                        <div class="text-muted small mb-1">
                            Unbilled Balance
                        </div>
                        <div class="fw-semibold fs-5">
                            {{
                                jobOrder.order.billing_summary?.amount_unbilled
                                    ?.formatted || '—'
                            }}
                        </div>
                    </div>
                    <div class="ms-auto">
                        <ModalLink
                            v-if="
                                (jobOrder.order.billing_summary?.amount_unbilled
                                    ?.value ?? 0) > 0
                            "
                            navigate
                            :href="
                                route(
                                    'admin.order.billing.create',
                                    jobOrder.order.id,
                                )
                            "
                            class="btn btn-sm btn-primary"
                        >
                            <i class="feather feather-file-plus me-1"></i>
                            Create Billing
                        </ModalLink>
                        <span v-else class="badge badge-soft-success px-3 py-2">
                            <i class="feather feather-check-circle me-1"></i>
                            Fully Billed
                        </span>
                    </div>
                </div>

                <div>
                    <label class="form-label required">Add Delivery Fee?</label>
                    <div class="d-flex gap-3 mb-1">
                        <div class="form-check">
                            <input
                                class="form-check-input"
                                type="radio"
                                id="delivery-fee-yes"
                                v-model="form.data.dispatching.add_delivery_fee"
                                value="yes"
                            />
                            <label
                                class="form-check-label"
                                for="delivery-fee-yes"
                                >Yes</label
                            >
                        </div>
                        <div class="form-check">
                            <input
                                class="form-check-input"
                                type="radio"
                                id="delivery-fee-no"
                                v-model="form.data.dispatching.add_delivery_fee"
                                value="no"
                            />
                            <label
                                class="form-check-label"
                                for="delivery-fee-no"
                                >No</label
                            >
                        </div>
                    </div>
                    <input-error
                        :message="form.errors['dispatching.add_delivery_fee']"
                    />
                    <div
                        v-if="form.data.dispatching.add_delivery_fee === 'yes'"
                        class="mt-2"
                    >
                        <ModalLink
                            navigate
                            :href="
                                route(
                                    'admin.order.billing.create',
                                    jobOrder.order.id,
                                )
                            "
                            class="btn btn-sm btn-outline-primary"
                        >
                            <i class="feather feather-plus me-1"></i>
                            Create Billing for Delivery Fee
                        </ModalLink>
                    </div>
                </div>
            </div>

            <!-- Dispatching Type -->
            <div>
                <h6 class="mb-3">Dispatching Type</h6>

                <div class="mb-3">
                    <label class="form-label required">Type</label>
                    <select
                        v-model="form.data.dispatching.dispatching_type"
                        class="form-select"
                        :class="{
                            'is-invalid':
                                form.errors['dispatching.dispatching_type'],
                        }"
                    >
                        <option value="">Select type</option>
                        <option value="delivery">Delivery</option>
                        <option value="pickup">Pick Up</option>
                    </select>
                    <input-error
                        :message="form.errors['dispatching.dispatching_type']"
                    />
                </div>

                <template v-if="form.data.dispatching.dispatching_type">
                    <div class="mb-3">
                        <label class="form-label required"
                            >Delivery Method</label
                        >
                        <input
                            type="text"
                            v-model="form.data.dispatching.delivery_method"
                            class="form-control"
                            :class="{
                                'is-invalid':
                                    form.errors['dispatching.delivery_method'],
                            }"
                            placeholder="Enter delivery method"
                        />
                        <input-error
                            :message="
                                form.errors['dispatching.delivery_method']
                            "
                        />
                    </div>

                    <div class="mb-0">
                        <label class="form-label required"
                            >Reference Number</label
                        >
                        <input
                            type="text"
                            v-model="form.data.dispatching.reference_number"
                            class="form-control"
                            :class="{
                                'is-invalid':
                                    form.errors['dispatching.reference_number'],
                            }"
                            placeholder="Enter reference number"
                        />
                        <input-error
                            :message="
                                form.errors['dispatching.reference_number']
                            "
                        />
                    </div>
                </template>
            </div>
        </div>
    </div>
</template>

<script setup>
const form = defineModel('form');

defineProps({
    jobOrder: {
        type: Object,
        required: true,
    },
});
</script>
