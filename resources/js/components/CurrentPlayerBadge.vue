<template>
    <div
        v-if="player"
        :class="['text-center', variant === 'large' ? 'mb-8' : 'mb-6']"
    >
        <!-- Large Variant (for Task Type Selector) -->
        <template v-if="variant === 'large'">
            <img
                :src="player.avatar"
                :alt="`${player.name}'s avatar`"
                class="w-28 h-28 rounded-full mx-auto mb-3"
            />
            <h2 class="text-3xl font-bold mb-1">{{ player.name }}'s Turn</h2>
            <p class="text-base-content/60">Choose your challenge</p>
        </template>

        <!-- Compact Variant (for Game Screen) -->
        <template v-else>
            <div
                class="inline-flex items-center gap-3 bg-primary text-primary-content px-6 py-3 rounded-full shadow-lg"
            >
                <img
                    :src="player.avatar"
                    :alt="`${player.name}'s avatar`"
                    class="w-12 h-12 rounded-full"
                />
                <span class="font-bold text-lg">{{ player.name }}</span>
            </div>
        </template>
    </div>
</template>

<script setup>
const props = defineProps({
    player: {
        type: Object,
        required: true,
    },
    variant: {
        type: String,
        default: "compact",
        validator: (value) => ["compact", "large"].includes(value),
    },
});
</script>
