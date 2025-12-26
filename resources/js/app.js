import "./bootstrap";
import { createApp } from "vue";
import GameScreen from "./components/GameScreen.vue";
import GameManager from "./components/GameManager.vue";

console.log("Vue app.js loaded");

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
        app.mount("game-manager");
        console.log("Vue GameManager mounted successfully");
        return;
    }

    // Check for game-screen element (legacy)
    const gameScreenElement = document.querySelector("game-screen");
    if (gameScreenElement) {
        console.log(
            "Found game-screen element, mounting Vue with GameScreen...",
        );
        const app = createApp(GameScreen);
        app.mount("game-screen");
        console.log("Vue GameScreen mounted successfully");
        return;
    }

    console.log("No game component elements found, Vue not mounted");
});
