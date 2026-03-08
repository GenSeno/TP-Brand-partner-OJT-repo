<template>
    <span
        v-if="props.status === UserStatus.ACTIVE"
        class="badge table-badge bg-success"
    >
        Active
    </span>
    <span
        v-else-if="props.status === UserStatus.INACTIVE"
        class="badge table-badge bg-secondary"
    >
        Inactive
    </span>
    <span v-else class="badge table-badge bg-warning">Suspended</span>
</template>
<script setup>
import { UserStatus } from '@/enums/user-status';

const props = defineProps({
    status: {
        type: String,
        required: true,
    },
});
</script>
