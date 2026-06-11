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

                        <div class="mb-3">
                            <label class="form-label">Product Images</label>
                            <div
                                v-if="productImages.length"
                                class="d-flex flex-wrap gap-3 mb-3"
                            >
                                <div
                                    v-for="image in productImages"
                                    :key="image.id"
                                    class="img-card"
                                >
                                    <div class="img-card-inner">
                                        <img
                                            :src="image.url"
                                            class="img-card-preview"
                                        />
                                        <button
                                            type="button"
                                            class="img-card-delete"
                                            title="Delete image"
                                            :disabled="imageActionLoading"
                                            @click="deleteImage(image)"
                                        >
                                            <vue-feather
                                                type="x"
                                                size="14"
                                            ></vue-feather>
                                        </button>
                                        <button
                                            v-if="!image.is_primary"
                                            type="button"
                                            class="img-card-primary-btn"
                                            title="Set as primary"
                                            :disabled="imageActionLoading"
                                            @click="setPrimary(image)"
                                        >
                                            <vue-feather
                                                type="star"
                                                size="14"
                                            ></vue-feather>
                                        </button>
                                        <span
                                            v-if="image.is_primary"
                                            class="img-card-primary-badge"
                                        >
                                            <vue-feather
                                                type="star"
                                                size="12"
                                            ></vue-feather>
                                            Primary
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex align-items-center gap-3">
                                <button
                                    type="button"
                                    class="btn btn-outline-primary btn-sm"
                                    :disabled="imageActionLoading"
                                    @click="imageInputRef?.click()"
                                >
                                    <vue-feather
                                        type="upload"
                                        size="14"
                                        class="me-1"
                                    ></vue-feather>
                                    Add Images
                                </button>
                                <input
                                    ref="imageInputRef"
                                    type="file"
                                    class="d-none"
                                    multiple
                                    accept="image/*"
                                    :disabled="imageActionLoading"
                                    @change="uploadNewImages"
                                />
                                <small class="text-muted"
                                    >Upload additional images.</small
                                >
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label required">Variants (Color / Size / Quantity)</label>
                            <div class="variants-table">
                                <div class="variants-header">
                                    <span>Color</span>
                                    <span>Size</span>
                                    <span>Qty</span>
                                    <span></span>
                                </div>
                                <div
                                    v-for="(v, i) in form.data.variants"
                                    :key="i"
                                    class="variants-row"
                                >
                                    <select v-model="v.color" class="form-select form-select-sm">
                                        <option value="">Select color</option>
                                        <option
                                            v-for="opt in colorSelectOptions"
                                            :key="opt.value"
                                            :value="opt.value"
                                        >
                                            {{ opt.label }}
                                        </option>
                                    </select>
                                    <select v-model="v.size" class="form-select form-select-sm">
                                        <option value="">Select size</option>
                                        <option
                                            v-for="opt in sizeSelectOptions"
                                            :key="opt.value"
                                            :value="opt.value"
                                        >
                                            {{ opt.label }}
                                        </option>
                                    </select>
                                    <input
                                        v-model="v.stock"
                                        type="number"
                                        min="0"
                                        class="form-control form-control-sm"
                                        placeholder="0"
                                    />
                                    <button
                                        type="button"
                                        class="btn btn-outline-danger btn-sm"
                                        @click="removeVariant(i)"
                                    >
                                        ×
                                    </button>
                                </div>
                            </div>
                            <button
                                type="button"
                                class="btn btn-outline-primary btn-sm mt-2"
                                @click="addVariant"
                            >
                                + Add Variant
                            </button>
                            <input-error :message="form.errors.variants" />
                        </div>
                    </div>

                    <div class="col-md-4">
                        <!-- Approval Status Banner -->
                        <div
                            v-if="
                                $page.props.features?.product_approval &&
                                product.approval_status !== 'approved'
                            "
                            class="alert d-flex align-items-start gap-2 mb-3 p-2"
                            :class="
                                product.approval_status === 'rejected'
                                    ? 'alert-danger'
                                    : 'alert-warning'
                            "
                            style="font-size: 13px"
                        >
                            <vue-feather
                                type="info"
                                size="16"
                                class="flex-shrink-0 mt-1"
                            />
                            <div>
                                <template
                                    v-if="product.approval_status === 'pending'"
                                >
                                    <strong>Pending Approval</strong> — This
                                    product is awaiting TPInkAdmin review.
                                    Publishing is disabled until approved.
                                </template>
                                <template
                                    v-else-if="
                                        product.approval_status === 'rejected'
                                    "
                                >
                                    <strong>Rejected</strong> — This product was
                                    rejected by TPInkAdmin.
                                    <div
                                        v-if="product.approval_notes"
                                        class="mt-1"
                                    >
                                        <em
                                            >Reason:
                                            {{ product.approval_notes }}</em
                                        >
                                    </div>
                                </template>
                            </div>
                        </div>

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
                                    {{ cat.label }}
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
                                    :disabled="
                                        value === 'published' &&
                                        $page.props.features
                                            ?.product_approval &&
                                        product.approval_status !== 'approved'
                                    "
                                >
                                    {{ label
                                    }}{{
                                        value === 'published' &&
                                        $page.props.features
                                            ?.product_approval &&
                                        product.approval_status !== 'approved'
                                            ? ' (requires approval)'
                                            : ''
                                    }}
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
                                <label class="form-label">Old Price</label>
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

                        <input type="hidden" name="track_stock" value="1" />

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
import { ref, computed, useTemplateRef } from 'vue';
import axios from 'axios';

