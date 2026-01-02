<template>
    <div class="max-w-3xl mx-auto p-4 sm:p-6 w-full">
        <!-- Header -->
        <div class="text-center mb-10">
            <Flag :size="80" class="mx-auto mb-4 text-primary" />
            <h1 class="text-4xl font-bold mb-2">Game Over!</h1>
            <p class="text-base-content/60">Great game everyone!</p>
        </div>

        <!-- Winner Card -->
        <winner-card :winner="gameResults.players[0]" />

        <!-- Leaderboard -->
        <leaderboard :players="gameResults.players" />

        <!-- Stats -->
        <div class="grid grid-cols-3 gap-3 mb-8">
            <div class="stat bg-base-200 rounded-lg p-4 text-center">
                <div class="stat-title text-xs">Total Rounds</div>
                <div class="stat-value text-2xl text-primary">
                    {{ gameResults.completed + gameResults.skipped }}
                </div>
            </div>
            <div class="stat bg-base-200 rounded-lg p-4 text-center">
                <div class="stat-title text-xs">Completed</div>
                <div class="stat-value text-2xl text-success">
                    {{ gameResults.completed }}
                </div>
            </div>
            <div class="stat bg-base-200 rounded-lg p-4 text-center">
                <div class="stat-title text-xs">Skipped</div>
                <div class="stat-value text-2xl text-warning">
                    {{ gameResults.skipped }}
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex gap-3 justify-center">
            <button
                @click="$emit('play-again')"
                class="btn btn-primary btn-lg gap-2"
            >
                <RotateCcw :size="20" />
                Play Again
            </button>
            <a href="/" class="btn btn-ghost btn-lg gap-2">
                <Home :size="20" />
                Home
            </a>
        </div>
    </div>
</template>

<script setup>
import { Flag, RotateCcw, Home } from "lucide-vue-next";
import WinnerCard from "./WinnerCard.vue";
import Leaderboard from "./Leaderboard.vue";

defineProps({
    gameResults: {
        type: Object,
        required: true,
    },
});

defineEmits(["play-again"]);
</script>

<style scoped>
/* Component styles */
</style>
