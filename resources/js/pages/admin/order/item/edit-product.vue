<template>
    <div>
        <Head title="Edit Product Items" />

        <!-- Edit Product Modal -->
        <Modal
            ref="modalRef"
            max-width="5xl"
            :close-explicitly="true"
            #default="{ close }"
        >
            <div class="page-header">
                <h4>Edit Order Items - {{ props.product?.name }}</h4>
            </div>

            <form @submit.prevent="submitForm">
                <div class="row page-body">
                    <!-- LEFT SIDE: Product Info -->
                    <div class="col-md-5">
                        <!-- Category -->
                         <div class="mb-3">
                            <label class="form-label">Category</label>
                            <input
                                type="text"
                                class="form-control"
                                :value="props.product?.category?.name"
                                disabled
                            />
                        </div>
                        <!-- Product -->
                        <div class="mb-3">
                            <label class="form-label required">Product</label>
                            <select
                                v-model="selectedProduct"
                                class="form-select"
                                disabled
                            >
                                <option :value="selectedProduct">
                                    {{ props.product?.name }}
                                </option>
                            </select>
                        </div>
                         <div class="mb-3">
                             <p v-if="props.product?.description">
                               {{props.product?.description }}
                            </p>
                        </div>
                    </div>

                    <!-- RIGHT SIDE: Variant Details -->
                    <div class="col-md-7" v-if="groupedVariants.withName.length || groupedVariants.noName.length">
                        <h6 class="mb-2">Selected Variants</h6>
                        <span class="text-info">
                            <i
                                data-feather="info"
                                class="feather-info px-1"
                            ></i>
                            Set Names, Input Quantity and Price
                        </span>
                        <hr />
                         <div v-if="groupedVariants.withName.length">
                            <h6 class="fw-bold text-dark">WITH NAME</h6>
                        <div
                            v-for="variant in groupedVariants.withName"
                            :key="variant.order_line_id"
                            class="mb-3"
                        >
                            <div class="d-flex align-items-center">
                                <!-- Variant description -->
                                <div class="flex-grow-1">
                                    {{ variant.size }}
                                </div>

                                <!-- Set Names button -->
                                <button
                                    v-if="needsSetNames(variant.description)"
                                    type="button"
                                    class="btn btn-xs btn-dark mx-2"
                                    @click="openSetNamesOverlay(variant)"
                                >
                                    {{
                                        variantQuantities[
                                            variant.order_line_id
                                        ] > 1
                                            ? 'Set Names'
                                            : 'Set Name'
                                    }}
                                </button>

                                <!-- Quantity input -->
                                <input
                                    type="number"
                                    min="1"
                                    class="form-control w-25 ms-3"
                                    v-model.number="
                                        variantQuantities[variant.order_line_id]
                                    "
                                />

                                <!-- Price input beside quantity -->
                                <input
                                    type="number"
                                    min="0"
                                    step="0.01"
                                    class="form-control w-25 ms-2"
                                    v-model.number="
                                        variantPrices[variant.order_line_id]
                                    "
                                />
                            </div>
                            <div
                                v-if="isOthersSize(variant.size)"
                                class="mt-3"
                                >
                                <p class="small text-muted mb-1">
                                    Dimension (W × H)
                                </p>

                                <div class="d-flex align-items-center gap-2">
                                    <input
                                        v-model.number="variantDimensions[variant.order_line_id].width"
                                        type="number"
                                        min="1"
                                        class="form-control form-control-sm text-center"
                                        style="max-width: 5rem"
                                        placeholder="W"
                                    />

                                    <span class="fw-bold text-muted">×</span>

                                    <input
                                        v-model.number="variantDimensions[variant.order_line_id].height"
                                        type="number"
                                        min="1"
                                        class="form-control form-control-sm text-center"
                                        style="max-width: 5rem"
                                        placeholder="H"
                                    />

                                    <small class="text-muted">inches</small>
                                </div>
                            </div>

                            <!-- Display names if any -->
                            <div
                                v-if="
                                    variantNames[variant.order_line_id]?.length
                                "
                                class="mt-1 small text-muted"
                            >
                                <span
                                    v-for="(name, idx) in variantNames[
                                        variant.order_line_id
                                    ]"
                                    :key="idx"
                                >
                                    {{ idx + 1 }}. {{ name || '—'
                                    }}<span
                                        v-if="
                                            idx <
                                            variantNames[variant.order_line_id]
                                                .length -
                                                1
                                        "
                                        >,
                                    </span>
                                </span>
                            </div>
                        </div>
                        </div>
                         <hr />
                         <div v-if="groupedVariants.noName.length">
                            <h6 class="fw-bold text-dark">NO NAME</h6>
                            <div
                            v-for="variant in groupedVariants.noName"
                            :key="variant.order_line_id"
                            class="mb-3"
                             >
                            <div class="d-flex align-items-center">
                                <!-- Variant description -->
                                <div class="flex-grow-1">
                                    {{ variant.size }}
                                </div>
                                <!-- Quantity input -->
                                <input
                                    type="number"
                                    min="1"
                                    class="form-control w-25 ms-3"
                                    v-model.number="
                                        variantQuantities[variant.order_line_id]
                                    "
                                />

                                <!-- Price input beside quantity -->
                                <input
                                    type="number"
                                    min="0"
                                    step="0.01"
                                    class="form-control w-25 ms-2"
                                    v-model.number="
                                        variantPrices[variant.order_line_id]
                                    "
                                />
                            </div>
                               <div
                                v-if="isOthersSize(variant.size)"
                                class="mt-3"
                                >
                                <p class="small text-muted mb-1">
                                    Dimension (W × H)
                                </p>

                                <div class="d-flex align-items-center gap-2">
                                    <input
                                        v-model.number="variantDimensions[variant.order_line_id].width"
                                        type="number"
                                        min="1"
                                        class="form-control form-control-sm text-center"
                                        style="max-width: 5rem"
                                        placeholder="W"
                                    />

                                    <span class="fw-bold text-muted">×</span>

                                    <input
                                        v-model.number="variantDimensions[variant.order_line_id].height"
                                        type="number"
                                        min="1"
                                        class="form-control form-control-sm text-center"
                                        style="max-width: 5rem"
                                        placeholder="H"
                                    />

                                    <small class="text-muted">inches</small>
                                </div>
                            </div>
                            </div>
                        </div>
                         <hr />
                        <div class="mb-3">
                            <label class="form-label required"
                                >Reason for changes</label
                            >
                            <textarea
                                v-model="reason"
                                class="form-control"
                                rows="2"
                                placeholder="Enter reason for altering the details..."
                                required
                            ></textarea>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="page-footer-buttons mt-3 text-end">
                    <button
                        type="button"
                        class="btn btn-secondary"
                        @click="modalRef.close()"
                    >
                        Cancel
                    </button>
                    <submit-btn :loading="form.processing"
                        >Update Items</submit-btn
                    >
                </div>
            </form>

            <!-- Set Names Overlay -->
            <div v-if="showSetNamesOverlay" class="overlay">
                <div
                    :class="[
                        'overlay-content',
                        { 'two-columns': tempNames.length > 10 },
                    ]"
                >
                    <h5 class="mb-2">
                        Set Names for {{ currentVariantForNames.description }}
                    </h5>
                    <div class="names-grid">
                        <div
                            v-for="(name, idx) in tempNames"
                            :key="idx"
                            class="mb-2"
                        >
                            <input
                                type="text"
                                class="form-control"
                                v-model="tempNames[idx]"
                                :placeholder="`Name ${idx + 1}`"
                            />
                        </div>
                    </div>
                    <div class="mt-3 text-end">
                        <button
                            class="btn btn-secondary me-2"
                            @click="closeSetNamesOverlay"
                        >
                            Cancel
                        </button>
                        <button class="btn btn-primary" @click="saveNames">
                            Set
                        </button>
                    </div>
                </div>
            </div>
        </Modal>
    </div>
