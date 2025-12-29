<template>
    <div
        class="card bg-gradient-to-br from-primary to-secondary text-primary-content shadow-2xl mb-8"
    >
        <div class="card-body items-center text-center py-10">
            <Trophy :size="80" class="mb-4" />
            <h2 class="text-3xl font-bold mb-2">
                {{ winner.name }}
            </h2>
            <p class="text-xl opacity-90">
                {{ winner.score }} points
            </p>
        </div>
    </div>
</template>

<script setup>
import { Trophy } from "lucide-vue-next";

defineProps({
    winner: {
        type: Object,
        required: true,
    },
});
</script>

<style scoped>
/* Component styles */
</style>
