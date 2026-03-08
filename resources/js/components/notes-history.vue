<template>
    <div>
        <!-- Files button with total count -->
        <button
            type="button"
            class="btn btn-secondary-ghost position-relative"
            :class="{ active: showNotes }"
            @click="showNotes = !showNotes"
        >
            <i data-feather="clock" class="feather-clock"></i>
            {{ showNotes ? 'Hide' : 'Show' }} history and notes
        </button>
        <!-- Attachment area -->
        <div v-show="showNotes">
            <div class="my-3">
                <form @submit.prevent="submitForm" class="mb-4">
                    <div class="mb-2">
                        <label for="note" class="form-label">Add Note</label>
                        <textarea
                            v-model="form.data.note"
                            id="note"
                            class="form-control"
                            rows="2"
                            placeholder="Write your note here..."
                        ></textarea>
                    </div>
                    <submit-btn
                        :loading="form.processing"
                        class="btn-sm me-2"
                        :disabled="!form.data.note.trim()"
                    >
                        Add Note
                    </submit-btn>
                </form>
            </div>
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
                            style="font-size: large; vertical-align: middle"
                        ></i>
                    </div>
                </div>
                <div
                    class="col-11 col-md-7 col-lg-8 col-xl-9 d-flex align-items-center gap-2"
                >
                    <div
                        :class="{
                            'text-primary':
                                form.recentlySuccessful && index === 0,
                        }"
                        style="transition: all 0.3s ease"
                    >
                        <p
                            v-if="activity.description === 'comment'"
                            class="mb-0"
                        >
                            {{ activity.properties.content }} - comment by
                            <strong>{{
                                activity.causer?.full_name ?? 'system'
                            }}</strong>
                        </p>
                        <p
                            v-else-if="activity.description === 'status-update'"
                            class="mb-0"
                        >
                            Status changed from
                            <strong>{{ activity.properties.previous }}</strong>
                            to
                            <strong>{{ activity.properties.new }}</strong>
                        </p>
                        <p
                            v-else-if="
                                activity.description === 'state-transition'
                            "
                            class="mb-0"
                        >
                            State changed from
                            <strong>{{ activity.properties.previous }}</strong>
                            to
                            <strong>{{ activity.properties.new }}</strong>
                        </p>
                        <p
                            v-else-if="
                                activity.description === 'urgency-flag-update'
                            "
                            class="mb-0"
                        >
                            Urgency changed from
                            <strong>{{ activity.properties.previous }}</strong>
                            to
                            <strong>{{ activity.properties.new }}</strong>
                        </p>
                        <p
                            v-else-if="activity.description === 'updated'"
                            class="mb-0"
                        >
                            <strong>{{ activity.subject }}</strong> updated by
                            <strong>{{
                                activity.causer?.full_name ?? 'system'
                            }}</strong>
                        </p>
                        <p
                            v-else-if="activity.description === 'created'"
                            class="mb-0"
                        >
                            <strong>{{ activity.subject }}</strong> created by
                            <strong>{{
                                activity.causer?.full_name ?? 'system'
                            }}</strong>
                        </p>
                        <p v-else class="mb-0">{{ activity.description }}</p>
                        <small>{{
                            dayjs(activity.created_at).format('hh:mm:ss A')
                        }}</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { capitalize, ref } from 'vue';
import { useAxiosForm } from '@/composables/axiosForm';
import { computed } from 'vue';
import dayjs from 'dayjs';
import { emitter } from '@/composables/eventBus';
import { getModelName } from '@/helpers/string';

const props = defineProps({
    postUrl: String,
    activities: {
        type: Array,
        default: () => [],
    },
});

const showNotes = ref(false);
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

const filteredActivities = computed(() => {
    let currentDate = null;
    return props.activities.map((activity) => {
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
const form = useAxiosForm({
    note: '',
});

const submitForm = () => {
    form.post(props.postUrl, {
        onSuccess: () => {
            form.data.note = '';
            // Emit event to refresh activities
            emitter.emit('note:created');
        },
    });
};

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
</script>
