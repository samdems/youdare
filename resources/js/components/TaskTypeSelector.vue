<template>
    <div class="task-type-selector max-w-3xl mx-auto p-6">
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
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
                <!-- Truth Button -->
                <button
                    @click="selectType('truth')"
                    class="btn btn-lg h-32 flex-col gap-2 hover:scale-105 transition-all bg-info hover:bg-info-focus border-none text-info-content"
                >
                    <MessageCircle :size="48" />
                    <span class="text-xl font-bold">Truth</span>
                </button>

                <!-- Dare Button -->
                <button
                    @click="selectType('dare')"
                    class="btn btn-lg h-32 flex-col gap-2 hover:scale-105 transition-all bg-secondary hover:bg-secondary-focus border-none text-secondary-content"
                >
                    <Target :size="48" />
                    <span class="text-xl font-bold">Dare</span>
                </button>

                <!-- Random Button -->
                <button
                    @click="selectType('both')"
                    class="btn btn-lg h-32 flex-col gap-2 hover:scale-105 transition-all bg-accent hover:bg-accent-focus border-none text-accent-content"
                >
                    <Shuffle :size="48" />
                    <span class="text-xl font-bold">Random</span>
                </button>
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
