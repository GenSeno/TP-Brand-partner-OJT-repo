<template>
  <Head title="Add Purchase Order" />

  <Modal ref="modalRef" max-width="7xl" :close-explicitly="true" v-slot="{ close }">
    <div class="page-header">
      <h4>Add Purchase Order</h4>
    </div>

    <form @submit.prevent="submitForm">
      <div class="page-body new-employee-field">
        <div class="row g-3">
          <!-- Supplier -->
          <div class="col-md-4">
            <label class="form-label required">Supplier</label>
            <vue-select
              v-model="form.data.supplier_id"
              :options="props.suppliers"
              :reduce="o => o.id"
              label="name"
              placeholder="Select supplier"
            />
            <input-error :message="form.errors.supplier_id" />
          </div>

          <div class="col-md-4">
              <label class="form-label">Order Date</label>
              <VueDatePicker
                  v-model="form.data.order_date"
                  placeholder="Select Date"
                  :time-config="{
                      enableTimePicker: false,
                  }"
                  @update:model-value="
                      form.clearErrors('order_date')
                  "
                  :value="currentDate"
                  auto-apply
              />
              
              <input-error :message="form.errors.order_date" />
          </div>

          <!-- Expected Delivery -->
          <div class="col-md-4">
              <label class="form-label">Expected Delivery</label>
                <VueDatePicker
                  v-model="form.data.expected_delivery_date"
                  placeholder="Select Date"
                  :time-config="{
                      enableTimePicker: false,
                  }"
                  @update:model-value="
                      form.clearErrors('expected_delivery_date')
                  "
                  auto-apply
              />
              <input-error :message="form.errors.expected_delivery_date" />
          </div>
        </div>
        <!-- Lines Table -->
        <div class="modal-body-table mt-3">
          <table class="table table-sm table-bordered">
            <thead>
              <tr>
                <th>Item Description</th>
                <th width="15%">Unit Price</th>
                <th width="15%">Quantity</th>
                <th width="15%">Total</th>
                <th width="5%"></th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(line, idx) in form.data.lines" :key="line.temp_id || idx">
                <td>
                  <vue-select
                    v-model="line.inventory_item_id"
                    :options="props.inventory"
                    label="item_name"
                    :reduce="o => o.id"
                    placeholder="Select raw material"
                    :append-to-body="true"
                  />
                  <input-error :message="form.errors[`lines.${idx}.inventory_item_id`]" />
                </td>

                <td>
                  <input-text v-model.number="line.unit_price" />
                  <input-error :message="form.errors[`lines.${idx}.unit_price`]" />
                </td>

                <td>
                  <input-text v-model.number="line.quantity" />
                  <input-error :message="form.errors[`lines.${idx}.quantity`]" />
                </td>

                <td class="text-end">
                  
                  <input-text
                    :value="formatMoney((line.unit_price || 0) * (line.quantity || 0))"
                    disabled
                  />
                </td>

                <td class="text-center">
                  <button
                    type="button"
                    class="btn btn-danger btn-sm"
                    @click="removeLine(idx)"
                  >
                   <i class="feather feather-x"></i>
                  </button>
                </td>
              </tr>
              <tr>
                <td>
                  <vue-select
                    v-model="newLine.item"
                    :options="props.inventory"
                    label="item_name"
                    :reduce="o => o.id"
                    placeholder="Select raw material"
                    :append-to-body="true"
                  />
                </td>
                <td>
                  <input-text v-model.number="newLine.unit_price" class="w-full" placeholder="Unit Price" />
                </td>
                <td>
                  <input-text v-model.number="newLine.quantity" class="w-full" placeholder="Quantity" />
                </td>
                <td class="text-end">
                  <input-text
                    :value="formatMoney((newLine.unit_price || 0) * (newLine.quantity || 0))"
                    disabled
                  />
                </td>
                <td class="text-center">
                    <button
                        type="button"
                        class="btn btn-icon btn-sm btn-primary mx-auto d-block"
                        @click="addLine"
                        :disabled="!newLine.item || !newLine.quantity"
                        title="Add line"
                    >
                        <i class="feather feather-plus"></i>
                    </button>
                </td>
              </tr>
            </tbody>
          </table>
          <small class="text-info mt-3">Click the <span class="text-primary fw-bold">+</span> button to save the row after filling out the form.</small>
          <input-error :message="form.errors.lines" />
        </div>

        <!-- Notes -->
        <div class="mb-3">
          <label class="form-label">Notes</label>
          <textarea
            v-model="form.data.notes"
            class="form-control"
            rows="3"
            placeholder="Additional notes..."
          ></textarea>
          <input-error :message="form.errors.notes" />
        </div>
      </div>

      <div class="page-footer-buttons">
        <div>
          <button type="button" class="btn btn-secondary me-2" @click="close()">Cancel</button>
          <submit-btn :loading="form.processing">Create Purchase Order</submit-btn>
        </div>
      </div>
    </form>
  </Modal>
</template>

<script setup>
import { Head } from '@inertiajs/vue3';
import { useAxiosForm } from '@/composables/axiosForm';
import { emitter } from '@/composables/eventBus';
import * as alert from '@/helpers/alert';
import { useTemplateRef, reactive } from 'vue';
import dayjs from 'dayjs';
import { VueDatePicker } from '@vuepic/vue-datepicker';

const props = defineProps({
  suppliers: Array,
  inventory: Array
});
const modalRef = useTemplateRef('modalRef');

// Form
const form = useAxiosForm({
  supplier_id: '',
  order_date: dayjs().format('YYYY-MM-DD'),
  expected_delivery_date: '',
  notes: '',
  lines: [],
  deleted_line_ids: [],
});

// Current date default
const currentDate = dayjs().format('YYYY-MM-DD');

// New line reactive
const newLine = reactive({
  item: null,
  description: '',
  unit_price: '',
  unit_quantity: '',
  quantity: '',
});

const addLine = () => {

  const selectedItem = props.inventory.find(
    item => item.value === newLine.item
  );


  if (!newLine.item || !newLine.quantity || !newLine.unit_price) {
    alert.showError('Please complete all line fields.');
    return;
  }

  form.data.lines.push({
    temp_id: Date.now() + Math.random(),
    inventory_item_id: newLine.item,
    description: selectedItem.label,
    unit_price: Number(newLine.unit_price),
    quantity: Number(newLine.quantity),
  });


  newLine.item = null;
  newLine.unit_price = '';
  newLine.quantity = '';
};

// Remove line
const removeLine = (idx) => {
  form.data.lines.splice(idx, 1);
};

// Format money (₱)
const formatMoney = (amount) => {
  return new Intl.NumberFormat('en-PH', {
    style: 'currency',
    currency: 'PHP',
    minimumFractionDigits: 2,
  }).format(amount);
};

// Submit
const submitForm = () => {
  if (!form.data.lines.length) {
    alert.showError('Please add at least one item.');
    return;
  }
  form.post(route('admin.purchase-order.store'), {
    onSuccess: ({ data }) => {
      modalRef.value.close();
      emitter.emit('purchase-order:created', data.purchaseOrder || null);
      alert.showSuccess(data.message || 'Purchase Order created successfully.');
    },
  });
};
defineEmits(['modalEvent']);
</script>