import apiClient from './client';

/**
 * Tasks API
 * Handles all task-related API requests
 */

/**
 * Get all tasks
 * @param {Object} [filters] - Optional filters
 * @param {string} [filters.type] - Task type ('truth' or 'dare')
 * @param {number} [filters.spice_rating] - Spice rating filter
 * @param {Array<number>} [filters.tag_ids] - Array of tag IDs to filter by
 * @returns {Promise<Object>} Response with tasks data
 */
export const getTasks = async (filters = {}) => {
    return await apiClient.get('/tasks', { params: filters });
};

/**
 * Get a specific task by ID
 * @param {number|string} taskId - The task ID
 * @returns {Promise<Object>} Response with task data
 */
export const getTask = async (taskId) => {
    return await apiClient.get(`/tasks/${taskId}`);
};

/**
 * Create a new task
 * @param {Object} taskData - The task data
 * @param {string} taskData.description - Task description
 * @param {string} taskData.type - Task type ('truth' or 'dare')
 * @param {number} taskData.spice_rating - Spice rating (1-5)
 * @param {Array<number>} [taskData.tag_ids] - Optional array of tag IDs
 * @param {Array<number>} [taskData.required_tag_ids] - Optional array of required tag IDs
 * @returns {Promise<Object>} Response with created task
 */
export const createTask = async (taskData) => {
    return await apiClient.post('/tasks', taskData);
};

/**
 * Update an existing task
 * @param {number|string} taskId - The task ID
 * @param {Object} taskData - The task data to update
 * @returns {Promise<Object>} Response with updated task
 */
export const updateTask = async (taskId, taskData) => {
    return await apiClient.put(`/tasks/${taskId}`, taskData);
};

/**
 * Delete a task
 * @param {number|string} taskId - The task ID
 * @returns {Promise<Object>} Response confirming deletion
 */
export const deleteTask = async (taskId) => {
    return await apiClient.delete(`/tasks/${taskId}`);
};

/**
 * Get random task
 * @param {Object} [options] - Options for random task selection
 * @param {string} [options.type] - Task type ('truth', 'dare', or 'both')
 * @param {number} [options.max_spice_rating] - Maximum spice rating
 * @param {Array<number>} [options.tag_ids] - Player's tag IDs for matching
 * @returns {Promise<Object>} Response with random task
 */
export const getRandomTask = async (options = {}) => {
    return await apiClient.get('/tasks/random', { params: options });
};

/**
 * Get tasks by type
 * @param {string} type - Task type ('truth' or 'dare')
 * @returns {Promise<Object>} Response with filtered tasks
 */
export const getTasksByType = async (type) => {
    return await apiClient.get('/tasks', {
        params: { type }
    });
};

/**
 * Get tasks by spice rating
 * @param {number} spiceRating - Spice rating (1-5)
 * @returns {Promise<Object>} Response with filtered tasks
 */
export const getTasksBySpiceRating = async (spiceRating) => {
    return await apiClient.get('/tasks', {
        params: { spice_rating: spiceRating }
    });
};

/**
 * Get tasks by tags
 * @param {Array<number>} tagIds - Array of tag IDs
 * @returns {Promise<Object>} Response with filtered tasks
 */
export const getTasksByTags = async (tagIds) => {
    return await apiClient.get('/tasks', {
        params: { tag_ids: tagIds }
    });
};

/**
 * Bulk import tasks
 * @param {Array<Object>} tasks - Array of task objects to import
 * @returns {Promise<Object>} Response with import results
 */
export const bulkImportTasks = async (tasks) => {
    return await apiClient.post('/tasks/bulk-import', { tasks });
};

/**
 * Mark task as used for a player
 * @param {number|string} taskId - The task ID
 * @param {number|string} playerId - The player ID
 * @returns {Promise<Object>} Response confirming task was marked as used
 */
export const markTaskAsUsed = async (taskId, playerId) => {
    return await apiClient.post(`/tasks/${taskId}/used`, { player_id: playerId });
};

/**
 * Get task statistics
 * @param {number|string} taskId - The task ID
 * @returns {Promise<Object>} Response with task statistics
 */
export const getTaskStats = async (taskId) => {
    return await apiClient.get(`/tasks/${taskId}/stats`);
};
