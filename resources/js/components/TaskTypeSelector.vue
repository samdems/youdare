<template>
    <div class="task-type-selector max-w-4xl mx-auto p-4">
        <!-- Player Turn Announcement -->
        <div class="text-center mb-8 animate-fade-in">
            <h1 class="text-5xl font-bold mb-4">
                <span class="text-6xl">{{ getPlayerAvatar(player.order) }}</span>
                <br />
                {{ player.name }}'s Turn!
            </h1>
            <p class="text-lg opacity-70">
                Choose your challenge
            </p>
        </div>

        <!-- Player Info Card -->
        <div class="card bg-primary text-primary-content shadow-xl mb-8">
            <div class="card-body">
                <div class="flex items-center justify-center gap-4 mb-4">
                    <div class="text-5xl">{{ getPlayerAvatar(player.order) }}</div>
                    <div class="text-center">
                        <h3 class="text-2xl font-bold">{{ player.name }}</h3>
                        <div class="flex items-center justify-center gap-2 mt-1">
                            <span v-if="player.gender" class="text-2xl">
                                {{ player.gender === "male" ? "👨" : "👩" }}
                            </span>
                            <span class="text-lg opacity-90">{{ player.score }} points</span>
                        </div>
                    </div>
                </div>

                <!-- Player Tags -->
                <div v-if="player.tags && player.tags.length > 0" class="mt-4">
                    <h4 class="text-sm font-semibold opacity-70 mb-2 text-center">Your Tags:</h4>
                    <div class="flex flex-wrap gap-2 justify-center">
                        <div
                            v-for="tag in player.tags"
                            :key="tag.id"
                            class="badge badge-lg badge-neutral gap-2"
                        >
                            <span>{{ tag.name }}</span>
                        </div>
                    </div>
                </div>
                <div v-else class="text-center text-sm opacity-70 mt-2">
                    No tags assigned yet
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
    name: 'TaskTypeSelector',
    props: {
        player: {
            type: Object,
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
                "😀", "😎", "🥳", "🤓", "🤠", "🥸", "😺", "🦊",
                "🐶", "🐼", "🦁", "🐯", "🐸", "🐙", "🦄", "🐲",
                "🌟", "⚡", "🔥", "💎"
            ],
        },
    },
    methods: {
        selectType(type) {
            this.$emit('type-selected', type);
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
