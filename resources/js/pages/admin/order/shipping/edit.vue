<template>
    <Head title="Add on Fee" />

    <Modal ref="modalRef" max-width="lg" v-slot="{ close }">
        <div class="page-header">
            <h4>Add on Fee</h4>
        </div>

        <form @submit.prevent="submitForm">
            <div class="page-body new-employee-field">
                <div
                    v-for="(line, index) in form.data.lines"
                    :key="index"
                    class="row g-2 mb-2 align-items-start"
                >
                    <div class="col">
                        <label v-if="index === 0" class="form-label"
                            >Description</label
                        >
                        <input-text
                            v-model="line.description"
                            placeholder="Description"
                        />
                        <input-error
                            :message="
                                form.errors[`lines.${index}.description`]
                            "
                        />
                    </div>
                    <div class="col-auto" style="width: 180px">
                        <label v-if="index === 0" class="form-label required"
                            >Amount</label
                        >
                        <input-text
                            type="number"
                            v-model="line.amount"
                            step="0.01"
                            placeholder="0.00"
                        />
                        <input-error
                            :message="form.errors[`lines.${index}.amount`]"
                        />
                    </div>
                    <div class="col-auto">
                        <label v-if="index === 0" class="form-label"
                            >&nbsp;</label
                        >
                        <button
                            type="button"
                            class="btn btn-sm btn-outline-danger d-block"
                            :disabled="form.data.lines.length <= 1"
                            @click="removeLine(index)"
                        >
                            <i
                                data-feather="trash-2"
                                class="feather-trash-2"
                            ></i>
                        </button>
                    </div>
                </div>

                <button
                    type="button"
                    class="btn btn-sm btn-outline-primary mt-1"
                    @click="addLine"
                >
                    <i
                        data-feather="plus"
                        class="feather-plus me-1"
                    ></i>
                    Add Line
                </button>
            </div>

            <div class="page-footer-buttons">
                <div>
                    <button
                        type="button"
                        class="btn btn-secondary me-2"
                        @click="close()"
                    >
                        Cancel
                    </button>
                    <submit-btn :loading="form.processing">
                        Save
                    </submit-btn>
                </div>
            </div>
        </form>
    </Modal>
</template>

<script setup>
import { useAxiosForm } from '@/composables/axiosForm';
import { emitter } from '@/composables/eventBus';
import * as alert from '@/helpers/alert';
import { Head } from '@inertiajs/vue3';
import { useTemplateRef } from 'vue';

const props = defineProps({
    order: Object,
    billingId: {
        type: [String, Number, null],
        default: null,
    },
});

const modalRef = useTemplateRef('modalRef');

const buildInitialLines = () => {
    const breakdown = props.order.shipping_breakdown;
    if (breakdown?.length > 0) {
        return breakdown.map((item) => ({
            description: item.name || '',
            amount: item.decimal || '',
        }));
    }
    return [{ description: '', amount: '' }];
};

const form = useAxiosForm({
    lines: buildInitialLines(),
});

const addLine = () => {
    form.data.lines.push({ description: '', amount: '' });
};

const removeLine = (index) => {
    if (form.data.lines.length > 1) {
        form.data.lines.splice(index, 1);
    }
};

const submitForm = () => {
    form.data.billing_id = props.billingId;

    form.put(route('admin.order.shipping.update', { order: props.order.id }), {
        onSuccess: () => {
            modalRef.value.close();
            emitter.emit('shipping:updated');
            alert.showSuccess('Add on fee saved successfully.');
        },
    });
};
</script>
