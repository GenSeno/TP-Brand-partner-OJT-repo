<template>
    <div>
        <table
            class="table table-sm table-bordered table-static"
            style="table-layout: fixed"
        >
            <thead class="text-center">
                <tr>
                    <td colspan="2" class="fw-bold">
                        {{ material.state }}
                    </td>
                </tr>
                <tr>
                    <th style="width: 65%">Item</th>
                    <th style="width: 35%">Qty</th>
                </tr>
            </thead>
            <tbody>
                <template v-if="material.type === 'input'">
                    <tr v-for="(item, index) in form.data.items" :key="index">
                        <td class="align-top">
                            <p class="fw-bold required my-1">
                                {{ _item(item.inventory_item_id)?.item_name }}
                            </p>
                            <small class="text-muted">
                                Stock:
                                {{
                                    _item(item.inventory_item_id)
                                        ?.current_stock ?? '—'
                                }}
                                {{ _item(item.inventory_item_id)?.uom_code }}
                            </small>
                        </td>
                        <td class="align-top">
                            <div class="input-group">
                                <input
                                    v-model="item.amount_used"
                                    type="number"
                                    class="form-control form-control-sm"
                                    min="0"
                                    step="0.001"
                                    :max="
                                        _item(item.inventory_item_id)
                                            ?.current_stock
                                    "
                                    @change="
                                        form.clearErrors(
                                            `items.${index}.amount_used`,
                                        )
                                    "
                                />
                                <span class="input-group-text">{{
                                    _item(item.inventory_item_id)?.uom_code
                                }}</span>
                            </div>
                            <input-error
                                :message="
                                    form.errors[`items.${index}.amount_used`]
                                "
                            />
                        </td>
                    </tr>
                </template>
                <template v-if="material.type === 'multiple'">
                    <tr v-for="(item, index) in form.data.items" :key="index">
                        <td class="align-top">
                            <vue-select
                                :options="
                                    availableOptions(item.inventory_item_id)
                                "
                                v-model="item.inventory_item_id"
                                placeholder="Select option"
                                class="vue3-select-sm"
                            />
                            <small
                                v-if="_item(item.inventory_item_id)"
                                class="text-muted"
                            >
                                Stock:
                                {{
                                    _item(item.inventory_item_id)?.current_stock
                                }}
                                {{ _item(item.inventory_item_id)?.uom_code }}
                            </small>
                        </td>
                        <td class="align-top">
                            <div class="d-flex align-items-center gap-2">
                                <div class="input-group">
                                    <input
                                        v-model="item.amount_used"
                                        type="number"
                                        class="form-control form-control-sm"
                                        min="0"
                                        step="0.001"
                                        @change="
                                            form.clearErrors(
                                                `items.${item.inventory_item_id}.amount_used`,
                                            )
                                        "
                                    />
                                    <span class="input-group-text">{{
                                        _item(item.inventory_item_id)
                                            ?.uom_code || '--'
                                    }}</span>
                                </div>
                                <button
                                    v-if="form.data.items.length > 1"
                                    type="button"
                                    class="btn btn-sm btn-soft-danger"
                                    @click="removeItem(index)"
                                >
                                    <i class="feather feather-x"></i>
                                </button>
                            </div>
                            <input-error
                                :message="
                                    form.errors[`items.${index}.amount_used`]
                                "
                            />
                        </td>
                    </tr>
                </template>
                <tr>
                    <td colspan="2">
                        <form @submit.prevent="submiteForm">
                            <div
                                class="d-flex align-items-center justify-content-end flex-wrap gap-2"
                            >
                                <small
                                    v-if="form.processing"
                                    class="text-muted me-auto"
                                    >Saving...</small
                                >
                                <small
                                    v-else-if="form.recentlySuccessful"
                                    class="text-success me-auto"
                                >
                                    <i class="feather feather-check me-1"></i
                                    >Saved!
                                </small>
                                <small
                                    v-else-if="form.errors.general"
                                    class="text-danger me-auto"
                                >
                                    <i
                                        class="feather feather-alert-circle me-1"
                                    ></i
                                    >{{ form.errors.general }}
                                </small>
                                <button
                                    v-if="itemAddable"
                                    type="button"
                                    class="btn btn-sm btn-secondary"
                                    @click="addItem"
                                >
                                    Add Item
                                </button>
                                <submit-btn
                                    type="submit"
                                    class="btn btn-sm btn-primary"
                                    :loading="form.processing"
                                >
                                    Save
                                </submit-btn>
                            </div>
                        </form>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>

<script setup>
import { useAxiosForm } from '@/composables/axiosForm';
import { emitter } from '@/composables/eventBus';
import { computed } from 'vue';
import { watch } from 'vue';

const props = defineProps({
    jobOrder: {
        type: Object,
        required: true,
    },
    material: {
        type: Object,
        required: true,
    },
    inventoryEmitter: {
        type: String,
        default: 'job-order-inventory:updated',
    },
});

const options = computed(() => {
    return props.material.items.map((item) => ({
        label: item.item_name,
        value: item.id,
    }));
});

const availableOptions = (currentItemId) => {
    const otherSelected = form.data.items
        .map((i) => i.inventory_item_id)
        .filter((id) => id !== '' && id !== currentItemId);

    return props.material.items
        .filter((item) => !otherSelected.includes(item.id))
        .map((item) => ({ label: item.item_name, value: item.id }));
};

const form = useAxiosForm({
    job_order_id: props.jobOrder.id,
    items: [],
});

const itemAddable = computed(
    () =>
        props.material.type === 'multiple' &&
        form.data.items.every((fi) => fi.inventory_item_id !== '') &&
        form.data.items.length < props.material.items.length,
);

const addItem = () => {
    form.data.items.push({ inventory_item_id: '', amount_used: '' });
};

const removeItem = (index) => {
    form.data.items.splice(index, 1);
};

const _item = (inventory_item_id) => {
    return props.material.items.find((item) => item.id === inventory_item_id);
};

// Filter saved materials by stage (server now includes stage on each record)
const _savedForStage = () => {
    return props.jobOrder.materials.filter(
        (invItem) =>
            invItem.stage === props.material.stage ||
            // Fallback for legacy records without a stage (matched by item type)
            (invItem.stage == null &&
                props.material.items.some(
                    (item) => item.id === invItem.inventory_item_id,
                )),
    );
};

const submiteForm = () => {
    form.post(route('admin.job-order.set-inventory-usage', props.jobOrder.id), {
        onSuccess: () => {
            emitter.emit(props.inventoryEmitter);
        },
    });
};

watch(
    () => props.material.type,
    () => {
        if (props.material.type === 'input') {
            form.data.items = props.material.items.map((item) => ({
                inventory_item_id: item.id,
                amount_used:
                    _savedForStage().find(
                        (invItem) => invItem.inventory_item_id === item.id,
                    )?.amount_used || '',
            }));
        } else if (props.material.type === 'multiple') {
            const saved = _savedForStage().map((invItem) => ({
                inventory_item_id: invItem.inventory_item_id,
                amount_used: invItem.amount_used,
            }));

            form.data.items =
                saved.length > 0
                    ? saved
                    : [{ inventory_item_id: '', amount_used: '' }];
        } else {
            form.data.items = [];
        }
    },
    { immediate: true },
);
</script>
