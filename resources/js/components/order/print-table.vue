<template>
    <div class="table-responsive mb-3">
        <table class="table table-bordered align-middle table-sm">
            <thead class="table-light text-center">
                <tr>
                    <th class="fw-bold" style="width: 50px">S/N</th>
                    <th class="fw-bold" style="width: 400px">Product</th>
                    <th class="fw-bold">Printing Option & Size</th>
                    <th class="fw-bold" style="width: 80px">Qty</th>
                    <th class="fw-bold" style="width: 80px">UOM</th>
                    <th class="fw-bold" style="width: 100px">Price</th>
                    <th class="fw-bold" style="width: 120px">TOTAL</th>
                </tr>
            </thead>
            <tbody v-if="!lines || lines.length == 0">
                <tr>
                    <td colspan="7" class="text-center">No items found.</td>
                </tr>
            </tbody>
            <tbody v-else>
                <tr v-for="(line, index) in lines" :key="line.id">
                    <td class="text-center">
                        {{ index + 1 }}
                    </td>
                    <td>
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <a
                                    href="javascript:void(0);"
                                    class="avatar avatar-xl bg-light-900 p-1"
                                    @click="previewImage(line.product.image)"
                                >
                                    <img
                                        class="object-fit-contain"
                                        :src="
                                            getImagePreview(line.product.image)
                                        "
                                        alt="img"
                                    />
                                </a>
                            </div>
                            <div class="col">
                                <small class="fw-bold">
                                    {{ line.product_name }}
                                </small>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div
                            v-for="(option, index) in line.options_payload"
                            :key="index"
                            class="small mb-3"
                        >
                            <p class="fw-bold mb-0">
                                {{ option.printing_option }}
                            </p>
                            <div v-for="item in option.items" :key="item">
                                <p class="mb-0">
                                    {{ item.size }}:
                                    {{ item.quantity }}
                                </p>
                                <div v-if="!!item.names" class="ms-2">
                                    <p
                                        v-for="(name, index) in item.names"
                                        :key="index"
                                        class="mb-0"
                                    >
                                        {{ index + 1 }})
                                        {{ name }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </td>
                    <td class="text-center">
                        {{ line.quantity }}
                    </td>
                    <td class="text-center">
                        {{ handleUomCode(line.uom_code, line.quantity) }}
                    </td>
                    <td class="text-end">
                        {{ line.unit_price?.formatted || 'N/A' }}
                    </td>
                    <td class="text-end">
                        {{ line.total?.formatted || 'N/A' }}
                    </td>
                </tr>
            </tbody>
            <tfoot ref="tfoot" v-if="$slots.footer">
                <slot name="footer" />
            </tfoot>
        </table>
    </div>

    <VueEasyLightbox
        :visible="lightbox.visible"
        :index="lightbox.index"
        :imgs="lightbox.gallery"
        @hide="lightbox.visible = false"
    >
    </VueEasyLightbox>
</template>

<script setup>
import { getImagePreview, getImageUrl } from '@/helpers/media';
import { pluralize } from '@/helpers/string';
import { capitalize } from 'lodash';
import { reactive } from 'vue';
import VueEasyLightbox from 'vue-easy-lightbox';

defineProps({
    lines: {
        type: Array,
        required: true,
    },
});

const lightbox = reactive({
    visible: false,
    index: 0,
    gallery: [],
});

const previewImage = (image) => {
    if (image) {
        lightbox.gallery = getImageUrl(image);
        lightbox.visible = true;
    }
};

const handleUomCode = (uomCode, count = null) => {
    if (!uomCode) return '-';
    return capitalize(pluralize(uomCode, count));
};
</script>
