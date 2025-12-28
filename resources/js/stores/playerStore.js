import { defineStore } from "pinia";
import { ref, computed } from "vue";
import { games } from "../api";

export const usePlayerStore = defineStore("player", () => {
    // State
    const players = ref([]);
    const currentPlayerId = ref(null);
    const newPlayerName = ref("");
    const nextPlayerId = ref(1);

    const playerAvatars = [
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
    ];

    // Getters
    const currentPlayer = computed(() => {
        return players.value.find((p) => p.id === currentPlayerId.value);
    });

    const sortedPlayersByScore = computed(() => {
        return [...players.value].sort((a, b) => b.score - a.score);
    });

    const playerCount = computed(() => players.value.length);

    const canStartGame = computed(() => players.value.length >= 2);

    // Actions
    const addPlayer = (gender, availableTagsFiltered = []) => {
        if (!newPlayerName.value.trim()) {
            return { success: false, error: "Please enter a player name" };
        }

        const playerName = newPlayerName.value.trim();

        // Check for duplicate names
        if (
            players.value.some(
                (p) => p.name.toLowerCase() === playerName.toLowerCase(),
            )
        ) {
            return {
                success: false,
                error: "A player with this name already exists",
            };
        }

        // Get random avatar
        const usedAvatars = players.value.map((p) => p.avatar);
        const availableAvatars = playerAvatars.filter(
            (avatar) => !usedAvatars.includes(avatar),
        );
        const avatar =
            availableAvatars.length > 0
                ? availableAvatars[
                      Math.floor(Math.random() * availableAvatars.length)
                  ]
                : playerAvatars[
                      Math.floor(Math.random() * playerAvatars.length)
                  ];

        // Start with default tags
        const defaultTagIds = availableTagsFiltered
            .filter((t) => t.is_default)
            .map((t) => t.id);

        // Add gender-specific tags (only if not 'other')
        if (gender !== "other") {
            const genderTags = availableTagsFiltered.filter(
                (t) =>
                    t.default_for_gender === gender ||
                    t.default_for_gender === "both",
            );

            genderTags.forEach((tag) => {
                if (!defaultTagIds.includes(tag.id)) {
                    defaultTagIds.push(tag.id);
                }
            });
        }

        const newPlayer = {
            id: nextPlayerId.value++,
            name: playerName,
            gender: gender,
            avatar: avatar,
            tags: defaultTagIds,
            score: 0,
            order: players.value.length,
        };

        players.value.push(newPlayer);
        newPlayerName.value = "";

        return { success: true, player: newPlayer };
    };

    const removePlayer = (playerId) => {
        const index = players.value.findIndex((p) => p.id === playerId);
        if (index !== -1) {
            players.value.splice(index, 1);
            // Update order
            players.value.forEach((p, i) => {
                p.order = i;
            });
        }
    };

    const togglePlayerTag = (playerId, tagId) => {
        const player = players.value.find((p) => p.id === playerId);
        if (!player) return;

        const tagIndex = player.tags.indexOf(tagId);
        if (tagIndex > -1) {
            player.tags.splice(tagIndex, 1);
        } else {
            player.tags.push(tagId);
        }
    };

    const loadPlayers = async (gameId) => {
        try {
            const data = await games.getGamePlayers(gameId);

            if (data.status === "success" || data.success === true) {
                players.value = data.data.map((player, index) => ({
                    ...player,
                    avatar: playerAvatars[index % playerAvatars.length],
                }));

                if (players.value.length > 0) {
                    currentPlayerId.value = players.value[0].id;
                }

                return { success: true };
            } else {
                throw new Error(data.message || "Failed to load players");
            }
        } catch (err) {
            console.error("Error loading players:", err);
            return {
                success: false,
                error: err.message || "Failed to load players",
            };
        }
    };

    const setPlayers = (playerList) => {
        players.value = playerList.map((player, index) => ({
            ...player,
            avatar:
                player.avatar || playerAvatars[index % playerAvatars.length],
            score: player.score || 0,
            order: index,
        }));

        if (players.value.length > 0 && !currentPlayerId.value) {
            currentPlayerId.value = players.value[0].id;
        }
    };

    const updatePlayer = (playerId, updates) => {
        const player = players.value.find((p) => p.id === playerId);
        if (player) {
            Object.assign(player, updates);
        }
    };

    const nextPlayer = () => {
        if (players.value.length === 0) return;

        const currentIndex = players.value.findIndex(
            (p) => p.id === currentPlayerId.value,
        );
        const nextIndex = (currentIndex + 1) % players.value.length;
        currentPlayerId.value = players.value[nextIndex].id;
    };

    const setCurrentPlayer = (playerId) => {
        if (players.value.some((p) => p.id === playerId)) {
            currentPlayerId.value = playerId;
        }
    };

    const getPlayerAvatar = (order) => {
        return playerAvatars[order % playerAvatars.length];
    };

    const processTaskDescription = (description, task = null) => {
        if (!description || !currentPlayer.value) return description;

        let processed = description;

        // Replace {{someone}} - random player filtered by task's someone_tags and someone_cant_have_tags
        if (processed.includes("{{someone}}")) {
            let eligiblePlayers = players.value.filter(
                (p) => p.id !== currentPlayer.value.id,
            );

            // If task has someone_tags, filter to players that have ALL of those tags (AND logic)
            if (task && task.someone_tags && task.someone_tags.length > 0) {
                eligiblePlayers = eligiblePlayers.filter((p) => {
                    const playerTagIds = p.tags ? p.tags.map((t) => t.id) : [];
                    // Player must have ALL required tags
                    return task.someone_tags.every((tagId) =>
                        playerTagIds.includes(tagId),
                    );
                });
            }

            // If task has someone_cant_have_tags, filter out players that have any of those tags
            if (
                task &&
                task.someone_cant_have_tags &&
                task.someone_cant_have_tags.length > 0
            ) {
                eligiblePlayers = eligiblePlayers.filter((p) => {
                    const playerTagIds = p.tags ? p.tags.map((t) => t.id) : [];
                    return !task.someone_cant_have_tags.some((tagId) =>
                        playerTagIds.includes(tagId),
                    );
                });
            }

            // If task has someone_gender filter, apply gender filtering
            if (task && task.someone_gender) {
                const currentGender = currentPlayer.value.gender;
                if (task.someone_gender === "same" && currentGender) {
                    // Filter to only players with the same gender
                    eligiblePlayers = eligiblePlayers.filter(
                        (p) => p.gender === currentGender,
                    );
                } else if (task.someone_gender === "other" && currentGender) {
                    // Filter to only players with a different gender
                    eligiblePlayers = eligiblePlayers.filter(
                        (p) => p.gender && p.gender !== currentGender,
                    );
                }
                // "any" or no gender set means no filtering needed
            }

            const randomPlayer =
                eligiblePlayers.length > 0
                    ? eligiblePlayers[
                          Math.floor(Math.random() * eligiblePlayers.length)
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
                players.value.length,
            );
        }

        // Replace {{number_of_players/X}} - divided and rounded
        const divisionRegex = /\{\{number_of_players\/(\d+)\}\}/g;
        processed = processed.replace(divisionRegex, (match, divisor) => {
            const result = Math.round(players.value.length / parseInt(divisor));
            return result;
        });

        return processed;
    };

    const reset = () => {
        players.value = [];
        currentPlayerId.value = null;
        newPlayerName.value = "";
        nextPlayerId.value = 1;
    };

    return {
        // State
        players,
        currentPlayerId,
        newPlayerName,
        nextPlayerId,
        playerAvatars,

        // Getters
        currentPlayer,
        sortedPlayersByScore,
        playerCount,
        canStartGame,

        // Actions
        addPlayer,
        removePlayer,
        togglePlayerTag,
        loadPlayers,
        setPlayers,
        updatePlayer,
        nextPlayer,
        setCurrentPlayer,
        getPlayerAvatar,
        processTaskDescription,
        reset,
    };
});
