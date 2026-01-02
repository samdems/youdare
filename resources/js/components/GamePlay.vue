<template>
    <div class="game-play max-w-4xl mx-auto p-2 sm:p-4 md:p-6 w-full">
        <!-- Group Task Screen -->
        <group-task-screen
            v-if="showingGroupTask"
            :task="currentGroupTask"
            :players="players"
            :round="currentRound"
            :loading="loading"
            :error="error"
            @complete="onGroupTaskComplete"
            @skip="onGroupTaskSkip"
        />

        <!-- Task Type Selector Screen -->
        <task-type-selector
            v-else-if="showingTypeSelector"
            :player="currentPlayer"
            :players="players"
            :round="completedCount + skippedCount + 1"
            :completed="completedCount"
            :skipped="skippedCount"
            :player-avatars="playerAvatars"
            :enable-group-tasks="enableGroupTasks"
            @type-selected="onTypeSelected"
        />

        <!-- Game Screen -->
        <template v-else>
            <!-- Scoreboard -->
            <div
                v-if="players.length > 0"
                class="flex items-center justify-center gap-2 sm:gap-3 mb-4 sm:mb-6 flex-wrap w-full"
            >
                <div
                    v-for="player in players"
                    :key="player.id"
                    :class="[
                        'flex items-center gap-1 sm:gap-2 px-2 sm:px-4 py-1 sm:py-2 rounded-full transition-all',
                        currentPlayerId === player.id
                            ? 'bg-primary text-primary-content scale-110 shadow-lg'
                            : 'bg-base-200 opacity-60',
                    ]"
                >
                    <img
                        :src="player.avatar"
                        :alt="`${player.name}'s avatar`"
                        class="w-6 h-6 sm:w-8 sm:h-8 rounded-full"
                    />
                    <span
                        class="font-semibold text-xs sm:text-sm truncate max-w-[80px] sm:max-w-none"
                        >{{ player.name }}</span
                    >
                    <span class="badge badge-xs sm:badge-sm">{{
                        player.score
                    }}</span>
                </div>
            </div>

            <!-- Current Task Card -->
            <div
                v-if="currentTask"
                class="card bg-base-100 shadow-2xl mb-4 sm:mb-6 w-full"
            >
                <div class="card-body p-4 sm:p-6 md:p-8">
                    <!-- Task Type & Spice -->
                    <div
                        class="flex justify-between items-center mb-4 sm:mb-6 flex-wrap gap-2"
                    >
                        <div
                            :class="[
                                'badge badge-md sm:badge-lg gap-1 sm:gap-2 px-2 sm:px-4 py-2 sm:py-4',
                                currentTask.type === 'truth'
                                    ? 'badge-info'
                                    : 'badge-secondary',
                            ]"
                        >
                            <MessageCircle
                                v-if="currentTask.type === 'truth'"
                                :size="20"
                                class="sm:w-7 sm:h-7"
                            />
                            <Target v-else :size="20" class="sm:w-7 sm:h-7" />
                            <span
                                class="text-sm sm:text-lg font-bold uppercase"
                                >{{ currentTask.type }}</span
                            >
                        </div>
                        <div class="flex gap-1">
                            <Flame
                                v-for="n in currentTask.spice_rating"
                                :key="n"
                                :size="18"
                                class="text-orange-500 sm:w-6 sm:h-6"
                            />
                        </div>
                    </div>

                    <!-- Task Description -->
                    <div class="text-center py-6 sm:py-8 md:py-12 px-2">
                        <p
                            class="text-xl sm:text-2xl md:text-3xl font-bold leading-relaxed break-words"
                        >
                            {{ processedTaskDescription }}
                        </p>
                    </div>

                    <!-- Task Tags -->
                    <div
                        v-if="currentTask.tags && currentTask.tags.length > 0"
                        class="flex flex-wrap gap-1 sm:gap-2 justify-center mb-4 sm:mb-6"
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
                    <div
                        class="flex flex-col sm:flex-row gap-2 sm:gap-3 justify-center w-full"
                    >
                        <button
                            @click="completeTask"
                            class="btn btn-success btn-md sm:btn-lg gap-2 flex-1 max-w-full sm:max-w-[200px]"
                            :disabled="loading"
                        >
                            <CheckCircle :size="24" />
                            Done
                        </button>
                        <button
                            @click="skipTask"
                            class="btn btn-outline btn-md sm:btn-lg gap-2 flex-1 max-w-full sm:max-w-[200px]"
                            :disabled="loading"
                        >
                            <SkipForward :size="24" />
                            Skip
                        </button>
                    </div>
                </div>
            </div>

            <!-- Loading State -->
            <div v-else-if="loading" class="card bg-base-100 shadow-xl w-full">
                <div
                    class="card-body items-center justify-center py-12 sm:py-20"
                >
                    <Loader :size="48" class="animate-spin text-primary" />
                    <p class="mt-4 text-lg opacity-70">Loading next task...</p>
                </div>
            </div>

            <!-- No Tasks Available -->
            <div
                v-else-if="!currentTask && !loading"
                class="card bg-base-100 shadow-xl w-full"
            >
                <div
                    class="card-body items-center justify-center py-12 sm:py-20"
                >
                    <Frown :size="64" class="mb-4 text-base-content/50" />
                    <p class="text-2xl font-bold mb-2">No tasks available</p>
                    <p class="text-sm opacity-70 mb-6">
                        Try adjusting your game settings
                    </p>
                    <button @click="getNextTask" class="btn btn-primary gap-2">
                        <RotateCcw :size="20" />
                        Try Again
                    </button>
                </div>
            </div>

            <!-- Stats -->
            <div class="grid grid-cols-3 gap-2 sm:gap-3 w-full">
                <div class="stat bg-base-200 rounded-lg p-4 text-center">
                    <div class="stat-title text-xs truncate">Round</div>
                    <div class="stat-value text-xl sm:text-2xl text-primary">
                        {{ completedCount + skippedCount + 1 }}
                    </div>
                </div>
                <div class="stat bg-base-200 rounded-lg p-4 text-center">
                    <div class="stat-title text-xs truncate">Completed</div>
                    <div class="stat-value text-xl sm:text-2xl text-success">
                        {{ completedCount }}
                    </div>
                </div>
                <div class="stat bg-base-200 rounded-lg p-4 text-center">
                    <div class="stat-title text-xs truncate">Skipped</div>
                    <div class="stat-value text-xl sm:text-2xl text-warning">
                        {{ skippedCount }}
                    </div>
                </div>
            </div>
        </template>

        <!-- Error Alert -->
        <div v-if="error" class="alert alert-error shadow-lg mt-6">
            <XCircle :size="24" />
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
import GroupTaskScreen from "./GroupTaskScreen.vue";
import {
    MessageCircle,
    Target,
    Flame,
    CheckCircle,
    SkipForward,
    Loader,
    Frown,
    RotateCcw,
    XCircle,
} from "lucide-vue-next";

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
    currentRound,
    showingGroupTask,
    currentGroupTask,
    enableGroupTasks,
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
    return playerStore.processTaskDescription(
        currentTask.value.description,
        currentTask.value,
    );
});

