<template>
    <div class="game-setup max-w-4xl mx-auto p-4 sm:p-6">
        <!-- 18+ Age Verification Modal -->
        <Teleport to="body">
            <dialog
                v-if="showAgeVerification"
                ref="ageVerificationModal"
                class="modal modal-open"
            >
                <div
                    class="modal-box !max-w-none w-[calc(100vw-2rem)] sm:!max-w-sm"
                >
                    <h3 class="font-bold text-lg sm:text-xl mb-3">
                        Age Verification
                    </h3>
                    <p class="py-2 text-sm sm:text-base">
                        This game has multiple heat levels. The first two levels
                        are family-friendly and safe for all ages.
                    </p>
                    <p class="py-2 font-semibold text-sm sm:text-base">
                        To unlock adult content (Heat levels 3-5), everyone in
                        the room must be 18 years or older. Heat levels 3-5 also
                        require a Pro account.
                    </p>
                    <p class="pb-4 text-xs sm:text-sm text-base-content/70">
                        Is everyone present 18+?
                    </p>
                    <div class="modal-action flex-col gap-2 sm:flex-row">
                        <button
                            @click="confirmAge(false)"
                            class="btn btn-outline w-full sm:w-auto"
                        >
                            No - Family Mode
                        </button>
                        <button
                            @click="confirmAge(true)"
                            class="btn btn-primary w-full sm:w-auto"
                        >
                            Yes - Everyone is 18+
                        </button>
                    </div>
                </div>
            </dialog>
        </Teleport>

        <!-- Step Indicator -->
        <div class="flex justify-center mb-6 sm:mb-8">
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
        <div v-if="setupStep === 1" class="space-y-6 sm:space-y-8">
            <div class="card bg-base-100 shadow-lg">
                <div class="card-body p-4 sm:p-6">
                    <h3
                        class="text-xl sm:text-2xl font-bold mb-3 sm:mb-4 text-center"
                    >
                        Step 1: Pick Your Heat 🔥
                    </h3>
                    <p
                        class="text-center text-sm sm:text-base text-base-content/60 mb-4 sm:mb-6"
                    >
                        Choose the spice level for your game
                    </p>

                    <!-- Spice Level -->
                    <spice-level-selector
                        v-model="maxSpiceRating"
                        :available-count="availableTagsFiltered.length"
                        :is-adult="isAdult"
                        :is-pro="isPro"
                    />

                    <!-- Group Tasks Option -->
                    <div class="form-control mt-6">
                        <label class="label cursor-pointer justify-start gap-4">
                            <input
                                type="checkbox"
                                v-model="enableGroupTasks"
                                class="checkbox checkbox-primary"
                            />
                            <div>
                                <span
                                    class="label-text font-semibold text-base"
                                >
                                    Enable Group Tasks
                                </span>
                                <p class="text-xs text-base-content/60 mt-1">
                                    At the end of each round, everyone
                                    participates in a group challenge together
                                </p>
                            </div>
                        </label>
                    </div>

                    <div class="card-actions justify-end mt-4 sm:mt-6">
                        <button
                            v-if="isPro || maxSpiceRating < 3"
                            @click="goToStep2"
                            class="btn btn-primary btn-md sm:btn-lg gap-2 w-full sm:w-auto"
                        >
                            Next: Set Tags
                            <ArrowRight :size="20" />
                        </button>
                        <a
                            v-if="!isPro && maxSpiceRating >= 3"
                            href="/go-pro"
                            class="btn btn-warning btn-md sm:btn-lg gap-2 w-full sm:w-auto"
                        >
                            Upgrade
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Step 2: Set Tags in Play -->
        <div v-else-if="setupStep === 2" class="space-y-6 sm:space-y-8">
            <div class="card bg-base-100 shadow-lg">
                <div class="card-body p-4 sm:p-6">
                    <h3
                        class="text-xl sm:text-2xl font-bold mb-3 sm:mb-4 text-center"
                    >
                        Step 2: Set Tags in Play 🏷️
                    </h3>
                    <p
                        class="text-center text-sm sm:text-base text-base-content/60 mb-4 sm:mb-6"
                    >
                        Select which tags will be available for this game (based
                        on spice level {{ maxSpiceRating }})
                    </p>

                    <!-- All Available Tags Grouped -->
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
                            class="bg-base-200 rounded-lg p-4 max-h-96 overflow-y-auto"
                        >
                            <!-- Display tags grouped by tag groups -->
                            <div
                                v-for="group in groupedTags"
                                :key="group.id || group.slug"
                                class="mb-6 last:mb-0"
                            >
                                <!-- Group Header -->
                                <div class="mb-3">
                                    <h5
                                        class="font-bold text-sm uppercase tracking-wide text-primary"
                                    >
                                        {{ group.name }}
                                    </h5>
                                    <p
                                        v-if="group.description"
                                        class="text-xs text-base-content/50 mt-1"
                                    >
                                        {{ group.description }}
                                    </p>
                                </div>

                                <!-- Tags in this group -->
                                <div class="space-y-1">
                                    <label
                                        v-for="tag in group.tags"
                                        :key="tag.id"
                                        class="flex items-start gap-3 p-3 rounded-lg hover:bg-base-100 cursor-pointer transition-colors group"
                                    >
                                        <input
                                            type="checkbox"
                                            :checked="
                                                selectedTagsInPlay.includes(
                                                    tag.id,
                                                )
                                            "
                                            @change="toggleTagInPlay(tag.id)"
                                            class="toggle toggle-primary mt-1"
                                        />
                                        <div class="flex-1 min-w-0">
                                            <div
                                                :class="{
                                                    'font-semibold text-primary':
                                                        selectedTagsInPlay.includes(
                                                            tag.id,
                                                        ),
                                                    'text-base-content/70':
                                                        !selectedTagsInPlay.includes(
                                                            tag.id,
                                                        ),
                                                }"
                                            >
                                                {{ tag.name }}
                                            </div>
                                            <div
                                                v-if="tag.description"
                                                class="text-sm text-base-content/50 mt-1"
                                            >
                                                {{ tag.description }}
                                            </div>
                                        </div>
                                        <Check
                                            v-if="
                                                selectedTagsInPlay.includes(
                                                    tag.id,
                                                )
                                            "
                                            :size="20"
                                            class="text-primary opacity-0 group-hover:opacity-100 transition-opacity mt-1"
                                        />
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div v-else class="text-center py-8">
                            <p class="text-base-content/60">
                                No tags available for this spice level
                            </p>
                        </div>
                    </div>

                    <div
                        class="card-actions justify-between mt-4 sm:mt-6 flex-col sm:flex-row gap-2"
                    >
                        <button
                            @click="setupStep = 1"
                            class="btn btn-outline w-full sm:w-auto order-2 sm:order-1"
                        >
                            <ArrowLeft :size="20" />
                            Back
                        </button>
                        <button
                            @click="goToStep3"
                            class="btn btn-primary btn-md sm:btn-lg gap-2 w-full sm:w-auto order-1 sm:order-2"
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
        <div v-else-if="setupStep === 3" class="space-y-6 sm:space-y-8">
            <!-- Add Player Card -->
            <div class="card bg-base-100 shadow-lg">
                <div class="card-body p-4 sm:p-6">
                    <h3
                        class="text-xl sm:text-2xl font-bold mb-3 sm:mb-4 text-center"
                    >
                        Step 3: Add Players 👥
                    </h3>
                    <p
                        class="text-center text-sm sm:text-base text-base-content/60 mb-4 sm:mb-6"
                    >
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
                            :auto-open-modal="player.id === lastAddedPlayerId"
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
            <div
                class="flex justify-between items-center flex-col sm:flex-row gap-3"
            >
                <button
                    @click="setupStep = 2"
                    class="btn btn-outline w-full sm:w-auto order-2 sm:order-1"
                >
                    <ArrowLeft :size="20" />
                    Back to Tags
                </button>

                <button
                    @click="createGame"
                    class="btn btn-success btn-md gap-2 w-full sm:w-auto sm:min-w-[180px] order-1 sm:order-2"
                    :disabled="players.length < 2 || creatingGame"
                    :class="{ loading: creatingGame }"
                >
                    <Play v-if="!creatingGame" :size="20" />
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
    Check,
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
    groupedTags,
    loadingTags,
    availableTagsFiltered,
    enableGroupTasks,
} = storeToRefs(gameStore);

const { players, newPlayerName } = storeToRefs(playerStore);

// Local state for stepped setup
const setupStep = ref(1); // 1 = pick heat, 2 = set tags, 3 = add players
const selectedTagsInPlay = ref([]);
const lastAddedPlayerId = ref(null);

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
const { fetchTags, fetchGroupedTags } = gameStore;
const { removePlayer, togglePlayerTag } = playerStore;

// Wrap addPlayer to pass filtered tags and track last added player
const addPlayer = async ({ name, gender }) => {
    newPlayerName.value = name;
    const result = await playerStore.addPlayer(
        gender,
        tagsInPlayFiltered.value,
    );
    if (result.success && result.player) {
        lastAddedPlayerId.value = result.player.id;
        // Reset after a delay to allow modal to open
        setTimeout(() => {
            lastAddedPlayerId.value = null;
        }, 500);
    }
    return result;
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
    fetchGroupedTags();
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
