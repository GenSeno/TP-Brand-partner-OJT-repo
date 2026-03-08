<template>
    <div class="row">
        <Header :expense="expense" />

        <!-- Add Item Button -->
        <div class="d-flex justify-content-end mb-2">
            <a @click.prevent="addItem" class="btn btn-primary btn-sm">
                <i data-feather="plus-circle" class="feather-plus-circle"></i>
                Add Item
            </a>
            <ModalLink
                href="#"
                class="btn btn-secondary btn-sm mx-1"
                title="Edit"
                @click.prevent="openAdjustmentModal"
            >
                <i data-feather="plus-circle" class="feather-plus-circle"></i>
                Add Discount
            </ModalLink>
        </div>

        <div class="table-responsive">
            <form @submit.prevent="submitForm">
                <table
                    class="table table-bordered align-middle table-responsive"
                >
                    <thead class="table-light text-center">
                        <tr>
                            <th class="fw-bold" width="80">S/N</th>
                            <th class="fw-bold">Item Description</th>
                            <th class="fw-bold" width="150">Qty</th>
                            <th class="fw-bold" width="150">Price</th>
                            <th class="fw-bold" width="150">Total</th>
                            <th class="fw-bold" width="100"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="(line, index) in lines"
                            :key="line.id ?? index"
                        >
                            <td class="text-center">{{ index + 1 }}</td>
                            <td>
                                <select
                                    v-model="line.expense_account_id"
                                    class="form-select"
                                >
                                    <option value="0" disabled>
                                        Select Account
                                    </option>
                                    <option
                                        v-for="(
                                            label, value
                                        ) in expense_accounts"
                                        :key="value"
                                        :value="label.value"
                                    >
                                        {{ label.label }}
                                    </option>
                                </select>
                                <input-error
                                    :message="
                                        form.errors[
                                            `expense_lines.${index}.expense_account_id`
                                        ]
                                    "
                                />
                                <textarea
                                    v-model="line.description"
                                    rows="2"
                                    class="form-control form-control-sm mt-2"
                                    placeholder="Enter description"
                                ></textarea>
                            </td>

                            <td>
                                <input
                                    type="number"
                                    min="1"
                                    v-model.number="line.qty"
                                    class="form-control form-control-sm text-center"
                                />
                                <input-error
                                    :message="
                                        form.errors[
                                            `expense_lines.${index}.qty`
                                        ]
                                    "
                                />
                            </td>

                            <td>
                                <input
                                    type="number"
                                    min="0"
                                    step="0.01"
                                    v-model.number="line.price"
                                    class="form-control form-control-sm text-end"
                                />
                                <input-error
                                    :message="
                                        form.errors[
                                            `expense_lines.${index}.price`
                                        ]
                                    "
                                />
                            </td>

                            <td class="text-end">
                                ₱ {{ formatter.format(lineTotal(line)) }}
                            </td>

                            <td class="text-center">
                                <dt-delete2
                                    v-if="line.id"
                                    :record-name="'record'"
                                    model-name="expense_lines"
                                    class="btn btn-danger-light btn-sm me-2"
                                    title="Delete"
                                    :url="
                                        route(
                                            'admin.expense.lines.destroy',
                                            line.id,
                                        )
                                    "
                                    :emitter-event="
                                        deleteExpenseLineEmitterEvent
                                    "
                                >
                                    <i
                                        data-feather="trash-2"
                                        class="feather-trash-2"
                                    ></i>
                                </dt-delete2>

                                <button
                                    v-else
                                    type="button"
                                    class="btn btn-danger-light btn-sm me-2"
                                    @click="removeItem(index)"
                                >
                                    <i
                                        data-feather="trash-2"
                                        class="feather-trash-2"
                                    ></i>
                                </button>
                            </td>
                        </tr>
                    </tbody>

                    <!-- Total Amount & Discounts -->
                    <tfoot>
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
                            <td class="fw-bold bg-light">Total</td>
                            <td class="text-end fw-bold">
                                ₱ {{ formatter.format(subTotal) }}
                            </td>
                            <td></td>
                        </tr>

                        <!-- Discounts -->
                        <template v-if="expense.discount_breakdown?.length">
                            <tr v-if="expense.discount_breakdown.length > 1">
                                <td
                                    colspan="3"
                                    class="fw-bold"
                                    style="
                                        border-left: 1px solid white !important;
                                        border-bottom: 1px solid white !important;
                                    "
                                ></td>
                                <td class="bg-light">
                                    <b>Discount Breakdown:</b>
                                    <i class="text-danger">
                                        ( -{{ formatter.format(totalDiscount) }}
                                        )
                                    </i>
                                </td>
                                <td></td>
                                <td></td>
                            </tr>

                            <tr
                                v-for="(discount, index) in computedDiscounts"
                                :key="'discount-' + index"
                            >
                                <td
                                    colspan="3"
                                    style="
                                        border-left: 1px solid white !important;
                                        border-bottom: 1px solid white !important;
                                    "
                                ></td>
                                <td>
                                    <i>
                                        {{ discount.label }}
                                        <span
                                            v-if="
                                                discount.method === 'percentage'
                                            "
                                        >
                                            ({{ discount.value }}%)
                                        </span>
                                    </i>
                                </td>
                                <td class="text-end fw-bold text-danger">
                                    - ₱ {{ discount.format }}
                                </td>
                                <td></td>
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
                            ></td>
                            <td class="fw-bold">Total Amount Due</td>
                            <td class="text-end fw-bold">
                                ₱ {{ formatter.format(amountDue) }}
                            </td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </form>
        </div>
        <Media :expenseId="expense?.id" :existingFiles="expense?.media" />
    </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import Header from './header.vue';
