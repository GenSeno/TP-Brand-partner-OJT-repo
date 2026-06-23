<template>
    <Head :title="product.name" />

    <div class="page-header">
        <div class="add-item d-flex">
            <div class="page-title">
                <h4>{{ product.name }}</h4>
                <h6>Product details</h6>
            </div>
        </div>
        <div class="page-btn d-flex gap-2">
            <Link :href="route('brand-partner.products.edit', product.id)" class="btn btn-added">
                <vue-feather type="edit" class="me-2"></vue-feather>
                Edit Product
            </Link>
            <Link :href="route('brand-partner.products.index')" class="btn btn-outline-secondary">
                <vue-feather type="arrow-left" class="me-2"></vue-feather>
                Back
            </Link>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="product-image-wrap mb-3">
                                <img
                                    :src="product.image_url || '/img/tshirt-placeholder.svg'"
                                    :alt="product.name"
                                    class="img-fluid rounded"
                                />
                            </div>
                            <div class="d-flex gap-2 flex-wrap" v-if="product.images?.length">
                                <img
                                    v-for="img in product.images"
                                    :key="img.id"
                                    :src="img.url"
                                    class="img-thumbnail"
                                    style="width: 64px; height: 64px; object-fit: cover; cursor: pointer;"
                                />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h4 class="mb-2">{{ product.name }}</h4>
                            <p class="text-muted mb-1" v-if="product.sku">SKU: {{ product.sku }}</p>
                            <p class="text-muted mb-1" v-if="product.category">Category: {{ product.category.name }}</p>
                            <p class="text-muted mb-1" v-if="product.event">Event: {{ product.event.name }}</p>

                            <div class="mt-3">
                                <h3 class="text-primary">{{ formatCurrency(product.price) }}</h3>
                                <del v-if="product.compare_price" class="text-muted ms-2">{{ formatCurrency(product.compare_price) }}</del>
                            </div>

                            <div class="mt-3">
                                <span class="badge me-1" :class="statusBadgeClass">{{ product.status }}</span>
                                <span class="badge" :class="approvalBadgeClass">{{ product.approval_status }}</span>
                            </div>

                            <div class="mt-3">
                                <p><strong>Stock:</strong> {{ product.track_stock ? product.stock : 'Unlimited' }}</p>
                                <p v-if="product.short_description" class="mt-2">{{ product.short_description }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4" v-if="product.description">
                        <h5>Description</h5>
                        <div v-html="product.description"></div>
                    </div>
                </div>
            </div>

            <div class="card" v-if="product.orderLines?.length">
                <div class="card-header">
                    <h5 class="card-title mb-0">Order History</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Order</th>
                                    <th>Customer</th>
                                    <th>Qty</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="line in product.orderLines" :key="line.id">
                                    <td>
                                        <Link :href="route('brand-partner.orders.show', line.order_id)" class="text-primary">
                                            {{ line.order?.reference }}
                                        </Link>
                                    </td>
                                    <td>{{ line.order?.customer_name }}</td>
                                    <td>{{ line.quantity }}</td>
                                    <td>{{ line.order?.status }}</td>
                                    <td>{{ formatDate(line.created_at) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Product Meta</h5>
                </div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-sm-5">Created</dt>
                        <dd class="col-sm-7">{{ formatDate(product.created_at) }}</dd>

                        <dt class="col-sm-5">Updated</dt>
                        <dd class="col-sm-7">{{ formatDate(product.updated_at) }}</dd>

                        <dt class="col-sm-5">Featured</dt>
                        <dd class="col-sm-7">{{ product.featured ? 'Yes' : 'No' }}</dd>

                        <dt class="col-sm-5">Colors</dt>
                        <dd class="col-sm-7">{{ product.colors_array?.join(', ') || '—' }}</dd>

                        <dt class="col-sm-5">Sizes</dt>
                        <dd class="col-sm-7">{{ product.sizes_array?.join(', ') || '—' }}</dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { computed } from 'vue';
import BrandPartnerLayout from '@/layouts/brand-partner-layout.vue';

defineOptions({ layout: BrandPartnerLayout });

const props = defineProps({
    product: Object,
});

const statusBadgeClass = computed(() => {
    const map = { published: 'bg-success', draft: 'bg-secondary', disabled: 'bg-danger' };
    return map[props.product.status] || 'bg-secondary';
});

const approvalBadgeClass = computed(() => {
    const map = { approved: 'bg-success', pending: 'bg-warning', rejected: 'bg-danger' };
    return map[props.product.approval_status] || 'bg-warning';
});

const formatCurrency = (amount) =>
    new Intl.NumberFormat('en-PH', {
        style: 'currency',
        currency: 'PHP',
    }).format(amount / 100);

const formatDate = (date) =>
    date ? new Date(date).toLocaleDateString('en-PH', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
    }) : '—';
</script>
