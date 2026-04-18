<style scoped>
td {
    vertical-align: top !important;
}
</style>

<template>
    <div class="table-responsive">
        <table class="table table-center table-bordered">
            <thead class="text-center">
                <tr>
                    <th>S/N</th>
                    <th>Product</th>
                    <th>Variations</th>
                </tr>
            </thead>
            <tbody>
                <template
                    v-for="(product, index) in form.data.products"
                    :key="product.id"
                >
                    <tr>
                        <td class="text-center py-4">
                            <span
                                class="badge badge-secondary rounded-circle"
                                >{{ index + 1 }}</span
                            >
                        </td>
                        <td>
                            <div class="mb-3">
                                <div class="d-flex align-items-center">
                                    <a
                                        href="javascript:void(0);"
                                        class="avatar avatar-xl me-2"
                                        @click="
                                            previewImage(
                                                _product(index).product?.image,
                                            )
                                        "
                                    >
                                        <img
                                            class="object-fit-contain"
                                            :src="
                                                getImagePreview(
                                                    _product(index).product
                                                        ?.image,
                                                )
                                            "
                                            alt="img"
                                        />
                                    </a>
                                    <span>{{
                                        _product(index).product?.name
                                    }}</span>
                                </div>
                                <input-error
                                    :message="
                                        form.errors[`products.${index}.id`]
                                    "
                                />
                            </div>
                            <template
                                v-if="
                                    can('job-orders:manage-new-tasks') &&
                                    editable
                                "
                            >
                                <div class="mb-3">
                                    <label
                                        :for="`fabric-type-${index}`"
                                        class="form-label required"
                                        >Fabric</label
                                    >
                                    <select
                                        v-model="product.inventory_item_id"
                                        :id="`fabric-type-${index}`"
                                        class="form-select form-select-sm"
                                        :class="{
                                            'border-success':
                                                !!product.inventory_item_id,
                                            'is-invalid':
                                                form.errors[
                                                    `products.${index}.inventory_item_id`
                                                ],
                                        }"
                                        @change="
                                            form.clearErrors(
                                                `products.${index}.inventory_item_id`,
                                            )
                                        "
                                        :disabled="
                                            !can('job-orders:manage-new-tasks')
                                        "
                                    >
                                        <option value="">Select fabric</option>
                                        <option
                                            v-for="material in fabrics"
                                            :key="material.id"
                                            :value="material.id"
                                        >
                                            {{ material.item_name }}
                                        </option>
                                    </select>
                                    <input-error
                                        :message="
                                            form.errors[
                                                `products.${index}.fabric_type`
                                            ]
                                        "
                                    />
                                </div>
                                <div class="mb-3">
                                    <label
                                        :for="`instructions-${index}`"
                                        class="form-label"
                                        >Instructions</label
                                    >
                                    <textarea
                                        v-model="product.notes"
                                        :id="`instructions-${index}`"
                                        class="form-control form-control-sm"
                                        rows="1"
                                        placeholder="Add instructions or notes..."
                                        :disabled="
                                            !can('job-orders:manage-new-tasks')
                                        "
                                        @input="autoResize($event.target)"
                                        style="overflow-y: hidden; resize: none"
                                    ></textarea>
                                    <input-error
                                        :message="
                                            form.errors[
                                                `products.${index}.notes`
                                            ]
                                        "
                                    />
                                </div>
                                <div
                                    v-if="
                                        viewingStage && viewingStage !== 'new'
                                    "
                                    class="mb-0"
                                >
                                    <label
                                        :for="`file-path-${index}`"
                                        class="form-label"
                                        >File Path
                                        <span class="text-danger"
                                            >*</span
                                        ></label
                                    >
                                    <input-text
                                        v-model="product.file_path"
                                        :id="`file-path-${index}`"
                                        class="form-control-sm"
                                        :class="{
                                            'border-success':
                                                !!product.file_path,
                                            'is-invalid':
                                                form.errors[
                                                    `products.${index}.file_path`
                                                ],
                                        }"
                                        @change="
                                            form.clearErrors(
                                                `products.${index}.file_path`,
                                            )
                                        "
                                        placeholder="Input file path here..."
                                    />
                                    <input-error
                                        :message="
                                            form.errors[
                                                `products.${index}.file_path`
                                            ]
                                        "
                                    />
                                </div>
                            </template>
                            <template v-else>
                                <ul style="max-width: 300px">
                                    <li
                                        class="row g-2 align-items-center flex-wrap py-1 border-bottom"
                                    >
                                        <span class="col fw-bold">Fabric</span>
                                        <span class="col-auto">{{
                                            _product(index).inventory_item
                                                ?.item_name || 'N/A'
                                        }}</span>
                                    </li>
                                    <li
                                        class="row g-2 align-items-center flex-wrap py-1 border-bottom"
                                    >
                                        <span class="col fw-bold">Notes</span>
                                        <span
                                            class="col-auto"
                                            style="white-space: normal"
                                            >{{
                                                _product(index).notes || 'N/A'
                                            }}</span
                                        >
                                    </li>
                                    <li
                                        class="row g-2 align-items-center flex-wrap py-1"
                                    >
                                        <template
                                            v-if="
                                                can(
                                                    'job-orders:manage-artist-tasks',
                                                ) && editable
                                            "
                                        >
                                            <span class="col-auto fw-bold"
                                                >File Path:
                                                <span class="text-danger"
                                                    >*</span
                                                ></span
                                            >
                                            <div class="col">
                                                <input-text
                                                    v-model="product.file_path"
                                                    class="form-control-sm px-0 rounded-0 border-top-0 border-start-0 border-end-0 text-info"
                                                    :class="{
                                                        'border-success':
                                                            !!product.file_path,
                                                        'is-invalid':
                                                            form.errors[
                                                                `products.${index}.file_path`
                                                            ],
                                                    }"
                                                    @change="
                                                        form.clearErrors(
                                                            `products.${index}.file_path`,
                                                        )
                                                    "
                                                    placeholder="Input file path here..."
                                                />
                                            </div>
                                        </template>
                                        <template v-else>
                                            <span class="col fw-bold"
                                                >File Path</span
                                            >
                                            <div
                                                v-if="
                                                    !!_product(index).file_path
                                                "
                                                class="col-auto"
                                            >
                                                <BPopover
                                                    v-model="popover[index]"
                                                    manual
                                                    body="Copied!"
                                                >
                                                    <template #target>
                                                        <a
                                                            href="javascript:void(0);"
                                                            class="text-info"
                                                            style="
                                                                white-space: normal;
                                                            "
                                                            @click="
                                                                copyToClipboard(
                                                                    _product(
                                                                        index,
                                                                    ).file_path,
                                                                    index,
                                                                )
                                                            "
                                                            >{{
                                                                _product(index)
                                                                    .file_path
                                                            }}</a
                                                        >
                                                    </template>
                                                    Copied!
                                                </BPopover>
                                            </div>
                                            <span v-else class="col-auto"
                                                >N/A</span
                                            >
                                        </template>
                                    </li>
                                </ul>
                            </template>
                        </td>
                        <td>
                            <div class="row g-2">
                                <div
                                    v-for="line in jobOrder.products[index]
                                        .order_lines"
                                    :key="line.id"
                                    class="col-lg-12"
                                >
                                    <OrderItemVariation
                                        :job-order="jobOrder"
                                        :line="line"
                                        :viewing-stage="viewingStage"
                                    />
                                </div>
                            </div>
                        </td>
                    </tr>
                </template>
            </tbody>
        </table>
    </div>
