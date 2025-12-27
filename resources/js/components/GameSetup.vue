<template>
    <div class="game-setup max-w-4xl mx-auto p-6">
        <!-- Header -->
        <div class="text-center mb-10">
            <h1 class="text-6xl font-bold mb-2">
                <span class="text-7xl">🎮</span>
            </h1>
            <h2 class="text-3xl font-bold mb-2">New Game</h2>
            <p class="text-base-content/60">
                Setup your game in just a few steps
            </p>
        </div>

        <div class="space-y-8">
            <!-- Game Settings Card -->
            <div class="card bg-base-100 shadow-lg">
                <div class="card-body">
                    <h3 class="text-xl font-bold mb-4 flex items-center gap-2">
                        <span>⚙️</span>
                        <span>Game Settings</span>
                    </h3>

                    <!-- Spice Level -->
                    <div>
                        <label class="label">
                            <span class="label-text font-semibold"
                                >Choose Spice Level</span
                            >
                        </label>
                        <div class="grid grid-cols-5 gap-3">
                            <button
                                v-for="level in 5"
                                :key="level"
                                @click="maxSpiceRating = level"
                                :class="[
                                    'btn btn-outline flex-col h-24 gap-2 transition-all px-4 py-4',
                                    maxSpiceRating === level
                                        ? 'btn-warning btn-active'
                                        : '',
                                ]"
                            >
                                <span class="text-2xl">{{
                                    "🌶️".repeat(level)
                                }}</span>
                                <span class="text-xs">{{
                                    level === 1
                                        ? "Mild"
                                        : level === 2
                                          ? "Medium"
                                          : level === 3
                                            ? "Hot"
                                            : level === 4
                                              ? "Extra"
                                              : "Extreme"
                                }}</span>
                            </button>
                        </div>
                    </div>

                    <div class="alert alert-info mt-4">
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            class="stroke-current shrink-0 w-5 h-5"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                            ></path>
                        </svg>
                        <span class="text-sm"
                            >{{ availableTagsFiltered.length }} tags available
                            at this level</span
                        >
                    </div>
                </div>
            </div>

            <!-- Add Player Card -->
            <div class="card bg-base-100 shadow-lg">
                <div class="card-body">
                    <h3 class="text-xl font-bold mb-4 flex items-center gap-2">
                        <span>👥</span>
                        <span>Players</span>
                        <span class="badge badge-primary">{{
                            players.length
                        }}</span>
                    </h3>

                    <!-- Add Player Form -->
                    <div class="space-y-3 mb-6">
                        <input
                            type="text"
                            v-model="newPlayerName"
                            placeholder="Enter player name"
                            class="input input-bordered w-full"
                            maxlength="20"
                        />
                        <div class="grid grid-cols-3 gap-2">
                            <button
                                @click="addPlayer('male')"
                                class="btn btn-outline gap-2"
                                :disabled="!newPlayerName.trim()"
                            >
                                <span class="text-2xl">👨</span>
                                <span>Add Male</span>
                            </button>
                            <button
                                @click="addPlayer('female')"
                                class="btn btn-outline gap-2"
                                :disabled="!newPlayerName.trim()"
                            >
                                <span class="text-2xl">👩</span>
                                <span>Add Female</span>
                            </button>
                            <button
                                @click="addPlayer('other')"
                                class="btn btn-outline gap-2"
                                :disabled="!newPlayerName.trim()"
                            >
                                <span class="text-2xl">🧑</span>
                                <span>Add Other</span>
                            </button>
                        </div>
                    </div>

                    <!-- Players List -->
                    <div v-if="players.length > 0" class="space-y-3">
                        <div
                            v-for="player in players"
                            :key="player.id"
                            class="flex items-start gap-3 p-3 rounded-lg bg-base-200 hover:bg-base-300 transition-colors"
                        >
                            <div class="text-3xl">{{ player.avatar }}</div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="font-bold">{{
                                        player.name
                                    }}</span>
                                    <span class="text-lg">{{
                                        player.gender === "male"
                                            ? "👨"
                                            : player.gender === "female"
                                              ? "👩"
                                              : "🧑"
                                    }}</span>
                                    <span class="text-xs text-base-content/60"
                                        >{{ player.tags.length }} tags</span
                                    >
                                </div>

                                <!-- Tags - All Available -->
                                <div class="border-t border-base-300 pt-2 mt-2">
                                    <div
                                        class="text-xs font-semibold mb-2 opacity-70"
                                    >
                                        Available Tags
                                    </div>
                                    <div class="flex flex-wrap gap-1">
                                        <button
                                            v-for="tag in availableTagsFiltered"
                                            :key="tag.id"
                                            @click="
                                                togglePlayerTag(
                                                    player.id,
                                                    tag.id,
                                                )
                                            "
                                            :class="[
                                                'badge badge-sm gap-1 cursor-pointer transition-all',
                                                player.tags.includes(tag.id)
                                                    ? 'badge-primary'
                                                    : 'badge-outline hover:badge-primary',
                                            ]"
                                        >
                                            {{ tag.name }}
                                            <span
                                                v-if="
                                                    player.tags.includes(tag.id)
                                                "
                                                >✓</span
                                            >
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <button
                                @click="removePlayer(player.id)"
                                class="btn btn-ghost btn-sm btn-circle"
                                title="Remove player"
                            >
                                ✕
                            </button>
                        </div>
                    </div>

                    <div
                        v-else
                        class="text-center py-12 border-2 border-dashed border-base-300 rounded-lg"
                    >
                        <p class="text-base-content/50">No players yet</p>
                        <p class="text-sm text-base-content/40 mt-1">
                            Add at least 2 players to start
                        </p>
                    </div>
                </div>
            </div>

            <!-- Start Game Button -->
            <div
                class="card bg-gradient-to-r from-success/10 to-primary/10 shadow-lg"
            >
                <div class="card-body items-center text-center">
                    <button
                        @click="createGame"
                        class="btn btn-success btn-lg gap-2 min-w-[200px]"
                        :disabled="players.length < 2 || creatingGame"
                        :class="{ loading: creatingGame }"
                    >
                        <svg
                            v-if="!creatingGame"
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-6 w-6"
                            viewBox="0 0 20 20"
                            fill="currentColor"
                        >
                            <path
                                fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z"
                                clip-rule="evenodd"
                            />
                        </svg>
                        {{ creatingGame ? "Creating..." : "Start Game" }}
                    </button>

                    <div
                        v-if="players.length >= 2"
                        class="text-sm text-base-content/60 mt-3"
                    >
                        Ready to play with {{ players.length }} player{{
                            players.length !== 1 ? "s" : ""
                        }}
                    </div>
                    <div v-else class="text-sm text-warning mt-3">
                        Add at least 2 players to continue
                    </div>
                </div>
            </div>
        </div>

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
export default {
    name: "GameSetup",
    data() {
        return {
            // Tags
            availableTags: [],
            loadingTags: true,

            // Players
            players: [],
            newPlayerName: "",
            nextPlayerId: 1,
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

            // Settings
            gameName: "",
            maxSpiceRating: 3,

            // State
            creatingGame: false,
            error: null,
        };
    },
    computed: {
        // Filter tags based on game's max spice level
        availableTagsFiltered() {
            return this.availableTags.filter(
                (t) => t.min_spice_level <= this.maxSpiceRating,
            );
        },
        defaultTags() {
            return this.availableTagsFiltered.filter((t) => t.is_default);
        },
    },
    mounted() {
        this.fetchTags();
    },
    methods: {
        async fetchTags() {
            this.loadingTags = true;
            try {
                const response = await fetch("/api/tags");
                const data = await response.json();
                if (data.status === "success" || data.success) {
                    this.availableTags = data.data;
                }
            } catch (err) {
                console.error("Error fetching tags:", err);
                this.error = "Failed to load tags";
            } finally {
                this.loadingTags = false;
            }
        },

        addPlayer(gender) {
            const name = this.newPlayerName.trim();
            if (!name || !gender) return;

            // Start with default tags
            const defaultTags = this.availableTagsFiltered
                .filter((t) => t.is_default)
                .map((t) => t.id);

            // Add gender-specific tags (only if not 'other')
            if (gender !== "other") {
                const genderTags = this.availableTagsFiltered.filter(
                    (t) =>
                        t.default_for_gender === gender ||
                        t.default_for_gender === "both",
                );

                genderTags.forEach((tag) => {
                    if (!defaultTags.includes(tag.id)) {
                        defaultTags.push(tag.id);
                    }
                });
            }

            const player = {
                id: this.nextPlayerId++,
                name: name,
                gender: gender || null,
                avatar: this.playerAvatars[
                    Math.floor(Math.random() * this.playerAvatars.length)
                ],
                tags: defaultTags,
                order: this.players.length,
            };

            this.players.push(player);
            this.newPlayerName = "";
        },

        removePlayer(playerId) {
            const index = this.players.findIndex((p) => p.id === playerId);
            if (index > -1) {
                this.players.splice(index, 1);
                // Update order
                this.players.forEach((p, i) => {
                    p.order = i;
                });
            }
        },

        togglePlayerTag(playerId, tagId) {
            const player = this.players.find((p) => p.id === playerId);
            if (!player) return;

            const index = player.tags.indexOf(tagId);
            if (index > -1) {
                player.tags.splice(index, 1);
            } else {
                player.tags.push(tagId);
            }
        },

        getTagName(tagId) {
            const tag = this.availableTags.find((t) => t.id === tagId);
            return tag ? tag.name : "Unknown";
        },

        async createGame() {
            if (this.players.length < 2) {
                this.error = "Please add at least 2 players";
                return;
            }

            this.creatingGame = true;
            this.error = null;

            try {
                // Step 1: Create the game
                const gameResponse = await fetch("/api/games", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": document.querySelector(
                            'meta[name="csrf-token"]',
                        ).content,
                    },
                    body: JSON.stringify({
                        name: this.gameName || "New Game",
                        max_spice_rating: this.maxSpiceRating,
                    }),
                });

                const gameData = await gameResponse.json();
                if (!gameData.success) {
                    throw new Error(
                        gameData.message || "Failed to create game",
                    );
                }

                const game = gameData.data;

                // Step 2: Add players
                for (const player of this.players) {
                    const playerResponse = await fetch(
                        `/api/games/${game.id}/players`,
                        {
                            method: "POST",
                            headers: {
                                "Content-Type": "application/json",
                                "X-CSRF-TOKEN": document.querySelector(
                                    'meta[name="csrf-token"]',
                                ).content,
                            },
                            body: JSON.stringify({
                                name: player.name,
                                gender: player.gender,
                                tag_ids: player.tags,
                            }),
                        },
                    );

                    const playerData = await playerResponse.json();
                    if (!playerData.success) {
                        throw new Error(`Failed to add player ${player.name}`);
                    }
                }

                // Step 3: Start the game
                const startResponse = await fetch(
                    `/api/games/${game.id}/start`,
                    {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": document.querySelector(
                                'meta[name="csrf-token"]',
                            ).content,
                        },
                    },
                );

                const startData = await startResponse.json();
                if (!startData.success) {
                    throw new Error(
                        startData.message || "Failed to start game",
                    );
                }

                // Emit event to parent component with game data
                this.$emit("game-created", startData.data);
            } catch (err) {
                console.error("Error creating game:", err);
                this.error =
                    err.message || "Failed to create game. Please try again.";
            } finally {
                this.creatingGame = false;
            }
        },
    },
};
</script>

<style scoped>
.game-setup {
    min-height: 70vh;
}
</style>
