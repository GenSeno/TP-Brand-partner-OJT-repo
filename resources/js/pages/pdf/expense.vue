<template>
    <Head title="Expense Report" />

    <div class="pdf-page">
        <div class="pdf-toolbar">
            <button @click="window.print()" class="btn btn-primary">
                <vue-feather type="printer" class="me-2"></vue-feather>
                Print / PDF
            </button>
            <Link :href="route('admin.expenses.index')" class="btn btn-outline-secondary ms-2">
                <vue-feather type="arrow-left" class="me-2"></vue-feather>
                Back to Expenses
            </Link>
        </div>

        <div class="pdf-preview">
            <div class="pdf-page">
                <div class="pdf-header">
                    <div class="pdf-brand">
                        <h2>Expense Report</h2>
                        <p>{{ expense.reference }}</p>
                    </div>
                    <div class="pdf-meta">
                        <p><strong>Date:</strong> {{ formatDate(expense.date) }}</p>
                        <p><strong>Status:</strong> {{ expense.status }}</p>
                    </div>
                </div>

                <div class="pdf-divider"></div>

                <div class="pdf-supplier" v-if="expense.supplier">
                    <h4>Supplier</h4>
                    <p>{{ expense.supplier.name }}</p>
                    <p v-if="expense.supplier.email">{{ expense.supplier.email }}</p>
                    <p v-if="expense.supplier.phone">{{ expense.supplier.phone }}</p>
                </div>

                <div class="pdf-divider"></div>

                <table class="pdf-table">
                    <thead>
                        <tr>
                            <th>Account</th>
                            <th>Description</th>
                            <th class="text-end">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="line in expense.lines" :key="line.id">
                            <td>{{ line.expense_account?.name || '—' }}</td>
                            <td>{{ line.description || '—' }}</td>
                            <td class="text-end">{{ formatCurrency(line.total) }}</td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="2" class="text-end">Total</th>
                            <th class="text-end">{{ formatCurrency(expense.total) }}</th>
                        </tr>
                    </tfoot>
                </table>

                <div class="pdf-divider"></div>

                <div class="pdf-footer">
                    <p>{{ expense.notes || '' }}</p>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3';

const props = defineProps({
    expense: Object,
});

const window = window;

const formatCurrency = (amount) =>
    new Intl.NumberFormat('en-PH', {
        style: 'currency',
        currency: 'PHP',
    }).format(amount / 100);

const formatDate = (date) =>
    new Date(date).toLocaleDateString('en-PH', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
    });
</script>

<style scoped>
.pdf-page {
    font-family: 'Public Sans', sans-serif;
    background: #f5f5f5;
    min-height: 100vh;
    padding: 20px;
}

.pdf-toolbar {
    max-width: 800px;
    margin: 0 auto 20px;
    display: flex;
    align-items: center;
}

.pdf-preview {
    max-width: 800px;
    margin: 0 auto;
}

.pdf-page {
    background: #fff;
    padding: 40px;
    box-shadow: 0 2px 12px rgba(0,0,0,0.08);
    border-radius: 8px;
}

.pdf-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
}

.pdf-brand h2 {
    margin: 0 0 4px;
    font-size: 22px;
    font-weight: 800;
}

.pdf-brand p {
    margin: 0;
    font-size: 13px;
    color: #888;
}

.pdf-meta p {
    margin: 0 0 4px;
    font-size: 13px;
    color: #555;
}

.pdf-divider {
    height: 1px;
    background: #e0e0e0;
    margin: 24px 0;
}

.pdf-supplier h4 {
    margin: 0 0 8px;
    font-size: 14px;
    font-weight: 700;
    color: #333;
}

.pdf-supplier p {
    margin: 0 0 4px;
    font-size: 13px;
    color: #666;
}

.pdf-table {
    width: 100%;
    border-collapse: collapse;
}

.pdf-table th {
    text-align: left;
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.06em;
    color: #888;
    padding: 8px 12px;
    border-bottom: 2px solid #e0e0e0;
}

.pdf-table td {
    padding: 10px 12px;
    font-size: 13px;
    color: #333;
    border-bottom: 1px solid #f0f0f0;
}

.pdf-table tfoot th {
    padding: 12px;
    font-size: 14px;
    color: #1a1a1a;
    border-bottom: none;
    border-top: 2px solid #e0e0e0;
}

.text-end {
    text-align: right;
}

.pdf-footer {
    font-size: 13px;
    color: #888;
}

@media print {
    .pdf-toolbar { display: none; }
    .pdf-page { box-shadow: none; padding: 20px; }
    .pdf-preview { max-width: 100%; }
}
</style>
