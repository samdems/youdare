<template>
    <div class="flex gap-1">
        <Flame
            v-for="n in rating"
            :key="n"
            :size="size"
            class="text-orange-500"
        />
    </div>
</template>

<script setup>
import { Flame } from "lucide-vue-next";

defineProps({
    rating: {
        type: Number,
        required: true,
        validator: (value) => value >= 1 && value <= 5,
    },
    size: {
        type: Number,
        default: 20,
    },
});
</script>
