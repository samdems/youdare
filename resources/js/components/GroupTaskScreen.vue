<template>
    <div class="group-task-screen max-w-4xl mx-auto p-4 sm:p-6 w-full">
        <!-- Header -->
        <div class="text-center mb-6 sm:mb-8">
            <div
                class="inline-block bg-success/20 rounded-full px-4 sm:px-8 py-3 sm:py-4 mb-4"
            >
                <h1
                    class="text-2xl sm:text-4xl font-bold text-success flex items-center gap-2 sm:gap-3"
                >
                    <Users :size="32" class="sm:w-10 sm:h-10" />
                    GROUP CHALLENGE!
                </h1>
            </div>
            <p class="text-base sm:text-xl opacity-70">
                Round {{ round }} Complete - Everyone participates!
            </p>
        </div>

        <!-- Group Task Card -->
        <div v-if="task" class="card bg-base-100 shadow-2xl mb-6">
            <div class="card-body p-8">
                <!-- Spice Rating -->
                <div class="flex justify-end items-center mb-6">
                    <spice-rating :rating="task.spice_rating" :size="18" />
                </div>

                <!-- Task Description -->
                <div class="text-center py-6 sm:py-8 md:py-12 px-2">
                    <p
                        class="text-xl sm:text-2xl md:text-3xl font-bold leading-relaxed break-words"
                    >
                        {{ processedDescription }}
                    </p>
                </div>

                <!-- Task Tags -->
                <div
                    v-if="task.tags && task.tags.length > 0"
                    class="flex flex-wrap gap-1 sm:gap-2 justify-center mb-4 sm:mb-6"
                >
                    <span
                        v-for="tag in task.tags"
                        :key="tag.id"
                        class="badge badge-ghost badge-sm"
                    >
                        {{ tag.name }}
                    </span>
                </div>

                <!-- Player Avatars -->
                <div class="flex justify-center gap-3 mb-8 flex-wrap">
                    <div
                        v-for="player in players"
                        :key="player.id"
                        class="flex items-center gap-1 sm:gap-2 px-2 sm:px-4 py-1 sm:py-2 bg-base-200 rounded-full"
                    >
                        <img
                            :src="player.avatar"
                            :alt="`${player.name}'s avatar`"
                            class="w-16 h-16 rounded-full border-4 border-success"
                        />
                        <span class="text-xs font-semibold">{{
                            player.name
                        }}</span>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div
                    class="flex flex-col sm:flex-row gap-2 sm:gap-3 justify-center w-full"
                >
                    <button
                        @click="$emit('complete')"
                        class="btn btn-success btn-md sm:btn-lg gap-2 flex-1 max-w-full sm:max-w-[200px]"
                        :disabled="loading"
                    >
                        <CheckCircle :size="24" />
                        <span v-if="loading">Completing...</span>
                        <span v-else>Done - Start Next Round</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Loading State -->
        <div v-else-if="loading" class="card bg-base-100 shadow-xl w-full">
            <div class="card-body items-center justify-center py-12 sm:py-20">
                <Loader :size="48" class="animate-spin text-success" />
                <p class="mt-4 text-lg opacity-70">Loading group task...</p>
            </div>
        </div>

        <!-- No Task Available -->
        <div v-else class="card bg-base-100 shadow-xl">
            <div class="card-body items-center justify-center py-20">
                <Frown :size="64" class="mb-4 text-base-content/50" />
                <p class="text-2xl font-bold mb-2">No group tasks available</p>
                <p class="text-sm opacity-70 mb-6">Starting next round...</p>
                <button @click="skipToNextRound" class="btn btn-primary gap-2">
                    <SkipForward :size="20" />
                    Continue
                </button>
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
import { computed } from "vue";
import {
    Users,
    CheckCircle,
    Loader,
    Frown,
    SkipForward,
    XCircle,
} from "lucide-vue-next";
import SpiceRating from "./SpiceRating.vue";

const props = defineProps({
    task: {
        type: Object,
        default: null,
    },
    players: {
        type: Array,
        required: true,
    },
    round: {
        type: Number,
        default: 1,
    },
    loading: {
        type: Boolean,
        default: false,
    },
    error: {
        type: String,
        default: null,
    },
});

const emit = defineEmits(["complete", "skip"]);

// Process task description with template variables
const processedDescription = computed(() => {
    if (!props.task || !props.task.description) return "";

    let processed = props.task.description;

    // Replace {{number_of_players}}
    if (processed.includes("{{number_of_players}}")) {
        processed = processed.replace(
            /\{\{number_of_players\}\}/g,
            props.players.length,
        );
    }

    // Replace {{number_of_players/X}} - divided and rounded
    const divisionRegex = /\{\{number_of_players\/(\d+)\}\}/g;
    processed = processed.replace(divisionRegex, (match, divisor) => {
        const result = Math.round(props.players.length / parseInt(divisor));
        return result;
    });

    return processed;
});

const completeTask = () => {
    emit("complete");
};

const skipToNextRound = () => {
    emit("skip");
};
</script>

<style scoped>
.group-task-screen {
    min-height: 70vh;
}

@keyframes spin {
    from {
        transform: rotate(0deg);
    }
    to {
        transform: rotate(360deg);
    }
}

.animate-spin {
    animation: spin 1s linear infinite;
}
</style>
