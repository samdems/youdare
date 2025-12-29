<template>
    <div v-if="task" class="card bg-base-100 shadow-2xl mb-6">
        <div class="card-body p-8">
            <!-- Task Type & Spice -->
            <div class="flex justify-between items-center mb-6">
                <div
                    :class="[
                        'badge badge-lg gap-2 px-4 py-4',
                        task.type === 'truth' ? 'badge-info' : 'badge-secondary',
                    ]"
                >
                    <MessageCircle v-if="task.type === 'truth'" :size="28" />
                    <Target v-else :size="28" />
                    <span class="text-lg font-bold uppercase">{{ task.type }}</span>
                </div>
                <div class="flex gap-1">
                    <Flame
                        v-for="n in task.spice_rating"
                        :key="n"
                        :size="24"
                        class="text-orange-500"
                    />
                </div>
            </div>

            <!-- Task Description -->
            <div class="text-center py-12">
                <p class="text-3xl font-bold leading-relaxed">
                    {{ taskDescription }}
                </p>
            </div>

            <!-- Task Tags -->
            <div
                v-if="task.tags && task.tags.length > 0"
                class="flex flex-wrap gap-2 justify-center mb-6"
            >
                <span
                    v-for="tag in task.tags"
                    :key="tag.id"
                    class="badge badge-ghost badge-sm"
                >
                    {{ tag.name }}
                </span>
            </div>

            <!-- Action Buttons -->
            <div class="flex gap-3 justify-center">
                <button
                    @click="$emit('complete')"
                    class="btn btn-success btn-lg gap-2 flex-1 max-w-[200px]"
                    :disabled="disabled"
                >
                    <CheckCircle :size="24" />
                    Done
                </button>
                <button
                    @click="$emit('skip')"
                    class="btn btn-outline btn-lg gap-2 flex-1 max-w-[200px]"
                    :disabled="disabled"
                >
                    <SkipForward :size="24" />
                    Skip
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import {
    MessageCircle,
    Target,
    Flame,
    CheckCircle,
    SkipForward,
} from "lucide-vue-next";

defineProps({
    task: {
        type: Object,
        required: true,
    },
    taskDescription: {
        type: String,
        required: true,
    },
    disabled: {
        type: Boolean,
        default: false,
    },
});

defineEmits(["complete", "skip"]);
</script>

<style scoped>
/* Component styles */
</style>
