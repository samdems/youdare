<template>
    <div class="game-setup max-w-4xl mx-auto p-6">
        <!-- 18+ Age Verification Modal -->
        <dialog
            v-if="showAgeVerification"
            ref="ageVerificationModal"
            class="modal modal-open"
        >
            <div class="modal-box">
                <h3 class="font-bold text-lg mb-4">Age Verification</h3>
                <p class="py-2">
                    This game has multiple heat levels. The first two levels are
                    family-friendly and safe for all ages.
                </p>
                <p class="py-2 font-semibold">
                    To unlock adult content (Heat levels 3-5), everyone in the
                    room must be 18 years or older. Heat levels 3-5 also require
                    a Pro account.
                </p>
                <p class="pb-4 text-sm text-base-content/70">
                    Is everyone present 18+?
                </p>
                <div class="modal-action">
                    <button @click="confirmAge(false)" class="btn btn-outline">
                        No - Family Mode Only
                    </button>
                    <button @click="confirmAge(true)" class="btn btn-primary">
                        Yes - Everyone is 18+
                    </button>
                </div>
            </div>
        </dialog>

        <!-- Step Indicator -->
        <div class="flex justify-center mb-8">
            <ul class="steps steps-horizontal">
                <li class="step" :class="{ 'step-primary': setupStep >= 1 }">
                    Pick Heat
                </li>
                <li class="step" :class="{ 'step-primary': setupStep >= 2 }">
                    Set Tags
                </li>
                <li class="step" :class="{ 'step-primary': setupStep >= 3 }">
                    Add Players
                </li>
            </ul>
        </div>

        <!-- Step 1: Pick Your Heat (Spice Level) -->
        <div v-if="setupStep === 1" class="space-y-8">
            <div class="card bg-base-100 shadow-lg">
                <div class="card-body">
                    <h3 class="text-2xl font-bold mb-4 text-center">
                        Step 1: Pick Your Heat 🔥
                    </h3>
                    <p class="text-center text-base-content/60 mb-6">
                        Choose the spice level for your game
                    </p>

                    <!-- Spice Level -->
                    <spice-level-selector
                        v-model="maxSpiceRating"
                        :available-count="availableTagsFiltered.length"
                        :is-adult="isAdult"
                        :is-pro="isPro"
                    />

                    <div class="card-actions justify-end mt-6">
                        <button
                            v-if="isPro || maxSpiceRating < 3"
                            @click="goToStep2"
                            class="btn btn-primary btn-lg gap-2"
                        >
                            Next: Set Tags
                            <ArrowRight :size="20" />
                        </button>
                        <a
                            v-if="!isPro && maxSpiceRating >= 3"
                            href="/go-pro"
                            class="btn btn-warning btn-lg gap-2"
                        >
                            Upgrade
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Step 2: Set Tags in Play -->
        <div v-else-if="setupStep === 2" class="space-y-8">
            <div class="card bg-base-100 shadow-lg">
                <div class="card-body">
                    <h3 class="text-2xl font-bold mb-4 text-center">
                        Step 2: Set Tags in Play 🏷️
                    </h3>
                    <p class="text-center text-base-content/60 mb-6">
                        Select which tags will be available for this game (based
                        on spice level {{ maxSpiceRating }})
                    </p>

                    <!-- All Available Tags -->
                    <div class="mb-4">
                        <div class="flex justify-between items-center mb-3">
                            <h4 class="font-semibold">Available Tags:</h4>
                            <button
                                @click="selectAllTags"
                                class="btn btn-sm btn-outline"
                            >
                                Select All
                            </button>
                        </div>

                        <div
                            v-if="availableTagsFiltered.length > 0"
                            class="grid grid-cols-2 md:grid-cols-4 gap-3"
                        >
                            <button
                                v-for="tag in availableTagsFiltered"
                                :key="tag.id"
                                @click="toggleTagInPlay(tag.id)"
                                class="btn btn-lg h-auto py-4 transition-all tooltip tooltip-top"
                                :class="{
                                    'btn-primary': selectedTagsInPlay.includes(
                                        tag.id,
                                    ),
                                    'btn-outline': !selectedTagsInPlay.includes(
                                        tag.id,
                                    ),
                                }"
                                :data-tip="tag.description || tag.name"
                            >
                                <span class="text-sm font-bold">{{
                                    tag.name
                                }}</span>
                            </button>
                        </div>
                        <div v-else class="text-center py-8">
                            <p class="text-base-content/60">
                                No tags available for this spice level
                            </p>
                        </div>
                    </div>

                    <div class="card-actions justify-between mt-6">
                        <button @click="setupStep = 1" class="btn btn-outline">
                            <ArrowLeft :size="20" />
                            Back
                        </button>
                        <button
                            @click="goToStep3"
                            class="btn btn-primary btn-lg gap-2"
                            :disabled="selectedTagsInPlay.length === 0"
                        >
                            Next: Add Players
                            <ArrowRight :size="20" />
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Step 3: Add Players -->
        <div v-else-if="setupStep === 3" class="space-y-8">
            <!-- Add Player Card -->
            <div class="card bg-base-100 shadow-lg">
                <div class="card-body">
                    <h3 class="text-2xl font-bold mb-4 text-center">
                        Step 3: Add Players 👥
                    </h3>
                    <p class="text-center text-base-content/60 mb-6">
                        Add players and assign tags (only selected tags are
                        shown)
                    </p>

                    <!-- Add Player Form -->
                    <div class="mb-6">
                        <player-form
                            v-model="newPlayerName"
                            @add-player="addPlayer"
                        />
                    </div>

                    <!-- Players List -->
                    <div v-if="players.length > 0" class="space-y-3">
                        <player-list-item
                            v-for="player in players"
                            :key="player.id"
                            :player="player"
                            :available-tags="tagsInPlayFiltered"
                            @remove="removePlayer"
                            @toggle-tag="togglePlayerTag"
                        />
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

            <!-- Navigation and Start Game -->
            <div class="flex justify-between items-center">
                <button @click="setupStep = 2" class="btn btn-outline">
                    <ArrowLeft :size="20" />
                    Back to Tags
                </button>

                <button
                    @click="createGame"
                    class="btn btn-success btn-lg gap-2 min-w-[200px]"
                    :disabled="players.length < 2 || creatingGame"
                    :class="{ loading: creatingGame }"
                >
                    <Play v-if="!creatingGame" :size="24" />
                    {{ creatingGame ? "Creating..." : "Start Game" }}
                </button>
            </div>

            <div class="text-center">
                <div
                    v-if="players.length >= 2"
                    class="text-sm text-base-content/60"
                >
                    Ready to play with {{ players.length }} player{{
                        players.length !== 1 ? "s" : ""
                    }}
                </div>
                <div
                    v-else
                    class="text-sm text-warning flex items-center gap-2 justify-center"
                >
                    <AlertCircle :size="16" />
                    Add at least 2 players to continue
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
import { ref, computed, onMounted } from "vue";
import { storeToRefs } from "pinia";
import { useGameStore } from "../stores/gameStore";
import { usePlayerStore } from "../stores/playerStore";
import SpiceLevelSelector from "./SpiceLevelSelector.vue";
import PlayerForm from "./PlayerForm.vue";
import PlayerListItem from "./PlayerListItem.vue";
import {
    Gamepad2,
    Settings,
    Users,
    UserPlus,
    Play,
    AlertCircle,
    XCircle,
    ArrowRight,
    ArrowLeft,
} from "lucide-vue-next";

