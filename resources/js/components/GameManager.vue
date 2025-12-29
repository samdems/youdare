<template>
    <div class="game-manager">
        <game-setup v-if="!gameStarted" @game-created="onGameCreated" />

        <game-play
            v-else-if="currentGame"
            :game="currentGame"
            @game-ended="onGameEnded"
        />

        <game-over-screen
            v-else-if="gameResults"
            :game-results="gameResults"
            @play-again="playAgain"
        />
    </div>
</template>

<script setup>
import { computed } from "vue";
import { useGameStore } from "../stores/gameStore";
import { usePlayerStore } from "../stores/playerStore";
import GameSetup from "./GameSetup.vue";
import GamePlay from "./GamePlay.vue";
import GameOverScreen from "./GameOverScreen.vue";

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
