<template>
    <div class="game-play max-w-4xl mx-auto p-6">
        <!-- Task Type Selector Screen -->
        <task-type-selector
            v-if="showingTypeSelector"
            :player="currentPlayer"
            :players="players"
            :round="completedCount + skippedCount + 1"
            :completed="completedCount"
            :skipped="skippedCount"
            :player-avatars="playerAvatars"
            @type-selected="onTypeSelected"
        />

        <!-- Game Screen -->
        <template v-else>
            <!-- Current Player Badge -->
            <div class="text-center mb-6">
                <div
                    class="inline-flex items-center gap-3 bg-primary text-primary-content px-6 py-3 rounded-full shadow-lg"
                >
                    <span class="text-3xl">{{
                        getPlayerAvatar(currentPlayer.order)
                    }}</span>
                    <span class="font-bold text-lg">{{
                        currentPlayer.name
                    }}</span>
                </div>
            </div>

            <!-- Scoreboard -->
            <div class="flex items-center justify-center gap-3 mb-6 flex-wrap">
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
                    <span class="text-xl">{{
                        getPlayerAvatar(player.order)
                    }}</span>
                    <span class="font-semibold text-sm">{{ player.name }}</span>
                    <span class="badge badge-sm">{{ player.score }}</span>
                </div>
            </div>

            <!-- Current Task Card -->
            <div v-if="currentTask" class="card bg-base-100 shadow-2xl mb-6">
                <div class="card-body p-8">
                    <!-- Task Type & Spice -->
                    <div class="flex justify-between items-center mb-6">
                        <div
                            :class="[
                                'badge badge-lg gap-2 px-4 py-4',
                                currentTask.type === 'truth'
                                    ? 'badge-info'
                                    : 'badge-secondary',
                            ]"
                        >
                            <span class="text-3xl">{{
                                currentTask.type === "truth" ? "💬" : "🎯"
                            }}</span>
                            <span class="text-lg font-bold uppercase">{{
                                currentTask.type
                            }}</span>
                        </div>
                        <div class="text-2xl">
                            {{ "🌶️".repeat(currentTask.spice_rating) }}
                        </div>
                    </div>

                    <!-- Task Description -->
                    <div class="text-center py-12">
                        <p class="text-3xl font-bold leading-relaxed">
                            {{
                                processTaskDescription(currentTask.description)
                            }}
                        </p>
                    </div>

                    <!-- Task Tags -->
                    <div
                        v-if="currentTask.tags && currentTask.tags.length > 0"
                        class="flex flex-wrap gap-2 justify-center mb-6"
                    >
                        <span
                            v-for="tag in currentTask.tags"
                            :key="tag.id"
                            class="badge badge-ghost badge-sm"
                        >
                            {{ tag.name }}
                        </span>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex gap-3 justify-center">
                        <button
                            @click="completeTask"
                            class="btn btn-success btn-lg gap-2 flex-1 max-w-[200px]"
                            :disabled="loading"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-6 w-6"
                                viewBox="0 0 20 20"
                                fill="currentColor"
                            >
                                <path
                                    fill-rule="evenodd"
                                    d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                    clip-rule="evenodd"
                                />
                            </svg>
                            Done
                        </button>
                        <button
                            @click="skipTask"
                            class="btn btn-outline btn-lg gap-2 flex-1 max-w-[200px]"
                            :disabled="loading"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-6 w-6"
                                viewBox="0 0 20 20"
                                fill="currentColor"
                            >
                                <path
                                    fill-rule="evenodd"
                                    d="M10.293 15.707a1 1 0 010-1.414L14.586 10l-4.293-4.293a1 1 0 111.414-1.414l5 5a1 1 0 010 1.414l-5 5a1 1 0 01-1.414 0z"
                                    clip-rule="evenodd"
                                />
                                <path
                                    fill-rule="evenodd"
                                    d="M4.293 15.707a1 1 0 010-1.414L8.586 10 4.293 5.707a1 1 0 011.414-1.414l5 5a1 1 0 010 1.414l-5 5a1 1 0 01-1.414 0z"
                                    clip-rule="evenodd"
                                />
                            </svg>
                            Skip
                        </button>
                    </div>
                </div>
            </div>

            <!-- Loading State -->
            <div v-else-if="loading" class="card bg-base-100 shadow-xl">
                <div class="card-body items-center justify-center py-20">
                    <span class="loading loading-spinner loading-lg"></span>
                    <p class="mt-4 text-lg opacity-70">Loading next task...</p>
                </div>
            </div>

            <!-- No Tasks Available -->
            <div
                v-else-if="!currentTask && !loading"
                class="card bg-base-100 shadow-xl"
            >
                <div class="card-body items-center justify-center py-20">
                    <div class="text-7xl mb-4">😕</div>
                    <p class="text-2xl font-bold mb-2">No tasks available</p>
                    <p class="text-sm opacity-70 mb-6">
                        Try adjusting your game settings
                    </p>
                    <button @click="getNextTask" class="btn btn-primary">
                        Try Again
                    </button>
                </div>
            </div>

            <!-- Stats -->
            <div class="grid grid-cols-3 gap-3">
                <div class="stat bg-base-200 rounded-lg p-4 text-center">
                    <div class="stat-title text-xs">Round</div>
                    <div class="stat-value text-2xl text-primary">
                        {{ completedCount + skippedCount + 1 }}
                    </div>
                </div>
                <div class="stat bg-base-200 rounded-lg p-4 text-center">
                    <div class="stat-title text-xs">Completed</div>
                    <div class="stat-value text-2xl text-success">
                        {{ completedCount }}
                    </div>
                </div>
                <div class="stat bg-base-200 rounded-lg p-4 text-center">
                    <div class="stat-title text-xs">Skipped</div>
                    <div class="stat-value text-2xl text-warning">
                        {{ skippedCount }}
                    </div>
                </div>
            </div>
        </template>

        <!-- Error Alert -->
        <div v-if="error" class="alert alert-error shadow-lg mt-6">
            <svg
                xmlns="http://www.w3.org/2000/svg"
                class="stroke-current shrink-0 h-6 w-6"
                fill="none"
                viewBox="0 0 24 24"
            >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"
                />
            </svg>
            <span>{{ error }}</span>
        </div>
    </div>
