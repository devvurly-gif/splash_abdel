import { defineStore } from 'pinia';
import { ref } from 'vue';

export const usePartnerStore = defineStore('partner', () => {
    // State
    const partners = ref([]);
    const loading = ref(false);
    const currentPartner = ref(null);
    const pagination = ref({
        current_page: 1,
        last_page: 1,
        per_page: 15,
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
    const fetchPartners = async (params = {}) => {
        loading.value = true;

        try {
            const queryParams = new URLSearchParams({
                per_page: params.per_page || 15,
                page: params.page || 1,
                ...(params.search && { search: params.search }),
                ...(params.type && { type: params.type }),
                ...(params.status !== undefined && { status: params.status ? 1 : 0 })
            });

            const response = await fetch(`/api/partners?${queryParams}`, {
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
                    partners.value = responseData.data;
                    pagination.value = {
                        current_page: responseData.current_page || 1,
                        last_page: responseData.last_page || 1,
                        per_page: responseData.per_page || 15,
                        total: responseData.total || 0
                    };
                } else if (Array.isArray(responseData)) {
                    // If it's a plain array
                    partners.value = responseData;
                    pagination.value = {
                        current_page: 1,
                        last_page: 1,
                        per_page: responseData.length,
                        total: responseData.length
                    };
                } else {
                    partners.value = [];
                }
                
                console.log('Partners fetched successfully:', partners.value.length, 'items');
                return { success: true, data: responseData };
            } else {
                console.error('Failed to fetch partners:', data);
                partners.value = [];
                return { success: false, error: data.message || 'Failed to fetch partners' };
            }
        } catch (err) {
            console.error('Fetch partners error:', err);
            partners.value = [];
            return { success: false, error: 'An error occurred while fetching partners' };
        } finally {
            loading.value = false;
        }
    };

    const createPartner = async (partnerData) => {
        loading.value = true;

        try {
            // Ensure CSRF cookie is set before making the request
            await ensureCsrfCookie();
            
            const response = await fetch('/api/partners', {
                method: 'POST',
                headers: getHeaders(),
                credentials: 'include',
                body: JSON.stringify(partnerData)
            });

            const data = await response.json();

            if (response.ok && data.success) {
                // Refresh partners list
                await fetchPartners({ page: pagination.value.current_page });
                return { success: true, data: data.data };
            } else {
                return { success: false, error: data.message || 'Failed to create partner' };
            }
        } catch (err) {
            console.error('Create partner error:', err);
            return { success: false, error: 'An error occurred while creating partner' };
        } finally {
            loading.value = false;
        }
    };

    const updatePartner = async (id, partnerData) => {
        loading.value = true;

        try {
            // Ensure CSRF cookie is set before making the request
            await ensureCsrfCookie();
            
            const response = await fetch(`/api/partners/${id}`, {
                method: 'PUT',
                headers: getHeaders(),
                credentials: 'include',
                body: JSON.stringify(partnerData)
            });

            const data = await response.json();

            if (response.ok && data.success) {
                // Refresh partners list
                await fetchPartners({ page: pagination.value.current_page });
                return { success: true, data: data.data };
            } else {
                return { success: false, error: data.message || 'Failed to update partner' };
            }
        } catch (err) {
            console.error('Update partner error:', err);
            return { success: false, error: 'An error occurred while updating partner' };
        } finally {
            loading.value = false;
        }
    };

    const deletePartner = async (id) => {
        loading.value = true;

        try {
            // Ensure CSRF cookie is set before making the request
            await ensureCsrfCookie();
            
            const response = await fetch(`/api/partners/${id}`, {
                method: 'DELETE',
                headers: getHeaders(),
                credentials: 'include'
            });

            const data = await response.json();

            if (response.ok && data.success) {
                // Refresh partners list
                await fetchPartners({ page: pagination.value.current_page });
                return { success: true };
            } else {
                return { success: false, error: data.message || 'Failed to delete partner' };
            }
        } catch (err) {
            console.error('Delete partner error:', err);
            return { success: false, error: 'An error occurred while deleting partner' };
        } finally {
            loading.value = false;
        }
    };

    const setCurrentPartner = (partner) => {
        currentPartner.value = partner ? { ...partner } : null;
    };

    const clearCurrentPartner = () => {
        currentPartner.value = null;
    };

    return {
        // State
        partners,
        loading,
        currentPartner,
        pagination,
        // Actions
        fetchPartners,
        createPartner,
        updatePartner,
        deletePartner,
        setCurrentPartner,
        clearCurrentPartner
    };
});

