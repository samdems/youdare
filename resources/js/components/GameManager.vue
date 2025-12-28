<template>
    <div class="game-manager">
        <!-- Setup Phase -->
        <game-setup v-if="!gameStarted" @game-created="onGameCreated" />

        <!-- Play Phase -->
        <game-play
            v-else-if="currentGame"
            :game="currentGame"
            @game-ended="onGameEnded"
        />

        <!-- Game Over Screen -->
        <div v-else-if="gameResults" class="max-w-3xl mx-auto p-6">
            <!-- Header -->
            <div class="text-center mb-10">
                <div class="text-8xl mb-4">🏁</div>
                <h1 class="text-4xl font-bold mb-2">Game Over!</h1>
                <p class="text-base-content/60">Great game everyone!</p>
            </div>

            <!-- Winner Card -->
            <div
                class="card bg-gradient-to-br from-primary to-secondary text-primary-content shadow-2xl mb-8"
            >
                <div class="card-body items-center text-center py-10">
                    <div class="text-8xl mb-4">🥇</div>
                    <h2 class="text-3xl font-bold mb-2">
                        {{ gameResults.players[0].name }}
                    </h2>
                    <p class="text-xl opacity-90">
                        {{ gameResults.players[0].score }} points
                    </p>
                </div>
            </div>

            <!-- Leaderboard -->
            <div class="card bg-base-100 shadow-xl mb-8">
                <div class="card-body">
                    <h3 class="text-xl font-bold mb-4">Final Standings</h3>
                    <div class="space-y-2">
                        <div
                            v-for="(player, index) in gameResults.players"
                            :key="player.id"
                            class="flex items-center gap-4 p-4 bg-base-200 rounded-lg"
                        >
                            <div class="text-3xl min-w-[3rem] text-center">
                                {{
                                    index === 0
                                        ? "🥇"
                                        : index === 1
                                          ? "🥈"
                                          : index === 2
                                            ? "🥉"
                                            : `#${index + 1}`
                                }}
                            </div>
                            <div class="flex-1">
                                <div class="font-bold text-lg">
                                    {{ player.name }}
                                </div>
                            </div>
                            <div class="text-2xl font-bold text-primary">
                                {{ player.score }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

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
                <button @click="playAgain" class="btn btn-primary btn-lg gap-2">
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-5 w-5"
                        viewBox="0 0 20 20"
                        fill="currentColor"
                    >
                        <path
                            fill-rule="evenodd"
                            d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z"
                            clip-rule="evenodd"
                        />
                    </svg>
                    Play Again
                </button>
                <a href="/" class="btn btn-ghost btn-lg gap-2">
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-5 w-5"
                        viewBox="0 0 20 20"
                        fill="currentColor"
                    >
                        <path
                            d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"
                        />
                    </svg>
                    Home
                </a>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed } from "vue";
import { useGameStore } from "../stores/gameStore";
import { usePlayerStore } from "../stores/playerStore";
import GameSetup from "./GameSetup.vue";
import GamePlay from "./GamePlay.vue";

const gameStore = useGameStore();
const playerStore = usePlayerStore();

const gameStarted = computed(() => gameStore.isPlayingPhase);
const currentGame = computed(() => gameStore.currentGame);
const gameResults = computed(() => gameStore.gameResults);

const onGameCreated = () => {
    // Game creation is handled in GameSetup, just transition phase
};

const onGameEnded = () => {
    gameStore.endGame(playerStore.players);
};

const playAgain = () => {
    gameStore.playAgain();
    playerStore.reset();
};
</script>

<style scoped>
.game-manager {
    min-height: 70vh;
}
</style>
