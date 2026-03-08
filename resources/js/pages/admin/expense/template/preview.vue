<template>
    <div class="row">
        <Header :expense="expense" />
        <!-- ITEM TABLE -->
        <div class="table-responsive" id="print_area">
            <table class="table table-bordered align-middle table-responsive">
                <thead class="table-light text-center">
                    <tr>
                        <th class="fw-bold" width="80">S/N</th>
                        <th class="fw-bold">Item Description</th>
                        <th class="fw-bold" width="80">Qty</th>
                        <th class="fw-bold" width="100">Price</th>
                        <th class="fw-bold" width="100">Total</th>
                    </tr>
                </thead>
                <tbody v-if="Object.keys(lines).length === 0">
                    <tr>
                        <td colspan="5" class="text-center">
                            No Item Selected, Please click the
                             <button
                                @click="gotoItems"
                                class="btn btn-sm btn-light"
                                :class="
                                    route().current('admin.expense.item') ? 'disabled' : ''
                                "
                                >
                                <i data-feather="edit" class="feather-edit px-1"></i>
                                <span class="d-none d-lg-inline ms-1">Edit</span>
                            </button>
                            button.
                        </td>
                    </tr>
                </tbody>
                <tbody v-if="Object.keys(lines).length">
                    <template v-for="(line, value) in lines" :key="line.id">
                        <tr>
                            <!-- Row number -->
                            <td class="text-center">{{ value + 1 }}</td>
                            <!-- Item Description -->
                            <td class="small">
                                {{ line?.expense_account.name }}<br />
                                {{ line?.description }}
                            </td>
                            <!-- Quantity -->
                            <td class="text-center">
                                {{ line.quantity }}
                            </td>
                            <!-- Price -->
                            <td class="text-end">
                                {{ line.price.formatted }}
                            </td>
                            <!-- Total-->
                            <td class="text-end">
                                {{ line.total.formatted }}
                            </td>
                        </tr>
                    </template>
                    <tr>
                        <td
                            colspan="3"
                            class="fst-italic small mt-1 text-muted"
                            style="
                                border-left: 1px solid white !important;
                                border-bottom: 1px solid white !important;
                            "
                        >
                            This is a system generated, no signature is
                            required.
                        </td>
                        <td class="bg-light">Total</td>
                        <td class="text-end fw-bold">
                            {{ expense.sub_total.formatted }}
                        </td>
                    </tr>
                    <template v-if="discountLines.length">
                        <tr v-if="discountLines.length > 1">
                            <td
                                colspan="3"
                                class="fw-bold"
                                style="
                                    border-left: 1px solid white !important;
                                    border-bottom: 1px solid white !important;
                                "
                            ></td>
                            <td class="bg-light">
                                <b>Discount Breakdown:</b
                                ><i class="text-danger">
                                    ( -{{ expense.discount_total.formatted }} )
                                </i>
                            </td>
                            <td class="text-end fw-bold"></td>
                        </tr>
                        <tr
                            v-for="(discount, index) in discountLines"
                            :key="'ship-' + index"
                        >
                            <td
                                colspan="3"
                                class="fw-bold"
                                style="
                                    border-left: 1px solid white !important;
                                    border-bottom: 1px solid white !important;
                                "
                            ></td>
                            <td class="bg-light">
                                &nbsp;&nbsp;<i
                                    >{{ discount.label
                                    }}<span
                                        class="px-1"
                                        v-if="discount.method === 'percentage'"
                                        >({{ discount.value }} %)</span
                                    ></i
                                >
                            </td>
                            <td class="text-end fw-bold text-danger">
                                - {{ discount.format }}
                            </td>
                        </tr>
                    </template>
                    <tr>
                        <td
                            colspan="3"
                            class="fw-bold"
                            style="
                                border-left: 1px solid white !important;
                                border-bottom: 1px solid white !important;
                            "
                        ></td>
                        <td class="fw-bold bg-light">Total Amount Due</td>
                        <td class="text-end fw-bold">
                            {{ expense.total_amount.formatted }}
                        </td>
                    </tr>
                    <tr>
                        <td
                            colspan="3"
                            class="small mt-1 text-muted"
                            style="
                                border-left: 1px solid white !important;
                                border-bottom: 1px solid white !important;
                                border-right: 1px solid white !important;
                            "
                        >
                            Received the amount stated above:
                        </td>
                        <td
                            colspan="2"
                            style="
                                border-right: 1px solid white !important;
                                border-left: 1px solid white !important;
                                border-bottom: 1px solid white !important;
                            "
                        ></td>
                    </tr>
                    <tr>
                        <td
                            colspan="5"
                            style="border: 1px solid white !important"
                        ></td>
                    </tr>
                    <tr>
                        <td
                            colspan="5"
                            style="border: 1px solid white !important"
                        ></td>
                    </tr>
                    <tr>
                        <td
                            colspan="3"
                            class="text-center small text-muted fs-12"
                            style="
                                border-left: 1px solid white !important;
                                border-left: 1px solid white !important;
                                border-bottom: 1px solid white !important;
                                border-right: 1px solid white !important;
                            "
                        >
                            <span class="top-border-text"
                                >Date, Full Name & Signature
                            </span>
                        </td>
                        <td
                            colspan="2"
                            style="border: 1px solid white !important"
                        ></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <Media :expenseId="expense?.id" :existingFiles="expense?.media" />
    </div>
</template>
<style scoped>
.top-border-text {
    display: block;
    border-top: 1px solid #dee2e6;
    padding-top: 6px;
    margin-top: 8px;
    font-weight: 600;
}
</style>
<script setup>
import { ref } from 'vue';
import Header from './header.vue';
import Media from './media.vue';
import { router } from '@inertiajs/vue3';

const props = defineProps({
    expense: Object,
});
const lines = ref(props.expense?.lines);
const discountLines = ref(props.expense?.discount_breakdown);

function gotoItems() {
    router.get(route('admin.expense.item', { expense: props?.expense?.id }));
}

</script>
