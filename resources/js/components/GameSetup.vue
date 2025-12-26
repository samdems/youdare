<template>
    <div class="game-setup max-w-6xl mx-auto p-4">
        <!-- Header -->
        <div class="text-center mb-8">
            <h1 class="text-5xl font-bold mb-3">
                <span class="text-6xl">🎮</span>
                <br />
                Truth or Dare
            </h1>
            <p class="text-lg opacity-70">
                Add players with custom tags and set your game preferences
            </p>
        </div>

        <!-- Setup Steps -->
        <div class="space-y-6">
            <!-- Step 1: Set Spice Level -->
            <div class="card bg-base-100 shadow-xl">
                <div class="card-body">
                    <div class="flex items-center gap-3 mb-4">
                        <div
                            class="badge badge-primary badge-lg text-lg px-4 py-3"
                        >
                            1
                        </div>
                        <h2 class="card-title text-2xl">🌶️ Set Spice Level</h2>
                    </div>

                    <div class="space-y-4">
                        <p class="text-base-content/70">
                            Choose the maximum spice level for this game. This
                            determines which tags and tasks will be available.
                        </p>

                        <!-- Spice Level Selection -->
                        <div>
                            <label class="label">
                                <span class="label-text font-bold text-lg"
                                    >Maximum Spice Level</span
                                >
                            </label>
                            <select
                                v-model.number="maxSpiceRating"
                                class="select select-bordered select-lg w-full"
                            >
                                <option :value="1">
                                    🌶️ Level 1 - Mild (Family Friendly)
                                </option>
                                <option :value="2">
                                    🌶️🌶️ Level 2 - Medium (Some Adult Content)
                                </option>
                                <option :value="3">
                                    🌶️🌶️🌶️ Level 3 - Hot (Adults Only)
                                </option>
                                <option :value="4">
                                    🌶️🌶️🌶️🌶️ Level 4 - Extra Hot
                                </option>
                                <option :value="5">
                                    🌶️🌶️🌶️🌶️🌶️ Level 5 - Extreme
                                </option>
                            </select>
                        </div>

                        <!-- Available Tags Info -->
                        <div class="alert alert-info">
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                fill="none"
                                viewBox="0 0 24 24"
                                class="stroke-current shrink-0 w-6 h-6"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                                ></path>
                            </svg>
                            <div>
                                <h3 class="font-bold">
                                    {{ availableTagsFiltered.length }} tags
                                    available at this spice level
                                </h3>
                                <div class="text-sm mt-1">
                                    <span v-if="maxSpiceRating === 1"
                                        >Basic tags only - suitable for all
                                        audiences</span
                                    >
                                    <span v-else-if="maxSpiceRating === 2"
                                        >Includes clothing-related tags</span
                                    >
                                    <span v-else-if="maxSpiceRating === 3"
                                        >Includes romantic content</span
                                    >
                                    <span v-else-if="maxSpiceRating >= 4"
                                        >All tags including extreme
                                        content</span
                                    >
                                </div>
                            </div>
                        </div>

                        <!-- Game Name (Optional) -->
                        <div>
                            <label class="label">
                                <span class="label-text font-bold"
                                    >🎮 Game Name (Optional)</span
                                >
                            </label>
                            <input
                                type="text"
                                v-model="gameName"
                                placeholder="e.g., Friday Night Fun"
                                class="input input-bordered w-full"
                                maxlength="50"
                            />
                        </div>
                    </div>
                </div>
            </div>

            <!-- Step 2: Add Players -->
            <div class="card bg-base-100 shadow-xl">
                <div class="card-body">
                    <div class="flex items-center gap-3 mb-4">
                        <div
                            class="badge badge-secondary badge-lg text-lg px-4 py-3"
                        >
                            2
                        </div>
                        <h2 class="card-title text-2xl">👥 Add Players</h2>
                    </div>

                    <p class="text-sm opacity-70 mb-4">
                        Add players and assign tags to each player. Player tags
                        determine which tasks will appear in the game.
                    </p>

                    <!-- Add Player Form -->
                    <div class="flex flex-col gap-2 mb-4">
                        <div class="flex gap-2">
                            <input
                                type="text"
                                v-model="newPlayerName"
                                @keyup.enter="addPlayer"
                                placeholder="Enter player name..."
                                class="input input-bordered flex-1 input-lg"
                                maxlength="20"
                            />
                            <select
                                v-model="newPlayerGender"
                                class="select select-bordered input-lg"
                                required
                            >
                                <option value="" disabled>
                                    Select Gender *
                                </option>
                                <option value="male">Male 👨</option>
                                <option value="female">Female 👩</option>
                            </select>
                            <button
                                @click="addPlayer"
                                class="btn btn-primary btn-lg gap-2"
                                :disabled="
                                    !newPlayerName.trim() || !newPlayerGender
                                "
                            >
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="h-5 w-5"
                                    viewBox="0 0 20 20"
                                    fill="currentColor"
                                >
                                    <path
                                        fill-rule="evenodd"
                                        d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z"
                                        clip-rule="evenodd"
                                    />
                                </svg>
                                Add Player
                            </button>
                        </div>
                        <div class="text-sm opacity-70 px-2">
                            <div v-if="defaultTags.length > 0" class="mb-2">
                                <span class="font-semibold"
                                    >📌 Default tags for all players ({{
                                        defaultTags.length
                                    }}):</span
                                >
                                <span class="ml-1">{{
                                    defaultTags.map((t) => t.name).join(", ")
                                }}</span>
                            </div>
                            <div v-else class="mb-2 text-warning">
                                ⚠️ No default tags available at spice level
                                {{ maxSpiceRating }}
                            </div>
                            <div
                                v-if="
                                    newPlayerGender &&
                                    genderSpecificTags.length > 0
                                "
                            >
                                <span class="font-semibold"
                                    >🏷️ Will also auto-select ({{
                                        genderSpecificTags.length
                                    }}):</span
                                >
                                <span class="ml-1">{{
                                    genderSpecificTags
                                        .map((t) => t.name)
                                        .join(", ")
                                }}</span>
                            </div>
                            <div
                                v-if="
                                    newPlayerGender &&
                                    genderSpecificTags.length === 0
                                "
                                class="text-warning"
                            >
                                ⚠️ No gender-specific tags available at spice
                                level {{ maxSpiceRating }}
                            </div>
                            <span v-if="!newPlayerGender" class="text-warning">
                                ⚠️ Please select a gender to continue
                            </span>
                        </div>
                    </div>

                    <!-- Players List -->
                    <div v-if="players.length > 0" class="space-y-3">
                        <div
                            v-for="(player, index) in players"
                            :key="player.id"
                            class="border border-base-300 rounded-lg p-4"
                        >
                            <div class="flex items-center justify-between mb-3">
                                <div class="flex items-center gap-3 flex-1">
                                    <div class="text-3xl">
                                        {{ player.avatar }}
                                    </div>
                                    <div class="flex-1">
                                        <div class="font-bold text-lg">
                                            {{ player.name }}
                                            <span
                                                v-if="player.gender"
                                                class="text-sm opacity-70"
                                            >
                                                {{
                                                    player.gender === "male"
                                                        ? "👨"
                                                        : "👩"
                                                }}
                                            </span>
                                        </div>
                                        <div class="text-sm opacity-60 mb-2">
                                            <span v-if="player.gender">
                                                {{
                                                    player.gender === "male"
                                                        ? "Male"
                                                        : "Female"
                                                }}
                                                ·
                                            </span>
                                            {{ player.tags.length }} tag{{
                                                player.tags.length !== 1
                                                    ? "s"
                                                    : ""
                                            }}
                                        </div>

                                        <!-- Display Player Tags Inline -->
                                        <div
                                            v-if="
                                                player.tags &&
                                                player.tags.length > 0
                                            "
                                            class="flex flex-wrap gap-1"
                                        >
                                            <span
                                                v-for="tagId in player.tags"
                                                :key="tagId"
                                                class="badge badge-primary badge-sm gap-1"
                                            >
                                                {{ getTagName(tagId) }}
                                                <button
                                                    @click="
                                                        togglePlayerTag(
                                                            player.id,
                                                            tagId,
                                                        )
                                                    "
                                                    class="hover:text-error"
                                                >
                                                    ✕
                                                </button>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <button
                                    @click="removePlayer(player.id)"
                                    class="btn btn-ghost btn-sm btn-circle"
                                >
                                    ✕
                                </button>
                            </div>

                            <!-- Add Tags Section -->
                            <div class="collapse collapse-arrow bg-base-200">
                                <input type="checkbox" />
                                <div class="collapse-title text-sm font-medium">
                                    ➕ Add more tags for {{ player.name }}
                                </div>
                                <div class="collapse-content">
                                    <div
                                        class="grid grid-cols-2 md:grid-cols-4 gap-2 pt-2"
                                    >
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
                                                'btn btn-xs btn-outline flex items-center gap-1',
                                                player.tags.includes(tag.id)
                                                    ? 'btn-primary'
                                                    : '',
                                            ]"
                                        >
                                            <span>{{ tag.name }}</span>
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
                        </div>
                    </div>

                    <div
                        v-else
                        class="text-center py-8 border-2 border-dashed border-base-300 rounded-lg"
                    >
                        <p class="text-lg opacity-50">No players added yet</p>
                        <p class="text-sm opacity-40">
                            Add at least 2 players to start
                        </p>
                    </div>
                </div>
            </div>

            <!-- Start Game Button -->
            <div class="text-center pb-8">
                <button
                    @click="createGame"
                    class="btn btn-success btn-lg px-12 gap-3 text-lg shadow-lg hover:shadow-xl transition-all"
                    :disabled="players.length < 2 || creatingGame"
                    :class="{ loading: creatingGame }"
                >
                    <svg
                        v-if="!creatingGame"
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-7 w-7"
                        viewBox="0 0 20 20"
                        fill="currentColor"
                    >
                        <path
                            fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z"
                            clip-rule="evenodd"
                        />
                    </svg>
                    <span>{{
                        creatingGame
                            ? "Creating Game..."
                            : "Create & Start Game"
                    }}</span>
                </button>

                <div
                    v-if="players.length >= 2"
                    class="mt-3 text-sm opacity-60 space-y-1"
                >
                    <p>
                        ✓ {{ players.length }} player{{
                            players.length !== 1 ? "s" : ""
                        }}
                        ready
                    </p>
                    <p>
                        ✓ Max spice level: {{ "🌶️".repeat(maxSpiceRating) }} ({{
                            maxSpiceRating
                        }})
                    </p>
                    <p>✓ {{ availableTagsFiltered.length }} tags available</p>
                </div>
                <div v-else class="mt-3 text-sm text-warning">
                    ⚠️ Add at least 2 players to start the game
                </div>
            </div>
        </div>

        <!-- Error Alert -->
        <div v-if="error" class="alert alert-error shadow-lg mt-4">
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
            newPlayerGender: "",
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
        genderSpecificTags() {
            if (!this.newPlayerGender) return [];
            return this.availableTagsFiltered.filter(
                (t) =>
                    !t.is_default &&
                    (t.default_for_gender === this.newPlayerGender ||
                        t.default_for_gender === "both"),
            );
        },
    },
    mounted() {
        this.fetchTags();
    },
    methods: {
        async fetchTags() {
            this.loadingTags = true;
            console.log("Fetching tags from /api/tags...");
            try {
                const response = await fetch("/api/tags");
                console.log("Response status:", response.status);
                const data = await response.json();
                console.log("Response data:", data);
                if (data.status === "success" || data.success) {
                    this.availableTags = data.data;
                    console.log("Tags loaded:", this.availableTags.length);
                } else {
                    console.warn("Unexpected response structure:", data);
                }
            } catch (err) {
                console.error("Error fetching tags:", err);
                this.error = "Failed to load tags";
            } finally {
                this.loadingTags = false;
                console.log(
                    "Loading complete. Available tags:",
                    this.availableTags,
                );
            }
        },

        addPlayer() {
            const name = this.newPlayerName.trim();
            if (!name || !this.newPlayerGender) return;

            // Start with default tags (tags marked as is_default and within spice level)
            const defaultTags = this.availableTagsFiltered
                .filter((t) => t.is_default)
                .map((t) => t.id);

            // Add gender-specific tags based on default_for_gender field
            const genderTags = this.availableTagsFiltered.filter(
                (t) =>
                    t.default_for_gender === this.newPlayerGender ||
                    t.default_for_gender === "both",
            );

            genderTags.forEach((tag) => {
                if (!defaultTags.includes(tag.id)) {
                    defaultTags.push(tag.id);
                }
            });

            const player = {
                id: this.nextPlayerId++,
                name: name,
                gender: this.newPlayerGender || null,
                avatar: this.playerAvatars[
                    Math.floor(Math.random() * this.playerAvatars.length)
                ],
                tags: defaultTags,
                order: this.players.length,
            };

            this.players.push(player);
            this.newPlayerName = "";
            this.newPlayerGender = "";
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
                    console.log(
                        `Player ${player.name} created with gender: ${player.gender}`,
                    );
                    console.log("Player data from API:", playerData);
                    console.log("Auto-assigned tags:", playerData.data?.tags);
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