</template>

<script>
import TaskTypeSelector from "./TaskTypeSelector.vue";

export default {
    name: "GamePlay",
    components: {
        TaskTypeSelector,
    },
    props: {
        game: {
            type: Object,
            required: true,
        },
    },
    data() {
        return {
            players: [],
            currentPlayerId: null,
            currentTask: null,
            completedCount: 0,
            skippedCount: 0,
            loading: false,
            error: null,
            showingTypeSelector: false,
            selectedTaskType: null,
            playerAvatars: [
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
        };
    },
    computed: {
        currentPlayer() {
            return this.players.find((p) => p.id === this.currentPlayerId);
        },
        sortedPlayersByScore() {
            return [...this.players].sort((a, b) => b.score - a.score);
        },
        processedTaskDescription() {
            if (!this.currentTask || !this.currentTask.description) return "";
            return this.processTaskDescription(this.currentTask.description);
        },
    },
    mounted() {
        this.loadPlayers();
    },
    methods: {
        async loadPlayers() {
            this.loading = true;
            try {
                const response = await fetch(`/api/games/${this.game.id}`);
                const data = await response.json();

                if (data.success) {
                    this.players = data.data.players || [];
                    this.currentPlayerId = data.data.current_player_id;
                    this.completedCount = data.data.completed_count || 0;
                    this.skippedCount = data.data.skipped_count || 0;

                    if (this.players.length === 0) {
                        this.error = "No players found";
                        return;
                    }

                    this.showTypeSelector();
                } else {
                    this.error = data.message || "Failed to load game";
                }
            } catch (err) {
                console.error("Error loading players:", err);
                this.error = "Failed to load game data";
            } finally {
                this.loading = false;
            }
        },

        async getNextTask() {
            this.loading = true;
            this.error = null;

            try {
                const url = `/api/games/${this.game.id}/next-task`;
                const params = new URLSearchParams({
                    player_id: this.currentPlayerId,
                });

                if (this.selectedTaskType && this.selectedTaskType !== "both") {
                    params.append("type", this.selectedTaskType);
                }

                const response = await fetch(`${url}?${params}`);
                const data = await response.json();

                if (data.success && data.data) {
                    this.currentTask = data.data;
                } else {
                    this.error = data.message || "No tasks available";
                }
            } catch (err) {
                console.error("Error getting next task:", err);
                this.error = "Failed to get next task";
            } finally {
                this.loading = false;
            }
        },

        async completeTask() {
            if (!this.currentTask) return;

            this.loading = true;
            this.error = null;

            try {
                const response = await fetch(
                    `/api/games/${this.game.id}/complete-task`,
                    {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": document.querySelector(
                                'meta[name="csrf-token"]',
                            ).content,
                        },
                        body: JSON.stringify({
                            task_id: this.currentTask.id,
                            player_id: this.currentPlayerId,
                        }),
                    },
                );

                const data = await response.json();

                if (data.success) {
                    this.completedCount++;
                    const player = this.players.find(
                        (p) => p.id === this.currentPlayerId,
                    );
                    if (player) {
                        player.score = (player.score || 0) + 1;
                    }

                    if (data.data && data.data.game_over) {
                        this.$emit("game-ended", {
                            players: this.sortedPlayersByScore,
                            completed: this.completedCount,
                            skipped: this.skippedCount,
                        });
                    } else {
                        this.nextPlayer();
                    }
                } else {
                    this.error = data.message || "Failed to complete task";
                }
            } catch (err) {
                console.error("Error completing task:", err);
                this.error = "Failed to complete task";
            } finally {
                this.loading = false;
            }
        },

        skipTask() {
            this.skippedCount++;
            this.nextPlayer();
        },

        nextPlayer() {
            const currentIndex = this.players.findIndex(
                (p) => p.id === this.currentPlayerId,
            );
            const nextIndex = (currentIndex + 1) % this.players.length;
            this.currentPlayerId = this.players[nextIndex].id;
            this.showTypeSelector();
        },

        showTypeSelector() {
            this.currentTask = null;
            this.showingTypeSelector = true;
            this.selectedTaskType = null;
        },

        onTypeSelected(type) {
            this.selectedTaskType = type;
            this.showingTypeSelector = false;
            this.getNextTask();
        },

        setTaskType(type) {
            this.selectedTaskType = type;
        },

        getPlayerAvatar(order) {
            return this.playerAvatars[order % this.playerAvatars.length];
        },

        processTaskDescription(description) {
            if (!description || !this.currentPlayer) return description;

            let processed = description;

            // Replace {{same_gender}} - random player with same gender
            if (processed.includes("{{same_gender}}")) {
                const sameGenderPlayers = this.players.filter(
                    (p) =>
                        p.id !== this.currentPlayer.id &&
                        p.gender === this.currentPlayer.gender,
                );
                const randomPlayer =
                    sameGenderPlayers.length > 0
                        ? sameGenderPlayers[
                              Math.floor(
                                  Math.random() * sameGenderPlayers.length,
                              )
                          ]
                        : null;
                processed = processed.replace(
                    /\{\{same_gender\}\}/g,
                    randomPlayer ? randomPlayer.name : "someone",
                );
            }

            // Replace {{other_gender}} - random player with different gender
            if (processed.includes("{{other_gender}}")) {
                const otherGenderPlayers = this.players.filter(
                    (p) =>
                        p.id !== this.currentPlayer.id &&
                        p.gender !== this.currentPlayer.gender,
                );
                const randomPlayer =
                    otherGenderPlayers.length > 0
                        ? otherGenderPlayers[
                              Math.floor(
                                  Math.random() * otherGenderPlayers.length,
                              )
                          ]
                        : null;
                processed = processed.replace(
                    /\{\{other_gender\}\}/g,
                    randomPlayer ? randomPlayer.name : "someone",
                );
            }

            // Replace {{any_gender}} - any random player
            if (processed.includes("{{any_gender}}")) {
                const otherPlayers = this.players.filter(
                    (p) => p.id !== this.currentPlayer.id,
                );
                const randomPlayer =
                    otherPlayers.length > 0
                        ? otherPlayers[
                              Math.floor(Math.random() * otherPlayers.length)
                          ]
                        : null;
                processed = processed.replace(
                    /\{\{any_gender\}\}/g,
                    randomPlayer ? randomPlayer.name : "someone",
                );
            }

            // Replace {{someone}} - any random player (alias for any_gender)
            if (processed.includes("{{someone}}")) {
                const otherPlayers = this.players.filter(
                    (p) => p.id !== this.currentPlayer.id,
                );
                const randomPlayer =
                    otherPlayers.length > 0
                        ? otherPlayers[
                              Math.floor(Math.random() * otherPlayers.length)
                          ]
                        : null;
                processed = processed.replace(
                    /\{\{someone\}\}/g,
                    randomPlayer ? randomPlayer.name : "someone",
                );
            }

            // Replace {{number_of_players}}
            if (processed.includes("{{number_of_players}}")) {
                processed = processed.replace(
                    /\{\{number_of_players\}\}/g,
                    this.players.length,
                );
            }

            // Replace {{number_of_players/X}} - divided and rounded
            const divisionRegex = /\{\{number_of_players\/(\d+)\}\}/g;
            processed = processed.replace(divisionRegex, (match, divisor) => {
                const result = Math.round(
                    this.players.length / parseInt(divisor),
                );
                return result;
            });

            return processed;
        },
    },
};
</script>

<style scoped>
.game-play {
    min-height: 70vh;
}
</style>
