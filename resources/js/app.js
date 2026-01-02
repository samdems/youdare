import "./bootstrap";
import { createApp } from "vue";
import { createPinia } from "pinia";
import GameManager from "./components/GameManager.vue";
import SpiceRatingInput from "./components/SpiceRatingInput.vue";
import SpiceLevelSelectorForm from "./components/SpiceLevelSelectorForm.vue";

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

    // Check for spice-level-selector-form element (task create/edit forms)
    const spiceLevelElement = document.querySelector(
        "spice-level-selector-form",
    );
    if (spiceLevelElement) {
        console.log("Found spice-level-selector-form element, mounting Vue...");

        // Get initial value from data attribute
        const initialValue =
            spiceLevelElement.getAttribute("data-initial-value") || "1";
        const inputName =
            spiceLevelElement.getAttribute("data-input-name") || "spice_rating";

        // Create app with the component
        const app = createApp(SpiceLevelSelectorForm, {
            initialValue: initialValue,
            inputName: inputName,
        });

        // Force enable devtools (for development only)
        app.config.devtools = true;
        app.config.performance = true;

        app.mount("spice-level-selector-form");
        console.log("Vue SpiceLevelSelectorForm mounted successfully");
        return;
    }

    // Check for spice-rating-input element (task create/edit forms - legacy)
    const spiceRatingElement = document.querySelector("spice-rating-input");
    if (spiceRatingElement) {
        console.log("Found spice-rating-input element, mounting Vue...");

        // Get initial value from data attribute
        const initialValue =
            spiceRatingElement.getAttribute("data-initial-value");
        const inputName =
            spiceRatingElement.getAttribute("data-input-name") ||
            "spice_rating";
        const showDescriptions =
            spiceRatingElement.getAttribute("data-show-descriptions") !==
            "false";

        // Create app with the component
        const app = createApp(SpiceRatingInput, {
            initialValue: initialValue,
            inputName: inputName,
            showDescriptions: showDescriptions,
        });

        // Force enable devtools (for development only)
        app.config.devtools = true;
        app.config.performance = true;

        app.mount("spice-rating-input");
        console.log("Vue SpiceRatingInput mounted successfully");
        return;
    }

    console.log("No game component elements found, Vue not mounted");
});
