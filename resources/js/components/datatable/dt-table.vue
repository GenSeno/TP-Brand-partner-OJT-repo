<template>
    <div class="mx-4 my-1">
        <p v-show="selected.length > 0" class="mb-0">
            {{ selected.length }} record(s) selected
        </p>
        <p v-show="selected.length === 0" class="mb-0">&nbsp;</p>
    </div>
    <div class="table-responsive">
        <table class="table table-hover table-center mb-0">
            <DtHeader
                v-model="sortings"
                :columns="columns"
                :selectable="selectable"
                :checked="selected.length === data.length && data.length > 0"
                @checked="toggleSelectRows"
                @sort="(emit('change:sortings'), emit('change'))"
            />
            <tbody v-if="data.length > 0">
                <tr v-for="(record, index) in data" :key="index">
                    <td
                        v-if="props.selectable"
                        @click="handleRowClick(record)"
                        class="selectable"
                    >
                        <div class="form-check form-check-md">
                            <input
                                class="form-check-input"
                                type="checkbox"
                                :checked="isRowSelected(record.id)"
                                :disabled="record.is_deletable !== false"
                            />
                        </div>
                    </td>
                    <td
                        v-for="column in columns"
                        :key="column.key"
                        :class="[column.class ?? '']"
                        :style="column.sortable ? { paddingLeft: '2rem' } : {}"
                    >
                        <slot
                            :name="column.key"
                            :row="record"
                            :value="getNestedValue(record, column.dataIndex)"
                            >{{
                                getNestedValue(record, column.dataIndex)
                            }}</slot
                        >
                    </td>
                </tr>
            </tbody>
            <tbody v-else>
                <tr>
                    <td
                        :colspan="
                            selectable ? columns.length + 1 : columns.length
                        "
                        class="text-center py-4"
                    >
                        No matching records found
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="px-4 py-3">
        <div class="row justify-content-center g-2">
            <div class="col-md-auto">
                <DtInfo
                    v-model="perPage"
                    :page-row-options="pageRowOptions"
                    :total-records="totalRecords"
                    :start-record="startRecord"
                    :end-record="endRecord"
                    @change="(emit('change:perPage'), emit('change'))"
                />
            </div>
            <div class="col-md-auto ms-auto">
                <DtPager :links="links" />
            </div>
        </div>
    </div>
</template>

<script setup>
import DtHeader from './dt-header.vue';
import DtInfo from './dt-info.vue';
import DtPager from './dt-pager.vue';

const emit = defineEmits([
    'change',
    'change:selected',
    'change:sortings',
    'change:perPage',
]);

const sortings = defineModel('sortings', {
    type: Array,
    required: true,
});

const perPage = defineModel('perPage', {
    type: Number,
    required: true,
});

const selected = defineModel('selected', {
    type: Array,
    default: () => [],
});

const props = defineProps({
    columns: {
        type: Array,
        required: true,
    },
    data: {
        type: Array,
        required: true,
    },
    links: {
        type: Array,
        default: () => [],
    },
    selectable: {
        type: Boolean,
        default: true,
    },
    pageRowOptions: {
        type: Array,
        default: () => [5, 10, 25, 50, 100],
    },
    totalRecords: {
        type: Number,
        default: 0,
    },
    startRecord: {
        type: Number,
        default: 0,
    },
    endRecord: {
        type: Number,
        default: 0,
    },
});

const isRowSelected = (id) => selected.value.some((row) => row.id === id);

const getNestedValue = (obj, path) => {
    if (!path) return undefined;
    return path.split('.').reduce((acc, part) => acc?.[part], obj);
};

const handleRowClick = (row) => {
    if (!props.selectable) return;

    if (isRowSelected(row.id)) {
        selected.value = selected.value.filter((r) => r.id !== row.id);
    } else {
        selected.value.push(row);
    }

    emit('change:selected', selected.value);
    emit('change', selected.value);
};

const toggleSelectRows = (checked) => {
    props.data.forEach((row) => {
        if (checked) {
            if (!isRowSelected(row.id)) {
                selected.value.push(row);
            }
        } else {
            selected.value = [];
        }
    });

    emit('change:selected', selected.value);
    emit('change', selected.value);
};
</script>
