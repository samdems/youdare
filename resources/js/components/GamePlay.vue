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
            <div v-if="currentPlayer" class="text-center mb-6">
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
                            {{ processedTaskDescription }}
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

<script setup>
import { onMounted, watch, computed } from "vue";
import { storeToRefs } from "pinia";
import { useGameStore } from "../stores/gameStore";
import { usePlayerStore } from "../stores/playerStore";
import TaskTypeSelector from "./TaskTypeSelector.vue";

const props = defineProps({
    game: {
        type: Object,
        required: true,
    },
});

const emit = defineEmits(["game-ended"]);

// Use the Pinia stores
const gameStore = useGameStore();
const playerStore = usePlayerStore();

// Get reactive refs from stores
const {
    currentTask,
    completedCount,
    skippedCount,
    loading,
    error,
    showingTypeSelector,
    selectedTaskType,
} = storeToRefs(gameStore);

const {
    players,
    currentPlayerId,
    currentPlayer,
    sortedPlayersByScore,
    playerAvatars,
} = storeToRefs(playerStore);

// Computed
const processedTaskDescription = computed(() => {
    if (!currentTask.value || !currentTask.value.description) return "";
    return playerStore.processTaskDescription(currentTask.value.description);
});

// Actions
const getNextTask = async () => {
    await gameStore.getNextTask(currentPlayerId.value);
};

const completeTask = async () => {
    const updatedPlayer = await gameStore.completeTask(currentPlayerId.value);
    if (updatedPlayer) {
        playerStore.updatePlayer(currentPlayerId.value, updatedPlayer);
        playerStore.nextPlayer();
        gameStore.showTypeSelector(playerStore.currentPlayer);
    }
};

const skipTask = () => {
    gameStore.skipTask();
    playerStore.nextPlayer();
    gameStore.showTypeSelector(playerStore.currentPlayer);
};

const showTypeSelector = () => {
    gameStore.showTypeSelector(currentPlayer.value);
};

const onTypeSelected = async (type) => {
    gameStore.onTypeSelected(type);
    await getNextTask();
};

const setTaskType = (type) => {
    gameStore.setTaskType(type);
};

const getPlayerAvatar = (order) => {
    return playerStore.getPlayerAvatar(order);
};

const nextPlayer = () => {
    playerStore.nextPlayer();
    gameStore.showTypeSelector(playerStore.currentPlayer);
};

// Watch for game prop changes
watch(
    () => props.game,
    (newGame) => {
        if (newGame) {
            gameStore.initializeGame(newGame);
            playerStore.setPlayers(newGame.players || []);
            gameStore.showTypeSelector(playerStore.currentPlayer);
        }
    },
    { immediate: true },
);

// Lifecycle
onMounted(() => {
    if (props.game) {
        gameStore.initializeGame(props.game);
        playerStore.setPlayers(props.game.players || []);
    }
});
</script>

<style scoped>
.game-play {
    min-height: 70vh;
}
</style>
