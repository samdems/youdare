<template>
    <div class="game-play max-w-4xl mx-auto p-4">
        <!-- Task Type Selector Screen (shown between rounds) -->
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

        <!-- Game Screen (shown during active task) -->
        <template v-else>
            <!-- Player Turn Header -->
            <player-turn-header
                v-if="currentPlayer"
                :player="currentPlayer"
                :player-avatars="playerAvatars"
            />

            <!-- Players Scoreboard -->
            <div class="card bg-base-200 shadow-lg mb-6">
                <div class="card-body p-4">
                    <div
                        class="flex flex-wrap items-center justify-center gap-4"
                    >
                        <div
                            v-for="player in players"
                            :key="player.id"
                            :class="[
                                'flex flex-col items-center p-3 rounded-lg transition-all min-w-[100px]',
                                currentPlayerId === player.id
                                    ? 'bg-primary text-primary-content scale-110 shadow-lg'
                                    : 'opacity-60',
                            ]"
                        >
                            <div class="text-3xl mb-1">
                                {{ getPlayerAvatar(player.order) }}
                            </div>
                            <div class="font-bold text-center text-sm">
                                {{ player.name }}
                                <span v-if="player.gender" class="text-xs">
                                    {{ player.gender === "male" ? "👨" : "👩" }}
                                </span>
                            </div>
                            <div class="text-xs opacity-70">
                                {{ player.score }} pts
                            </div>
                            <!-- Player Tags -->
                            <div
                                v-if="player.tags && player.tags.length > 0"
                                class="flex flex-wrap gap-1 justify-center mt-1 max-w-[120px]"
                            >
                                <div
                                    v-for="tag in player.tags.slice(0, 3)"
                                    :key="tag.id"
                                    class="badge badge-xs"
                                    :class="
                                        currentPlayerId === player.id
                                            ? 'badge-neutral'
                                            : 'badge-ghost'
                                    "
                                    :title="tag.name"
                                >
                                    {{ tag.name }}
                                </div>
                                <div
                                    v-if="player.tags.length > 3"
                                    class="badge badge-xs"
                                    :class="
                                        currentPlayerId === player.id
                                            ? 'badge-neutral'
                                            : 'badge-ghost'
                                    "
                                    :title="
                                        player.tags
                                            .slice(3)
                                            .map((t) => t.name)
                                            .join(', ')
                                    "
                                >
                                    +{{ player.tags.length - 3 }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Current Task Card -->
            <div v-if="currentTask" class="card bg-base-100 shadow-xl mb-6">
                <div class="card-body">
                    <!-- Task Type Badge -->
                    <div class="flex justify-between items-start mb-4">
                        <div
                            :class="[
                                'badge badge-lg gap-2 px-4 py-3',
                                currentTask.type === 'truth'
                                    ? 'badge-info'
                                    : 'badge-secondary',
                            ]"
                        >
                            <span class="text-2xl">{{
                                currentTask.type === "truth" ? "💬" : "🎯"
                            }}</span>
                            <span class="text-lg font-bold">{{
                                currentTask.type.toUpperCase()
                            }}</span>
                        </div>
                        <div class="flex gap-2">
                            <div class="badge badge-warning badge-lg">
                                {{ "🌶️".repeat(currentTask.spice_rating) }}
                            </div>
                        </div>
                    </div>

                    <!-- Task Description -->
                    <div class="text-center py-8">
                        <p
                            class="text-2xl md:text-3xl font-semibold leading-relaxed"
                        >
                            {{ currentTask.description }}
                        </p>
                    </div>

                    <!-- Task Tags -->
                    <div
                        v-if="currentTask.tags && currentTask.tags.length > 0"
                        class="flex flex-wrap gap-2 justify-center mb-4"
                    >
                        <div
                            v-for="tag in currentTask.tags"
                            :key="tag.id"
                            class="badge badge-outline"
                        >
                            <span>{{ tag.name }}</span>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="card-actions justify-center gap-4 mt-6">
                        <button
                            @click="completeTask"
                            class="btn btn-success btn-lg gap-2 px-8"
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
                            Completed!
                        </button>
                        <button
                            @click="skipTask"
                            class="btn btn-outline btn-lg gap-2 px-8"
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
                <div class="card-body items-center justify-center py-16">
                    <span class="loading loading-spinner loading-lg"></span>
                    <p class="mt-4 text-lg">Getting next task...</p>
                </div>
            </div>

            <!-- No Tasks Available -->
            <div
                v-else-if="!currentTask && !loading"
                class="card bg-base-100 shadow-xl"
            >
                <div class="card-body items-center justify-center py-16">
                    <div class="text-6xl mb-4">😕</div>
                    <p class="text-xl font-semibold mb-2">No tasks available</p>
                    <p class="text-sm opacity-70 mb-4">
                        Try adjusting your tags or spice level
                    </p>
                    <button @click="getNextTask" class="btn btn-primary">
                        Try Again
                    </button>
                </div>
            </div>

            <!-- Game Stats -->
            <div class="stats shadow w-full mb-6">
                <div class="stat">
                    <div class="stat-title">Round</div>
                    <div class="stat-value text-primary">
                        {{ completedCount + skippedCount + 1 }}
                    </div>
                </div>
                <div class="stat">
                    <div class="stat-title">Completed</div>
                    <div class="stat-value text-success">
                        {{ completedCount }}
                    </div>
                </div>
                <div class="stat">
                    <div class="stat-title">Skipped</div>
                    <div class="stat-value text-warning">
                        {{ skippedCount }}
                    </div>
                </div>
            </div>
        </template>

        <!-- Error Alert -->
        <div v-if="error" class="alert alert-error shadow-lg">
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
import PlayerTurnHeader from "./PlayerTurnHeader.vue";

