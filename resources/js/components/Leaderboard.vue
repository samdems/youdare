<template>
    <div class="card bg-base-100 shadow-xl mb-8">
        <div class="card-body">
            <h3 class="text-xl font-bold mb-4">Final Standings</h3>
            <div class="space-y-2">
                <div
                    v-for="(player, index) in players"
                    :key="player.id"
                    class="flex items-center gap-4 p-4 bg-base-200 rounded-lg"
                >
                    <div class="min-w-[3rem] text-center">
                        <Trophy
                            v-if="index === 0"
                            :size="32"
                            color="gold"
                        />
                        <Medal
                            v-else-if="index === 1"
                            :size="32"
                            color="silver"
                        />
                        <Award
                            v-else-if="index === 2"
                            :size="32"
                            color="#CD7F32"
                        />
                        <span v-else class="text-2xl font-bold"
                            >#{{ index + 1 }}</span
                        >
                    </div>
                    <div class="flex-1">
                        <div class="font-bold text-lg">
                            {{ player.name }}
                        </div>
                    </div>
                    <div class="text-2xl font-bold text-primary">
                        {{ player.score }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { Trophy, Medal, Award } from "lucide-vue-next";

defineProps({
    players: {
        type: Array,
        required: true,
    },
});
</script>

<style scoped>
/* Component styles */
</style>
