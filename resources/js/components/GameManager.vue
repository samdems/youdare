<template>
    <div class="game-manager">
        <!-- Setup Phase -->
        <game-setup
            v-if="!gameStarted"
            @game-created="onGameCreated"
        />

        <!-- Play Phase -->
        <game-play
            v-else-if="currentGame"
            :game="currentGame"
            @game-ended="onGameEnded"
        />

        <!-- Results Screen -->
        <div v-else-if="gameResults" class="max-w-4xl mx-auto p-4">
            <div class="text-center mb-8">
                <h1 class="text-5xl font-bold mb-3">
                    <span class="text-6xl">🏁</span>
                    <br />
                    Game Over!
                </h1>
                <p class="text-lg opacity-70">
                    Great game everyone!
                </p>
            </div>

            <!-- Winner Card -->
            <div class="card bg-gradient-to-br from-primary to-secondary text-primary-content shadow-2xl mb-6">
                <div class="card-body items-center text-center">
                    <div class="text-8xl mb-4">🥇</div>
                    <h2 class="card-title text-4xl mb-2">
                        {{ gameResults.players[0].name }} Wins!
                    </h2>
                    <p class="text-2xl opacity-90">
                        {{ gameResults.players[0].score }} points
                    </p>
                </div>
            </div>

            <!-- Final Standings -->
            <div class="card bg-base-100 shadow-xl mb-6">
                <div class="card-body">
                    <h3 class="card-title text-2xl mb-4">Final Standings</h3>
                    <div class="space-y-3">
                        <div
                            v-for="(player, index) in gameResults.players"
                            :key="player.id"
                            class="flex items-center justify-between p-4 bg-base-200 rounded-lg"
                        >
                            <div class="flex items-center gap-4">
                                <div class="text-4xl">
                                    {{ index === 0 ? '🥇' : index === 1 ? '🥈' : index === 2 ? '🥉' : `#${index + 1}` }}
                                </div>
                                <div>
                                    <div class="font-bold text-lg">{{ player.name }}</div>
                                    <div class="text-sm opacity-70">{{ player.score }} points</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Game Stats -->
            <div class="stats shadow w-full mb-6">
                <div class="stat">
                    <div class="stat-figure text-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="inline-block w-8 h-8 stroke-current">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="stat-title">Completed Tasks</div>
                    <div class="stat-value text-primary">{{ gameResults.completed }}</div>
                </div>

                <div class="stat">
                    <div class="stat-figure text-secondary">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="inline-block w-8 h-8 stroke-current">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                    </div>
                    <div class="stat-title">Total Rounds</div>
                    <div class="stat-value text-secondary">{{ gameResults.completed + gameResults.skipped }}</div>
                </div>

                <div class="stat">
                    <div class="stat-figure text-warning">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="inline-block w-8 h-8 stroke-current">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path>
                        </svg>
                    </div>
                    <div class="stat-title">Skipped</div>
                    <div class="stat-value text-warning">{{ gameResults.skipped }}</div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex gap-4 justify-center">
                <button @click="playAgain" class="btn btn-primary btn-lg gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z" clip-rule="evenodd" />
                    </svg>
                    Play Again
                </button>
                <a href="/" class="btn btn-ghost btn-lg gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z" />
                    </svg>
                    Back to Home
                </a>
            </div>
        </div>
    </div>
</template>

<script>
import GameSetup from './GameSetup.vue';
import GamePlay from './GamePlay.vue';

export default {
    name: 'GameManager',
    components: {
        GameSetup,
        GamePlay,
    },
    data() {
        return {
            gameStarted: false,
            currentGame: null,
            gameResults: null,
        };
    },
    methods: {
        onGameCreated(game) {
            console.log('Game created:', game);
            this.currentGame = game;
            this.gameStarted = true;
        },

        onGameEnded(results) {
            console.log('Game ended:', results);
            this.gameResults = results;
            this.gameStarted = false;
            this.currentGame = null;
        },

        playAgain() {
            this.gameResults = null;
            this.gameStarted = false;
            this.currentGame = null;
        },
    },
};
</script>

<style scoped>
.game-manager {
    min-height: 70vh;
}
</style>
