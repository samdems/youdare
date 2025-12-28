/**
 * API Module Index
 * Central export point for all API modules
 */

// Import all API modules
import * as tags from './tags';
import * as games from './games';
import * as players from './players';
import * as tasks from './tasks';

// Export the axios client for direct access if needed
export { default as apiClient } from './client';

// Export all API modules
export { tags, games, players, tasks };

// Export a unified API object
export default {
    tags,
    games,
    players,
    tasks,
};
