<template>
    <!-- HEADER -->
    <div class="row">
        <!-- COMPANY INFO -->
        <div class="col-md-6">
            <img
                src="/img/logo/logo-pdf.png"
                alt="Company Logo"
                style="width: 160px"
                class="mb-2"
            />
        </div>
        <div class="col-md-6 text-end">
            <h3 class="fw-bold">PAYMENT VOUCHER</h3>
            <h4 class="fw-bold text-muted">{{ expense.reference }}</h4>
        </div>
    </div>
    <div class="d-flex border-bottom mb-2">
        <div class="col-md-12">
            <Company />
        </div>
    </div>
    <div class="d-flex justify-content-end">
        <ModalLink
            navigate
            :href="route('admin.expense.edit', expense.id)"
            class="btn btn-light btn-sm"
            title="Edit"
        >
            <i data-feather="edit" class="feather-edit px-1"></i> Edit Info
        </ModalLink>
    </div>
    <div class="d-flex mb-2">
        <!-- BILL TO -->
        <div class="col-md-6 mb-4 mt-4">
            <span class="text-muted">PAY TO:</span>
            <div class="small text-muted mt-1">
                <!-- Name is always required -->
                <span class="fw-bold text-black">{{ supplier.name }}</span
                ><br />

                <!-- Only show if contact_person exists -->
                <template v-if="supplier.contact_person">
                    {{ supplier.contact_person }}<br />
                </template>

                <!-- Only show if address exists -->
                <template v-if="supplier.address">
                    {{ supplier.address }}<br />
                </template>

                <!-- Only show if city exists -->
                <template v-if="supplier.city">
                    {{ supplier.city }}<br />
                </template>

                <!-- Province + postcode + country -->
                <template
                    v-if="
                        supplier.province || supplier.postcode || country?.name
                    "
                >
                    {{ supplier.province ? supplier.province : '' }}
                    {{ supplier.postcode ? ', ' + supplier.postcode : '' }}
                    {{ country?.name ? ', ' + country.name : '' }}<br />
                </template>

                <!-- Email / Mobile -->
                <template v-if="supplier.email || supplier.phone">
                    Email: {{ supplier.email ? supplier.email : '-' }} / Mobile:
                    {{ supplier.phone ? '+' + supplier.phone : '-' }}
                </template>
            </div>
        </div>
        <!-- DATE TO -->
        <div class="col-md-6 mb-4 mt-4">
            <span class="text-muted">&nbsp;</span>
            <div class="small text-muted mt-1">
                <table class="medium text-muted table-responsive">
                    <tbody>
                        <tr>
                            <td class="fw-bold text-black pr-4">Date</td>
                            <td>&nbsp;&nbsp;&nbsp;</td>
                            <td>
                                :
                                {{
                                    expense?.expense_date
                                        ? dayjs(expense.expense_date).format(
                                              dateFormat,
                                          )
                                        : 'Not Set'
                                }}
                            </td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-black pr-4">
                                Mode of Payment
                            </td>
                            <td>&nbsp;&nbsp;&nbsp;</td>
                            <td class="text-capitalize">
                                {{
                                    expense?.payment_method === 'bank'
                                        ? 'Bank Transfer'
                                        : expense?.payment_method
                                          ? expense?.payment_method
                                          : 'Not Set'
                                }}
                            </td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-black pr-4">
                                Payment Reference
                            </td>
                            <td>&nbsp;&nbsp;&nbsp;</td>
                            <td>
                                :
                                {{
                                    expense?.reference_no
                                        ? expense?.reference_no
                                        : 'Not Set'
                                }}
                            </td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-black pr-4">
                                Payment Date
                            </td>
                            <td>&nbsp;&nbsp;&nbsp;</td>
                            <td>
                                :
                                {{
                                    expense?.payment_date
                                        ? dayjs(expense?.payment_date).format(
                                              dateFormat,
                                          )
                                        : 'Not Set'
                                }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>
<script setup>
import { onMounted, onUnmounted, ref } from 'vue';
import dayjs from 'dayjs';
import { emitter } from '@/composables/eventBus';
import Company from '../../company-info.vue';

const props = defineProps({
    expense: Object,
});

// Just use the original prop object
let expense = ref(props.expense);

// Supplier & country (normal objects)
let supplier = ref(props.expense.supplier);
let country = ref(props.expense.supplier.country);

// Date format
const dateFormat = 'MMM. DD, YYYY';

// Update expense when modal emits
const updateExpenseHandler = (updatedExpense) => {
    expense.value = { ...updatedExpense };
    supplier.value = { ...updatedExpense.supplier };
};

onMounted(() => {
    emitter.on('expenseheader:updated', updateExpenseHandler);
});

onUnmounted(() => {
    emitter.off('expenseheader:updated', updateExpenseHandler);
});
</script>
