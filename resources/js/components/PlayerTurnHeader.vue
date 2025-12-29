<template>
    <div class="text-center mb-8 animate-fade-in">
        <h1 class="text-5xl font-bold mb-4">
            <span class="text-6xl">{{ getPlayerAvatar(player.order) }}</span>
            <br />
            {{ player.name }}'s Turn!
        </h1>
        <p class="text-lg opacity-70">Choose your challenge</p>
    </div>
</template>

<script setup>
import { defineProps } from "vue";
import { User } from "lucide-vue-next";

const props = defineProps({
    player: {
        type: Object,
        required: true,
    },
    playerAvatars: {
        type: Array,
        default: () => [
            "😀",
            "😎",
            "🥳",
            "🤓",
            "🤠",
            "🥸",
            "😺",
            "🦊",
            "🐶",
            "🐼",
            "🦁",
            "🐯",
            "🐸",
            "🐙",
            "🦄",
            "🐲",
            "🌟",
            "⚡",
            "🔥",
            "💎",
        ],
    },
});

const getPlayerAvatar = (order) => {
    return props.playerAvatars[order % props.playerAvatars.length];
};
</script>

<style scoped>
@keyframes fade-in {
    from {
        opacity: 0;
        transform: translateY(-20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-fade-in {
    animation: fade-in 0.5s ease-out;
}
</style>
