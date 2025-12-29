<template>
    <div
        v-if="players.length > 0"
        class="flex items-center justify-center gap-3 mb-6 flex-wrap"
    >
        <div
            v-for="player in players"
            :key="player.id"
            :class="[
                'flex items-center gap-2 px-4 py-2 rounded-full transition-all',
                currentPlayerId === player.id
                    ? 'bg-primary text-primary-content scale-110 shadow-lg'
                    : 'bg-base-200 opacity-60',
            ]"
        >
            <img
                :src="player.avatar"
                :alt="`${player.name}'s avatar`"
                class="w-8 h-8 rounded-full"
            />
            <span class="font-semibold text-sm">{{ player.name }}</span>
            <span class="badge badge-sm">{{ player.score }}</span>
        </div>
    </div>
</template>

<script setup>
const props = defineProps({
    players: {
        type: Array,
        required: true,
    },
    currentPlayerId: {
        type: Number,
        required: false,
        default: null,
    },
});
</script>

<style scoped>
/* Component styles */
</style>