export default {
    name: "GamePlay",
    components: {
        TaskTypeSelector,
        PlayerTurnHeader,
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
            loading: false,
            error: null,
            completedCount: 0,
            skippedCount: 0,
            taskType: "both",

            showingTypeSelector: false,
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
    },
    mounted() {
        this.loadPlayers();
    },
    methods: {
        async loadPlayers() {
            try {
                const response = await fetch(
                    `/api/games/${this.game.id}/players`,
                );
                const data = await response.json();
                if (data.success) {
                    this.players = data.data;
                    console.log("Loaded players:", this.players);
                    console.log(
                        "Player tags:",
                        this.players.map((p) => ({
                            name: p.name,
                            tags: p.tags,
                        })),
                    );
                    if (this.players.length > 0) {
                        this.currentPlayerId = this.players[0].id;
                        // Show type selector for first player
                        this.showingTypeSelector = true;
                    }
                }
            } catch (err) {
                console.error("Error loading players:", err);
                this.error = "Failed to load players";
            }
        },

        async getNextTask() {
            if (!this.currentPlayer) return;

            this.loading = true;
            this.error = null;
            this.currentTask = null;

            try {
                const typeParam =
                    this.taskType !== "both" ? `?type=${this.taskType}` : "";
                const response = await fetch(
                    `/api/players/${this.currentPlayerId}/tasks/random${typeParam}`,
                );
                const data = await response.json();

                if (data.success) {
                    this.currentTask = data.data;
                    console.log("Current task:", this.currentTask);
                    console.log("Task tags:", this.currentTask.tags);
                } else {
                    this.error = data.message || "No tasks available";
                }
            } catch (err) {
                console.error("Error fetching task:", err);
                this.error = "Failed to load task";
            } finally {
                this.loading = false;
            }
        },

        async completeTask() {
            if (!this.currentTask || !this.currentPlayer) return;

            this.loading = true;

            try {
                // Complete the task (increments score AND removes tags)
                const response = await fetch(
                    `/api/players/${this.currentPlayerId}/complete-task`,
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
                            points: 1,
                        }),
                    },
                );

                const data = await response.json();
                if (data.success) {
                    // Update local player data (score and tags)
                    const player = this.players.find(
                        (p) => p.id === this.currentPlayerId,
                    );
                    if (player && data.data.player) {
                        player.score = data.data.player.score;
                        player.tags = data.data.player.tags;
                    }
                    this.completedCount++;

                    // Log if tags were removed
                    if (data.data.removed_tags_count > 0) {
                        console.log(
                            `Removed ${data.data.removed_tags_count} tag(s) from player`,
                        );
                    }
                }

                // Move to next player and show type selector
                this.nextPlayer();
                this.showTypeSelector();
            } catch (err) {
                console.error("Error completing task:", err);
                this.error = "Failed to complete task";
                this.loading = false;
            }
        },

        skipTask() {
            this.skippedCount++;
            this.nextPlayer();
            this.showTypeSelector();
        },

        nextPlayer() {
            const currentIndex = this.players.findIndex(
                (p) => p.id === this.currentPlayerId,
            );
            const nextIndex = (currentIndex + 1) % this.players.length;
            this.currentPlayerId = this.players[nextIndex].id;
        },

        showTypeSelector() {
            this.currentTask = null;
            this.showingTypeSelector = true;
        },

        onTypeSelected(type) {
            this.taskType = type;
            this.showingTypeSelector = false;
            this.getNextTask();
        },

        setTaskType(type) {
            this.taskType = type;
            this.getNextTask();
        },

        getPlayerAvatar(order) {
            return this.playerAvatars[order % this.playerAvatars.length];
        },
    },
};
</script>

<style scoped>
.game-play {
    min-height: 70vh;
}
</style>
