<template>
    <div>
        <label class="label">
            <span class="label-text font-semibold">Choose Spice Level</span>
        </label>
        <div class="grid grid-cols-5 gap-3">
            <button
                v-for="level in 5"
                :key="level"
                v-show="isAdult === null || isAdult === true || level <= 2"
                @click="selectLevel(level)"
                :class="[
                    'btn btn-outline flex-col h-24 gap-2 transition-all px-4 py-4',
                    modelValue === level ? 'btn-warning btn-active' : '',
                ]"
            >
                <div class="flex gap-0.5">
                    <Flame
                        v-for="n in level"
                        :key="n"
                        :size="20"
                        class="text-orange-500"
                    />
                </div>
                <span class="text-xs">{{ getLevelName(level) }}</span>
            </button>
        </div>
        <div :class="['alert mt-4', getAlertClass(modelValue)]">
            <Info :size="20" />
            <div class="flex flex-col gap-1">
                <span class="text-lg font-semibold">{{
                    getLevelTitle(modelValue)
                }}</span>
                <span class="text-sm">{{
                    getLevelDescription(modelValue)
                }}</span>
                <span class="text-xs opacity-70"
                    >{{ availableCount }} tags available at this level</span
                >
            </div>
        </div>
    </div>
</template>

<script setup>
import { Flame, Info } from "lucide-vue-next";

const props = defineProps({
    modelValue: {
        type: Number,
        default: 3,
    },
    availableCount: {
        type: Number,
        default: 0,
    },
    isAdult: {
        type: Boolean,
        default: null,
    },
});

const emit = defineEmits(["update:modelValue"]);

const selectLevel = (level) => {
    emit("update:modelValue", level);
};

const getLevelName = (level) => {
    const names = {
        1: "Mild",
        2: "Medium",
        3: "Hot",
        4: "Extra",
        5: "Extreme",
    };
    return names[level] || "Unknown";
};

const getLevelTitle = (level) => {
    const titles = {
        1: "Super Safe Family Game",
        2: "Teens Safe",
        3: "Adult Only - Remove Some Clothing",
        4: "Adult Only - Remove All Clothing",
        5: "Sex Party",
    };
    return titles[level] || "";
};

const getLevelDescription = (level) => {
    const descriptions = {
        1: "Completely wholesome and kid-friendly. Focuses on fun, silly, and innocent truths and dares only. Perfect for all ages and family settings—no romance or anything beyond light, harmless laughs.",
        2: "Clean and appropriate for teenagers. Mildly flirty or embarrassing but entirely non-explicit. Keeps things fun and suitable for ice-breaking in younger groups.",
        3: "Adults-only with light physical vulnerability. Truths become more personal, and dares involve partial undressing to create playful teasing and tension. Everyone remains partially clothed.",
        4: "Full nudity required. Truths are intimate and revealing; dares center on complete undressing and comfortable vulnerability while nude. Focuses on body positivity, trust, and sensual exposure—no sexual acts.",
        5: "Extremely explicit and wild for consenting adults only. Truths explore deep sexual fantasies and experiences; dares involve touching, oral, intercourse, or group activities. Suited for very open parties—clear boundaries and enthusiastic consent are mandatory.",
    };
    return descriptions[level] || "";
};

const getAlertClass = (level) => {
    if (level <= 2) {
        return "alert-info";
    } else if (level === 3) {
        return "alert-warning";
    } else {
        return "alert-error";
    }
};
</script>

<style scoped>
/* Component styles */
</style>
