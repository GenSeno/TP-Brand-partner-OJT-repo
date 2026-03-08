<template>
    <div class="card">
        <div class="card-body">
            <!-- Dropdown filter -->
            <div class="relative inline-block w-56 mb-2">
                <div class="dropdown">
                    Quotation List
                    <button
                        class="w-full border rounded bg-white float-end"
                        type="button"
                        data-bs-toggle="dropdown"
                        data-bs-auto-close="outside"
                        aria-expanded="false"
                    >
                        <i
                            data-feather="chevron-down"
                            class="feather-chevron-down mt-1"
                        ></i>
                    </button>

                    <ul class="dropdown-menu dropdown-menu-end form-control">
                        <li>
                            <label
                                v-for="status in statuses"
                                :key="status.value"
                                class="d-block py-1 cursor-pointer"
                            >
                                <input
                                    type="checkbox"
                                    :value="status.value"
                                    v-model="selected"
                                    @change="fetchQuotations"
                                />
                                {{ status.label }}
                            </label>
                        </li>
                        <hr />
                        <li>
                            <div class="d-flex justify-content-end gap-2 px-2">
                                <button
                                    type="button"
                                    class="btn btn-xs btn-primary"
                                    @click.stop="selectAllAndFetch"
                                >
                                    Select all
                                </button>
                                <button
                                    type="button"
                                    class="btn btn-xs btn-secondary"
                                    @click.stop="clearAllAndFetch"
                                >
                                    Clear
                                </button>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>

            <hr />

            <!-- Quotation List -->
            <div class="row relative">
                <div class="col-12">
                    <!-- Loading / Empty states -->
                    <div v-if="loading1" class="d-flex justify-content-center">
                        <i class="spinner-border spinner-border-sm me-1"></i
                        >Loading...
                    </div>
                    <div
                        v-else-if="quotations.length === 0"
                        class="alert bg-light"
                    >
                        No quotations found.
                    </div>

                    <!-- Quotation Items -->
                    <div v-else class="list-group">
                        <div
                            v-for="quote in paginatedQuotations"
                            :key="quote.id"
                            class="list-group-item d-flex flex-column justify-content-center cursor-pointer"
                            :class="
                                quote.id === quotation.id
                                    ? 'border-start-primary'
                                    : 'border-transparent'
                            "
                            @click="selectQuotation(quote)"
                        >
                            <a href="#">
                                <div class="row">
                                    <label class="col-md-8 mb-0 fw-bold fs-12">
                                        {{
                                            quote.billing_address.company_name
                                                ? quote.billing_address
                                                      .company_name
                                                : `${quote.billing_address?.title ?? ''} ${quote.billing_address?.first_name ?? ''} ${quote.billing_address?.last_name ?? ''}`.trim()
                                        }}
                                    </label>
                                    <label
                                        class="col-md-4 mb-0 fw-bold fs-10 text-end text-muted"
                                    >
                                        {{ quote.total.formatted }}
                                    </label>
                                </div>
                                <div class="row mt-1">
                                    <div class="col-md-6">
                                        <small class="fs-10">{{
                                            quote.reference
                                        }}</small>
                                    </div>
                                    <div class="col-md-6 text-end text-muted">
                                        <small class="fs-10">
                                            <i
                                                data-feather="calendar"
                                                class="feather-calendar mt-1"
                                            ></i>
                                            {{
                                                dayjs(quote.created_at).format(
                                                    dateFormat,
                                                )
                                            }}
                                        </small>
                                    </div>
                                </div>
                                <div class="row mt-1">
                                    <div class="col-md-12">
                                        <small class="fs-10 text-capitalize"
                                            ><span
                                                :class="
                                                    statusText(quote.status)
                                                "
                                                ><span
                                                    v-if="
                                                        quote.status ==
                                                            'request' ||
                                                        quote.status == 'draft'
                                                    "
                                                    >{{ quote.status }} / </span
                                                >{{
                                                    statusLabel(quote.status)
                                                }}</span
                                            ></small
                                        >
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>

                    <!-- Pagination Controls -->
                    <nav class="mt-2">
                        <ul class="pagination justify-content-center mb-0">
                            <li
                                class="page-item"
                                :class="{ disabled: currentPage === 1 }"
                            >
                                <a
                                    class="page-link btn btn-xs"
                                    href="#"
                                    @click.prevent="prevPage"
                                    >Prev</a
                                >
                            </li>
                            <li
                                class="page-item"
                                :class="{
                                    disabled: currentPage === totalPages,
                                }"
                            >
                                <a
                                    class="page-link btn btn-xs"
                                    href="#"
                                    @click.prevent="nextPage"
                                    >Next</a
                                >
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.border-start-primary {
    border-left: 5px solid #fe9f43;
}
</style>

