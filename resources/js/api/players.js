import apiClient from './client';

/**
 * Players API
 * Handles all player-related API requests
 */

/**
 * Get all players
 * @returns {Promise<Object>} Response with players data
 */
export const getPlayers = async () => {
    return await apiClient.get('/players');
};

/**
 * Get a specific player by ID
 * @param {number|string} playerId - The player ID
 * @returns {Promise<Object>} Response with player data
 */
export const getPlayer = async (playerId) => {
    return await apiClient.get(`/players/${playerId}`);
};

/**
 * Update an existing player
 * @param {number|string} playerId - The player ID
 * @param {Object} playerData - The player data to update
 * @returns {Promise<Object>} Response with updated player
 */
export const updatePlayer = async (playerId, playerData) => {
    return await apiClient.put(`/players/${playerId}`, playerData);
};

/**
 * Delete a player
 * @param {number|string} playerId - The player ID
 * @returns {Promise<Object>} Response confirming deletion
 */
export const deletePlayer = async (playerId) => {
    return await apiClient.delete(`/players/${playerId}`);
};

/**
 * Get a random task for a player
 * @param {number|string} playerId - The player ID
 * @param {string} [type] - Optional task type filter ('truth', 'dare', or 'both')
 * @returns {Promise<Object>} Response with random task
 */
export const getRandomTask = async (playerId, type = null) => {
    const params = type ? { type } : {};
    return await apiClient.get(`/players/${playerId}/tasks/random`, { params });
};

/**
 * Complete a task for a player
 * @param {number|string} playerId - The player ID
 * @param {Object} completionData - Task completion data
 * @param {number|string} completionData.task_id - The task ID
 * @param {number} completionData.points - Points awarded
 * @returns {Promise<Object>} Response with updated player and completion data
 */
export const completeTaskForPlayer = async (playerId, completionData) => {
    return await apiClient.post(`/players/${playerId}/complete-task`, completionData);
};

/**
 * Update player score
 * @param {number|string} playerId - The player ID
 * @param {number} points - Points to add (can be negative)
 * @returns {Promise<Object>} Response with updated player
 */
export const updatePlayerScore = async (playerId, points) => {
    return await apiClient.post(`/players/${playerId}/score`, { points });
};

/**
 * Get player's tags
 * @param {number|string} playerId - The player ID
 * @returns {Promise<Object>} Response with player's tags
 */
export const getPlayerTags = async (playerId) => {
    return await apiClient.get(`/players/${playerId}/tags`);
};

/**
 * Add a tag to a player
 * @param {number|string} playerId - The player ID
 * @param {number|string} tagId - The tag ID
 * @returns {Promise<Object>} Response confirming tag addition
 */
export const addTagToPlayer = async (playerId, tagId) => {
    return await apiClient.post(`/players/${playerId}/tags`, { tag_id: tagId });
};

/**
 * Remove a tag from a player
 * @param {number|string} playerId - The player ID
 * @param {number|string} tagId - The tag ID
 * @returns {Promise<Object>} Response confirming tag removal
 */
export const removeTagFromPlayer = async (playerId, tagId) => {
    return await apiClient.delete(`/players/${playerId}/tags/${tagId}`);
};

/**
 * Get player's completed tasks
 * @param {number|string} playerId - The player ID
 * @returns {Promise<Object>} Response with completed tasks
 */
export const getPlayerCompletedTasks = async (playerId) => {
    return await apiClient.get(`/players/${playerId}/completed-tasks`);
};

/**
 * Get player statistics
 * @param {number|string} playerId - The player ID
 * @returns {Promise<Object>} Response with player statistics
 */
export const getPlayerStats = async (playerId) => {
    return await apiClient.get(`/players/${playerId}/stats`);
};
