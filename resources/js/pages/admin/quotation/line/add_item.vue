<template>
    <div>
        <Head title="Add Item" />

        <Modal
            ref="modalRef"
            max-width="7xl"
            :close-explicitly="true"
            v-slot="{ close }"
        >
            <div class="pos-five">
                <div class="page-header">
                    <h4 class="page-title">Add Item</h4>
                </div>

                <div class="position-relative pos-products">
                    <div style="overflow-y: auto; max-height: 75vh">
                        <div class="row g-0 mx-0">
                            <div class="col-xl bg-light">
                                <div class="p-4">
                                    <div
                                        class="d-flex align-items-center justify-content-between flex-wrap row-gap-3 mb-3"
                                    >
                                        <dt-search
                                            v-model="filterForm.filter.search"
                                            @search="submitFilters"
                                        />
                                        <div
                                            class="d-flex table-dropdown my-xl-auto right-content align-items-center flex-wrap row-gap-3"
                                        >
                                            <select-filter
                                                v-model="
                                                    filterForm.filter.category
                                                "
                                                :options="props.categories"
                                                name="Category"
                                                style="margin: 0 !important"
                                                @change="submitFilters"
                                            ></select-filter>
                                        </div>
                                    </div>
                                    <div class="row g-3">
                                        <div
                                            v-for="product in products.data"
                                            :key="product.id"
                                            class="col-sm-6 col-md-6 col-lg-6 col-xl-4"
                                        >
                                            <div
                                                class="product-info card mb-0"
                                                :class="{
                                                    active:
                                                        product.id ===
                                                        selected?.id,
                                                }"
                                                @click="select(product)"
                                            >
                                                <a
                                                    href="javascript:void(0);"
                                                    class="pro-img"
                                                >
                                                    <img
                                                        class="img-fluid"
                                                        :src="
                                                            getImagePreview(
                                                                product.image,
                                                            )
                                                        "
                                                        alt="Products"
                                                    />
                                                    <span
                                                        ><i
                                                            class="ti ti-circle-check-filled"
                                                        ></i
                                                    ></span>
                                                </a>
                                                <h6 class="cat-name">
                                                    <a
                                                        href="javascript:void(0);"
                                                        >{{
                                                            product.category
                                                                .name
                                                        }}</a
                                                    >
                                                </h6>
                                                <h6 class="product-name">
                                                    <a
                                                        href="javascript:void(0);"
                                                        >{{ product.name }}</a
                                                    >
                                                </h6>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="px-4 py-3">
                                        <DtPagerModel
                                            v-model="filterForm.page"
                                            :total-pages="products.last_page"
                                            @change="submitFilters"
                                        />
                                    </div>
                                </div>
                            </div>
                            <div
                                v-show="selected"
                                class="col-xl-6 bg-secondary-transparent"
                                >
                                <aside
                                    class="product-order-list bg-secondary-transparent flex-fill"
                                    style="height: 100%"
                                    >
                                    <div class="card">
                                        <div class="card-body">
                                            <div
                                                class="order-head d-flex justify-content-between w-100"
                                            >
                                                <div>
                                                    <h3>
                                                        {{ selected?.name }}
                                                    </h3>
                                                    <p class="mb-0">
                                                        {{
                                                            selected?.category
                                                                ?.name
                                                        }}
                                                    </p>
                                                </div>
                                                <button
                                                    type="button"
                                                    class="btn btn-sm btn-icon btn-danger fs-16"
                                                    @click="select(null)"
                                                >
                                                    <i class="ti ti-x"></i>
                                                </button>
                                            </div>
                                            <div>
                                                <p v-if="selected?.description">
                                                    {{ selected?.description }}
                                                </p>
                                                <div v-if="selected?.options">
                                                    <!-- PRINTING OPTION -->
                                                    <div
                                                    v-for="option in selected.options.filter(o => o.name === 'Printing Option')"
                                                    :key="option.id"
                                                    class="mb-3"
                                                    >
                                                        <p class="small fw-bold mb-1">
                                                            {{ option.pluralized_name }}
                                                        </p>

                                                        <div class="d-flex flex-wrap gap-2">
                                                            <button
                                                                v-for="value in option.values"
                                                                :key="value.id"
                                                                type="button"
                                                                class="btn btn-secondary-ghost"
                                                                :class="{ active: selectedPrintingValue === value.id }"
                                                                @click="selectPrinting(value)"
                                                            >
                                                                {{ value.label }}
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- SIZE BUTTONS -->
                                                    <div v-if="availableSizes.length" class="mb-3">
                                                        <p class="small fw-bold mb-1">Sizes</p>
                                                        <div class="d-flex flex-wrap gap-2">
                                                            <button
                                                                v-for="size in availableSizes"
                                                                :key="size.variant_id"
                                                                type="button"
                                                                class="btn btn-secondary-ghost"
                                                                :class="{ active: isSelectedSize(size.variant_id) }"
                                                                @click="toggleSize(size)"
                                                            >
                                                                {{ size.label }}
                                                            </button>
                                                        </div>
                                                        <div
                                                            v-if="isOthersSelected"
                                                            class="mt-3"
                                                        >
                                                            <p class="small text-muted mb-1">
                                                                Dimension (W × H)
                                                            </p>

                                                            <div class="d-flex align-items-center gap-2">

                                                                <input
                                                                    v-model="customWidth"
                                                                    type="number"
                                                                    min="1"
                                                                    class="form-control form-control-sm text-center"
                                                                    style="max-width: 5rem"
                                                                    placeholder="W"
                                                                />

                                                                <span class="fw-bold text-muted">×</span>

                                                                <input
                                                                    v-model="customHeight"
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
                                                     <div class="mb-3">
                                                        <label class="form-check">
                                                            <input
                                                            type="checkbox"
                                                            v-model="samePriceForAll"
                                                            class="form-check-input"
                                                            />
                                                            <span class="text-muted">Apply same price to all sizes</span>
                                                        </label>
                                                          <label class="form-check d-block mt-2">
                                                            <input
                                                                type="checkbox"
                                                                v-model="sameQtyAndNamesForAll"
                                                                class="form-check-input"
                                                            />
                                                                <span class="text-muted">
                                                                    Apply same quantity and names to all sizes
                                                                </span>
                                                        </label>
                                                    </div>
                                                </div>
                                                <div
                                                    v-for="row in sizeRows"
                                                    :key="row.variant_id"
                                                    class="mb-3"
                                                >

                                                    <div class="fw-bold mb-2">
                                                        Size: {{ row.size }}
                                                    </div>

                                                    <div class="row align-items-center mb-1">

                                                        <div class="col-md-5">
                                                            <div class="input-group">
                                                                <span class="input-group-text required">Qty</span>
                                                                <input
                                                                    v-model.number="row.quantity"
                                                                    type="number"
                                                                    min="1"
                                                                    class="form-control text-center"
                                                                />
                                                            </div>
                                                        </div>

                                                        <span class="col-auto">×</span>

                                                        <div class="col-md-6">
                                                            <div class="input-group">
                                                                <span class="input-group-text">
                                                                    {{ quotation.currency.symbol }}
                                                                </span>
                                                                <input
                                                                    v-model.number="row.price"
                                                                    type="number"
                                                                    class="form-control text-end"
                                                                />
                                                            </div>
                                                        </div>

                                                    </div>

                                                    <!-- NAMES -->
                                                    <div
                                                        class="row g-2"
                                                        v-if="printingOptionLabel == 'WITH name'"
                                                        >
                                                        <p class="mb-0" v-if="row?.quantity">Names</p>
                                                        <div
                                                            v-for="n in row.quantity"
                                                            :key="n"
                                                            class="col-md-4"
                                                            >
                                                            <input
                                                                v-model="row.names[n - 1]"
                                                                type="text"
                                                                class="form-control"
                                                                :placeholder="`Name #${n}`"
                                                            />
                                                        </div>
                                                    </div>

                                                    <hr/>
                                                </div>
                                            </div>
                                        </div>
                                </aside>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="new-employee-field">
                    <form @submit.prevent="submitForm">
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
                                <submit-btn
                                    :loading="form.processing"
                                    :disabled="!canAddItem"
                                    >Add Item</submit-btn
                                >
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </Modal>
    </div>
