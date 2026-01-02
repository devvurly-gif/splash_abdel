import { defineStore } from 'pinia';
import { ref } from 'vue';

export const useNumberingSystemStore = defineStore('numberingSystem', () => {
    // State
    const numberingSystems = ref([]);
    const loading = ref(false);
    const currentNumberingSystem = ref(null);
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
    const fetchNumberingSystems = async (params = {}) => {
        loading.value = true;

        try {
            const queryParams = new URLSearchParams({
                per_page: params.per_page || 10,
                page: params.page || 1,
                ...(params.search && { search: params.search }),
                ...(params.domain && { domain: params.domain }),
                ...(params.type && { type: params.type }),
                ...(params.isActive !== undefined && { isActive: params.isActive ? 1 : 0 })
            });

            const response = await fetch(`/api/numbering-systems?${queryParams}`, {
                method: 'GET',
                headers: getHeaders(),
                credentials: 'include'
            });

            const data = await response.json();

            if (response.ok && data.success) {
                const responseData = data.data;
                
                // Check if it's a paginated response (has 'data' property)
                if (responseData && Array.isArray(responseData.data)) {
                    numberingSystems.value = responseData.data;
                    pagination.value = {
                        current_page: responseData.current_page || 1,
                        last_page: responseData.last_page || 1,
                        per_page: responseData.per_page || 10,
                        total: responseData.total || 0
                    };
                } else if (Array.isArray(responseData)) {
                    // If it's a plain array
                    numberingSystems.value = responseData;
                    pagination.value = {
                        current_page: 1,
                        last_page: 1,
                        per_page: responseData.length,
                        total: responseData.length
                    };
                } else {
                    numberingSystems.value = [];
                }
                
                console.log('Numbering systems fetched successfully:', numberingSystems.value.length, 'items');
                return { success: true, data: responseData };
            } else {
                console.error('Failed to fetch numbering systems:', data);
                numberingSystems.value = [];
                return { success: false, error: data.message || 'Failed to fetch numbering systems' };
            }
        } catch (err) {
            console.error('Fetch numbering systems error:', err);
            numberingSystems.value = [];
            return { success: false, error: 'An error occurred while fetching numbering systems' };
        } finally {
            loading.value = false;
        }
    };

    const fetchNumberingSystem = async (id) => {
        loading.value = true;

        try {
            const response = await fetch(`/api/numbering-systems/${id}`, {
                method: 'GET',
                headers: getHeaders(),
                credentials: 'include'
            });

            const data = await response.json();

            if (response.ok && data.success) {
                return { success: true, data: data.data };
            } else {
                return { success: false, error: data.message || 'Failed to fetch numbering system' };
            }
        } catch (err) {
            console.error('Fetch numbering system error:', err);
            return { success: false, error: 'An error occurred while fetching numbering system' };
        } finally {
            loading.value = false;
        }
    };

    const createNumberingSystem = async (numberingSystemData) => {
        loading.value = true;

        try {
            // Ensure CSRF cookie is set before making the request
            await ensureCsrfCookie();
            
            const response = await fetch('/api/numbering-systems', {
                method: 'POST',
                headers: getHeaders(),
                credentials: 'include',
                body: JSON.stringify(numberingSystemData)
            });

            const data = await response.json();

            if (response.ok && data.success) {
                // Refresh numbering systems list
                await fetchNumberingSystems({ page: pagination.value.current_page });
                return { success: true, data: data.data };
            } else {
                return { 
                    success: false, 
                    error: data.message || 'Failed to create numbering system',
                    errors: data.errors || {}
                };
            }
        } catch (err) {
            console.error('Create numbering system error:', err);
            return { success: false, error: 'An error occurred while creating numbering system' };
        } finally {
            loading.value = false;
        }
    };

    const updateNumberingSystem = async (id, numberingSystemData) => {
        loading.value = true;

        try {
            // Ensure CSRF cookie is set before making the request
            await ensureCsrfCookie();
            
            const response = await fetch(`/api/numbering-systems/${id}`, {
                method: 'PUT',
                headers: getHeaders(),
                credentials: 'include',
                body: JSON.stringify(numberingSystemData)
            });

            const data = await response.json();

            if (response.ok && data.success) {
                // Refresh numbering systems list
                await fetchNumberingSystems({ page: pagination.value.current_page });
                return { success: true, data: data.data };
            } else {
                return { 
                    success: false, 
                    error: data.message || 'Failed to update numbering system',
                    errors: data.errors || {}
                };
            }
        } catch (err) {
            console.error('Update numbering system error:', err);
            return { success: false, error: 'An error occurred while updating numbering system' };
        } finally {
            loading.value = false;
        }
    };

    const deleteNumberingSystem = async (id) => {
        loading.value = true;

        try {
            // Ensure CSRF cookie is set before making the request
            await ensureCsrfCookie();
            
            const response = await fetch(`/api/numbering-systems/${id}`, {
                method: 'DELETE',
                headers: getHeaders(),
                credentials: 'include'
            });

            const data = await response.json();

            if (response.ok && data.success) {
                // Refresh numbering systems list
                await fetchNumberingSystems({ page: pagination.value.current_page });
                return { success: true };
            } else {
                return { success: false, error: data.message || 'Failed to delete numbering system' };
            }
        } catch (err) {
            console.error('Delete numbering system error:', err);
            return { success: false, error: 'An error occurred while deleting numbering system' };
        } finally {
            loading.value = false;
        }
    };

    const generateNext = async (id) => {
        loading.value = true;

        try {
            await ensureCsrfCookie();
            
            const response = await fetch(`/api/numbering-systems/${id}/generate-next`, {
                method: 'POST',
                headers: getHeaders(),
                credentials: 'include'
            });

            const data = await response.json();

            if (response.ok && data.success) {
                // Refresh the numbering system to get updated next_trick
                await fetchNumberingSystems({ page: pagination.value.current_page });
                return { success: true, data: data.data };
            } else {
                return { success: false, error: data.message || 'Failed to generate next number' };
            }
        } catch (err) {
            console.error('Generate next number error:', err);
            return { success: false, error: 'An error occurred while generating next number' };
        } finally {
            loading.value = false;
        }
    };

    const generateByDomainAndType = async (domain, type) => {
        loading.value = true;

        try {
            await ensureCsrfCookie();
            
            const response = await fetch('/api/numbering-systems/generate', {
                method: 'POST',
                headers: getHeaders(),
                credentials: 'include',
                body: JSON.stringify({ domain, type })
            });

            const data = await response.json();

            if (response.ok && data.success) {
                // Refresh numbering systems list to get updated next_trick
                await fetchNumberingSystems({ page: pagination.value.current_page });
                return { success: true, data: data.data };
            } else {
                return { success: false, error: data.message || 'Failed to generate number' };
            }
        } catch (err) {
            console.error('Generate by domain and type error:', err);
            return { success: false, error: 'An error occurred while generating number' };
        } finally {
            loading.value = false;
        }
    };

    const setCurrentNumberingSystem = (numberingSystem) => {
        currentNumberingSystem.value = numberingSystem ? { ...numberingSystem } : null;
    };

    const clearCurrentNumberingSystem = () => {
        currentNumberingSystem.value = null;
    };

    return {
        // State
        numberingSystems,
        loading,
        currentNumberingSystem,
        pagination,
        // Actions
        fetchNumberingSystems,
        fetchNumberingSystem,
        createNumberingSystem,
        updateNumberingSystem,
        deleteNumberingSystem,
        generateNext,
        generateByDomainAndType,
        setCurrentNumberingSystem,
        clearCurrentNumberingSystem
    };
});

