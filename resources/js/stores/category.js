import { defineStore } from 'pinia';
import { ref } from 'vue';

export const useCategoryStore = defineStore('category', () => {
    // State
    const categories = ref([]);
    const loading = ref(false);
    const currentCategory = ref(null);
    const pagination = ref({
        current_page: 1,
        last_page: 1,
        per_page: 10,
        total: 0
    });

    // Helper function to get CSRF token
    const getCsrfToken = () => {
        return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    };

    // Helper function to ensure CSRF cookie is set
    const ensureCsrfCookie = async () => {
        try {
            await fetch('/sanctum/csrf-cookie', {
                method: 'GET',
                credentials: 'include'
            });
        } catch (err) {
            console.error('Failed to fetch CSRF cookie:', err);
        }
    };

    // Helper function to get headers with CSRF token
    const getHeaders = () => {
        const csrfToken = getCsrfToken();
        if (!csrfToken) {
            console.warn('CSRF token not found in meta tag');
        }
        return {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        };
    };

    // Actions
    const fetchCategories = async (params = {}) => {
        loading.value = true;

        try {
            const queryParams = new URLSearchParams({
                per_page: params.per_page || 10,
                page: params.page || 1,
                ...(params.search && { search: params.search }),
                ...(params.status !== undefined && { status: params.status ? 1 : 0 })
            });

            const response = await fetch(`/api/categories?${queryParams}`, {
                method: 'GET',
                headers: getHeaders(),
                credentials: 'include'
            });

            const data = await response.json();

            if (response.ok && data.success) {
                // Handle paginated response structure
                const responseData = data.data;
                
                // Check if it's a paginated response (has 'data' property)
                if (responseData && Array.isArray(responseData.data)) {
                    categories.value = responseData.data;
                    pagination.value = {
                        current_page: responseData.current_page || 1,
                        last_page: responseData.last_page || 1,
                        per_page: responseData.per_page || 10,
                        total: responseData.total || 0
                    };
                } else if (Array.isArray(responseData)) {
                    // If it's a plain array
                    categories.value = responseData;
                    pagination.value = {
                        current_page: 1,
                        last_page: 1,
                        per_page: responseData.length,
                        total: responseData.length
                    };
                } else {
                    categories.value = [];
                }
                
                console.log('Categories fetched successfully:', categories.value.length, 'items');
                return { success: true, data: responseData };
            } else {
                console.error('Failed to fetch categories:', data);
                categories.value = [];
                return { success: false, error: data.message || 'Failed to fetch categories' };
            }
        } catch (err) {
            console.error('Fetch categories error:', err);
            categories.value = [];
            return { success: false, error: 'An error occurred while fetching categories' };
        } finally {
            loading.value = false;
        }
    };

    const createCategory = async (categoryData) => {
        loading.value = true;

        try {
            // Ensure CSRF cookie is set before making the request
            await ensureCsrfCookie();
            
            const response = await fetch('/api/categories', {
                method: 'POST',
                headers: getHeaders(),
                credentials: 'include',
                body: JSON.stringify(categoryData)
            });

            const data = await response.json();

            if (response.ok && data.success) {
                // Refresh categories list
                await fetchCategories({ page: pagination.value.current_page });
                return { success: true, data: data.data };
            } else {
                return { success: false, error: data.message || 'Failed to create category' };
            }
        } catch (err) {
            console.error('Create category error:', err);
            return { success: false, error: 'An error occurred while creating category' };
        } finally {
            loading.value = false;
        }
    };

    const updateCategory = async (id, categoryData) => {
        loading.value = true;

        try {
            // Ensure CSRF cookie is set before making the request
            await ensureCsrfCookie();
            
            const response = await fetch(`/api/categories/${id}`, {
                method: 'PUT',
                headers: getHeaders(),
                credentials: 'include',
                body: JSON.stringify(categoryData)
            });

            const data = await response.json();

            if (response.ok && data.success) {
                // Refresh categories list
                await fetchCategories({ page: pagination.value.current_page });
                return { success: true, data: data.data };
            } else {
                return { success: false, error: data.message || 'Failed to update category' };
            }
        } catch (err) {
            console.error('Update category error:', err);
            return { success: false, error: 'An error occurred while updating category' };
        } finally {
            loading.value = false;
        }
    };

    const deleteCategory = async (id) => {
        loading.value = true;

        try {
            // Ensure CSRF cookie is set before making the request
            await ensureCsrfCookie();
            
            const response = await fetch(`/api/categories/${id}`, {
                method: 'DELETE',
                headers: getHeaders(),
                credentials: 'include'
            });

            const data = await response.json();

            if (response.ok && data.success) {
                // Refresh categories list
                await fetchCategories({ page: pagination.value.current_page });
                return { success: true };
            } else {
                return { success: false, error: data.message || 'Failed to delete category' };
            }
        } catch (err) {
            console.error('Delete category error:', err);
            return { success: false, error: 'An error occurred while deleting category' };
        } finally {
            loading.value = false;
        }
    };

    const setCurrentCategory = (category) => {
        currentCategory.value = category ? { ...category } : null;
    };

    const clearCurrentCategory = () => {
        currentCategory.value = null;
    };

    return {
        // State
        categories,
        loading,
        currentCategory,
        pagination,
        // Actions
        fetchCategories,
        createCategory,
        updateCategory,
        deleteCategory,
        setCurrentCategory,
        clearCurrentCategory
    };
});

