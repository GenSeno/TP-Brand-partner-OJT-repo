<style scoped>
.modal-sidebar-wrapper {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 100%;
    width: 100%;
    background-color: rgba(0, 0, 0, 0.25);
    z-index: 1;
}

.modal-sidebar-wrapper .modal-sidebar-content {
    max-width: 500px;
    margin-left: auto;
    height: 100%;
    display: flex;
    flex-direction: column;
}

.notes-container {
    display: flex;
    flex-direction: column;
    flex: 1;
    overflow: hidden;
}

.notes-header {
    position: relative;
    transition: box-shadow 0.3s ease;
}

.notes-header.scrolled {
    box-shadow: 0 4px 12px -2px rgba(0, 0, 0, 0.15);
}

.notes-body {
    flex: 1;
    overflow-y: auto;
}

.notes-form {
    padding-top: 0.5rem;
    border-top: 1px solid #dee2e6;
    background-color: white;
}

@media (max-width: 768px) {
    .modal-sidebar-wrapper .modal-sidebar-content {
        max-width: 400px;
    }
}
</style>

<template>
    <transition name="fade">
        <div
            v-show="sidebarVisible"
            class="modal-sidebar-wrapper p-3"
            @click.self="sidebarVisible = false"
        >
            <div class="modal-sidebar-content bg-white p-3 rounded-4 shadow">
                <div
                    v-if="can('job-orders:manage-notes')"
                    class="notes-container"
                >
                    <div
                        ref="notesHeaderRef"
                        class="notes-header py-2 border-bottom"
                        :class="{ scrolled: isScrolled }"
                    >
                        <h6>Notes / History</h6>
                    </div>

                    <div
                        ref="notesBodyRef"
                        class="notes-body pe-2"
                        @scroll="handleScroll"
                    >
                        <div v-if="activities.length === 0" class="text-muted">
                            No notes available.
                        </div>
                        <div ref="notesRef">
                            <div
                                v-for="(activity, index) in filteredActivities"
                                :key="activity.id"
                                class="row align-items-center justify-content-between g-3 my-2 me-0"
                            >
                                <div class="col-12 col-md-4 col-lg-3 col-xl-2">
                                    <strong>{{ activity.date }}</strong>
                                </div>
                                <div class="col-1 text-sm-center">
                                    <div
                                        v-if="activity.causer"
                                        class="avatar avatar-md rounded-circle bg-light-900 p-1"
                                    >
                                        <img
                                            class="object-fit-contain rounded-circle"
                                            :src="activity.causer.avatar_url"
                                            alt="Avatar"
                                        />
                                    </div>
                                    <div
                                        v-else
                                        class="avatar avatar-md rounded-circle bg-light-900 text-muted p-1"
                                    >
                                        <i
                                            v-if="activity.badge"
                                            data-feather="cpu"
                                            class="feather-cpu"
                                            style="
                                                font-size: large;
                                                vertical-align: middle;
                                            "
                                        ></i>
                                    </div>
                                </div>
                                <div
                                    class="col-11 col-md-7 col-lg-8 col-xl-9 d-flex align-items-center gap-2"
                                >
                                    <div
                                        :class="{
                                            'text-primary':
                                                noteForm.recentlySuccessful &&
                                                index ===
                                                    filteredActivities.length -
                                                        1,
                                        }"
                                        style="transition: all 0.3s ease"
                                    >
                                        <p
                                            v-if="
                                                activity.description ===
                                                'comment'
                                            "
                                            class="mb-0"
                                        >
                                            {{ activity.properties.content }} -
                                            comment by
                                            <strong>{{
                                                activity.causer?.full_name ??
                                                'system'
                                            }}</strong>
                                        </p>
                                        <p
                                            v-else-if="
                                                activity.description ===
                                                'status-update'
                                            "
                                            class="mb-0"
                                        >
                                            Status changed to
                                            <strong>{{
                                                activity.new_status
                                            }}</strong>
                                        </p>
                                        <p
                                            v-else-if="
                                                activity.description ===
                                                'state-transition'
                                            "
                                            class="mb-0"
                                        >
                                            Stage changed from
                                            <StageBadge
                                                :stage="
                                                    activity.properties.previous
                                                "
                                            />
                                            to
                                            <StageBadge
                                                :stage="activity.properties.new"
                                            />
                                        </p>
                                        <p
                                            v-else-if="
                                                activity.description ===
                                                'urgency-flag-update'
                                            "
                                            class="mb-0"
                                        >
                                            Urgency changed from
                                            <UrgentStatus
                                                :urgency-flag="
                                                    activity.properties.previous
                                                "
                                            />
                                            to
                                            <UrgentStatus
                                                :urgency-flag="
                                                    activity.properties.new
                                                "
                                            />
                                        </p>
                                        <p
                                            v-else-if="
                                                activity.description ===
                                                'updated'
                                            "
                                            class="mb-0"
                                        >
                                            <strong>{{
                                                activity.subject
                                            }}</strong>
                                            updated by
                                            <strong>{{
                                                activity.causer?.full_name ??
                                                'system'
                                            }}</strong>
                                        </p>
                                        <p
                                            v-else-if="
                                                activity.description ===
                                                'created'
                                            "
                                            class="mb-0"
                                        >
                                            <strong>{{
                                                activity.subject
                                            }}</strong>
                                            created by
                                            <strong>{{
                                                activity.casuer?.full_name ??
                                                'system'
                                            }}</strong>
                                        </p>
                                        <p v-else class="mb-0">
                                            {{ activity.description }}
                                        </p>
                                        <small>{{
                                            dayjs(activity.created_at).format(
                                                'hh:mm:ss A',
                                            )
                                        }}</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="notes-form">
                        <form @submit.prevent="submitNote">
                            <textarea
                                v-model="noteForm.data.note"
                                class="form-control bg-light"
                                rows="2"
                                placeholder="Add a note"
                            ></textarea>
                            <div class="mt-2">
                                <button
                                    type="button"
                                    class="btn btn-sm btn-secondary me-2"
                                    @click="sidebarVisible = false"
                                >
                                    Close
                                </button>
                                <submit-btn
                                    :loading="noteForm.processing"
                                    class="btn btn-primary btn-sm"
                                    :disabled="!noteForm.data.note.trim()"
                                >
                                    Add Note
                                </submit-btn>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </transition>
