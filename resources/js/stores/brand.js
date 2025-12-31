import { defineStore } from 'pinia';
import { ref } from 'vue';

export const useBrandStore = defineStore('brand', () => {
    // State
    const brands = ref([]);
    const loading = ref(false);
    const currentBrand = ref(null);
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
    const fetchBrands = async (params = {}) => {
        loading.value = true;

        try {
            const queryParams = new URLSearchParams({
                per_page: params.per_page || 15,
                page: params.page || 1,
                ...(params.search && { search: params.search }),
                ...(params.status !== undefined && params.status !== '' && { status: params.status ? 1 : 0 })
            });

            const response = await fetch(`/api/brands?${queryParams}`, {
                method: 'GET',
                headers: getHeaders(),
                credentials: 'include'
            });

            const data = await response.json();

            if (response.ok && data.success) {
                const responseData = data.data;
                
                if (responseData && Array.isArray(responseData.data)) {
                    brands.value = responseData.data;
                    pagination.value = {
                        current_page: responseData.current_page || 1,
                        last_page: responseData.last_page || 1,
                        per_page: responseData.per_page || 15,
                        total: responseData.total || 0
                    };
                } else if (Array.isArray(responseData)) {
                    brands.value = responseData;
                    pagination.value = {
                        current_page: 1,
                        last_page: 1,
                        per_page: responseData.length,
                        total: responseData.length
                    };
                } else {
                    brands.value = [];
                    pagination.value = { current_page: 1, last_page: 1, per_page: 15, total: 0 };
                }
                
                console.log('Brands fetched successfully:', brands.value.length, 'items');
                return { success: true, data: responseData };
            } else {
                console.error('Failed to fetch brands:', data);
                brands.value = [];
                pagination.value = { current_page: 1, last_page: 1, per_page: 15, total: 0 };
                return { success: false, error: data.message || 'Failed to fetch brands' };
            }
        } catch (err) {
            console.error('Fetch brands error:', err);
            brands.value = [];
            pagination.value = { current_page: 1, last_page: 1, per_page: 15, total: 0 };
            return { success: false, error: 'An error occurred while fetching brands' };
        } finally {
            loading.value = false;
        }
    };

    const createBrand = async (brandData) => {
        loading.value = true;

        try {
            await ensureCsrfCookie();
            
            const response = await fetch('/api/brands', {
                method: 'POST',
                headers: getHeaders(),
                credentials: 'include',
                body: JSON.stringify(brandData)
            });

            const data = await response.json();

            if (response.ok && data.success) {
                await fetchBrands({ page: pagination.value.current_page });
                return { success: true, data: data.data };
            } else {
                console.error('Failed to create brand:', data);
                return { success: false, error: data.message || 'Failed to create brand' };
            }
        } catch (err) {
            console.error('Create brand error:', err);
            return { success: false, error: 'An error occurred while creating brand' };
        } finally {
            loading.value = false;
        }
    };

    const updateBrand = async (id, brandData) => {
        loading.value = true;

        try {
            await ensureCsrfCookie();
            
            const response = await fetch(`/api/brands/${id}`, {
                method: 'PUT',
                headers: getHeaders(),
                credentials: 'include',
                body: JSON.stringify(brandData)
            });

            const data = await response.json();

            if (response.ok && data.success) {
                await fetchBrands({ page: pagination.value.current_page });
                return { success: true, data: data.data };
            } else {
                console.error('Failed to update brand:', data);
                return { success: false, error: data.message || 'Failed to update brand' };
            }
        } catch (err) {
            console.error('Update brand error:', err);
            return { success: false, error: 'An error occurred while updating brand' };
        } finally {
            loading.value = false;
        }
    };

    const deleteBrand = async (id) => {
        loading.value = true;

        try {
            await ensureCsrfCookie();
            
            const response = await fetch(`/api/brands/${id}`, {
                method: 'DELETE',
                headers: getHeaders(),
                credentials: 'include'
            });

            const data = await response.json();

            if (response.ok && data.success) {
                await fetchBrands({ page: pagination.value.current_page });
                return { success: true };
            } else {
                console.error('Failed to delete brand:', data);
                return { success: false, error: data.message || 'Failed to delete brand' };
            }
        } catch (err) {
            console.error('Delete brand error:', err);
            return { success: false, error: 'An error occurred while deleting brand' };
        } finally {
            loading.value = false;
        }
    };

    const setCurrentBrand = (brand) => {
        currentBrand.value = brand ? { ...brand } : null;
    };

    const clearCurrentBrand = () => {
        currentBrand.value = null;
    };

    return {
        brands,
        loading,
        currentBrand,
        pagination,
        fetchBrands,
        createBrand,
        updateBrand,
        deleteBrand,
        setCurrentBrand,
        clearCurrentBrand
    };
});

