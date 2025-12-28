import apiClient from './client';

/**
 * Games API
 * Handles all game-related API requests
 */

/**
 * Get all games
 * @returns {Promise<Object>} Response with games data
 */
export const getGames = async () => {
    return await apiClient.get('/games');
};

/**
 * Get a specific game by ID
 * @param {number|string} gameId - The game ID
 * @returns {Promise<Object>} Response with game data
 */
export const getGame = async (gameId) => {
    return await apiClient.get(`/games/${gameId}`);
};

/**
 * Create a new game
 * @param {Object} gameData - The game data
 * @param {string} gameData.name - Game name
 * @param {number} gameData.max_spice_rating - Maximum spice rating (1-5)
 * @returns {Promise<Object>} Response with created game
 */
export const createGame = async (gameData) => {
    return await apiClient.post('/games', gameData);
};

/**
 * Update an existing game
 * @param {number|string} gameId - The game ID
 * @param {Object} gameData - The game data to update
 * @returns {Promise<Object>} Response with updated game
 */
export const updateGame = async (gameId, gameData) => {
    return await apiClient.put(`/games/${gameId}`, gameData);
};

/**
 * Delete a game
 * @param {number|string} gameId - The game ID
 * @returns {Promise<Object>} Response confirming deletion
 */
export const deleteGame = async (gameId) => {
    return await apiClient.delete(`/games/${gameId}`);
};

/**
 * Start a game
 * @param {number|string} gameId - The game ID
 * @returns {Promise<Object>} Response with started game data
 */
export const startGame = async (gameId) => {
    return await apiClient.post(`/games/${gameId}/start`);
};

/**
 * End a game
 * @param {number|string} gameId - The game ID
 * @returns {Promise<Object>} Response with game results
 */
export const endGame = async (gameId) => {
    return await apiClient.post(`/games/${gameId}/end`);
};

/**
 * Get players for a specific game
 * @param {number|string} gameId - The game ID
 * @returns {Promise<Object>} Response with players data
 */
export const getGamePlayers = async (gameId) => {
    return await apiClient.get(`/games/${gameId}/players`);
};

/**
 * Add a player to a game
 * @param {number|string} gameId - The game ID
 * @param {Object} playerData - The player data
 * @param {string} playerData.name - Player name
 * @param {string} playerData.gender - Player gender ('male', 'female', 'other')
 * @param {Array<number>} playerData.tag_ids - Array of tag IDs
 * @returns {Promise<Object>} Response with created player
 */
export const addPlayerToGame = async (gameId, playerData) => {
    return await apiClient.post(`/games/${gameId}/players`, playerData);
};

/**
 * Get next task for a game
 * @param {number|string} gameId - The game ID
 * @param {string} [type] - Optional task type filter
 * @returns {Promise<Object>} Response with next task
 */
export const getNextTask = async (gameId, type = null) => {
    const params = type ? { type } : {};
    return await apiClient.get(`/games/${gameId}/tasks/next`, { params });
};

/**
 * Complete a task
 * @param {number|string} gameId - The game ID
 * @param {number|string} taskId - The task ID
 * @param {Object} completionData - Task completion data
 * @param {number|string} completionData.player_id - Player ID who completed the task
 * @returns {Promise<Object>} Response with completion confirmation
 */
export const completeTask = async (gameId, taskId, completionData) => {
    return await apiClient.post(`/games/${gameId}/tasks/${taskId}/complete`, completionData);
};
