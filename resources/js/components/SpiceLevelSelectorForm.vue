<template>
    <div>
        <SpiceLevelSelector
            v-model="selectedLevel"
            :available-count="0"
            :is-adult="true"
            :is-pro="true"
        />
        <input
            type="hidden"
            :name="inputName"
            :value="selectedLevel"
            :required="required"
        />
    </div>
</template>

<script setup>
import { ref, watch } from "vue";
import SpiceLevelSelector from "./SpiceLevelSelector.vue";

const props = defineProps({
    initialValue: {
        type: [Number, String],
        default: 1,
    },
    inputName: {
        type: String,
        default: "spice_rating",
    },
    required: {
        type: Boolean,
        default: true,
    },
});

const selectedLevel = ref(
    props.initialValue ? Number(props.initialValue) : 1,
);

// Watch for changes and log them (helpful for debugging)
watch(selectedLevel, (newValue) => {
    console.log("Spice level changed to:", newValue);
});
</script>
