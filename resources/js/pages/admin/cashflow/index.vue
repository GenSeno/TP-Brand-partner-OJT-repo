<template>
    <Head title="Cash Flow" />

    <div class="page-header">
        <div class="add-item d-flex">
            <div class="page-title">
                <h4>Cash Flow</h4>
                <h6>Monitor your Cash Flow</h6>
            </div>
        </div>
        <ul class="table-top-head">
            <li>
                <a data-bs-toggle="tooltip" data-bs-placement="top" title="Pdf">
                    <img src="/img/icons/pdf.svg" alt="img" />
                </a>
            </li>
            <li>
                <a
                    data-bs-toggle="tooltip"
                    data-bs-placement="top"
                    title="Excel"
                >
                    <img src="/img/icons/excel.svg" alt="img" />
                </a>
            </li>
            <li>
                <a
                    data-bs-toggle="tooltip"
                    data-bs-placement="top"
                    title="Refresh"
                    @click="refreshPage"
                >
                    <i class="ti ti-refresh"></i>
                </a>
            </li>
            <li>
                <a
                    data-bs-toggle="tooltip"
                    data-bs-placement="top"
                    title="Collapse"
                    id="collapse-header"
                    @click="toggleHeader"
                >
                    <i class="ti ti-chevron-up"></i>
                </a>
            </li>
        </ul>
    </div>
    <div class="row">
        <div class="col-xl-3 col-sm-6 col-12 d-flex">
            <div class="dash-count bg-success">
                <div class="dash-counts">
                    <h4 class="mb-1">
                        {{ formatAmount1(counts.posted_payments) }}
                    </h4>
                    <p class="text-white mb-0">
                        Total Posted Payments For This Month
                    </p>
                </div>
                <div class="dash-imgs">
                    <i data-feather="trash-2" class="feather-plus-circle"></i>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6 col-12 d-flex">
            <div class="dash-count bg-primary">
                <div class="dash-counts">
                    <h4 class="mb-1">
                        {{ formatAmount1(counts.posted_expenses) }}
                    </h4>
                    <p class="text-white mb-0">
                        Total Posted Expenses For This Month
                    </p>
                </div>
                <div class="dash-imgs">
                    <i data-feather="trash-2" class="feather-minus-circle"></i>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-sm-6 col-12 d-flex">
            <div class="dash-count bg-dark">
                <div class="dash-counts">
                    <h4 class="mb-1">
                        {{ formatAmount1(counts.expenses_for_posting) }}
                    </h4>
                    <p class="text-white mb-0">
                        Total Debit For Posting This Month
                    </p>
                </div>
                <div class="dash-imgs">
                    <i data-feather="trash-2" class="feather-minus"></i>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6 col-12 d-flex">
            <div class="dash-count bg-cyan">
                <div class="dash-counts">
                    <h4 class="mb-1">
                        {{ formatAmount1(counts.payments_for_posting) }}
                    </h4>
                    <p class="text-white mb-0">
                        Total Credit For Posting This Month
                    </p>
                </div>
                <div class="dash-imgs">
                    <i data-feather="trash-2" class="feather-plus"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="card table-list-card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th>Mode of Payment</th>
                            <th class="text-end">Balance</th>
                            <th class="text-end">Debit for Posting</th>
                            <th class="text-end">Credit for Posting</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        <tr v-if="!account.data.length">
                            <td colspan="5" class="text-center text-muted py-4">
                                No cash flow data available
                            </td>
                        </tr>

                        <tr v-for="row in account.data" :key="row.id">
                            <td class="fw-semibold">
                                {{ row.payment_method }}
                            </td>

                            <td class="text-end fw-bold">
                                {{ formatAmount(row.balance) }}
                            </td>

                            <td class="text-end text-danger">
                                {{ formatAmount(row.debit) }}
                            </td>

                            <td class="text-end text-success">
                                {{ formatAmount(row.credit) }}
                            </td>

                            <td class="text-center">
                                <Link
                                    :href="route('admin.cashflow.show', row.id)"
                                    class="btn btn-outline-light btn-sm me-2"
                                >
                                    <i
                                        data-feather="eye"
                                        class="feather-eye"
                                    ></i>
                                </Link>
                            </td>
                        </tr>
                    </tbody>

                    <!-- Footer for totals -->
                    <tfoot>
                        <tr class="fw-bold">
                            <td class="text-start">Total</td>
                            <td class="text-end">
                                {{ formatAmount(totalBalance) }}
                            </td>
                            <td class="text-end text-danger">
                                {{ formatAmount(totalDebit) }}
                            </td>
                            <td class="text-end text-success">
                                {{ formatAmount(totalCredit) }}
                            </td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</template>
<script setup>
import { ref, computed, onMounted } from 'vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { removeEmptyValues } from '@/helpers/form';
import * as alert from '@/helpers/alert';
import { emitter } from '@/composables/eventBus';
import DashboardLayout from '@/layouts/dashboard-layout.vue';

defineOptions({
    layout: DashboardLayout,
});

const props = defineProps({
    account: Object,
    errors: Object,
    auth: Object,
    flash: Object,
    counts: Object,
});

const formatAmount = (value) => {
    return new Intl.NumberFormat('en-PH', {
        style: 'currency',
        currency: 'PHP',
        minimumFractionDigits: 2, // optional, adds .00
    }).format(value ?? 0);
};

const formatAmount1 = (value) => {
    return new Intl.NumberFormat('en-PH', {
        minimumFractionDigits: 2, // optional, adds .00
    }).format(value ?? 0);
};

const totalBalance = computed(() => {
    return props.account.data.reduce((sum, row) => sum + (row.balance ?? 0), 0);
});

const totalDebit = computed(() => {
    return props.account.data.reduce((sum, row) => sum + (row.debit ?? 0), 0);
});

const totalCredit = computed(() => {
    return props.account.data.reduce((sum, row) => sum + (row.credit ?? 0), 0);
});
</script>