// Actions
const getNextTask = async () => {
    await gameStore.getNextTask(currentPlayerId.value);
};

const completeTask = async () => {
    const updatedPlayer = await gameStore.completeTask(currentPlayerId.value);
    if (updatedPlayer) {
        playerStore.updatePlayer(currentPlayerId.value, updatedPlayer);

        // Check if round is complete and show group task
        const shouldShowGroupTask = await gameStore.checkForGroupTask();
        if (shouldShowGroupTask) {
            await gameStore.getGroupTask();
        } else {
            playerStore.nextPlayer();
            gameStore.showTypeSelector(playerStore.currentPlayer);
        }
    }
};

const skipTask = async () => {
    await gameStore.skipTask();

    // Check if round is complete and show group task
    const shouldShowGroupTask = await gameStore.checkForGroupTask();
    if (shouldShowGroupTask) {
        await gameStore.getGroupTask();
    } else {
        playerStore.nextPlayer();
        gameStore.showTypeSelector(playerStore.currentPlayer);
    }
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

const onGroupTaskComplete = async () => {
    const success = await gameStore.completeGroupTask();
    if (success) {
        // Reset to first player for new round
        playerStore.setCurrentPlayer(players.value[0].id);
        gameStore.showTypeSelector(playerStore.currentPlayer);
    }
};

const onGroupTaskSkip = async () => {
    const success = await gameStore.completeGroupTask();
    if (success) {
        // Reset to first player for new round
        playerStore.setCurrentPlayer(players.value[0].id);
        gameStore.showTypeSelector(playerStore.currentPlayer);
    }
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

@keyframes spin {
    from {
        transform: rotate(0deg);
    }
    to {
        transform: rotate(360deg);
    }
}

.animate-spin {
    animation: spin 1s linear infinite;
}
</style>