const props = defineProps({
    isPro: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(["game-created"]);

// Age verification
const ageVerificationModal = ref(null);
const isAdult = ref(null);
const showAgeVerification = ref(true);

const confirmAge = (adult) => {
    isAdult.value = adult;
    showAgeVerification.value = false;
    if (!adult && maxSpiceRating.value > 2) {
        maxSpiceRating.value = 2;
    }
};

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

// Local state for stepped setup
const setupStep = ref(1); // 1 = pick heat, 2 = set tags, 3 = add players
const selectedTagsInPlay = ref([]);

// Computed: Filter available tags to only show selected ones
const tagsInPlayFiltered = computed(() => {
    return availableTagsFiltered.value.filter((tag) =>
        selectedTagsInPlay.value.includes(tag.id),
    );
});

// Helper function to get tag name by ID
const getTagName = (tagId) => {
    const tag = availableTagsFiltered.value.find((t) => t.id === tagId);
    return tag ? tag.name : tagId;
};

// Step navigation
const goToStep2 = () => {
    setupStep.value = 2;
};

const goToStep3 = () => {
    if (selectedTagsInPlay.value.length === 0) {
        error.value = "Please select at least one tag";
        return;
    }
    setupStep.value = 3;
};

// Tag selection
const toggleTagInPlay = (tagId) => {
    const index = selectedTagsInPlay.value.indexOf(tagId);
    if (index > -1) {
        selectedTagsInPlay.value.splice(index, 1);
    } else {
        selectedTagsInPlay.value.push(tagId);
    }
};

const selectAllTags = () => {
    selectedTagsInPlay.value = availableTagsFiltered.value.map((tag) => tag.id);
};

// Get actions from stores
const { fetchTags } = gameStore;
const { removePlayer, togglePlayerTag } = playerStore;

// Wrap addPlayer to pass filtered tags
const addPlayer = ({ name, gender }) => {
    newPlayerName.value = name;
    return playerStore.addPlayer(gender, tagsInPlayFiltered.value);
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
    // Show age verification on mount
    if (showAgeVerification.value && ageVerificationModal.value) {
        ageVerificationModal.value.showModal();
    }
});
</script>

<style scoped>
.game-setup {
    min-height: 70vh;
}
</style>
