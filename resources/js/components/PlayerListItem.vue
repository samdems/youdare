<template>
    <div
        class="flex items-start gap-3 p-3 rounded-lg bg-base-200 hover:bg-base-300 transition-colors"
    >
        <img
            :src="player.avatar"
            :alt="`${player.name}'s avatar`"
            class="w-12 h-12 rounded-full"
        />
        <div class="flex-1 min-w-0">
            <div class="flex items-center gap-2 mb-1">
                <span class="font-bold">{{ player.name }}</span>
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
                <div class="text-xs font-semibold mb-2 opacity-70">
                    Available Tags
                </div>
                <div class="flex flex-wrap gap-1">
                    <button
                        v-for="tag in availableTags"
                        :key="tag.id"
                        @click="$emit('toggle-tag', player.id, tag.id)"
                        :class="[
                            'badge badge-sm gap-1 cursor-pointer transition-all tooltip tooltip-top',
                            player.tags.includes(tag.id)
                                ? 'badge-primary'
                                : 'badge-outline hover:badge-primary',
                        ]"
                        :data-tip="tag.description || tag.name"
                    >
                        {{ tag.name }}
                        <Check v-if="player.tags.includes(tag.id)" :size="12" />
                    </button>
                </div>
            </div>
        </div>
        <button
            @click="$emit('remove', player.id)"
            class="btn btn-ghost btn-sm btn-circle"
            title="Remove player"
        >
            <X :size="16" />
        </button>
    </div>
</template>

<script setup>
import { User, Check, X } from "lucide-vue-next";

defineProps({
    player: {
        type: Object,
        required: true,
    },
    availableTags: {
        type: Array,
        default: () => [],
    },
});

defineEmits(["remove", "toggle-tag"]);
</script>

<style scoped>
/* Component styles */
</style>
