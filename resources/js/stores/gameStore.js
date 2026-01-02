import { defineStore } from "pinia";
import { ref, computed } from "vue";
import { tags, games, players } from "../api";

export const useGameStore = defineStore("game", () => {
    // State
    const currentGame = ref(null);
    const gamePhase = ref("setup"); // 'setup', 'playing', 'results'
    const gameResults = ref(null);
    const gameName = ref("");
    const maxSpiceRating = ref(1);
    const creatingGame = ref(false);
    const error = ref(null);
    const availableTags = ref([]);
    const groupedTags = ref([]);
    const loadingTags = ref(true);
    const currentTask = ref(null);
    const completedCount = ref(0);
    const skippedCount = ref(0);
    const loading = ref(false);
    const showingTypeSelector = ref(false);
    const selectedTaskType = ref(null);
    const currentRound = ref(1);
    const showingGroupTask = ref(false);
    const currentGroupTask = ref(null);
    const enableGroupTasks = ref(true);

    const defaultTags = [
        { id: "blonde", name: "Blonde" },
        { id: "brunette", name: "Brunette" },
    ];

    // Getters
    const isSetupPhase = computed(() => gamePhase.value === "setup");
    const isPlayingPhase = computed(() => gamePhase.value === "playing");
    const isResultsPhase = computed(() => gamePhase.value === "results");
    const isGameActive = computed(() => currentGame.value !== null);

    const availableTagsFiltered = computed(() => {
        return availableTags.value.filter(
            (tag) => tag.min_spice_level <= maxSpiceRating.value,
        );
    });

    // Actions
    const fetchTags = async () => {
        loadingTags.value = true;
        try {
            const data = await tags.getTags();
            if (data.status === "success" || data.success === true) {
                availableTags.value = data.data;
            } else {
                availableTags.value = defaultTags;
            }
        } catch (err) {
            console.error("Failed to load tags:", err);
            availableTags.value = defaultTags;
        } finally {
            loadingTags.value = false;
        }
    };

    const fetchGroupedTags = async () => {
        loadingTags.value = true;
        try {
            const data = await tags.getGroupedTags(maxSpiceRating.value);
            if (data.status === "success" || data.success === true) {
                groupedTags.value = data.data;
                // Also flatten for availableTags for backward compatibility
                // Tags already have tag_group from API eager loading
                availableTags.value = data.data.flatMap((group) => group.tags);
            } else {
                groupedTags.value = [];
                availableTags.value = defaultTags;
            }
        } catch (err) {
            console.error("Failed to load grouped tags:", err);
            groupedTags.value = [];
            availableTags.value = defaultTags;
        } finally {
            loadingTags.value = false;
        }
    };

    const createGame = async (playersList) => {
        if (playersList.length < 2) {
            error.value = "Please add at least 2 players";
            return null;
        }

        creatingGame.value = true;
        error.value = null;

        try {
            // Step 1: Create the game
            const gameData = await games.createGame({
                name: gameName.value || "New Game",
                max_spice_rating: maxSpiceRating.value,
                enable_group_tasks: enableGroupTasks.value,
            });

            if (gameData.status !== "success" && gameData.success !== true) {
                throw new Error(gameData.message || "Failed to create game");
            }

            const game = gameData.data;

            // Step 2: Add players
            for (const player of playersList) {
                const playerData = await games.addPlayerToGame(game.id, {
                    name: player.name,
                    gender: player.gender,
                    tag_ids: player.tags,
                });

                if (
                    playerData.status !== "success" &&
                    playerData.success !== true
                ) {
                    throw new Error(`Failed to add player ${player.name}`);
                }
            }

            // Step 3: Start the game
            const startData = await games.startGame(game.id);

            if (startData.status !== "success" && startData.success !== true) {
                throw new Error(startData.message || "Failed to start game");
            }

            currentGame.value = startData.data;
            gamePhase.value = "playing";
            return startData.data;
        } catch (err) {
            console.error("Error creating game:", err);
            error.value =
                err.message || "Failed to create game. Please try again.";
            return null;
        } finally {
            creatingGame.value = false;
        }
    };

    const initializeGame = (gameData) => {
        currentGame.value = gameData;
        completedCount.value = gameData.completed_count || 0;
        skippedCount.value = gameData.skipped_count || 0;
        enableGroupTasks.value = gameData.enable_group_tasks ?? true;
        gamePhase.value = "playing";
    };

    const getNextTask = async (playerId) => {
        if (!currentGame.value || !playerId) return;

        loading.value = true;
        error.value = null;
        currentTask.value = null;

        try {
            const taskType =
                selectedTaskType.value && selectedTaskType.value !== "both"
                    ? selectedTaskType.value
                    : null;

            const data = await players.getRandomTask(playerId, taskType);

            if (data.status === "success" || data.success === true) {
                currentTask.value = data.data;
            } else {
                error.value = data.message || "No tasks available";
            }
        } catch (err) {
            console.error("Error fetching task:", err);
            error.value = err.message || "Failed to load task";
        } finally {
            loading.value = false;
        }
    };

    const completeTask = async (playerId) => {
        if (!currentTask.value || !playerId) return null;

        loading.value = true;

        try {
            const data = await players.completeTaskForPlayer(playerId, {
                task_id: currentTask.value.id,
                points: 1,
            });

            if (data.status === "success" || data.success === true) {
                completedCount.value++;

                // Advance player in round tracking
                if (currentGame.value) {
                    await games.advancePlayer(currentGame.value.id);
                }

                return data.data.player;
            }
            return null;
        } catch (err) {
            console.error("Error completing task:", err);
            error.value = err.message || "Failed to complete task";
            return null;
        } finally {
            loading.value = false;
        }
    };

    const skipTask = async () => {
        skippedCount.value++;
        currentTask.value = null;

        // Advance player in round tracking
        if (currentGame.value) {
            try {
                await games.advancePlayer(currentGame.value.id);
            } catch (err) {
                console.error("Error advancing player:", err);
            }
        }

        showingTypeSelector.value = true;
    };

    const showTypeSelector = (currentPlayer) => {
        currentTask.value = null;
        showingTypeSelector.value = true;
        selectedTaskType.value = null;

        // Log current player's info to console (without avatar URL)
        if (currentPlayer) {
            console.log("=== NEW TURN ===");
            console.log(
                `Player: ${currentPlayer.name} (${currentPlayer.gender || "N/A"})`,
            );
            console.log(`Score: ${currentPlayer.score || 0}`);

            if (currentPlayer.tags && currentPlayer.tags.length > 0) {
                console.log("Tags:");
                currentPlayer.tags.forEach((tag) => {
                    const tagName = typeof tag === "object" ? tag.name : tag;
                    console.log(`  - ${tagName}`);
                });
            } else {
                console.log("Tags: None");
            }
            console.log("================");
        }
    };

    const onTypeSelected = (type) => {
        selectedTaskType.value = type;
        showingTypeSelector.value = false;
    };

    const setTaskType = (type) => {
        selectedTaskType.value = type;
    };

    const checkForGroupTask = async () => {
        if (!currentGame.value || !enableGroupTasks.value) return false;

        try {
            const response = await games.getGame(currentGame.value.id);
            if (response.success || response.status === "success") {
                const game = response.data;

                // Update enableGroupTasks from game data
                if (game.enable_group_tasks !== undefined) {
                    enableGroupTasks.value = game.enable_group_tasks;
                }

                // Only proceed if group tasks are enabled
                if (!game.enable_group_tasks) return false;

                const playerCount = game.players ? game.players.length : 0;

                // Check if round is complete
                if (
                    playerCount > 0 &&
                    game.current_player_index >= playerCount
                ) {
                    return true;
                }
            }
        } catch (err) {
            console.error("Error checking for group task:", err);
        }

        return false;
    };

    const getGroupTask = async () => {
        if (!currentGame.value) return;

        loading.value = true;
        error.value = null;
        currentGroupTask.value = null;

        try {
            const data = await games.getGroupTask(currentGame.value.id);

            if (data.status === "success" || data.success === true) {
                currentGroupTask.value = data.data;
                showingGroupTask.value = true;
            } else {
                error.value = data.message || "No group tasks available";
            }
        } catch (err) {
            console.error("Error fetching group task:", err);
            error.value = err.message || "Failed to load group task";
        } finally {
            loading.value = false;
        }
    };

    const completeGroupTask = async () => {
        if (!currentGame.value) return;

        loading.value = true;

        try {
            const data = await games.completeGroupTask(currentGame.value.id);

            if (data.status === "success" || data.success === true) {
                currentRound.value++;
                currentGroupTask.value = null;
                showingGroupTask.value = false;
                return true;
            }
            return false;
        } catch (err) {
            console.error("Error completing group task:", err);
            error.value = err.message || "Failed to complete group task";
            return false;
        } finally {
            loading.value = false;
        }
    };

    const endGame = (players) => {
        const sortedPlayers = [...players].sort((a, b) => b.score - a.score);
        gameResults.value = {
            players: sortedPlayers,
            completed: completedCount.value,
            skipped: skippedCount.value,
        };
        gamePhase.value = "results";
    };

    const playAgain = () => {
        currentGame.value = null;
        gameResults.value = null;
        gameName.value = "";
        maxSpiceRating.value = 1;
        currentTask.value = null;
        completedCount.value = 0;
        skippedCount.value = 0;
        showingTypeSelector.value = false;
        selectedTaskType.value = null;
        gamePhase.value = "setup";
    };

    const reset = () => {
        currentGame.value = null;
        gamePhase.value = "setup";
        gameResults.value = null;
        gameName.value = "";
        maxSpiceRating.value = 1;
        creatingGame.value = false;
        error.value = null;
        currentTask.value = null;
        completedCount.value = 0;
        skippedCount.value = 0;
        loading.value = false;
        showingTypeSelector.value = false;
        selectedTaskType.value = null;
        currentRound.value = 1;
        showingGroupTask.value = false;
        currentGroupTask.value = null;
        enableGroupTasks.value = true;
    };

    return {
        // State
        currentGame,
        gamePhase,
        gameResults,
        gameName,
        maxSpiceRating,
        creatingGame,
        error,
        availableTags,
        groupedTags,
        loadingTags,
        currentTask,
        completedCount,
        skippedCount,
        loading,
        showingTypeSelector,
        selectedTaskType,
        defaultTags,
        currentRound,
        showingGroupTask,
        currentGroupTask,
        enableGroupTasks,

        // Getters
        isSetupPhase,
        isPlayingPhase,
        isResultsPhase,
        isGameActive,
        availableTagsFiltered,

        // Actions
        fetchTags,
        fetchGroupedTags,
        createGame,
        initializeGame,
        getNextTask,
        completeTask,
        skipTask,
        showTypeSelector,
        onTypeSelected,
        setTaskType,
        checkForGroupTask,
        getGroupTask,
        completeGroupTask,
        endGame,
        playAgain,
        reset,
    };
});
