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
                <Flag :size="80" class="mx-auto mb-4 text-primary" />
                <h1 class="text-4xl font-bold mb-2">Game Over!</h1>
                <p class="text-base-content/60">Great game everyone!</p>
            </div>

            <!-- Winner Card -->
            <div
                class="card bg-gradient-to-br from-primary to-secondary text-primary-content shadow-2xl mb-8"
            >
                <div class="card-body items-center text-center py-10">
                    <Trophy :size="80" class="mb-4" />
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
                            <div class="min-w-[3rem] text-center">
                                <Trophy
                                    v-if="index === 0"
                                    :size="32"
                                    color="gold"
                                />
                                <Medal
                                    v-else-if="index === 1"
                                    :size="32"
                                    color="silver"
                                />
                                <Award
                                    v-else-if="index === 2"
                                    :size="32"
                                    color="#CD7F32"
                                />
                                <span v-else class="text-2xl font-bold"
                                    >#{{ index + 1 }}</span
                                >
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
                    <RotateCcw :size="20" />
                    Play Again
                </button>
                <a href="/" class="btn btn-ghost btn-lg gap-2">
                    <Home :size="20" />
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
import { Flag, Trophy, Medal, Award, RotateCcw, Home } from "lucide-vue-next";

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
