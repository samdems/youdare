<template>
    <div class="space-y-3">
        <input
            type="text"
            v-model="playerName"
            placeholder="Enter player name"
            class="input input-bordered w-full"
            maxlength="20"
        />
        <div class="grid grid-cols-3 gap-2">
            <button
                @click="handleAddPlayer('male')"
                class="btn btn-outline gap-2"
                :disabled="!playerName.trim()"
            >
                <User :size="20" />
                <span>Add Male</span>
            </button>
            <button
                @click="handleAddPlayer('female')"
                class="btn btn-outline gap-2"
                :disabled="!playerName.trim()"
            >
                <User :size="20" />
                <span>Add Female</span>
            </button>
            <button
                @click="handleAddPlayer('other')"
                class="btn btn-outline gap-2"
                :disabled="!playerName.trim()"
            >
                <User :size="20" />
                <span>Add Other</span>
            </button>
        </div>
    </div>
</template>

<script setup>
import { ref } from "vue";
import { User } from "lucide-vue-next";

const props = defineProps({
    modelValue: {
        type: String,
        default: "",
    },
});

const emit = defineEmits(["update:modelValue", "add-player"]);

const playerName = ref(props.modelValue);

const handleAddPlayer = (gender) => {
    if (playerName.value.trim()) {
        emit("add-player", { name: playerName.value.trim(), gender });
        playerName.value = "";
        emit("update:modelValue", "");
    }
};
</script>

<style scoped>
/* Component styles */
</style>
