<template>
    <div class="space-y-4">
        <div class="flex gap-4 flex-wrap items-start">
            <label
                v-for="i in 5"
                :key="i"
                class="cursor-pointer group flex flex-col items-center gap-2"
                :title="`Level ${i}`"
            >
                <input
                    type="radio"
                    :name="inputName"
                    :value="i"
                    v-model="selectedRating"
                    class="hidden peer"
                    :required="required"
                />
                <div
                    class="flex gap-1 p-2 rounded-lg transition-all peer-checked:bg-orange-500/20 peer-checked:scale-110 hover:bg-orange-500/10"
                >
                    <Flame
                        v-for="f in i"
                        :key="f"
                        :size="20"
                        :class="[
                            'transition-all',
                            selectedRating === i
                                ? 'text-orange-500'
                                : 'text-orange-500/40 group-hover:text-orange-500/60',
                        ]"
                    />
                </div>
                <span
                    :class="[
                        'text-xs font-semibold transition-all',
                        selectedRating === i
                            ? 'text-orange-500'
                            : 'text-base-content/60',
                    ]"
                >
                    Level {{ i }}
                </span>
            </label>
        </div>

        <div v-if="showDescriptions" class="text-sm opacity-70">
            <div class="grid grid-cols-1 sm:grid-cols-5 gap-2">
                <div>
                    <strong>Level 1:</strong> Completely wholesome and
                    kid-friendly
                </div>
                <div>
                    <strong>Level 2:</strong> Clean and appropriate for
                    teenagers
                </div>
                <div>
                    <strong>Level 3:</strong> Adults-only with light physical
                    vulnerability
                </div>
                <div><strong>Level 4:</strong> Full nudity required</div>
                <div>
                    <strong>Level 5:</strong> Extremely explicit - clear consent
                    mandatory
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { Flame } from "lucide-vue-next";

const props = defineProps({
    initialValue: {
        type: [Number, String],
        default: null,
    },
    inputName: {
        type: String,
        default: "spice_rating",
    },
    showDescriptions: {
        type: Boolean,
        default: true,
    },
    required: {
        type: Boolean,
        default: true,
    },
});

const selectedRating = ref(
    props.initialValue ? Number(props.initialValue) : null,
);

onMounted(() => {
    // If initial value is set, make sure the rating is selected
    if (props.initialValue) {
        selectedRating.value = Number(props.initialValue);
    }
});
</script>
