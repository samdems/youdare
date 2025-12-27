<template>
    <div class="task-type-selector max-w-4xl mx-auto p-4">
        <!-- Player Turn Announcement -->
        <div class="text-center mb-8 animate-fade-in">
            <h1 class="text-5xl font-bold mb-4">
                <span class="text-6xl">{{
                    getPlayerAvatar(player.order)
                }}</span>
                <br />
                {{ player.name }}'s Turn!
            </h1>
            <p class="text-lg opacity-70">Choose your challenge</p>
        </div>

        <!-- Players Scoreboard -->
        <div class="card bg-base-200 shadow-lg mb-8">
            <div class="card-body p-4">
                <div class="flex flex-wrap items-center justify-center gap-4">
                    <div
                        v-for="p in players"
                        :key="p.id"
                        :class="[
                            'flex flex-col items-center p-3 rounded-lg transition-all min-w-[100px]',
                            p.id === player.id
                                ? 'bg-primary text-primary-content scale-110 shadow-lg'
                                : 'opacity-60',
                        ]"
                    >
                        <div class="text-3xl mb-1">
                            {{ getPlayerAvatar(p.order) }}
                        </div>
                        <div class="font-bold text-center text-sm">
                            {{ p.name }}
                            <span v-if="p.gender" class="text-xs">
                                {{ p.gender === "male" ? "👨" : "👩" }}
                            </span>
                        </div>
                        <div class="text-xs opacity-70">{{ p.score }} pts</div>
                        <!-- Player Tags -->
                        <div
                            v-if="p.tags && p.tags.length > 0"
                            class="flex flex-wrap gap-1 justify-center mt-1 max-w-[120px]"
                        >
                            <div
                                v-for="tag in p.tags.slice(0, 3)"
                                :key="tag.id"
                                class="badge badge-xs"
                                :class="
                                    p.id === player.id
                                        ? 'badge-neutral'
                                        : 'badge-ghost'
                                "
                                :title="tag.name"
                            >
                                {{ tag.name }}
                            </div>
                            <div
                                v-if="p.tags.length > 3"
                                class="badge badge-xs"
                                :class="
                                    p.id === player.id
                                        ? 'badge-neutral'
                                        : 'badge-ghost'
                                "
                                :title="
                                    p.tags
                                        .slice(3)
                                        .map((t) => t.name)
                                        .join(', ')
                                "
                            >
                                +{{ p.tags.length - 3 }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Choice Buttons -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <!-- Random Button -->
            <button
                @click="selectType('both')"
                class="btn btn-lg h-auto py-8 flex-col gap-3 hover:scale-105 transition-transform"
            >
                <span class="text-6xl">🎲</span>
                <span class="text-2xl font-bold">Random</span>
                <span class="text-sm opacity-70">Feeling lucky?</span>
            </button>

            <!-- Truth Button -->
            <button
                @click="selectType('truth')"
                class="btn btn-info btn-lg h-auto py-8 flex-col gap-3 hover:scale-105 transition-transform"
            >
                <span class="text-6xl">💬</span>
                <span class="text-2xl font-bold">Truth</span>
                <span class="text-sm opacity-70">Answer honestly</span>
            </button>

            <!-- Dare Button -->
            <button
                @click="selectType('dare')"
                class="btn btn-secondary btn-lg h-auto py-8 flex-col gap-3 hover:scale-105 transition-transform"
            >
                <span class="text-6xl">🎯</span>
                <span class="text-2xl font-bold">Dare</span>
                <span class="text-sm opacity-70">Take the challenge</span>
            </button>
        </div>

        <!-- Game Stats -->
        <div class="stats shadow w-full">
            <div class="stat">
                <div class="stat-title">Round</div>
                <div class="stat-value text-primary">{{ round }}</div>
            </div>
            <div class="stat">
                <div class="stat-title">Completed</div>
                <div class="stat-value text-success">{{ completed }}</div>
            </div>
            <div class="stat">
                <div class="stat-title">Skipped</div>
                <div class="stat-value text-warning">{{ skipped }}</div>
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
}

@keyframes fade-in {
    from {
        opacity: 0;
        transform: translateY(-20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-fade-in {
    animation: fade-in 0.5s ease-out;
}
</style>