</template>

<script setup>
import { ref, reactive, computed } from 'vue';
import { useAxiosForm } from '@/composables/axiosForm';
import { emitter } from '@/composables/eventBus';
import * as alert from '@/helpers/alert';
import { Head, router } from '@inertiajs/vue3';

const props = defineProps({
    order: Object,
    product: Object,
    lines: Array,
});

const modalRef = ref(null);
const reason = ref('');

// Category & Product
const selectedProduct = ref(props.product.id);

// Track quantities, prices, names per quote line
const variantQuantities = reactive({});
const variantPrices = reactive({});
const variantNames = reactive({});
const variantDimensions = reactive({});



props.lines.forEach((line) => {
    variantQuantities[line.id] = line.quantity;
    variantPrices[line.id] = line.unit_price?.decimal || 0;
    variantNames[line.id] = line.meta?.names || [];
    const dimension = line.meta?.custom_dimension || '';
    const [w, h] = dimension.split('x').map(v => v?.trim() || '');
    variantDimensions[line.id] = { width: w, height: h };
});

const groupedVariants = computed(() => {
  const groups = { withName: [], noName: [] };

  // Loop through order lines
  props.lines.forEach((line) => {
    const description = line.purchasable?.description || '';

    // Find the corresponding print_line for this order line
    const printLine = props.order?.print_lines?.find(
      (pl) => pl.order_line_id === line.id
    );

    // Get the size from printLine if available
    const size = printLine?.size || description?.split('/')[1]?.trim() || 'N/A';

    const variantObject = {
      ...line.purchasable,
      order_line_id: line.id,
      description,
      size,
      quantity: line.quantity,
      unit_price: line.unit_price?.decimal || 0,
      names: line.meta?.names || [],
    };

    // Group by printing option
    if (description.toUpperCase().includes('WITH NAME')) {
      groups.withName.push(variantObject);
    } else {
      groups.noName.push(variantObject);
    }
  });

  return groups;
});