import { useAxiosForm } from '@/composables/axiosForm';
import { emitter } from '@/composables/eventBus';
import * as alert from '@/helpers/alert';
import { router } from '@inertiajs/vue3';
import Media from './media.vue';

const props = defineProps({
    expense: Object,
    expense_accounts: Object,
});

const expense = ref(props.expense);

const lines = ref(
    (props.expense?.lines ?? []).map((line) => ({
        id: line.id || null,
        expense_account_id: line.expense_account_id || 0,
        description: line.description || '',
        qty: line.quantity || 1,
        price: line.price?.decimal || 0,
    })),
);

const formatter = new Intl.NumberFormat('en-US', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2,
});

const deleteExpenseLineEmitterEvent = ref('expenseline:deleted');
const updateExpenseEmitterEvent = ref('expense:updated');
const stopLoadingEmitterEvent = ref('header:saveFinished');
const submitEmitterEvent = ref('header:submitExpenseLines');
const discountEmitterEvent = ref('discountexpense:updated');

const subTotal = computed(() =>
    lines.value.reduce(
        (sum, line) => sum + (line.qty || 0) * (line.price || 0),
        0,
    ),
);

const computedDiscounts = computed(() => {
    if (!expense.value.discount_breakdown?.length) return [];

    return expense.value.discount_breakdown.map((discount) => {
        let amount = 0;

        if (discount.method === 'percentage') {
            amount = subTotal.value * (discount.value / 100);
        } else {
            amount = discount.decimal ?? 0;
        }

        return {
            ...discount,
            amount,
            format: formatter.format(amount),
        };
    });
});

const totalDiscount = computed(() =>
    computedDiscounts.value.reduce((sum, d) => sum + d.amount, 0),
);

const amountDue = computed(() =>
    Math.max(subTotal.value - totalDiscount.value, 0),
);

const hasUnsavedRows = computed(() => lines.value.some((line) => !line.id));

const lineTotal = (line) => (line.qty || 0) * (line.price || 0);

const addItem = () => {
    lines.value.push({
        id: null,
        expense_account_id: 0,
        description: '',
        qty: 1,
        price: 0,
    });
};

const removeItem = (index) => lines.value.splice(index, 1);

const form = useAxiosForm({ lines: [], status: 'draft' });

const submitLines = (status) => {
    if (!lines.value.length) {
        alert.showError('Please add at least one item.');
        emitter.emit(stopLoadingEmitterEvent.value);
        return;
    }

    const payload = lines.value.map((line) => ({
        id: line.id,
        expense_id: expense.value.id,
        expense_account_id: line.expense_account_id,
        description: line.description,
        qty: line.qty,
        price: line.price,
    }));

    form.submit('post', route('admin.expense.lines.store', expense.value.id), {
        data: { expense_lines: payload, status },
        onSuccess: (response) => {
            alert.showSuccess(
                response.data.message || 'Items saved successfully!',
            );
            updateLines(response.data.expense_lines, response.data.expense);
            emitter.emit(
                updateExpenseEmitterEvent.value,
                response.data.expense,
            );
        },
        onFinish: () => emitter.emit(stopLoadingEmitterEvent.value),
    });
};

const updateLines = (expenseLinesData = null, updatedExpense = null) => {
    if (expenseLinesData) {
        lines.value = expenseLinesData.map((line) => ({
            id: line.id,
            expense_account_id: line.expense_account_id,
            description: line.description,
            qty: line.quantity,
            price: line.price?.decimal ?? line.price,
        }));
    }

    if (updatedExpense) {
        expense.value = {
            ...expense.value,
            discount_breakdown: updatedExpense.discount_breakdown ?? [],
            discount_total: updatedExpense.discount_total ?? 0,
        };
    }
};

onMounted(() => {
    emitter.on(submitEmitterEvent.value, submitLines);

    emitter.on(deleteExpenseLineEmitterEvent.value, (data) => {
        alert.showSuccess(data.message || 'Items deleted successfully!');
        updateLines(data.expense_lines, data.expense);
    });

    emitter.on(discountEmitterEvent.value, (data) => {
        updateLines(null, data.expense);
    });
});

onUnmounted(() => {
    emitter.off(submitEmitterEvent.value, submitLines);
    emitter.off(deleteExpenseLineEmitterEvent.value);
    emitter.off(discountEmitterEvent.value);
});

const openAdjustmentModal = () => {
    if (hasUnsavedRows.value) {
        alert.showError('Please save items before applying discount.');
        return;
    }

    router.visit(route('admin.expense.adjustment', expense.value.id));
};
</script>
