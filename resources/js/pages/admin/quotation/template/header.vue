<template>
    <!-- HEADER -->
    <div class="row">
        <!-- COMPANY INFO -->
        <div class="col-md-6">
            <img
                src="/img/logo/logo_pakaras_white.png"
                alt="Company Logo"
                style="width: 160px"
                class="mb-2"
            />
        </div>
        <div class="col-md-6 text-end">
            <h3 class="fw-bold">QUOTATION</h3>
        </div>
    </div>
    <div class="d-flex justify-content-end">
        <ModalLink
            v-if="!route().current('admin.quotation.show')"
            navigate
            :href="route('admin.quotation.edit_date', quotation.id)"
            class="btn btn-light btn-sm"
            title="Edit"
        >
            <i data-feather="edit" class="feather-edit px-1"></i> Edit Info
        </ModalLink>
    </div>
    <div class="d-flex border-bottom mb-2">
        <div class="col-md-6">
            <Company />
        </div>

        <!-- RIGHT DETAILS -->
        <div class="col-md-6">
            <table class="medium text-muted table-responsive">
                <tbody>
                    <tr>
                        <td class="fw-bold text-black pr-4">Quotation No.</td>
                        <td>&nbsp;&nbsp;&nbsp;</td>
                        <td>: {{ quotation?.reference }}</td>
                    </tr>
                    <tr>
                        <td class="fw-bold text-black pr-4">Date</td>
                        <td>&nbsp;&nbsp;&nbsp;</td>
                        <td>
                            :
                            {{
                                quotation?.quoted_at
                                    ? dayjs(quotation.quoted_at).format(
                                          dateFormat,
                                      )
                                    : 'Not Set'
                            }}
                        </td>
                    </tr>
                    <tr>
                        <td class="fw-bold text-black pr-4">
                            Expected Delivery Date
                        </td>
                        <td>&nbsp;&nbsp;&nbsp;</td>
                        <td>
                            :
                            {{
                                quotation?.expected_delivery
                                    ? dayjs(quotation.expected_delivery).format(
                                          dateFormat,
                                      )
                                    : 'Not Set'
                            }}
                        </td>
                    </tr>
                    <tr>
                        <td class="fw-bold text-black pr-4">Validity</td>
                        <td>&nbsp;&nbsp;&nbsp;</td>
                        <td>
                            :
                            {{
                                quotation?.validity_days
                                    ? quotation.validity_days + ' Days'
                                    : 'Not Set'
                            }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <div class="d-flex justify-content-end">
        <ModalLink
            v-if="!route().current('admin.quotation.show')"
            navigate
            :href="route('admin.quotation.edit', quotation.id)"
            class="btn btn-light btn-sm"
            title="Edit"
        >
            <i data-feather="edit" class="feather-edit px-1"></i> Edit Info
        </ModalLink>
    </div>
    <div class="d-flex mb-2">
        <!-- BILL TO -->
        <div class="col-md-12 mb-4">
            <span class="text-muted">BILL TO:</span>
            <div class="small text-muted mt-1">
                <h6>{{ address.company_name }}</h6>
                {{ address.title }} {{ address.first_name }}
                {{ address.last_name }}<br />
                {{ address.line1 }}, {{ address.line2 }}<br />
                {{ address.barangay }}, {{ address.city }}<br />
                {{ address.province }}, {{ address.postcode }},
                {{ address.country.name }}<br />
                Email: {{ address.email }} / Mobile:+{{ address.phone }}
            </div>
        </div>
    </div>
</template>
<script setup>
import { onMounted, onUnmounted, ref, computed, watch } from 'vue';
import dayjs from 'dayjs';
import { emitter } from '@/composables/eventBus';
import Company from '../../company-info.vue';

const props = defineProps({
    quotation: Object,
});

const quotation = ref({
    billing_address: {},
    shipping_address: {},
    currency: {},
});

watch(
    () => props.quotation,
    (val) => val && (quotation.value = val),
    { immediate: true },
);

const address = computed(() => quotation.value.billing_address ?? {});
const dateFormat = ref('MMM. DD, YYYY');
</script>