const props = defineProps({
    product: Object,
    categories: Array,
    events: Array,
    collections: Array,
    colorOptions: Array,
    sizeOptions: Array,
    statusOptions: Object,
});

const colorSelectOptions = computed(() =>
    (props.colorOptions || []).map((c) => ({ label: c.label, value: c.label })),
);

const sizeSelectOptions = computed(() =>
    (props.sizeOptions || []).map((c) => ({ label: c.label, value: c.label })),
);

const modalRef = useTemplateRef('modalRef');
const imageInputRef = useTemplateRef('imageInputRef');
const productImages = ref([...(props.product.images ?? [])]);
const imageActionLoading = ref(false);

function parseVariants() {
    const meta = props.product.meta;
    if (meta?.variants?.length) {
        return meta.variants.map(v => ({ color: v.color, size: v.size, stock: v.stock ?? 0 }));
    }
    const colors = props.product.colors ? props.product.colors.split(',').map(s => s.trim()) : [];
    const sizes = props.product.sizes ? props.product.sizes.split(',').map(s => s.trim()) : [];
    if (!colors.length && !sizes.length) return [{ color: '', size: '', stock: 0 }];
    const variants = [];
    for (const c of colors.length ? colors : ['']) {
        for (const s of sizes.length ? sizes : ['']) {
            variants.push({ color: c, size: s, stock: 0 });
        }
    }
    return variants;
}

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
    variants: parseVariants(),
    track_stock: 1,
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

const uploadNewImages = async (e) => {
    const files = Array.from(e.target.files);
    e.target.value = '';
    imageActionLoading.value = true;
    for (const file of files) {
        const formData = new FormData();
        formData.append('image', file);
        const response = await axios.post(
            route('brand-partner.products.images.store', props.product.id),
            formData,
        );
        console.log(response.data.image);
        productImages.value.push(response.data.image);
    }
    imageActionLoading.value = false;
};

const deleteImage = async (image) => {
    imageActionLoading.value = true;
    await axios.delete(
        route('brand-partner.products.images.destroy', [
            props.product.id,
            image.id,
        ]),
    );
    productImages.value = productImages.value.filter(
        (img) => img.id !== image.id,
    );
    imageActionLoading.value = false;
};

