<template>
    <Head title="Products" />

    <div class="page-header">
        <div class="add-item d-flex">
            <div class="page-title">
                <h4>Products</h4>
                <h6>Manage your products</h6>
            </div>
        </div>
        <div class="page-btn d-flex gap-2">
            <Link
                :href="route('brand-partner.product-options.index')"
                class="btn btn-added btn-dark"
            >
                <vue-feather type="settings" class="me-2"></vue-feather>
                Product Options
            </Link>
            <ModalLink
                navigate
                :href="route('brand-partner.products.create')"
                class="btn btn-added"
                #default="{ loading }"
            >
                <loading-text :loading="loading">
                    <vue-feather type="plus-circle" class="me-2"></vue-feather>
                    Add Product
                </loading-text>
            </ModalLink>
        </div>
    </div>

    <div class="card table-list-card">
        <div
            class="card-header d-flex align-items-center justify-content-between flex-wrap row-gap-3"
        >
            <div class="d-flex align-items-center gap-2">
                <dt-search
                    v-model="form.filter.search"
                    @search="submitFilters"
                />
            </div>
            <div
                class="d-flex table-dropdown my-xl-auto right-content align-items-center flex-wrap row-gap-3"
            >
                <select-filter
                    v-model="form.filter.status"
                    :options="statusOptions"
                    name="Status"
                    @change="submitFilters"
                ></select-filter>
                <select-filter
                    v-model="form.filter.category_id"
                    :options="categoryOptions"
                    name="Category"
                    @change="submitFilters"
                ></select-filter>
            </div>
        </div>

        <div class="card-body p-0">
            <dt-table
                v-model:sortings="form.sort"
                v-model:perPage="form.per_page"
                :columns="columns"
                :data="products.data"
                :total-records="products.total"
                :start-record="products.from"
                :end-record="products.to"
                :links="products.links"
                @change="submitFilters"
            >
                <template #image="{ row }">
                    <img
                        :src="getProductImage(row)"
                        :alt="row.name"
                        style="
                            width: 48px;
                            height: 48px;
                            object-fit: cover;
                            border-radius: 6px;
                        "
                    />
                </template>

                <template #name="{ row, value }">
                    {{ value }}
                </template>

                <template #price="{ row }">
                    {{ formatCurrency(row.price) }}
                </template>

                <template #status="{ value }">
                    <span class="badge" :class="`bg-${getStatusColor(value)}`">
                        {{ value }}
                    </span>
                </template>

                <template v-if="approvalEnabled" #approval_status="{ row }">
                    <span
                        class="badge"
                        :class="`bg-${getApprovalColor(row.approval_status)}`"
                    >
                        {{ getApprovalLabel(row.approval_status) }}
                    </span>
                    <div
                        v-if="
                            row.approval_status === 'rejected' &&
                            row.approval_notes
                        "
                        class="small text-danger mt-1"
                        style="max-width: 180px; white-space: normal"
                    >
                        {{ row.approval_notes }}
                    </div>
                </template>

                <template #action="{ row, value }">
                    <div class="action-table-data">
                        <div class="edit-delete-action">
                            <ModalLink
                                navigate
                                :href="
                                    route('brand-partner.products.edit', value)
                                "
                                class="btn btn-icon btn-outline-light btn-sm me-2"
                                title="Edit"
                            >
                                <vue-feather
                                    type="edit"
                                    class="feather-14"
                                ></vue-feather>
                            </ModalLink>
                            <dt-delete
                                :id="value"
                                route-name="brand-partner.products.destroy"
                                :name="row.name"
                                model-name="product"
                                class="btn btn-icon btn-danger-light btn-sm"
                                title="Delete"
                            >
                                <vue-feather
                                    type="trash-2"
                                    class="feather-14"
                                ></vue-feather>
                            </dt-delete>
                        </div>
                    </div>
                </template>
            </dt-table>
        </div>
    </div>
</template>

<script setup>
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import { removeEmptyValues } from '@/helpers/form';

const props = defineProps({
    products: Object,
    categories: Array,
    events: Array,
    statusOptions: Object,
    filter: Object,
});

const page = usePage();
const approvalEnabled = computed(
    () => page.props.features?.product_approval ?? true,
);

const columns = computed(() => {
    const cols = [
        { title: 'Image', dataIndex: 'image_url', key: 'image' },
        { title: 'Product', dataIndex: 'name', key: 'name', sortable: true },
        { title: 'Category', dataIndex: 'category.name', key: 'category' },
        { title: 'Price', dataIndex: 'price', key: 'price', sortable: true },
        { title: 'Stock', dataIndex: 'stock', key: 'stock' },
        { title: 'Status', dataIndex: 'status', key: 'status', sortable: true },
    ];
    if (approvalEnabled.value) {
        cols.push({
            title: 'Approval',
            dataIndex: 'approval_status',
            key: 'approval_status',
        });
    }
    cols.push({ title: '', dataIndex: 'id', key: 'action' });
    return cols;
});

const categoryOptions = computed(() => {
    return props.categories.reduce((acc, cat) => {
        acc[cat.id] = cat.label;
        return acc;
    }, {});
});

const form = useForm({
    filter: {
        search: props.filter?.search || '',
        status: props.filter?.status || '',
        category_id: props.filter?.category_id || '',
    },
    sort: [],
    per_page: props.products?.per_page || 10,
});

const submitFilters = () => {
    form.transform((data) =>
        removeEmptyValues({
            ...data,
            sort: data.sort.join(','),
            per_page: data.per_page === 10 ? '' : data.per_page,
        }),
    ).get(route('brand-partner.products.index'), {
        preserveState: true,
        replace: true,
    });
};

const formatCurrency = (amount) => {
    return new Intl.NumberFormat('en-PH', {
        style: 'currency',
        currency: 'PHP',
    }).format(amount / 100);
};

const getStatusColor = (status) => {
    const colors = {
        draft: 'warning',
        published: 'success',
        disabled: 'secondary',
    };
    return colors[status] || 'secondary';
};

const getApprovalColor = (status) => {
    return (
        { pending: 'warning', approved: 'success', rejected: 'danger' }[
            status
        ] ?? 'secondary'
    );
};

const getApprovalLabel = (status) => {
    return (
        { pending: 'Pending', approved: 'Approved', rejected: 'Rejected' }[
            status
        ] ?? status
    );
};

const getProductImage = (product) => {
    const primaryImage = product.images?.find((img) => img.is_primary);
    return primaryImage?.url || product.images?.[0]?.url || '/img/default.png';
};
</script>
