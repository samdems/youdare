<template>
    <div
        class="flex flex-col sm:flex-row items-start gap-3 p-4 rounded-lg bg-base-200 hover:bg-base-300 transition-colors"
    >
        <div class="flex items-start gap-3 flex-1 w-full sm:w-auto">
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
                    <button
                        @click="openModal"
                        class="badge badge-primary gap-1 cursor-pointer hover:badge-primary/80 transition-colors"
                        :class="{
                            'badge-outline': player.tags.length === 0,
                        }"
                    >
                        <Tags :size="12" />
                        {{ player.tags.length }} tag{{
                            player.tags.length !== 1 ? "s" : ""
                        }}
                    </button>
                </div>
            </div>
        </div>
        <div class="flex gap-2 w-full sm:w-auto justify-end sm:justify-start">
            <button
                @click="openModal"
                class="btn btn-primary btn-sm flex-1 sm:flex-initial"
                title="Edit tags"
            >
                <Tags :size="16" />
                <span class="hidden sm:inline">Edit Tags</span>
                <span class="sm:hidden">Tags</span>
            </button>
            <button
                @click="$emit('remove', player.id)"
                class="btn btn-ghost btn-sm btn-circle shrink-0"
                title="Remove player"
            >
                <X :size="16" />
            </button>
        </div>

        <!-- Tags Modal -->
        <dialog :data-player-modal="player.id" class="modal">
            <div class="modal-box max-w-2xl">
                <form method="dialog">
                    <button
                        class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2"
                    >
                        ✕
                    </button>
                </form>
                <h3 class="font-bold text-lg mb-4 flex items-center gap-2">
                    <Tags :size="20" />
                    Tags for {{ player.name }}
                </h3>

                <div
                    v-if="availableTags.length > 0"
                    class="bg-base-200 rounded-lg p-4 max-h-96 overflow-y-auto"
                >
                    <div class="space-y-1">
                        <label
                            v-for="tag in availableTags"
                            :key="tag.id"
                            class="flex items-start gap-3 p-3 rounded-lg hover:bg-base-100 cursor-pointer transition-colors group"
                        >
                            <input
                                type="checkbox"
                                :checked="player.tags.includes(tag.id)"
                                @change="$emit('toggle-tag', player.id, tag.id)"
                                class="toggle toggle-primary mt-1"
                            />
                            <div class="flex-1 min-w-0">
                                <div
                                    class="font-medium"
                                    :class="{
                                        'text-primary': player.tags.includes(
                                            tag.id,
                                        ),
                                        'text-base-content/70':
                                            !player.tags.includes(tag.id),
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
                                v-if="player.tags.includes(tag.id)"
                                :size="20"
                                class="text-primary opacity-0 group-hover:opacity-100 transition-opacity mt-1"
                            />
                        </label>
                    </div>
                </div>
                <div v-else class="text-center py-8 text-base-content/60">
                    <Tags :size="48" class="mx-auto mb-2 opacity-30" />
                    <p>No tags available</p>
                </div>

                <div class="modal-action">
                    <form method="dialog">
                        <button class="btn btn-primary">Done</button>
                    </form>
                </div>
            </div>
            <form method="dialog" class="modal-backdrop">
                <button>close</button>
            </form>
        </dialog>
    </div>
</template>

<script setup>
import { ref, onMounted, watch } from "vue";
import { User, Check, X, Tags } from "lucide-vue-next";

const props = defineProps({
    player: {
        type: Object,
        required: true,
    },
    availableTags: {
        type: Array,
        default: () => [],
    },
    autoOpenModal: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(["remove", "toggle-tag"]);

const openModal = () => {
    const modal = document.querySelector(
        `[data-player-modal="${props.player.id}"]`,
    );
    if (modal) {
        modal.showModal();
    }
};

// Auto-open modal when component mounts if autoOpenModal is true
onMounted(() => {
    if (props.autoOpenModal) {
        // Small delay to ensure DOM is ready
        setTimeout(() => {
            openModal();
        }, 100);
    }
});

// Watch for autoOpenModal changes
watch(
    () => props.autoOpenModal,
    (newVal) => {
        if (newVal) {
            openModal();
        }
    },
);
</script>

<script>
export default {
    name: "PlayerListItem",
};
</script>

<style scoped>
/* Component styles */
</style>