const setPrimary = async (image) => {
    imageActionLoading.value = true;
    await axios.post(
        route('brand-partner.products.images.primary', [
            props.product.id,
            image.id,
        ]),
    );
    productImages.value.forEach((img) => {
        img.is_primary = img.id === image.id;
    });
    imageActionLoading.value = false;
};

const addVariant = () => {
    form.data.variants.push({ color: '', size: '', stock: 0 });
};

const removeVariant = (index) => {
    form.data.variants.splice(index, 1);
};

const submitForm = () => {
    form.transform((data) => {
        const variants = data.variants.filter(v => v.color || v.size);
        const colors = [...new Set(variants.map(v => v.color).filter(Boolean))].join(',');
        const sizes = [...new Set(variants.map(v => v.size).filter(Boolean))].join(',');
        const stock = variants.reduce((sum, v) => sum + (parseInt(v.stock) || 0), 0);
        return {
            ...data,
            variants,
            colors,
            sizes,
            stock,
        };
    }).put(route('brand-partner.products.update', props.product.id), {
        onSuccess: ({ data }) => {
            alert.showSuccess(data.message || 'Product updated successfully.');
            modalRef.value.close();
            router.reload();
        },
    });
};
</script>

<style scoped>
.variants-table {
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    overflow: hidden;
}

.variants-header {
    display: grid;
    grid-template-columns: 1fr 1fr 100px 40px;
    gap: 8px;
    padding: 8px 10px;
    background: #f8f9fa;
    font-size: 12px;
    font-weight: 700;
    color: #555;
    text-transform: uppercase;
    letter-spacing: 0.3px;
    border-bottom: 1px solid #e0e0e0;
}

.variants-row {
    display: grid;
    grid-template-columns: 1fr 1fr 100px 40px;
    gap: 8px;
    padding: 6px 10px;
    align-items: center;
    border-bottom: 1px solid #f0f0f0;
}

.variants-row:last-child {
    border-bottom: none;
}

.img-card {
    width: 120px;
}

.img-card-inner {
    position: relative;
    border-radius: 8px;
    overflow: hidden;
    border: 2px solid #e0e0e0;
    background: #f5f5f5;
    transition: border-color 0.2s;
}

.img-card-inner:hover {
    border-color: #adb5bd;
}

.img-card-preview {
    width: 120px;
    height: 120px;
    object-fit: cover;
    display: block;
}

.img-card-delete {
    position: absolute;
    top: 4px;
    right: 4px;
    width: 26px;
    height: 26px;
    border-radius: 50%;
    border: none;
    background: rgba(0, 0, 0, 0.55);
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    opacity: 0;
    transition: opacity 0.15s, background 0.15s;
}

.img-card-inner:hover .img-card-delete {
    opacity: 1;
}

.img-card-delete:hover {
    background: rgba(220, 53, 69, 0.85);
}

.img-card-delete:disabled {
    opacity: 0.3;
    cursor: not-allowed;
}

.img-card-primary-btn {
    position: absolute;
    top: 4px;
    left: 4px;
    width: 26px;
    height: 26px;
    border-radius: 50%;
    border: none;
    background: rgba(0, 0, 0, 0.55);
    color: #ffc107;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    opacity: 0;
    transition: opacity 0.15s, background 0.15s;
}

.img-card-inner:hover .img-card-primary-btn {
    opacity: 1;
}

.img-card-primary-btn:hover {
    background: rgba(255, 193, 7, 0.85);
    color: #fff;
}

.img-card-primary-btn:disabled {
    opacity: 0.3;
    cursor: not-allowed;
}

.img-card-primary-badge {
    position: absolute;
    bottom: 4px;
    left: 50%;
    transform: translateX(-50%);
    display: inline-flex;
    align-items: center;
    gap: 3px;
    background: #0d6efd;
    color: #fff;
    font-size: 10px;
    font-weight: 600;
    padding: 2px 8px;
    border-radius: 10px;
    white-space: nowrap;
}
</style>
