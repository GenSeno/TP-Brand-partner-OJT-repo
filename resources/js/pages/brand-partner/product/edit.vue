<template>
    <Head title="Edit Product" />

    <Modal
        ref="modalRef"
        max-width="xl"
        :close-explicitly="true"
        v-slot="{ close }"
    >
        <div class="page-header">
            <h4>Edit Product</h4>
        </div>

        <form @submit.prevent="submitForm">
            <div class="page-body new-employee-field">
                <div class="row">
                    <div class="col-md-8">
                        <div class="mb-3">
                            <label class="form-label required">Name</label>
                            <input
                                v-model="form.data.name"
                                type="text"
                                class="form-control"
                            />
                            <input-error :message="form.errors.name" />
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Slug</label>
                            <input
                                v-model="form.data.slug"
                                type="text"
                                class="form-control"
                            />
                            <input-error :message="form.errors.slug" />
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Short Description</label>
                            <input
                                v-model="form.data.short_description"
                                type="text"
                                class="form-control"
                                maxlength="500"
                            />
                            <input-error
                                :message="form.errors.short_description"
                            />
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea
                                v-model="form.data.description"
                                class="form-control"
                                rows="4"
                            ></textarea>
                            <input-error :message="form.errors.description" />
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label required">Category</label>
                            <select
                                v-model="form.data.category_id"
                                class="form-select"
                                @change="onCategoryChange"
                            >
                                <option value="">Select Category</option>
                                <option
                                    v-for="cat in categories"
                                    :key="cat.id"
                                    :value="cat.id"
                                >
                                    {{ cat.name }} ({{ cat.type }})
                                </option>
                            </select>
                            <input-error :message="form.errors.category_id" />
                        </div>

                        <div v-if="showEventSelect" class="mb-3">
                            <label class="form-label">Event</label>
                            <select
                                v-model="form.data.event_id"
                                class="form-select"
                            >
                                <option value="">Select Event</option>
                                <option
                                    v-for="event in events"
                                    :key="event.id"
                                    :value="event.id"
                                >
                                    {{ event.name }}
                                </option>
                            </select>
                            <input-error :message="form.errors.event_id" />
                        </div>

                        <div class="mb-3">
                            <label class="form-label required">Status</label>
                            <select
                                v-model="form.data.status"
                                class="form-select"
                            >
                                <option
                                    v-for="(label, value) in statusOptions"
                                    :key="value"
                                    :value="value"
                                >
                                    {{ label }}
                                </option>
                            </select>
                            <input-error :message="form.errors.status" />
                        </div>

                        <div class="row">
                            <div class="col-6 mb-3">
                                <label class="form-label required">Price</label>
                                <input
                                    v-model="form.data.price"
                                    type="number"
                                    step="0.01"
                                    min="0"
                                    class="form-control"
                                />
                                <input-error :message="form.errors.price" />
                            </div>
                            <div class="col-6 mb-3">
                                <label class="form-label">Compare Price</label>
                                <input
                                    v-model="form.data.compare_price"
                                    type="number"
                                    step="0.01"
                                    min="0"
                                    class="form-control"
                                />
                                <input-error
                                    :message="form.errors.compare_price"
                                />
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">SKU</label>
                            <input
                                v-model="form.data.sku"
                                type="text"
                                class="form-control"
                            />
                            <input-error :message="form.errors.sku" />
                        </div>

                        <div class="row">
                            <div class="col-6 mb-3">
                                <label class="form-label">Stock</label>
                                <input
                                    v-model="form.data.stock"
                                    type="number"
                                    min="0"
                                    class="form-control"
                                />
                                <input-error :message="form.errors.stock" />
                            </div>
                            <div class="col-6 mb-3 d-flex align-items-end">
                                <div class="form-check">
                                    <input
                                        v-model="form.data.track_stock"
                                        type="checkbox"
                                        class="form-check-input"
                                        id="track_stock"
                                    />
                                    <label
                                        class="form-check-label"
                                        for="track_stock"
                                        >Track Stock</label
                                    >
                                </div>
                            </div>
                        </div>

                        <div class="mb-0">
                            <div class="form-check">
                                <input
                                    v-model="form.data.featured"
                                    type="checkbox"
                                    class="form-check-input"
                                    id="featured"
                                />
                                <label class="form-check-label" for="featured"
                                    >Featured Product</label
                                >
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="page-footer-buttons">
                <div class="me-auto"></div>
                <div>
                    <button
                        type="button"
                        class="btn btn-secondary me-2"
                        @click="close()"
                    >
                        Cancel
                    </button>
                    <submit-btn :loading="form.processing">
                        Update Product
                    </submit-btn>
                </div>
            </div>
        </form>
    </Modal>
</template>

<script setup>
import { useAxiosForm } from '@/composables/axiosForm';
import * as alert from '@/helpers/alert';
import { Head, router } from '@inertiajs/vue3';
import { computed, useTemplateRef } from 'vue';

const props = defineProps({
    product: Object,
    categories: Array,
    events: Array,
    statusOptions: Object,
});

const modalRef = useTemplateRef('modalRef');

const form = useAxiosForm({
    name: props.product.name,
    slug: props.product.slug,
    category_id: props.product.category_id,
    event_id: props.product.event_id || '',
    description: props.product.description || '',
    short_description: props.product.short_description || '',
    price: props.product.price / 100,
    compare_price: props.product.compare_price
        ? props.product.compare_price / 100
        : '',
    sku: props.product.sku || '',
    stock: props.product.stock,
    track_stock: props.product.track_stock,
    status: props.product.status,
    featured: props.product.featured,
});

const selectedCategory = computed(() => {
    return props.categories.find((c) => c.id === form.data.category_id);
});

const showEventSelect = computed(() => {
    return selectedCategory.value?.type === 'event';
});

const onCategoryChange = () => {
    if (!showEventSelect.value) {
        form.data.event_id = '';
    }
};

const submitForm = () => {
    form.put(route('brand-partner.products.update', props.product.id), {
        onSuccess: ({ data }) => {
            alert.showSuccess(data.message || 'Product updated successfully.');
            modalRef.value.close();
            router.reload();
        },
    });
};
</script>
