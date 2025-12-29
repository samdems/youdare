import "./bootstrap";
import { createApp } from "vue";
import { createPinia } from "pinia";
import GameManager from "./components/GameManager.vue";

console.log("Vue app.js loaded");

// Enable Vue DevTools in development
if (import.meta.env.DEV) {
    console.log("Development mode - Vue DevTools enabled");
}

// Wait for DOM to be ready
document.addEventListener("DOMContentLoaded", () => {
    console.log("DOM Content Loaded");

    // Check for game-manager element (new tag-based game)
    const gameManagerElement = document.querySelector("game-manager");
    if (gameManagerElement) {
        console.log(
            "Found game-manager element, mounting Vue with GameManager...",
        );
        const app = createApp(GameManager);
        const pinia = createPinia();
        app.use(pinia);

        // Force enable devtools (for development only)
        app.config.devtools = true;
        app.config.performance = true;

        app.mount("game-manager");
        console.log("Vue GameManager mounted successfully");
        return;
    }

    console.log("No game component elements found, Vue not mounted");
});
