<template>
    <div class="task-type-selector max-w-3xl mx-auto p-4 sm:p-6 w-full">
        <!-- Fixed Header Section -->
        <div class="header-section">
            <!-- Current Player Highlight -->
            <current-player-badge
                v-if="player"
                :player="player"
                variant="large"
            />

            <!-- Scoreboard -->
            <scoreboard
                :players="players"
                :current-player-id="player ? player.id : null"
            />
        </div>

        <!-- Flexible Content Section -->
        <div class="content-section">
            <!-- Choice Buttons -->
            <div
                class="grid grid-cols-1 md:grid-cols-3 gap-3 sm:gap-4 mb-6 sm:mb-8 w-full"
            >
                <!-- Truth Button -->
                <button
                    @click="selectType('truth')"
                    class="btn btn-md sm:btn-lg h-24 sm:h-32 flex-col gap-2 hover:scale-105 transition-all bg-info hover:bg-info-focus border-none text-info-content"
                >
                    <MessageCircle :size="40" class="sm:w-12 sm:h-12" />
                    <span class="text-lg sm:text-xl font-bold">Truth</span>
                </button>

                <!-- Dare Button -->
                <button
                    @click="selectType('dare')"
                    class="btn btn-md sm:btn-lg h-24 sm:h-32 flex-col gap-2 hover:scale-105 transition-all bg-secondary hover:bg-secondary-focus border-none text-secondary-content"
                >
                    <Target :size="40" class="sm:w-12 sm:h-12" />
                    <span class="text-lg sm:text-xl font-bold">Dare</span>
                </button>

                <!-- Random Button -->
                <button
                    @click="selectType('both')"
                    class="btn btn-md sm:btn-lg h-24 sm:h-32 flex-col gap-2 hover:scale-105 transition-all bg-accent hover:bg-accent-focus border-none text-accent-content"
                >
                    <Shuffle :size="40" class="sm:w-12 sm:h-12" />
                    <span class="text-lg sm:text-xl font-bold">Random</span>
                </button>
            </div>

            <!-- Group Task Hint -->
            <div
                v-if="enableGroupTasks && players.length > 0"
                class="alert alert-info mb-4"
            >
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
                <div class="text-sm">
                    <span class="font-semibold">Group Task Coming!</span> After
                    all players complete this round, everyone will participate
                    in a group challenge together.
                </div>
            </div>

            <!-- Stats -->
            <game-stats
                :completed="completed"
                :skipped="skipped"
                :round="round"
            />
        </div>
    </div>
</template>

<script setup>
import { defineProps, defineEmits } from "vue";
import { MessageCircle, Target, Shuffle } from "lucide-vue-next";
import CurrentPlayerBadge from "./CurrentPlayerBadge.vue";
import Scoreboard from "./Scoreboard.vue";
import GameStats from "./GameStats.vue";

const props = defineProps({
    player: {
        type: Object,
        required: true,
    },
    players: {
        type: Array,
        required: true,
    },
    round: {
        type: Number,
        default: 1,
    },
    completed: {
        type: Number,
        default: 0,
    },
    skipped: {
        type: Number,
        default: 0,
    },
    enableGroupTasks: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(["type-selected"]);

const selectType = (type) => {
    emit("type-selected", type);
};
</script>

<style scoped>
.task-type-selector {
    min-height: 70vh;
    display: flex;
    flex-direction: column;
    max-width: 100%;
    overflow-x: hidden;
}

.header-section {
    flex-shrink: 0;
}

.content-section {
    flex-grow: 1;
    display: flex;
    flex-direction: column;
    justify-content: center;
}
</style>