</template>

<script setup>
import DtPagerModel from '@/components/datatable/dt-pager-model.vue';
import { useAxiosForm } from '@/composables/axiosForm';
import { emitter } from '@/composables/eventBus';
import * as alert from '@/helpers/alert';
import { removeEmptyValues } from '@/helpers/form';
import { getImagePreview } from '@/helpers/media';
import { Head, useForm } from '@inertiajs/vue3';
import { debounce } from 'lodash';
import { computed, ref, useTemplateRef, watch } from 'vue';

const props = defineProps({
    quotation: Object,
    products: Object,
    categories: Object,
    filter: Object,
});
const modalRef = useTemplateRef('modalRef');
const createAnother = ref(false);
const selected = ref(null);
const sizeRows = ref([]);
const availableSizes = ref([]);
const selectedPrintingValue = ref(null);
const printingOptionLabel = ref(null);
const samePriceForAll = ref(false)
const sameQtyAndNamesForAll = ref(false)

const filterForm = useForm({
    filter: {
        search: props.filter.search || '',
        category: props.filter.category || '',
    },
    per_page: props.products.per_page,
    page: 1,
});

const form = useAxiosForm({
    product_id: null,
    names: [],
    custom_dimension: '',
}); 

// CUSTOM DIMENSION
const customWidth = ref('');
const customHeight = ref('');

const isOthersSelected = computed(() => {
    return sizeRows.value.some(
        row => row.size?.toLowerCase() === 'custom'
    )
})

watch([customWidth, customHeight], ([w, h]) => {
    form.data.custom_dimension = w && h ? `${w}x${h}` : '';
});

// CHECK IF CAN ADD ITEM
const canAddItem = computed(() => {
  if (!form.data.product_id || !selectedPrintingValue) return false

  if (sizeRows.value.length === 0) return false

for (const row of sizeRows.value) {
    if (!row.quantity || row.quantity <= 0) return false
    if (!row.price || row.price <= 0) return false
  }
  return true
})

