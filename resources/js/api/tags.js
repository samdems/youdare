import apiClient from "./client";

/**
 * Tags API
 * Handles all tag-related API requests
 */

/**
 * Get all available tags
 * @returns {Promise<Object>} Response with tags data
 */
export const getTags = async () => {
    return await apiClient.get("/tags");
};

/**
 * Get a specific tag by ID
 * @param {number|string} tagId - The tag ID
 * @returns {Promise<Object>} Response with tag data
 */
export const getTag = async (tagId) => {
    return await apiClient.get(`/tags/${tagId}`);
};

/**
 * Create a new tag
 * @param {Object} tagData - The tag data
 * @param {string} tagData.name - Tag name
 * @param {string} tagData.description - Tag description
 * @param {number} tagData.max_spice_rating - Maximum spice rating
 * @returns {Promise<Object>} Response with created tag
 */
export const createTag = async (tagData) => {
    return await apiClient.post("/tags", tagData);
};

/**
 * Update an existing tag
 * @param {number|string} tagId - The tag ID
 * @param {Object} tagData - The tag data to update
 * @returns {Promise<Object>} Response with updated tag
 */
export const updateTag = async (tagId, tagData) => {
    return await apiClient.put(`/tags/${tagId}`, tagData);
};

/**
 * Delete a tag
 * @param {number|string} tagId - The tag ID
 * @returns {Promise<Object>} Response confirming deletion
 */
export const deleteTag = async (tagId) => {
    return await apiClient.delete(`/tags/${tagId}`);
};

/**
 * Get tags filtered by spice rating
 * @param {number} maxSpiceRating - Maximum spice rating to filter by
 * @returns {Promise<Object>} Response with filtered tags
 */
export const getTagsBySpiceRating = async (maxSpiceRating) => {
    return await apiClient.get("/tags", {
        params: { max_spice_rating: maxSpiceRating },
    });
};

/**
 * Get tags grouped by tag groups
 * @param {number} [minSpiceLevel] - Optional minimum spice level filter
 * @returns {Promise<Object>} Response with tags organized by groups
 */
export const getGroupedTags = async (minSpiceLevel = null) => {
    const params = {};
    if (minSpiceLevel !== null) {
        params.min_spice_level = minSpiceLevel;
    }
    return await apiClient.get("/tags/grouped", { params });
};