<script setup>
import { ref, computed, onMounted, onBeforeUnmount, defineExpose } from 'vue';
import { router } from '@inertiajs/vue3';
import axios from 'axios';
import dayjs from 'dayjs';
import { statusText, statusLabel } from '@/helpers/status';
import { emitter } from '@/composables/eventBus';

const loading1 = ref(false);
const quotations = ref([]);
const selected = ref(['request', 'draft', 'completed', 'sent', 'cancelled']);
const currentPage = ref(1);
const perPage = ref(20); // show 5 items per page

const props = defineProps({
    quotation: Object,
    template: String,
});

const dateFormat = ref('MMM. DD, YYYY');

const statuses = [
    { label: 'New / Request', value: 'request' },
    { label: 'Draft / On Going', value: 'draft' },
    { label: 'Completed', value: 'completed' },
    { label: 'Sent', value: 'sent' },
    { label: 'Cancelled', value: 'cancelled' },
];

const selectAllAndFetch = () => {
    selected.value = statuses.map((s) => s.value); // select all
    fetchQuotations(); // fetch after select all
};

const clearAllAndFetch = () => {
    selected.value = []; // clear selection
    fetchQuotations(); // fetch after clear
};

// computed pagination
const totalPages = computed(() =>
    Math.ceil(quotations.value.length / perPage.value),
);
const paginatedQuotations = computed(() => {
    const start = (currentPage.value - 1) * perPage.value;
    return quotations.value.slice(start, start + perPage.value);
});
const filteredQuotations = computed(() =>
    quotations.value.filter((q) => selected.value.includes(q.status)),
);
// fetch quotations
const fetchQuotations = async () => {
    loading1.value = true;
    try {
        const response = await axios.get(
            route('admin.quotation.status', { status: selected.value }),
        );
        quotations.value = response.data;
        currentPage.value = 1; // reset page on new data
    } catch (error) {
        console.error('Error fetching quotations:', error);
    } finally {
        loading1.value = false;
    }
};

// select quotation
const selectQuotation = (quote) => {
    if (props.template === 'template') {
        router.get(route('admin.quotation.show', { quotation: quote?.id }));
    } else {
        router.get(route('admin.quotation.item', { quotation: quote?.id }));
    }
};

// pagination methods
const prevPage = () => {
    if (currentPage.value > 1) currentPage.value--;
};
const nextPage = () => {
    if (currentPage.value < totalPages.value) currentPage.value++;
};

/* Close dropdown when clicking outside */
const open = ref(false);
const handleClickOutside = (e) => {
    if (!e.target.closest('.relative')) open.value = false;
};

onMounted(() => {
    fetchQuotations();
    emitter.on('quotation:updated', () => {
        fetchQuotations(); // refresh sidebar
    });
    emitter.on('adjustment:updated', () => {
        fetchQuotations(); // refresh sidebar
    });
    emitter.on('quotationline:created', () => {
        fetchQuotations(); // refresh sidebar
    });
    emitter.on('quotationline:updated', () => {
        fetchQuotations(); // refresh sidebar
    });
    emitter.on('quote_lines:deleted', () => {
        fetchQuotations(); // refresh sidebar
    });
    document.addEventListener('click', handleClickOutside);
});

onBeforeUnmount(() => {
    emitter.off('quotation:updated');
    emitter.off('adjustment:updated');
    emitter.off('quotationline:created');
    emitter.off('quotationline:updated');
    document.removeEventListener('click', handleClickOutside);
});

defineExpose({
    fetchQuotations,
});
</script>
