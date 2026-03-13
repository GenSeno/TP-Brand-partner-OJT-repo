<template>
    <Head title="Add Product" />

    <Modal
        ref="modalRef"
        max-width="xl"
        :close-explicitly="true"
        v-slot="{ close }"
    >
        <div class="page-header">
            <h4>Add Product</h4>
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
                                placeholder="Auto-generated from name"
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

                        <div class="mb-3">
                            <label class="form-label">Product Images</label>
                            <input
                                ref="imageInputRef"
                                type="file"
                                class="form-control"
                                multiple
                                accept="image/*"
                                @change="onImagesSelected"
                            />
                            <small class="text-muted">First image will be set as primary.</small>
                            <div v-if="selectedImages.length" class="d-flex flex-wrap gap-2 mt-2">
                                <div
                                    v-for="(img, idx) in selectedImages"
                                    :key="idx"
                                    class="position-relative"
                                >
                                    <img
                                        :src="img.preview"
                                        style="width: 80px; height: 80px; object-fit: cover; border-radius: 6px; border: 1px solid #dee2e6;"
                                    />
                                    <button
                                        type="button"
                                        class="btn btn-danger btn-sm position-absolute top-0 end-0"
                                        style="padding: 1px 5px; font-size: 11px; line-height: 1.4;"
                                        @click="removeSelectedImage(idx)"
                                    >×</button>
                                    <span
                                        v-if="idx === 0"
                                        class="badge bg-primary position-absolute bottom-0 start-0"
                                        style="font-size: 9px;"
                                    >Primary</span>
                                </div>
                            </div>
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

                        <div class="mb-3">
                            <label class="form-label">Colors</label>
                            <input
                                v-model="form.data.colors"
                                type="text"
                                class="form-control"
                                placeholder="e.g. Red, Blue, Green"
                            />
                            <small class="text-muted">Separate with commas.</small>
                            <input-error :message="form.errors.colors" />
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Sizes</label>
                            <input
                                v-model="form.data.sizes"
                                type="text"
                                class="form-control"
                                placeholder="e.g. S, M, L, XL"
                            />
                            <small class="text-muted">Separate with commas.</small>
                            <input-error :message="form.errors.sizes" />
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
                <div class="me-auto">
                    <label class="form-check">
                        <input
                            class="form-check-input"
                            type="checkbox"
                            v-model="createAnother"
                        />
                        Create Another
                    </label>
                </div>
                <div>
                    <button
                        type="button"
                        class="btn btn-secondary me-2"
                        @click="close()"
                    >
                        Cancel
                    </button>
                    <submit-btn :loading="form.processing || uploadingImages">
                        {{ uploadingImages ? 'Uploading Images...' : 'Create Product' }}
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
import { ref, computed, useTemplateRef } from 'vue';
import axios from 'axios';

const props = defineProps({
    categories: Array,
    events: Array,
    statusOptions: Object,
});

const modalRef = useTemplateRef('modalRef');
const imageInputRef = useTemplateRef('imageInputRef');
const createAnother = ref(false);
const selectedImages = ref([]);
const uploadingImages = ref(false);

const form = useAxiosForm({
    name: '',
    slug: '',
    category_id: '',
    event_id: '',
    description: '',
    short_description: '',
    price: '',
    compare_price: '',
    sku: '',
    colors: '',
    sizes: '',
    stock: 0,
    track_stock: false,
    status: 'draft',
    featured: false,
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

const onImagesSelected = (e) => {
    const files = Array.from(e.target.files);
    files.forEach((file) => {
        selectedImages.value.push({ file, preview: URL.createObjectURL(file) });
    });
    e.target.value = '';
};

const removeSelectedImage = (idx) => {
    URL.revokeObjectURL(selectedImages.value[idx].preview);
    selectedImages.value.splice(idx, 1);
};

const uploadImages = async (productId) => {
    for (const img of selectedImages.value) {
        const formData = new FormData();
        formData.append('image', img.file);
        await axios.post(
            route('brand-partner.products.images.store', productId),
            formData,
        );
    }
};

const submitForm = () => {
    form.post(route('brand-partner.products.store'), {
        onSuccess: async ({ data }) => {
            if (selectedImages.value.length > 0) {
                uploadingImages.value = true;
                await uploadImages(data.product.id);
                uploadingImages.value = false;
            }
            alert.showSuccess(data.message || 'Product created successfully.');
            if (createAnother.value) {
                form.reset();
                selectedImages.value = [];
                router.reload();
            } else {
                modalRef.value.close();
                router.reload();
            }
        },
    });
};
</script>
