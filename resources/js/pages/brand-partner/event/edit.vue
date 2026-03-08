<template>
    <Head title="Edit Event" />

    <Modal
        ref="modalRef"
        max-width="md"
        :close-explicitly="true"
        v-slot="{ close }"
    >
        <div class="page-header">
            <h4>Edit Event</h4>
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
                <div class="me-auto"></div>
                <div>
                    <button
                        type="button"
                        class="btn btn-secondary me-2"
                        @click="close()"
                    >
                        Cancel
                    </button>
                    <submit-btn :loading="form.processing">
                        Update Event
                    </submit-btn>
                </div>
            </div>
        </form>
    </Modal>
</template>

<script setup>
import { useAxiosForm } from '@/composables/axiosForm';
import * as alert from '@/helpers/alert';
import { Head } from '@inertiajs/vue3';
import { useTemplateRef } from 'vue';

const props = defineProps({
    event: Object,
    statusOptions: Object,
});

const modalRef = useTemplateRef('modalRef');

const form = useAxiosForm({
    name: props.event.name,
    slug: props.event.slug,
    description: props.event.description || '',
    start_date: props.event.start_date || '',
    end_date: props.event.end_date || '',
    status: props.event.status,
    enabled: props.event.enabled,
});

const submitForm = () => {
    form.put(route('brand-partner.events.update', props.event.id), {
        onSuccess: ({ data }) => {
            modalRef.value.close();
            alert.showSuccess(data.message || 'Event updated successfully.');
        },
    });
};
</script>
