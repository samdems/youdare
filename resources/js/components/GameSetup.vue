<template>
    <div class="game-setup max-w-4xl mx-auto p-6">
        <!-- Header -->
        <div class="text-center mb-10">
            <h1 class="text-6xl font-bold mb-2">
                <Gamepad2 :size="80" class="mx-auto mb-4 text-primary" />
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
                        <Settings :size="24" />
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
                                <div class="flex gap-0.5">
                                    <Flame
                                        v-for="n in level"
                                        :key="n"
                                        :size="20"
                                        class="text-orange-500"
                                    />
                                </div>
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
                        <Info :size="20" />
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
                        <Users :size="24" />
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
                                <User :size="20" />
                                <span>Add Male</span>
                            </button>
                            <button
                                @click="addPlayer('female')"
                                class="btn btn-outline gap-2"
                                :disabled="!newPlayerName.trim()"
                            >
                                <User :size="20" />
                                <span>Add Female</span>
                            </button>
                            <button
                                @click="addPlayer('other')"
                                class="btn btn-outline gap-2"
                                :disabled="!newPlayerName.trim()"
                            >
                                <User :size="20" />
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
                                    <User
                                        :size="16"
                                        :class="
                                            player.gender === 'male'
                                                ? 'text-blue-500'
                                                : player.gender === 'female'
                                                  ? 'text-pink-500'
                                                  : 'text-purple-500'
                                        "
                                    />
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
                                                'badge badge-sm gap-1 cursor-pointer transition-all tooltip tooltip-top',
                                                player.tags.includes(tag.id)
                                                    ? 'badge-primary'
                                                    : 'badge-outline hover:badge-primary',
                                            ]"
                                            :data-tip="
                                                tag.description || tag.name
                                            "
                                        >
                                            {{ tag.name }}
                                            <Check
                                                v-if="
                                                    player.tags.includes(tag.id)
                                                "
                                                :size="12"
                                            />
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <button
                                @click="removePlayer(player.id)"
                                class="btn btn-ghost btn-sm btn-circle"
                                title="Remove player"
                            >
                                <X :size="16" />
                            </button>
                        </div>
                    </div>

                    <div
                        v-else
                        class="text-center py-12 border-2 border-dashed border-base-300 rounded-lg"
                    >
                        <UserPlus
                            :size="48"
                            class="mx-auto mb-3 text-base-content/30"
                        />
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
                        <Play v-if="!creatingGame" :size="24" />
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
                    <div
                        v-else
                        class="text-sm text-warning mt-3 flex items-center gap-2"
                    >
                        <AlertCircle :size="16" />
                        Add at least 2 players to continue
                    </div>
                </div>
            </div>
        </div>

        <!-- Error Alert -->
        <div v-if="error" class="alert alert-error shadow-lg mt-6">
            <XCircle :size="24" />
            <span>{{ error }}</span>
        </div>
    </div>
</template>

<script setup>
import { onMounted } from "vue";
import { storeToRefs } from "pinia";
import { useGameStore } from "../stores/gameStore";
import { usePlayerStore } from "../stores/playerStore";
import {
    Gamepad2,
    Settings,
    Flame,
    Info,
    Users,
    User,
    UserPlus,
    Check,
    X,
    Play,
    AlertCircle,
    XCircle,
} from "lucide-vue-next";

const emit = defineEmits(["game-created"]);

// Use the Pinia stores
const gameStore = useGameStore();
const playerStore = usePlayerStore();

// Get reactive refs from stores
const {
    gameName,
    maxSpiceRating,
    creatingGame,
    error,
    availableTags,
    loadingTags,
    availableTagsFiltered,
} = storeToRefs(gameStore);

const { players, newPlayerName } = storeToRefs(playerStore);

// Get actions from stores
const { fetchTags } = gameStore;
const { removePlayer, togglePlayerTag } = playerStore;

// Wrap addPlayer to pass available tags
const addPlayer = (gender) => {
    return playerStore.addPlayer(gender, availableTagsFiltered.value);
};

const getTagName = (tagId) => {
    const tag = availableTags.value.find((t) => t.id === tagId);
    return tag ? tag.name : "Unknown";
};

// Wrapper for createGame to emit event
const createGame = async () => {
    const gameData = await gameStore.createGame(players.value);
    if (gameData) {
        playerStore.setPlayers(gameData.players || []);
        emit("game-created", gameData);
    }
};

// Lifecycle
onMounted(() => {
    fetchTags();
});
</script>

<style scoped>
.game-setup {
    min-height: 70vh;
}
</style>
