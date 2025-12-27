<template>
    <div class="task-type-selector max-w-3xl mx-auto p-6">
        <!-- Current Player Highlight -->
        <div class="text-center mb-8">
            <div class="text-7xl mb-3">
                {{ getPlayerAvatar(player.order) }}
            </div>
            <h2 class="text-3xl font-bold mb-1">{{ player.name }}'s Turn</h2>
            <p class="text-base-content/60">Choose your challenge</p>
        </div>

        <!-- Choice Buttons -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
            <!-- Truth Button -->
            <button
                @click="selectType('truth')"
                class="btn btn-lg h-32 flex-col gap-2 hover:scale-105 transition-all bg-info hover:bg-info-focus border-none text-info-content"
            >
                <span class="text-5xl">💬</span>
                <span class="text-xl font-bold">Truth</span>
            </button>

            <!-- Dare Button -->
            <button
                @click="selectType('dare')"
                class="btn btn-lg h-32 flex-col gap-2 hover:scale-105 transition-all bg-secondary hover:bg-secondary-focus border-none text-secondary-content"
            >
                <span class="text-5xl">🎯</span>
                <span class="text-xl font-bold">Dare</span>
            </button>

            <!-- Random Button -->
            <button
                @click="selectType('both')"
                class="btn btn-lg h-32 flex-col gap-2 hover:scale-105 transition-all bg-accent hover:bg-accent-focus border-none text-accent-content"
            >
                <span class="text-5xl">🎲</span>
                <span class="text-xl font-bold">Random</span>
            </button>
        </div>

        <!-- Scoreboard -->
        <div class="card bg-base-200 shadow-lg mb-6">
            <div class="card-body p-4">
                <div class="flex items-center justify-center gap-4 flex-wrap">
                    <div
                        v-for="p in players"
                        :key="p.id"
                        :class="[
                            'flex items-center gap-2 px-3 py-2 rounded-lg transition-all',
                            p.id === player.id
                                ? 'bg-primary text-primary-content font-bold'
                                : 'opacity-60',
                        ]"
                    >
                        <span class="text-2xl">
                            {{ getPlayerAvatar(p.order) }}
                        </span>
                        <div class="text-sm">
                            <div>{{ p.name }}</div>
                            <div class="text-xs opacity-70">
                                {{ p.score }} pts
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stats -->
        <div class="grid grid-cols-3 gap-2">
            <div class="stat bg-base-200 rounded-lg p-3 text-center">
                <div class="stat-title text-xs">Round</div>
                <div class="stat-value text-2xl text-primary">{{ round }}</div>
            </div>
            <div class="stat bg-base-200 rounded-lg p-3 text-center">
                <div class="stat-title text-xs">Completed</div>
                <div class="stat-value text-2xl text-success">
                    {{ completed }}
                </div>
            </div>
            <div class="stat bg-base-200 rounded-lg p-3 text-center">
                <div class="stat-title text-xs">Skipped</div>
                <div class="stat-value text-2xl text-warning">
                    {{ skipped }}
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: "TaskTypeSelector",
    props: {
        player: {
            type: Object,
            required: true,
        },
        players: {
            type: Array,
            required: true,
        },
        round: {
            type: Number,
            default: 1,
        },
        completed: {
            type: Number,
            default: 0,
        },
        skipped: {
            type: Number,
            default: 0,
        },
        playerAvatars: {
            type: Array,
            default: () => [
                "😀",
                "😎",
                "🥳",
                "🤓",
                "🤠",
                "🥸",
                "😺",
                "🦊",
                "🐶",
                "🐼",
                "🦁",
                "🐯",
                "🐸",
                "🐙",
                "🦄",
                "🐲",
                "🌟",
                "⚡",
                "🔥",
                "💎",
            ],
        },
    },
    methods: {
        selectType(type) {
            this.$emit("type-selected", type);
        },
        getPlayerAvatar(order) {
            return this.playerAvatars[order % this.playerAvatars.length];
        },
    },
};
</script>

<style scoped>
.task-type-selector {
    min-height: 70vh;
    display: flex;
    flex-direction: column;
    justify-content: center;
}
</style>
