<template>
  <Head title="Movement History" />

  <Modal ref="modalRef" max-width="4xl" :close-explicitly="true" v-slot="{ close }">
    <div class="page-header">
      <h4>Movement History</h4>
    </div>

    <div class="page-body">
      <!-- Date Range Filter -->
      <div class="d-flex gap-2 mb-3">
        <VueDatePicker
            v-model="dateRange.from"
            placeholder="Select Date From"
            :time-config="{
                enableTimePicker: false,
            }"
            auto-apply
        />
         <VueDatePicker
            v-model="dateRange.to"
            placeholder="Select Date To"
            :time-config="{
                enableTimePicker: false,
            }"
            auto-apply
        />
        <button class="btn btn-sm btn-primary" @click="filterMovements">Filter</button>
        <button class="btn btn-sm btn-secondary mr-1" @click="resetFilter">Reset</button>
      </div>

      <!-- Inventory Info -->
      <div class="alert alert-light border mb-3">
        <div class="d-flex justify-content-between mb-2">
          <span class="fw-bold">{{ props.inventoryItem.item_name }}</span>
          <span class="badge shadow-none badge-xs badge-soft-info">{{ props.inventoryItem.type }}</span>
        </div>
        <div class="d-flex">
          <span class="text-muted">Current Balance:</span>
          <strong class="ml-1">
            &nbsp;{{ simplifyFloat(props.inventoryItem.current_stock) }}
            {{ props.inventoryItem.unit_measure?.code }}
          </strong>
        </div>
      </div>

      <!-- No Records -->
      <div v-if="filteredMovements.length === 0" class="text-center text-muted py-4">
        No movement records found.
      </div>

      <!-- Transactions Table -->
      <div v-else class="table-responsive" style="max-height: 400px; overflow-y: auto">
        <table class="table table-sm table-bordered text-center mb-0">
          <thead style="position: sticky; top: 0; z-index: 1; background: #fff">
            <tr>
              <th>Date</th>
              <th>Addition ({{ props.inventoryItem.unit_measure?.code }})</th>
              <th>Deduction ({{ props.inventoryItem.unit_measure?.code }})</th>
              <th>Reference No</th>
              <th>Notes</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="movement in filteredMovements" :key="movement.id">
              <!-- Date -->
              <td>{{ dayjs(movement.created_at).format('ddd, DD MMM YYYY') }}, {{ dayjs(movement.created_at).format('DD MMM YYYY HH:mm') }}</td>

              <!-- Addition -->
              <td class="text-success" v-if="movement.type === 'addition'">
                {{ simplifyFloat(movement.amount) }}
              </td>
              <td v-else>—</td>

              <!-- Deduction -->
              <td class="text-danger" v-if="movement.type === 'deduction'">
                {{ simplifyFloat(movement.amount) }}
              </td>
              <td v-else>—</td>

              <!-- Reference No / Source -->
              <td class="text-nowrap">
                <span v-if="movement.adjustment" class="text-muted">Manual Adjustment</span>
                <span v-else-if="movement.source">Job Order #{{ movement.source.id }}</span>
                <span v-else class="text-muted">—</span>
              </td>

              <!-- Notes -->
              <td class="text-start" style="min-width: 140px">
                <small>{{ movement.notes || '—' }}</small>
              </td>
            </tr>

            <!-- Total Row -->
            <tr class="fw-bold bg-light">
              <td>Total</td>
              <td>{{ simplifyFloat(totalAddition) }}</td>
              <td>{{ simplifyFloat(totalDeduction) }}</td>
              <td>—</td>
              <td>—</td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div
        v-if="props.movements.last_page > 1"
        class="d-flex justify-content-between align-items-center mt-3 flex-wrap gap-2"
      >
        <small class="text-muted">
          Showing {{ props.movements.from }}–{{ props.movements.to }} of
          {{ props.movements.total }} records
        </small>
        <div class="btn-group btn-group-sm">
          <Link
            v-for="link in props.movements.links"
            :key="link.label"
            :href="link.url || '#'"
            :class="['btn', link.active ? 'btn-primary' : 'btn-outline-secondary', !link.url ? 'disabled' : '']"
            preserve-scroll
            v-html="link.label"
          />
        </div>
      </div>
    </div>

    <div class="page-footer-buttons">
      <button type="button" class="btn btn-secondary" @click="close()">Close</button>
    </div>
  </Modal>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { useTemplateRef, ref, computed } from 'vue';
import dayjs from 'dayjs';
import { simplifyFloat } from '@/helpers/number';
import { VueDatePicker } from '@vuepic/vue-datepicker';

const props = defineProps({
  inventoryItem: Object,
  movements: Object,
});
const modalRef = useTemplateRef('modalRef');

// Date range filter
const dateRange = ref({ from: '', to: '' });

// Filtered movements computed, sorted ascending
const filteredMovements = computed(() => {
  const filtered = props.movements.data.filter(movement => {
    const date = dayjs(movement.created_at);
    const from = dateRange.value.from ? dayjs(dateRange.value.from) : null;
    const to = dateRange.value.to ? dayjs(dateRange.value.to).endOf('day') : null;

    if (from && date.isBefore(from)) return false;
    if (to && date.isAfter(to)) return false;
    return true;
  });

  // Sort ascending
  return filtered.sort((a, b) => new Date(a.created_at) - new Date(b.created_at));
});

// Totals
const totalAddition = computed(() => {
  return filteredMovements.value
    .filter(m => m.type === 'addition')
    .reduce((sum, m) => sum + Number(m.amount), 0);
});

const totalDeduction = computed(() => {
  return filteredMovements.value
    .filter(m => m.type === 'deduction')
    .reduce((sum, m) => sum + Number(m.amount), 0);
});

// Filter and reset functions
const filterMovements = () => {
  // Reactive computed updates automatically
};

const resetFilter = () => {
  dateRange.value.from = '';
  dateRange.value.to = '';
};
defineEmits(['modalEvent']);
</script>