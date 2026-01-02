/**
 * API Module Index
 * Central export point for all API modules
 */

// Import all API modules
import * as tags from "./tags";
import * as games from "./games";
import * as players from "./players";
import * as tasks from "./tasks";
import * as playerGroups from "./playerGroups";

// Export all API modules
export { tags, games, players, tasks, playerGroups };

// Export a unified API object
export default {
    tags,
    games,
    players,
    tasks,
    playerGroups,
};
