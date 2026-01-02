import apiClient from "./client";

/**
 * Player Groups API
 * Handles all player group-related API requests
 */

/**
 * Get all player groups
 * @param {Object} params - Query parameters
 * @param {boolean} params.with_counts - Include player counts
 * @param {boolean} params.with_players - Include players in each group
 * @returns {Promise<Object>} Response with player groups data
 */
export const getPlayerGroups = async (params = {}) => {
    return await apiClient.get("/player-groups", { params });
};

/**
 * Get a specific player group by ID
 * @param {number|string} playerGroupId - The player group ID
 * @returns {Promise<Object>} Response with player group data
 */
export const getPlayerGroup = async (playerGroupId) => {
    return await apiClient.get(`/player-groups/${playerGroupId}`);
};

/**
 * Create a new player group
 * @param {Object} playerGroupData - The player group data
 * @param {string} playerGroupData.name - Group name
 * @param {string} playerGroupData.slug - URL-friendly identifier (optional)
 * @param {string} playerGroupData.description - Group description (optional)
 * @param {string} playerGroupData.color - Hex color code (optional, default: #3b82f6)
 * @param {string} playerGroupData.icon - Emoji or icon (optional)
 * @param {number} playerGroupData.sort_order - Sort order (optional, default: 0)
 * @returns {Promise<Object>} Response with created player group
 */
export const createPlayerGroup = async (playerGroupData) => {
    return await apiClient.post("/player-groups", playerGroupData);
};

/**
 * Update an existing player group
 * @param {number|string} playerGroupId - The player group ID
 * @param {Object} playerGroupData - The player group data to update
 * @returns {Promise<Object>} Response with updated player group
 */
export const updatePlayerGroup = async (playerGroupId, playerGroupData) => {
    return await apiClient.put(`/player-groups/${playerGroupId}`, playerGroupData);
};

/**
 * Delete a player group
 * @param {number|string} playerGroupId - The player group ID
 * @returns {Promise<Object>} Response confirming deletion
 */
export const deletePlayerGroup = async (playerGroupId) => {
    return await apiClient.delete(`/player-groups/${playerGroupId}`);
};