</template>

<script setup>
import dayjs from 'dayjs';
import { useAxiosForm } from '@/composables/axiosForm';
import { can } from '@/helpers/guard';
import { capitalize, nextTick, ref, useTemplateRef, watch } from 'vue';
import { computed } from 'vue';
import { getModelName } from '@/helpers/string';
import UrgentStatus from '@/components/job-order/urgent-status.vue';
import StageBadge from '@/components/job-order/stage-badge.vue';

const props = defineProps({
    jobOrder: {
        type: Object,
        required: true,
    },
    activities: {
        type: Array,
        default: () => [],
    },
});

const sidebarVisible = defineModel('show');
const modalRef = defineModel('modalRef');

const notesRef = useTemplateRef('notesRef');
const notesHeaderRef = useTemplateRef('notesHeaderRef');
const notesBodyRef = useTemplateRef('notesBodyRef');
const isScrolled = ref(false);
const activityBadge = {
    comment: {
        icon: 'message-circle',
        color: 'primary',
    },
    'status-update': {
        icon: 'refresh-cw',
        color: 'warning',
    },
    'state-transition': {
        icon: 'shuffle',
        color: 'info',
    },
    'urgency-flag-update': {
        icon: 'alert-triangle',
        color: 'danger',
    },
    updated: {
        icon: 'edit-2',
        color: 'info',
    },
    created: {
        icon: 'plus-circle',
        color: 'success',
    },
    default: {
        icon: 'info',
        color: 'secondary',
    },
};

const noteForm = useAxiosForm({
    note: '',
});

const handleScroll = () => {
    if (notesBodyRef.value) {
        isScrolled.value = notesBodyRef.value.scrollTop > 0;
    }
};

const scrollNotesToBottom = () => {
    if (notesBodyRef.value) {
        notesBodyRef.value.scrollTop = notesBodyRef.value.scrollHeight;
    }
};

const filteredActivities = computed(() => {
    let currentDate = null;
    // Sort activities by id ascending (lowest id first, highest id last)
    const sorted = [...props.activities].sort((a, b) => a.id - b.id);
    return sorted.map((activity) => {
        let output = {
            ...activity,
            subject: capitalize(getModelName(activity.subject_type)),
            changes: getChangedProperties(
                activity.properties.attributes,
                activity.properties.old,
            ),
            date: null,
            badge: activity.description
                ? activityBadge[activity.description]
                : activityBadge.default,
        };
        const date = dayjs(activity.created_at).format('DD MMM YYYY');
        if (date !== currentDate) {
            currentDate = date;
            output.date = date;
        }
        return output;
    });
});

/**
 * Returns an array of property names that have different values between two objects.
 * Only compares own enumerable properties (shallow comparison).
 */
function getChangedProperties(obj1, obj2) {
    const keys = new Set([
        ...Object.keys(obj1 || {}),
        ...Object.keys(obj2 || {}),
    ]);
    const changed = [];
    for (const key of keys) {
        if (
            obj1?.[key] !== obj2?.[key] &&
            ['updated_at', 'id'].includes(key) === false
        ) {
            changed.push(getModelName(key));
        }
    }
    return changed;
}

const submitNote = () => {
    noteForm.post(route('admin.job-order.note', props.jobOrder.id), {
        onSuccess: async () => {
            noteForm.reset();
            modalRef.value.reload({
                only: ['activities'],
                onFinish: () => {
                    nextTick(() => {
                        scrollNotesToBottom();
                    });
                },
            });
        },
    });
};

watch(sidebarVisible, (isOpen) => {
    if (isOpen) {
        nextTick(() => {
            scrollNotesToBottom();
        });
    }
});
</script>