</template>

<script setup>
import { getImagePreview } from '@/helpers/media';
import OrderItemVariation from './order-item-variation.vue';
import { emitter } from '@/composables/eventBus';
import { can } from '@/helpers/guard';
import { reactive } from 'vue';

const form = defineModel('form');

const props = defineProps({
    editable: Boolean,
    fabrics: {
        type: Object,
        required: true,
    },
    jobOrder: {
        type: Object,
        required: true,
    },
    viewingStage: {
        type: String,
        default: null,
    },
    imageEmitter: {
        type: String,
        default: 'job-order:preview-image',
    },
});

const popover = reactive(
    props.jobOrder.products.reduce((acc, item, index) => {
        acc[index] = false;
        return acc;
    }, {}),
);

const previewImage = (image) => {
    emitter.emit(props.imageEmitter, image);
};

const copyToClipboard = async (text, index) => {
    try {
        await navigator.clipboard.writeText(text);
        popover[index] = true;
        setTimeout(() => {
            popover[index] = false;
        }, 1500);
        return true;
    } catch (err) {
        console.error('Failed to copy:', err);
        return false;
    }
};

const _product = (index) => {
    return props.jobOrder.products[index];
};

const autoResize = (textarea) => {
    textarea.style.height = 'auto';
    const maxHeight = 200; // Maximum height in pixels
    const newHeight = Math.min(textarea.scrollHeight, maxHeight);
    textarea.style.height = newHeight + 'px';
    textarea.style.overflowY =
        textarea.scrollHeight > maxHeight ? 'auto' : 'hidden';
};
</script>