// SELECT PRINTING OPTIONS
function selectPrinting(value) {
    selectedPrintingValue.value = value.id
    printingOptionLabel.value = value.label
}

const submitFilters = () => {
    filterForm
        .transform((data) =>
            removeEmptyValues({
                ...data,
                per_page:
                    data.per_page === props.filter.default_per_page
                        ? ''
                        : data.per_page,
            }),
        )
        .get(route('admin.quotation.add', props.quotation.id), {
            preserveState: true,
            preserveScroll: true,
            replace: true,
        });
};

// SELECT PRODUCT
const select = (product) => {
    selected.value = product;
};

watch(
    selected,
    (product) => {
        if (!product) {
            form.data.product_id = null;
            form.data.values = {};
            return;
        }
        form.data.product_id = product.id;
        form.data.values = product.options.reduce((acc, option) => {
            acc[option.id] = null;
            return acc;
        }, {});
    },
    { immediate: true },
);

// UPDATE ROWS IF CHECKBOX TICKED
watch(
  () => sizeRows.value[0],
  (firstRow) => {
    if (!firstRow) return

      sizeRows.value.forEach((row, index) => {
      if (index === 0) return 

      if (samePriceForAll.value) row.price = firstRow.price
      if (sameQtyAndNamesForAll.value) {
        row.quantity = firstRow.quantity
        row.names = [...firstRow.names]
      }
    })
  },
  { deep: true }
)

// TOGGLE SIZE FUNCTION
function toggleSize(size) {
  const index = sizeRows.value.findIndex(r => r.variant_id === size.variant_id)

  if (index !== -1) {
    sizeRows.value.splice(index, 1)
    return
  }

  const firstRow = sizeRows.value[0]

  sizeRows.value.push({
    product_id: selected?.id,
    variant_id: size.variant_id,
    size: size.label,
    quantity: (sameQtyAndNamesForAll.value && firstRow) ? firstRow.quantity : 1,
    price: (samePriceForAll.value && firstRow) ? firstRow.price : size.price,
    names: (sameQtyAndNamesForAll.value && firstRow) ? [...firstRow.names] : []
  })
}

watch(selected, () => {
    // When user selects a new product, reset printing option and sizes
    selectedPrintingValue.value = null
    sizeRows.value = []
    availableSizes.value = []
})

watch(selectedPrintingValue, async (valueId) => {

    // Reset rows whenever printing option changes
    sizeRows.value = []
    availableSizes.value = []

    // Only proceed if product and printing option are selected
    if (!selected.value || !valueId) return

    try {
        const url = route('admin.products.variants.by-option', [
            selected.value.id,
            valueId
        ])
        const { data } = await axios.get(url)

        buildAvailableSizes(data)
    } catch (error) {
        console.error('Failed to load sizes:', error)
    }

})

function buildAvailableSizes(variants) {

    availableSizes.value = variants.map(variant => {

        const sizeValue = variant.values.find(
            v => v.option?.name === 'Size'
        )

        let label = sizeValue?.label ?? variant.description

        // Remove "NO name / " or "WITH name / "
        if (label.includes('/')) {
            label = label.split('/')[1].trim()
        }

        return {
            variant_id: variant.id,
            label: label,
            price: variant.price
        }
    })
}



function isSelectedSize(variantId) {
    return sizeRows.value.some(
        r => r.variant_id === variantId
    )
}


// watch(selected, (product) => {

//     if (!product?.options) return

//     // Find Printing Option
//     const printingOption = product.options.find(
//         o => o.name === 'Printing Option'
//     )

//     if (!printingOption) return

//     // Find WITH name (case-insensitive safe)
//     const withName = printingOption.values.find(
//         v => v.label.toLowerCase() === 'with name'
//     )

//     if (!withName) return

//     // ✅ Set as default
//     selectedPrintingValue.value = withName.id
//     printingOptionLabel.value = withName.label

// }, { immediate: true })


const submitForm = () => {
    const payload = {
        product_id: form.data.product_id,
        printing_option_id: selectedPrintingValue.value, 
        printing_option_value: printingOptionLabel.value, 
        custom_dimension: form.data.custom_dimension,
        sizes: sizeRows.value.map(row => ({
            variant_id: row.variant_id,             
            product_id: form.data.product_id,       
            size_label: row.size,                  
            quantity: row.quantity,
            price: row.price,
            names: row.names?.filter(n => n && n.trim() !== '') || [], 
        }))
    }

    form.post(route('admin.quotation.lines', props.quotation.id), {
        data: payload,  
        onSuccess: ({ data }) => {
            if (createAnother.value) form.reset();
            else modalRef.value.close();

            emitter.emit('quotationline:created', data.quote_lines || null);
            alert.showSuccess(data.message || 'Item added successfully.');
        },
        onError: (error) => {
            if (error.status === 409) {
                alert.showError(
                    error.response.data.message || 'Conflict error.',
                );
            }
        },
    });
};
</script>
