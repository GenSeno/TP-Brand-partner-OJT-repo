<template>
  <Head title="Purchase Orders" />

  <!-- Page Header -->
  <div class="page-header">
    <div class="add-item d-flex">
      <div class="page-title">
        <h4>Purchase Orders</h4>
        <h6>Manage your purchase orders</h6>
      </div>
    </div>
    <div class="page-btn">
      <ModalLink
        navigate
        :href="route('admin.purchase-order.create')"
        class="btn btn-added btn-primary"
        #default="{ loading }"
      >
        <loading-text :loading="loading">
          <vue-feather type="plus-circle" class="me-2" />
          Add Purchase Order
        </loading-text>
      </ModalLink>
    </div>
  </div>

  <!-- Table Card -->
  <div class="card table-list-card">
    <div
      class="card-header d-flex align-items-center justify-content-between flex-wrap row-gap-3"
    >
      <div class="d-flex align-items-center gap-2">
        <dt-search
          v-model="form.filter.search"
          @search="submitFilters"
          placeholder="Search Reference #, Supplier, Status"
        />
      </div>
      <div
        class="d-flex table-dropdown my-xl-auto right-content align-items-center flex-wrap row-gap-3"
      >
     <select-filter
        v-model="form.filter.supplier_id"
        :options="supplierMap"
        label="name"
        value="id"
        name="Supplier"
        @change="submitFilters"
    />
      </div>
    </div>

    <div class="card-body p-0">
      <dt-table
        v-model:sortings="form.sort"
        v-model:perPage="form.per_page"
        v-model:selected="selected"
        :columns="columns"
        :data="props.purchaseOrders.data"
        :total-records="props.purchaseOrders.total"
        :start-record="props.purchaseOrders.from"
        :end-record="props.purchaseOrders.to"
        :links="props.purchaseOrders.links"
        @change="submitFilters"
        :selectable="false"
      >
        <template #reference="{ value }">{{ value }}</template>
        <template #supplier="{ row }">{{ row.supplier?.name || '-' }}</template>
        <template #total="{ row }">{{ row.total?.formatted || '-' }}</template>
        <template #status="{ row }">{{ row.status }}</template>
        <template #created_at="{ value }">{{ dayjs(value).format(dateFormat) }}</template>

        <!-- Action buttons -->
        <template #action="{ row, value }">
          <div class="action-table-data">
            <div class="edit-delete-action">

              <!-- Receive Button -->
              <ModalLink
                navigate
                :href="route('admin.purchase-order.receive', value)"
                class="btn btn-icon btn-secondary-light btn-sm me-2"
                title="Receive"
                :class="{ 'pointer-events-none opacity-50': row.status === 'received' }"
              >
                <i class="ti ti-package"></i>
              </ModalLink>

              <!-- Edit Button -->
              <ModalLink
                navigate
                :href="route('admin.purchase-order.edit', value)"
                class="btn btn-icon btn-outline-light btn-sm me-2"
                title="Edit"
                :class="{ 'disabled pointer-events-none opacity-50': row.status === 'received' }"
              >
                <i class="ti ti-edit"></i>
              </ModalLink>

              <!-- Delete Button -->
              <dt-delete2
                v-if="row.status !== 'received'"
                :record-name="row.reference"
                model-name="purchase-order"
                :url="route('admin.purchase-order.destroy', value)"
                class="btn btn-icon btn-danger-light btn-sm me-2"
                title="Delete"
                :emitter-event="deleteEmitterEvent"
              >
                <i class="feather-trash-2"></i>
              </dt-delete2>

              <!-- Disabled Delete -->
              <button
                v-else
                class="btn btn-icon btn-danger-light btn-sm me-2 opacity-50"
                disabled
                title="Cannot delete received PO"
              >
                <i class="feather-trash-2"></i>
              </button>

            </div>
          </div>
        </template>

      </dt-table>
    </div>
  </div>
</template>

<script setup>
import { onMounted, ref, computed } from 'vue';
import { Head, router, useForm } from '@inertiajs/vue3';
import { removeEmptyValues } from '@/helpers/form';
import dayjs from 'dayjs';
import DashboardLayout from '@/layouts/dashboard-layout.vue';
import { emitter } from '@/composables/eventBus';

defineOptions({ layout: DashboardLayout });

const props = defineProps({
  purchaseOrders: Object,
  filter: Object,
  suppliers: Object,
});

const deleteEmitterEvent = ref('purchase-orders-deleted');
const selected = ref([]);

const columns = [
  { title: 'Reference', dataIndex: 'reference', key: 'reference', sortable: true },
  { title: 'Supplier', dataIndex: 'supplier', key: 'supplier' },
  { title: 'Total', dataIndex: 'total', key: 'total' },
  { title: 'Status', dataIndex: 'status', key: 'status' },
  { title: 'Created', dataIndex: 'created_at', key: 'created_at', sortable: true },
  { title: '', dataIndex: 'id', key: 'action' },
];

const dateFormat = 'DD MMM YYYY';

const form = useForm({
  filter: {
    search: props.filter?.search || '',
    supplier_id: props.filter?.supplier_id || '', // must match backend custom filter
  },
  sort: [],
  per_page: props.purchaseOrders.per_page,
});

const submitFilters = () => {
  form.transform((data) =>
    removeEmptyValues({
      ...data,
      sort: data.sort.join(','),
      per_page: data.per_page === props.filter.default_per_page ? '' : data.per_page,
    }),
    
  ).get(route('admin.purchase-order.index'), {
    preserveState: true,
    preserveScroll: true,
    replace: true,
  });
};

const refreshPage = () => {
  router.get(route('admin.purchase-order.index'), {}, { preserveState: false, preserveScroll: true, replace: true });
};

onMounted(() => {
  emitter.on('purchase-order:created', () => router.reload({ preserveState: true, preserveScroll: true, replace: true }));
  emitter.on('purchase-order:updated', () => router.reload({ preserveState: true, preserveScroll: true, replace: true }));
  emitter.on(deleteEmitterEvent.value, (data) => {
    router.reload({ preserveState: true, preserveScroll: true, replace: true });
    selected.value = selected.value.filter(item => !data.deleted.includes(item.id));
  });
});

const supplierMap = computed(() => {
  return Object.fromEntries(
    props.suppliers.map(s => [s.id, s.name])
  );
});


</script>