<template>
    <div>
        <Head title="Edit Order Item" />

        <Modal ref="modalRef" max-width="lg" :close-explicitly="true">
            <div class="page-header">
                <h4>Edit Item</h4>
                <p class="text-muted mb-0">
                    {{ line.description }} &mdash;
                    <span class="fst-italic">{{ line.option }}</span>
                </p>
            </div>

            <form @submit.prevent="submitForm">
                <div class="row page-body g-3">
                    <!-- Quantity -->
                    <div class="col-md-6">
                        <label class="form-label required">Quantity</label>
                        <input
                            type="number"
                            min="1"
                            class="form-control"
                            v-model.number="quantity"
                        />
                    </div>

                    <!-- Price -->
                    <div class="col-md-6">
                        <label class="form-label required">Price</label>
                        <input
                            type="number"
                            min="0"
                            step="0.01"
                            class="form-control"
                            v-model.number="price"
                        />
                    </div>

                    <!-- Set Names -->
                    <div class="col-12" v-if="hasNames">
                        <div
                            class="d-flex align-items-center justify-content-between mb-2"
                        >
                            <label class="form-label mb-0">Names</label>
                            <button
                                type="button"
                                class="btn btn-xs btn-dark"
                                @click="openSetNamesOverlay"
                            >
                                {{ quantity > 1 ? 'Set Names' : 'Set Name' }}
                            </button>
                        </div>
                        <div v-if="names.length" class="small text-muted">
                            <span v-for="(name, idx) in names" :key="idx">
                                {{ idx + 1 }}. {{ name || '—'
                                }}<span v-if="idx < names.length - 1">, </span>
                            </span>
                        </div>
                    </div>

                    <!-- Reason -->
                    <div class="col-12">
                        <label class="form-label required"
                            >Reason for changes</label
                        >
                        <textarea
                            v-model="reason"
                            class="form-control"
                            rows="2"
                            placeholder="Enter reason for altering the details..."
                            required
                        ></textarea>
                    </div>
                </div>

                <!-- Footer -->
                <div class="page-footer-buttons mt-3 text-end">
                    <button
                        type="button"
                        class="btn btn-secondary"
                        @click="modalRef.close()"
                    >
                        Cancel
                    </button>
                    <submit-btn
                        :loading="form.processing"
                        :disabled="!reason.trim()"
                        >Update Item</submit-btn
                    >
                </div>
            </form>

            <!-- Set Names Overlay -->
            <div v-if="showSetNamesOverlay" class="overlay">
                <div
                    :class="[
                        'overlay-content',
                        { 'two-columns': tempNames.length > 10 },
                    ]"
                >
                    <h5 class="mb-2">Set Names</h5>
                    <div class="names-grid">
                        <div
                            v-for="(name, idx) in tempNames"
                            :key="idx"
                            class="mb-2"
                        >
                            <input
                                type="text"
                                class="form-control"
                                v-model="tempNames[idx]"
                                :placeholder="`Name ${idx + 1}`"
                            />
                        </div>
                    </div>
                    <div class="mt-3 text-end">
                        <button
                            class="btn btn-secondary me-2"
                            @click="showSetNamesOverlay = false"
                        >
                            Cancel
                        </button>
                        <button class="btn btn-primary" @click="saveNames">
                            Set
                        </button>
                    </div>
                </div>
            </div>
        </Modal>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import { useAxiosForm } from '@/composables/axiosForm';
import { emitter } from '@/composables/eventBus';
import * as alert from '@/helpers/alert';
import { Head, router } from '@inertiajs/vue3';

const props = defineProps({
    order: Object,
    line: Object,
});

const modalRef = ref(null);
const reason = ref('');
const quantity = ref(props.line.quantity);
const price = ref(props.line.unit_price?.decimal || 0);
const names = ref(props.line.meta?.names || []);
const hasNames = (props.line.option || '').toUpperCase().includes('WITH NAME');

// Set Names overlay
const showSetNamesOverlay = ref(false);
const tempNames = ref([]);

const openSetNamesOverlay = () => {
    const qty = quantity.value || 1;
    tempNames.value = [
        ...(names.value.length ? names.value : Array(qty).fill('')),
    ];

    if (tempNames.value.length < qty)
        tempNames.value.push(...Array(qty - tempNames.value.length).fill(''));
    if (tempNames.value.length > qty)
        tempNames.value = tempNames.value.slice(0, qty);

    showSetNamesOverlay.value = true;
};

const saveNames = () => {
    names.value = [...tempNames.value];
    showSetNamesOverlay.value = false;
};

// Submit
const form = useAxiosForm({});

const submitForm = () => {
    if (!reason.value.trim()) return;

    form.submit(
        'put',
        route('admin.order.item.update', {
            order: props.order.id,
            item: props.line.id,
        }),
        {
            data: {
                quantity: quantity.value,
                price: price.value,
                meta: { names: names.value },
                reason: reason.value,
            },
            onSuccess: (res) => {
                alert.showSuccess(
                    res.data?.message || 'Item updated successfully.',
                );
                modalRef.value?.close();
                emitter.emit('order:item-updated');
            },
        },
    );
};
</script>

<style scoped>
.overlay {
    position: fixed;
    inset: 0;
    background: rgba(0, 0, 0, 0.5);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 1050;
}
.overlay-content {
    background: white;
    border-radius: 8px;
    width: 90%;
    max-width: 600px;
    padding: 1rem;
    max-height: 80vh;
    overflow-y: auto;
}
.names-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 0.5rem;
}
.overlay-content.two-columns .names-grid {
    grid-template-columns: 1fr 1fr;
    gap: 0.5rem 1rem;
}
</style>
