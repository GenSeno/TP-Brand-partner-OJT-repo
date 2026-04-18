<template>
    <Head title="Add New Order" />

    <Modal
        ref="modalRef"
        max-width="lg"
        :close-explicitly="true"
        v-slot="{ close }"
    >
        <div class="page-header">
            <h4>Add New Order</h4>
        </div>

        <form @submit.prevent="submitForm">
            <div class="page-body new-employee-field">
                <h6 class="mb-3">Customer Information</h6>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label required">Customer Name</label>
                        <input
                            v-model="form.data.customer_name"
                            type="text"
                            class="form-control"
                            autofocus
                        />
                        <input-error :message="form.errors.customer_name" />
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Company Name</label>
                        <input
                            v-model="form.data.company_name"
                            type="text"
                            class="form-control"
                        />
                        <input-error :message="form.errors.company_name" />
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label required">Email</label>
                        <input
                            v-model="form.data.customer_email"
                            type="email"
                            class="form-control"
                        />
                        <input-error :message="form.errors.customer_email" />
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Phone</label>
                        <input
                            v-model="form.data.customer_phone"
                            type="text"
                            class="form-control"
                        />
                        <input-error :message="form.errors.customer_phone" />
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Address Line 1</label>
                    <input
                        v-model="form.data.address_line1"
                        type="text"
                        class="form-control"
                        placeholder="Street / Building"
                    />
                    <input-error :message="form.errors.address_line1" />
                </div>

                <div class="mb-3">
                    <label class="form-label">Address Line 2</label>
                    <input
                        v-model="form.data.address_line2"
                        type="text"
                        class="form-control"
                        placeholder="Unit / Floor / Suite (optional)"
                    />
                    <input-error :message="form.errors.address_line2" />
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Barangay</label>
                        <input
                            v-model="form.data.barangay"
                            type="text"
                            class="form-control"
                        />
                        <input-error :message="form.errors.barangay" />
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">City</label>
                        <input
                            v-model="form.data.city"
                            type="text"
                            class="form-control"
                        />
                        <input-error :message="form.errors.city" />
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Province</label>
                        <input
                            v-model="form.data.province"
                            type="text"
                            class="form-control"
                        />
                        <input-error :message="form.errors.province" />
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Postcode</label>
                    <input
                        v-model="form.data.postcode"
                        type="text"
                        class="form-control"
                        style="max-width: 150px"
                    />
                    <input-error :message="form.errors.postcode" />
                </div>

                <div class="mb-3">
                    <label class="form-label">Order Date</label>
                    <input
                        v-model="form.data.placed_at"
                        type="date"
                        class="form-control"
                    />
                    <input-error :message="form.errors.placed_at" />
                </div>

                <hr />

                <h6 class="mb-3">Order Items</h6>

                <div
                    v-for="(line, index) in form.data.lines"
                    :key="index"
                    class="row mb-2 align-items-end"
                >
                    <div class="col-md-5">
                        <label v-if="index === 0" class="form-label required"
                            >Product</label
                        >
                        <select
                            v-model="line.product_id"
                            class="form-select"
                            @change="onProductSelect(index)"
                        >
                            <option value="">Select product</option>
                            <option
                                v-for="p in products"
                                :key="p.id"
                                :value="p.id"
                            >
                                {{ p.name }}
                            </option>
                        </select>
                        <input-error
                            :message="form.errors[`lines.${index}.product_id`]"
                        />
                    </div>
                    <div class="col-md-2">
                        <label v-if="index === 0" class="form-label required"
                            >Qty</label
                        >
                        <input
                            v-model.number="line.quantity"
                            type="number"
                            class="form-control"
                            min="1"
                        />
                        <input-error
                            :message="form.errors[`lines.${index}.quantity`]"
                        />
                    </div>
                    <div class="col-md-3">
                        <label v-if="index === 0" class="form-label required"
                            >Unit Price</label
                        >
                        <input
                            v-model.number="line.unit_price"
                            type="number"
                            class="form-control"
                            min="0"
                            step="0.01"
                        />
                        <input-error
                            :message="form.errors[`lines.${index}.unit_price`]"
                        />
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button
                            type="button"
                            class="btn btn-danger-light btn-sm"
                            :disabled="form.data.lines.length === 1"
                            @click="removeLine(index)"
                        >
                            <vue-feather
                                type="trash-2"
                                class="feather-14"
                            ></vue-feather>
                        </button>
                    </div>
                </div>

                <button
                    type="button"
                    class="btn btn-outline-primary btn-sm mt-1"
                    @click="addLine"
                >
                    <vue-feather
                        type="plus"
                        class="feather-14 me-1"
                    ></vue-feather>
                    Add Item
                </button>

                <hr />

                <div class="mb-3">
                    <label class="form-label">Notes</label>
                    <textarea
                        v-model="form.data.notes"
                        class="form-control"
                        rows="2"
                    ></textarea>
                    <input-error :message="form.errors.notes" />
                </div>
            </div>

            <div class="page-footer-buttons">
                <div>
                    <button
                        type="button"
                        class="btn btn-secondary me-2"
                        @click="close()"
                    >
                        Cancel
                    </button>
                    <submit-btn :loading="form.processing"
                        >Create Order</submit-btn
                    >
                </div>
            </div>
        </form>
    </Modal>
</template>

<script setup>
import { useAxiosForm } from '@/composables/axiosForm';
import * as alert from '@/helpers/alert';
import { Head, router } from '@inertiajs/vue3';
import { useTemplateRef } from 'vue';
import dayjs from 'dayjs';

const props = defineProps({
    products: Array,
});

const modalRef = useTemplateRef('modalRef');

const newLine = () => ({ product_id: '', quantity: 1, unit_price: 0 });

const form = useAxiosForm({
    customer_name: '',
    company_name: '',
    customer_email: '',
    customer_phone: '',
    address_line1: '',
    address_line2: '',
    barangay: '',
    city: '',
    province: '',
    postcode: '',
    placed_at: dayjs().format('YYYY-MM-DD'),
    notes: '',
    lines: [newLine()],
});

const onProductSelect = (index) => {
    const productId = form.data.lines[index].product_id;
    const product = props.products.find((p) => p.id === productId);
    if (product) {
        form.data.lines[index].unit_price = product.price / 100;
    }
};

const addLine = () => {
    form.data.lines.push(newLine());
};

const removeLine = (index) => {
    form.data.lines.splice(index, 1);
};

const submitForm = () => {
    form.post(route('brand-partner.orders.store'), {
        onSuccess: ({ data }) => {
            alert.showSuccess('Order created successfully.');
            modalRef.value.close();
            if (data?.order?.id) {
                router.visit(route('brand-partner.orders.show', data.order.id));
            } else {
                router.visit(route('brand-partner.orders.index'));
            }
        },
    });
};
</script>
