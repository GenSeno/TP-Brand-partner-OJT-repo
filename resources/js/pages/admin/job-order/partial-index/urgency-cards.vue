<template>
    <div class="row">
        <template v-for="(item, urgency) in urgencyFlags" :key="urgency">
            <div class="col-xl-3 col-sm-6 col-12 d-flex">
                <div
                    class="dash-count"
                    :class="
                        total[urgency] > 0
                            ? item.class
                            : 'bg-dark bg-opacity-50'
                    "
                >
                    <div class="dash-counts">
                        <h4>{{ total[urgency] }}</h4>
                        <p class="mb-0">{{ item.label }}</p>
                    </div>
                    <div class="dash-imgs">
                        <vue-feather :type="item.icon"></vue-feather>
                    </div>
                </div>
            </div>
        </template>
    </div>
</template>

<script setup>
import { UrgencyFlag } from '@/enums/urgency-flag';

defineProps({
    total: Object,
});

const urgencyFlags = {
    all: {
        label: 'Total Jobs',
        class: 'bg-dark',
        icon: 'list',
    },
    [UrgencyFlag.REGULAR]: {
        label: 'Regular Jobs',
        class: 'bg-success-gradient',
        icon: 'check-circle',
    },
    [UrgencyFlag.PRIORITY]: {
        label: 'Priority Urgency',
        class: 'bg-warning-gradient',
        icon: 'alert-circle',
    },
    [UrgencyFlag.RUSH]: {
        label: 'Rush Urgency',
        class: 'bg-danger-gradient',
        icon: 'zap',
    },
};
</script>