const isOthersSize = (description) => {
    return description.toLowerCase() === 'custom';
};


// Overlay for setting names
const showSetNamesOverlay = ref(false);
const currentVariantForNames = ref(null);
const tempNames = ref([]);
const needsSetNames = (desc) => desc?.toUpperCase().includes('WITH NAME');

const openSetNamesOverlay = (variant) => {
    currentVariantForNames.value = variant;
    const qty = variantQuantities[variant.order_line_id] || 1;
    tempNames.value = [
        ...(variantNames[variant.order_line_id] || Array(qty).fill('')),
    ];

    if (tempNames.value.length < qty)
        tempNames.value.push(...Array(qty - tempNames.value.length).fill(''));
    if (tempNames.value.length > qty)
        tempNames.value = tempNames.value.slice(0, qty);

    showSetNamesOverlay.value = true;
};

const closeSetNamesOverlay = () => (showSetNamesOverlay.value = false);
const saveNames = () => {
    if (currentVariantForNames.value) {
        variantNames[currentVariantForNames.value.order_line_id] = [
            ...tempNames.value,
        ];
        showSetNamesOverlay.value = false;
    }
};

// Submit form
const form = useAxiosForm({ order_lines: props.lines });

const submitForm = () => {
    if (!reason.value.trim()) return;

    const order_lines = props.lines.map((line) => ({
        id: line.id,
        quantity: variantQuantities[line.id],
        price: variantPrices[line.id],
        meta: {
             ...line.meta,
            names: variantNames[line.id] ?? [],
            custom_dimension: `${variantDimensions[line.id].width} x ${variantDimensions[line.id].height}`,
        },
    }));

    form.submit(
        'post',
        route('admin.order.item.update-bulk', {
            order: props.order.id,
            product: props.product.id,
        }),
        {
            data: { order_lines, reason: reason.value },
            onSuccess: (res) => {
                alert.showSuccess(
                    res.data?.message || 'Items updated successfully.',
                );
                modalRef.value?.close();
                emitter.emit('order:item-updated');
            },
        },
    );
};
</script>

<style scoped>
.overlay {
    position: fixed;
    inset: 0;
    background: rgba(0, 0, 0, 0.5);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 1050;
}
.overlay-content {
    background: white;
    border-radius: 8px;
    width: 90%;
    max-width: 600px;
    padding: 1rem;
    max-height: 80vh;
    overflow-y: auto;
}
.names-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 0.5rem;
}
.overlay-content.two-columns .names-grid {
    grid-template-columns: 1fr 1fr;
    gap: 0.5rem 1rem;
}
</style>
