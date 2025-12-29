<template>
    <div class="grid grid-cols-3 gap-3">
        <div class="stat bg-base-200 rounded-lg p-4 text-center">
            <div class="stat-title text-xs">Round</div>
            <div class="stat-value text-2xl text-primary">
                {{ round }}
            </div>
        </div>
        <div class="stat bg-base-200 rounded-lg p-4 text-center">
            <div class="stat-title text-xs">Completed</div>
            <div class="stat-value text-2xl text-success">
                {{ completed }}
            </div>
        </div>
        <div class="stat bg-base-200 rounded-lg p-4 text-center">
            <div class="stat-title text-xs">Skipped</div>
            <div class="stat-value text-2xl text-warning">
                {{ skipped }}
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed } from "vue";

const props = defineProps({
    completed: {
        type: Number,
        default: 0,
    },
    skipped: {
        type: Number,
        default: 0,
    },
    round: {
        type: Number,
        default: null,
    },
});

// If round is not provided, calculate it
const round = computed(() => {
    return props.round !== null ? props.round : props.completed + props.skipped + 1;
});
</script>

<style scoped>
/* Component styles */
</style>
