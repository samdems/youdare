<template>
    <div class="game-screen">
        <!-- Setup Screen -->
        <div v-if="!gameStarted" class="max-w-4xl mx-auto">
            <div class="text-center mb-8">
                <h1 class="text-5xl font-bold mb-3">
                    <span class="text-6xl">🎮</span>
                    <br />
                    Truth or Dare
                </h1>
                <p class="text-lg opacity-70">
                    Set up your game and let's have some fun!
                </p>
            </div>

            <!-- Setup Cards -->
            <div class="space-y-6">
                <!-- Step 1: Players -->
                <div class="card bg-base-100 shadow-xl">
                    <div class="card-body">
                        <div class="flex items-center gap-3 mb-4">
                            <div
                                class="badge badge-primary badge-lg text-lg px-4 py-3"
                            >
                                1
                            </div>
                            <h2 class="card-title text-2xl">
                                👥 Add Players (Optional)
                            </h2>
                        </div>

                        <p class="text-sm opacity-70 mb-4">
                            Playing with friends? Add them here! Or skip for
                            solo play.
                        </p>

                        <!-- Add Player Input -->
                        <div class="flex gap-2 mb-4">
                            <input
                                type="text"
                                v-model="newPlayerName"
                                @keyup.enter="addPlayer"
                                placeholder="Enter player name..."
                                class="input input-bordered flex-1 input-lg"
                                maxlength="20"
                            />
                            <button
                                @click="addPlayer"
                                class="btn btn-primary btn-lg gap-2"
                                :disabled="!newPlayerName.trim()"
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

                        <!-- Players Grid -->
                        <div
                            v-if="players.length > 0"
                            class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3"
                        >
                            <div
                                v-for="(player, index) in players"
                                :key="player.id"
                                class="relative group"
                            >
                                <div
                                    class="card bg-gradient-to-br from-primary/20 to-secondary/20 hover:shadow-lg transition-all"
                                >
                                    <div class="card-body p-4 items-center">
                                        <div class="text-4xl mb-2">
                                            {{ player.avatar }}
                                        </div>
                                        <div
                                            class="font-semibold text-center truncate w-full"
                                        >
                                            {{ player.name }}
                                        </div>
                                        <button
                                            @click="removePlayer(player.id)"
                                            class="btn btn-ghost btn-xs btn-circle absolute top-1 right-1 opacity-0 group-hover:opacity-100 transition-opacity"
                                        >
                                            ✕
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div
                            v-else
                            class="text-center py-8 border-2 border-dashed border-base-300 rounded-lg"
                        >
                            <p class="text-lg opacity-50">
                                No players added yet
                            </p>
                            <p class="text-sm opacity-40">
                                Add players or continue without them
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Step 2: Game Settings -->
                <div class="card bg-base-100 shadow-xl">
                    <div class="card-body">
                        <div class="flex items-center gap-3 mb-4">
                            <div
                                class="badge badge-secondary badge-lg text-lg px-4 py-3"
                            >
                                2
                            </div>
                            <h2 class="card-title text-2xl">
                                ⚙️ Game Settings
                            </h2>
                        </div>

                        <div class="grid md:grid-cols-2 gap-6">
                            <!-- Spice Level -->
                            <div>
                                <label class="label">
                                    <span class="label-text font-bold text-lg"
                                        >🌶️ Spice Level</span
                                    >
                                </label>
                                <div class="space-y-2">
                                    <button
                                        v-for="level in 5"
                                        :key="level"
                                        @click="settings.maxSpice = level"
                                        :class="[
                                            'btn btn-block justify-between',
                                            settings.maxSpice === level
                                                ? 'btn-primary'
                                                : 'btn-outline',
                                        ]"
                                    >
                                        <span class="flex items-center gap-2">
                                            <span>{{
                                                "🌶️".repeat(level)
                                            }}</span>
                                            <span>{{
                                                getSpiceLevelName(level)
                                            }}</span>
                                        </span>
                                        <span v-if="settings.maxSpice === level"
                                            >✓</span
                                        >
                                    </button>
                                </div>
                            </div>

                            <!-- Task Type -->
                            <div>
                                <label class="label">
                                    <span class="label-text font-bold text-lg"
                                        >🎯 Task Type</span
                                    >
                                </label>
                                <div class="space-y-2">
                                    <button
                                        @click="settings.includeType = 'both'"
                                        :class="[
                                            'btn btn-block justify-between',
                                            settings.includeType === 'both'
                                                ? 'btn-primary'
                                                : 'btn-outline',
                                        ]"
                                    >
                                        <span class="flex items-center gap-2">
                                            <span>🎯💬</span>
                                            <span>Both</span>
                                        </span>
                                        <span
                                            v-if="
                                                settings.includeType === 'both'
                                            "
                                            >✓</span
                                        >
                                    </button>
                                    <button
                                        @click="settings.includeType = 'truth'"
                                        :class="[
                                            'btn btn-block justify-between',
                                            settings.includeType === 'truth'
                                                ? 'btn-info'
                                                : 'btn-outline',
                                        ]"
                                    >
                                        <span class="flex items-center gap-2">
                                            <span>💬</span>
                                            <span>Truth Only</span>
                                        </span>
                                        <span
                                            v-if="
                                                settings.includeType === 'truth'
                                            "
                                            >✓</span
                                        >
                                    </button>
                                    <button
                                        @click="settings.includeType = 'dare'"
                                        :class="[
                                            'btn btn-block justify-between',
                                            settings.includeType === 'dare'
                                                ? 'btn-secondary'
                                                : 'btn-outline',
                                        ]"
                                    >
                                        <span class="flex items-center gap-2">
                                            <span>🎯</span>
                                            <span>Dare Only</span>
                                        </span>
                                        <span
                                            v-if="
                                                settings.includeType === 'dare'
                                            "
                                            >✓</span
                                        >
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Start Game Button -->
                <div class="text-center pb-8">
                    <button
                        @click="startGame"
                        class="btn btn-success btn-lg px-12 gap-3 text-lg shadow-lg hover:shadow-xl transition-all"
                    >
                        <svg
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
                        <span>Start Game</span>
                    </button>
                    <p class="mt-3 text-sm opacity-60">
                        <span v-if="players.length > 0"
                            >{{ players.length }} player{{
                                players.length !== 1 ? "s" : ""
                            }}
                            ready •
                        </span>
                        Max spice: {{ "🌶️".repeat(settings.maxSpice) }} •
                        {{
                            settings.includeType === "both"
                                ? "Truths & Dares"
                                : settings.includeType === "truth"
                                  ? "Truths Only"
                                  : "Dares Only"
                        }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Game Screen -->
        <div v-else class="max-w-4xl mx-auto">
            <!-- Header with Stats -->
            <div class="mb-6">
                <h2 class="text-3xl font-bold">🎮 Game On!</h2>
                <p class="text-sm opacity-70">
                    {{ completedTasks.length }} completed •
                    {{ skippedTasks.length }} skipped
                </p>
            </div>

            <!-- Current Player Card (if players exist) -->
            <div
                v-if="players.length > 0 && currentPlayer"
                class="mb-6 relative"
            >
                <div
                    class="card bg-gradient-to-r from-primary to-secondary text-primary-content shadow-xl"
                >
                    <div class="card-body py-6">
                        <div
                            class="flex items-center justify-between flex-wrap gap-4"
                        >
                            <div class="flex items-center gap-4">
                                <div class="text-5xl">
                                    {{ currentPlayer.avatar }}
                                </div>
                                <div>
                                    <h3 class="text-2xl font-bold">
                                        {{ currentPlayer.name }}'s Turn
                                    </h3>
                                    <div class="flex gap-4 text-sm mt-1">
                                        <span class="opacity-90"
                                            >✓
                                            {{ currentPlayer.completed }}
                                            completed</span
                                        >
                                        <span class="opacity-90"
                                            >⊗
                                            {{ currentPlayer.skipped }}
                                            skipped</span
                                        >
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Leaderboard -->
            <div
                v-if="players.length > 1"
                class="card bg-base-100 shadow-xl mb-6"
            >
                <div class="card-body">
                    <h3 class="card-title text-xl mb-4">🏆 Leaderboard</h3>
                    <div class="space-y-2">
                        <div
                            v-for="(player, index) in sortedPlayers"
                            :key="player.id"
                            :class="[
                                'flex items-center gap-4 p-4 rounded-lg transition-all',
                                currentPlayer && currentPlayer.id === player.id
                                    ? 'bg-primary/20 ring-2 ring-primary'
                                    : 'bg-base-200',
                            ]"
                        >
                            <div
                                class="badge badge-lg"
                                :class="
                                    index === 0
                                        ? 'badge-warning'
                                        : 'badge-ghost'
                                "
                            >
                                {{ index + 1 }}
                            </div>
                            <div class="text-3xl">{{ player.avatar }}</div>
                            <div class="flex-1">
                                <div class="font-bold">{{ player.name }}</div>
                                <div class="text-sm opacity-70">
                                    {{ player.completed }} completed •
                                    {{ player.skipped }} skipped •
                                    {{ player.completed + player.skipped }}
                                    total
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="text-2xl font-bold text-success">
                                    {{ player.completed }}
                                </div>
                                <div class="text-xs opacity-70">points</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Loading State -->
            <div
                v-if="loading"
                class="card bg-base-100 shadow-xl animate-pulse"
            >
                <div class="card-body items-center text-center py-20">
                    <div
                        class="loading loading-spinner loading-lg text-primary"
                    ></div>
                    <p class="mt-4 text-lg">Getting your next challenge...</p>
                </div>
            </div>

            <!-- Error State -->
            <div v-else-if="error" class="alert alert-error shadow-xl text-lg">
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
                <div class="flex-1">
                    <div class="font-bold">Oops!</div>
                    <div class="text-sm">{{ error }}</div>
                </div>
                <button @click="getRandomTask" class="btn btn-sm">
                    Try Again
                </button>
            </div>

            <!-- Current Task Card -->
            <div v-else-if="currentTask" class="space-y-6">
                <!-- Task Card -->
                <div class="card bg-base-100 shadow-2xl overflow-hidden">
                    <!-- Task Header -->
                    <div
                        :class="[
                            'text-white p-8',
                            currentTask.type === 'truth'
                                ? 'bg-gradient-to-br from-blue-500 via-blue-600 to-cyan-600'
                                : 'bg-gradient-to-br from-purple-500 via-purple-600 to-pink-600',
                        ]"
                    >
                        <div class="flex items-start justify-between mb-4">
                            <div>
                                <div class="text-6xl mb-3">
                                    {{
                                        currentTask.type === "truth"
                                            ? "💬"
                                            : "🎯"
                                    }}
                                </div>
                                <h2 class="text-4xl font-bold">
                                    {{
                                        currentTask.type === "truth"
                                            ? "Truth"
                                            : "Dare"
                                    }}
                                </h2>
                            </div>
                            <div class="text-right">
                                <div
                                    class="badge badge-lg bg-white/30 border-white text-white gap-2 mb-2"
                                >
                                    {{ "🌶️".repeat(currentTask.spice_rating) }}
                                </div>
                                <div class="text-sm opacity-90">
                                    {{
                                        getSpiceLevelText(
                                            currentTask.spice_rating,
                                        )
                                    }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Task Content -->
                    <div class="card-body p-8">
                        <div
                            class="bg-base-200 rounded-xl p-8 text-center mb-6"
                        >
                            <p class="text-2xl leading-relaxed font-medium">
                                {{ currentTask.description }}
                            </p>
                        </div>

                        <!-- Tags Display -->
                        <div
                            v-if="
                                currentTask.tags ||
                                currentTask.addable_tags ||
                                currentTask.removable_tags ||
                                currentTask.cant_have_tags_details
                            "
                            class="mb-6 space-y-3"
                        >
                            <!-- Required Tags -->
                            <div
                                v-if="
                                    currentTask.tags &&
                                    currentTask.tags.length > 0
                                "
                                class="flex flex-wrap gap-2 items-center"
                            >
                                <span class="text-sm font-semibold opacity-70"
                                    >🏷️ Required Tags:</span
                                >
                                <span
                                    v-for="tag in currentTask.tags"
                                    :key="'req-' + tag.id"
                                    class="badge badge-primary badge-sm"
                                >
                                    {{ tag.name }}
                                </span>
                            </div>

                            <!-- Tags to Add -->
                            <div
                                v-if="
                                    currentTask.addable_tags &&
                                    currentTask.addable_tags.length > 0
                                "
                                class="flex flex-wrap gap-2 items-center"
                            >
                                <span class="text-sm font-semibold opacity-70"
                                    >➕ Adds Tags:</span
                                >
                                <span
                                    v-for="tag in currentTask.addable_tags"
                                    :key="'add-' + tag.id"
                                    class="badge badge-success badge-sm"
                                >
                                    {{ tag.name }}
                                </span>
                            </div>

                            <!-- Tags to Remove -->
                            <div
                                v-if="
                                    currentTask.removable_tags &&
                                    currentTask.removable_tags.length > 0
                                "
                                class="flex flex-wrap gap-2 items-center"
                            >
                                <span class="text-sm font-semibold opacity-70"
                                    >➖ Removes Tags:</span
                                >
                                <span
                                    v-for="tag in currentTask.removable_tags"
                                    :key="'rem-' + tag.id"
                                    class="badge badge-warning badge-sm"
                                >
                                    {{ tag.name }}
                                </span>
                            </div>

                            <!-- Can't Have Tags -->
                            <div
                                v-if="
                                    currentTask.cant_have_tags_details &&
                                    currentTask.cant_have_tags_details.length >
                                        0
                                "
                                class="flex flex-wrap gap-2 items-center"
                            >
                                <span class="text-sm font-semibold opacity-70"
                                    >🚫 Can't Have Tags:</span
                                >
                                <span
                                    v-for="tag in currentTask.cant_have_tags_details"
                                    :key="'cant-' + tag.id"
                                    class="badge badge-error badge-sm"
                                >
                                    {{ tag.name }}
                                </span>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex gap-3 justify-center flex-wrap">
                            <button
                                @click="completeTask"
                                class="btn btn-success btn-lg gap-2 flex-1 max-w-xs"
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
                                Done!
                            </button>

                            <button
                                @click="skipTask"
                                class="btn btn-warning btn-lg gap-2 flex-1 max-w-xs"
                            >
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="h-6 w-6"
                                    viewBox="0 0 20 20"
                                    fill="currentColor"
                                >
                                    <path
                                        fill-rule="evenodd"
                                        d="M6.707 4.879A3 3 0 018.828 4H15a3 3 0 013 3v6a3 3 0 01-3 3H8.828a3 3 0 01-2.121-.879l-4.415-4.414a1 1 0 010-1.414l4.415-4.414zM10 11a1 1 0 100-2 1 1 0 000 2zm3-1a1 1 0 11-2 0 1 1 0 012 0z"
                                        clip-rule="evenodd"
                                    />
                                </svg>
                                Skip
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Quick Stats -->
                <div class="flex gap-4 justify-center flex-wrap text-center">
                    <div class="stats shadow">
                        <div class="stat py-4 px-6">
                            <div class="stat-title text-xs">Completed</div>
                            <div class="stat-value text-success text-3xl">
                                {{ completedTasks.length }}
                            </div>
                        </div>
                    </div>
                    <div class="stats shadow">
                        <div class="stat py-4 px-6">
                            <div class="stat-title text-xs">Skipped</div>
                            <div class="stat-value text-warning text-3xl">
                                {{ skippedTasks.length }}
                            </div>
                        </div>
                    </div>
                    <div class="stats shadow">
                        <div class="stat py-4 px-6">
                            <div class="stat-title text-xs">Total</div>
                            <div class="stat-value text-primary text-3xl">
                                {{
                                    completedTasks.length + skippedTasks.length
                                }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- History Section -->
            <div
                v-if="completedTasks.length > 0"
                class="mt-8 card bg-base-100 shadow-xl"
            >
                <div class="card-body">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="card-title text-xl">
                            📋 History
                            <span class="badge badge-primary">{{
                                completedTasks.length
                            }}</span>
                        </h3>
                        <button
                            @click="showHistory = !showHistory"
                            class="btn btn-sm btn-ghost"
                        >
                            {{ showHistory ? "Hide" : "Show" }}
                        </button>
                    </div>

                    <div v-if="showHistory" class="space-y-2">
                        <div
                            v-for="(task, index) in completedTasks
                                .slice()
                                .reverse()"
                            :key="`${task.id}-${index}`"
                            class="flex items-center gap-3 p-3 rounded-lg bg-base-200 hover:bg-base-300 transition"
                        >
                            <div
                                v-if="task.playerName"
                                class="text-2xl"
                                :title="task.playerName"
                            >
                                {{ task.playerAvatar }}
                            </div>
                            <div
                                class="badge"
                                :class="
                                    task.type === 'truth'
                                        ? 'badge-info'
                                        : 'badge-secondary'
                                "
                            >
                                {{ task.type === "truth" ? "💬" : "🎯" }}
                            </div>
                            <div class="flex-1">
                                <div
                                    v-if="task.playerName"
                                    class="font-semibold text-xs opacity-70"
                                >
                                    {{ task.playerName }}
                                </div>
                                <div class="text-sm">
                                    {{ task.description.substring(0, 100)
                                    }}{{
                                        task.description.length > 100
                                            ? "..."
                                            : ""
                                    }}
                                </div>
                            </div>
                            <div class="badge badge-outline badge-sm">
                                {{ "🌶️".repeat(task.spice_rating) }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: "GameScreen",
    data() {
        return {
            gameStarted: false,
            loading: false,
            error: null,
            currentTask: null,
            completedTasks: [],
            skippedTasks: [],
            settings: {
                maxSpice: 3,
                includeType: "both",
            },
            // Player Management
            players: [],
            newPlayerName: "",
            currentPlayerIndex: 0,
            showHistory: false,
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
            if (this.players.length === 0) return null;
            return this.players[this.currentPlayerIndex];
        },
        sortedPlayers() {
            return [...this.players].sort((a, b) => b.completed - a.completed);
        },
    },
    methods: {
        addPlayer() {
            const name = this.newPlayerName.trim();
            if (!name) return;

            // Check for duplicate names
            if (
                this.players.some(
                    (p) => p.name.toLowerCase() === name.toLowerCase(),
                )
            ) {
                alert("A player with this name already exists!");
                return;
            }

            // Get a random unused avatar
            const usedAvatars = this.players.map((p) => p.avatar);
            const availableAvatars = this.playerAvatars.filter(
                (a) => !usedAvatars.includes(a),
            );
            const avatar =
                availableAvatars.length > 0
                    ? availableAvatars[
                          Math.floor(Math.random() * availableAvatars.length)
                      ]
                    : this.playerAvatars[
                          Math.floor(Math.random() * this.playerAvatars.length)
                      ];

            this.players.push({
                id: Date.now(),
                name: name,
                avatar: avatar,
                completed: 0,
                skipped: 0,
            });

            this.newPlayerName = "";
        },

        removePlayer(playerId) {
            const index = this.players.findIndex((p) => p.id === playerId);
            if (index !== -1) {
                this.players.splice(index, 1);
                // Adjust current player index if necessary
                if (
                    this.currentPlayerIndex >= this.players.length &&
                    this.players.length > 0
                ) {
                    this.currentPlayerIndex = 0;
                }
            }
        },

        nextPlayer() {
            if (this.players.length === 0) return;
            this.currentPlayerIndex =
                (this.currentPlayerIndex + 1) % this.players.length;
        },

        startGame() {
            this.gameStarted = true;
            this.completedTasks = [];
            this.skippedTasks = [];
            this.currentPlayerIndex = 0;
            this.showHistory = false;

            // Reset player stats
            this.players.forEach((player) => {
                player.completed = 0;
                player.skipped = 0;
            });

            this.getRandomTask();
        },

        async getRandomTask() {
            this.loading = true;
            this.error = null;

            try {
                const params = new URLSearchParams({
                    max_spice: this.settings.maxSpice,
                });

                if (this.settings.includeType !== "both") {
                    params.append("type", this.settings.includeType);
                }

                const response = await fetch(
                    `/api/tasks/random?${params.toString()}`,
                );

                if (!response.ok) {
                    throw new Error("Failed to fetch task");
                }

                const data = await response.json();

                if (data.status === "success" && data.data) {
                    // Avoid showing recently completed or skipped tasks
                    const recentTaskIds = [
                        ...this.completedTasks.map((t) => t.id),
                        ...this.skippedTasks.map((t) => t.id),
                    ].slice(-10);

                    if (
                        recentTaskIds.includes(data.data.id) &&
                        recentTaskIds.length < 5
                    ) {
                        // Try to get a different task
                        this.getRandomTask();
                        return;
                    }

                    this.currentTask = data.data;
                } else {
                    this.error =
                        data.message || "No tasks found with your criteria";
                }
            } catch (err) {
                this.error = "Error loading task. Please try again.";
                console.error("Error fetching task:", err);
            } finally {
                this.loading = false;
            }
        },

        completeTask() {
            if (this.currentTask) {
                const taskRecord = {
                    ...this.currentTask,
                    playerName: this.currentPlayer
                        ? this.currentPlayer.name
                        : null,
                    playerAvatar: this.currentPlayer
                        ? this.currentPlayer.avatar
                        : null,
                };

                this.completedTasks.push(taskRecord);

                // Update player stats
                if (this.currentPlayer) {
                    this.currentPlayer.completed++;
                    this.nextPlayer();
                }

                this.getRandomTask();
            }
        },

        skipTask() {
            if (this.currentTask) {
                const taskRecord = {
                    ...this.currentTask,
                    playerName: this.currentPlayer
                        ? this.currentPlayer.name
                        : null,
                    playerAvatar: this.currentPlayer
                        ? this.currentPlayer.avatar
                        : null,
                };

                this.skippedTasks.push(taskRecord);

                // Update player stats
                if (this.currentPlayer) {
                    this.currentPlayer.skipped++;
                    this.nextPlayer();
                }

                this.getRandomTask();
            }
        },

        getSpiceLevelText(rating) {
            const levels = {
                1: "Mild",
                2: "Medium",
                3: "Hot",
                4: "Extra Hot",
                5: "Extreme",
            };
            return levels[rating] || "Unknown";
        },

        getSpiceLevelName(level) {
            const names = {
                1: "Mild",
                2: "Medium",
                3: "Hot",
                4: "Extra Hot",
                5: "Extreme",
            };
            return names[level];
        },
    },
};
</script>

<style scoped>
.game-screen {
    min-height: 70vh;
    padding: 2rem 1rem;
}
</style>
