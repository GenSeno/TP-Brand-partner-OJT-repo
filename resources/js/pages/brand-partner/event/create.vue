<template>
    <Head title="Add Event" />

    <Modal
        ref="modalRef"
        max-width="md"
        :close-explicitly="true"
        v-slot="{ close }"
    >
        <div class="page-header">
            <h4>Add Event</h4>
        </div>

        <form @submit.prevent="submitForm">
            <div class="page-body new-employee-field">
                <div class="mb-3">
                    <label class="form-label required">Name</label>
                    <input-text v-model="form.data.name" autofocus />
                    <input-error :message="form.errors.name" />
                </div>

                <div class="mb-3">
                    <label class="form-label">Slug</label>
                    <input-slug
                        v-model="form.data.slug"
                        :reference="form.data.name"
                        :hide-tip="!!form.errors.slug"
                    />
                    <input-error :message="form.errors.slug" />
                </div>

                <div class="mb-3">
                    <label class="form-label">Description</label>
                    <textarea
                        v-model="form.data.description"
                        class="form-control"
                        rows="3"
                    ></textarea>
                    <input-error :message="form.errors.description" />
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Start Date</label>
                        <input
                            v-model="form.data.start_date"
                            type="date"
                            class="form-control"
                        />
                        <input-error :message="form.errors.start_date" />
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">End Date</label>
                        <input
                            v-model="form.data.end_date"
                            type="date"
                            class="form-control"
                        />
                        <input-error :message="form.errors.end_date" />
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label required">Status</label>
                    <select v-model="form.data.status" class="form-select">
                        <option
                            v-for="(label, value) in statusOptions"
                            :key="value"
                            :value="value"
                        >
                            {{ label }}
                        </option>
                    </select>
                    <input-error :message="form.errors.status" />
                </div>

                <div class="mb-0">
                    <div
                        class="status-toggle modal-status d-flex justify-content-between align-items-center"
                    >
                        <span class="status-label">Enabled</span>
                        <input
                            v-model="form.data.enabled"
                            type="checkbox"
                            id="enabled"
                            class="check"
                        />
                        <label for="enabled" class="checktoggle"></label>
                    </div>
                </div>
            </div>

            <div class="page-footer-buttons">
                <div class="me-auto">
                    <label class="form-check">
                        <input
                            class="form-check-input"
                            type="checkbox"
                            v-model="createAnother"
                        />
                        Create Another
                    </label>
                </div>
                <div>
                    <button
                        type="button"
                        class="btn btn-secondary me-2"
                        @click="close()"
                    >
                        Cancel
                    </button>
                    <submit-btn :loading="form.processing">
                        Create Event
                    </submit-btn>
                </div>
            </div>
        </form>
    </Modal>
</template>

<script setup>
import { useAxiosForm } from '@/composables/axiosForm';
import * as alert from '@/helpers/alert';
import { Head, router } from '@inertiajs/vue3';
import { ref, useTemplateRef } from 'vue';

const props = defineProps({
    statusOptions: Object,
});

const modalRef = useTemplateRef('modalRef');
const createAnother = ref(false);

const form = useAxiosForm({
    name: '',
    slug: '',
    description: '',
    start_date: '',
    end_date: '',
    status: 'upcoming',
    enabled: true,
});

const submitForm = () => {
    form.post(route('brand-partner.events.store'), {
        onSuccess: ({ data }) => {
            if (createAnother.value) {
                form.reset();
                router.reload();
            } else {
                modalRef.value.close();
            }
            alert.showSuccess(data.message || 'Event created successfully.');
        },
    });
};
</script>
