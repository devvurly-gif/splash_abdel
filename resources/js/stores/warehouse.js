import { defineStore } from 'pinia';
import { ref } from 'vue';

export const useWarehouseStore = defineStore('warehouse', () => {
    // State
    const warehouses = ref([]);
    const loading = ref(false);
    const currentWarehouse = ref(null);
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
    const fetchWarehouses = async (params = {}) => {
        loading.value = true;

        try {
            const queryParams = new URLSearchParams({
                per_page: params.per_page || 10,
                page: params.page || 1,
                ...(params.search && { search: params.search }),
                ...(params.type && { type: params.type }),
                ...(params.isprincipal !== undefined && { isprincipal: params.isprincipal ? 1 : 0 })
            });

            const response = await fetch(`/api/warehouses?${queryParams}`, {
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
                    warehouses.value = responseData.data;
                    pagination.value = {
                        current_page: responseData.current_page || 1,
                        last_page: responseData.last_page || 1,
                        per_page: responseData.per_page || 10,
                        total: responseData.total || 0
                    };
                } else if (Array.isArray(responseData)) {
                    // If it's a plain array
                    warehouses.value = responseData;
                    pagination.value = {
                        current_page: 1,
                        last_page: 1,
                        per_page: responseData.length,
                        total: responseData.length
                    };
                } else {
                    warehouses.value = [];
                }
                
                console.log('Warehouses fetched successfully:', warehouses.value.length, 'items');
                return { success: true, data: responseData };
            } else {
                console.error('Failed to fetch warehouses:', data);
                warehouses.value = [];
                return { success: false, error: data.message || 'Failed to fetch warehouses' };
            }
        } catch (err) {
            console.error('Fetch warehouses error:', err);
            warehouses.value = [];
            return { success: false, error: 'An error occurred while fetching warehouses' };
        } finally {
            loading.value = false;
        }
    };

    const createWarehouse = async (warehouseData) => {
        loading.value = true;

        try {
            // Ensure CSRF cookie is set before making the request
            await ensureCsrfCookie();
            
            const response = await fetch('/api/warehouses', {
                method: 'POST',
                headers: getHeaders(),
                credentials: 'include',
                body: JSON.stringify(warehouseData)
            });

            const data = await response.json();

            if (response.ok && data.success) {
                // Refresh warehouses list
                await fetchWarehouses({ page: pagination.value.current_page });
                return { success: true, data: data.data };
            } else {
                return { success: false, error: data.message || 'Failed to create warehouse', errors: data.errors };
            }
        } catch (err) {
            console.error('Create warehouse error:', err);
            return { success: false, error: 'An error occurred while creating warehouse' };
        } finally {
            loading.value = false;
        }
    };

    const updateWarehouse = async (id, warehouseData) => {
        loading.value = true;

        try {
            // Ensure CSRF cookie is set before making the request
            await ensureCsrfCookie();
            
            const response = await fetch(`/api/warehouses/${id}`, {
                method: 'PUT',
                headers: getHeaders(),
                credentials: 'include',
                body: JSON.stringify(warehouseData)
            });

            const data = await response.json();

            if (response.ok && data.success) {
                // Refresh warehouses list
                await fetchWarehouses({ page: pagination.value.current_page });
                return { success: true, data: data.data };
            } else {
                return { success: false, error: data.message || 'Failed to update warehouse', errors: data.errors };
            }
        } catch (err) {
            console.error('Update warehouse error:', err);
            return { success: false, error: 'An error occurred while updating warehouse' };
        } finally {
            loading.value = false;
        }
    };

    const deleteWarehouse = async (id) => {
        loading.value = true;

        try {
            // Ensure CSRF cookie is set before making the request
            await ensureCsrfCookie();
            
            const response = await fetch(`/api/warehouses/${id}`, {
                method: 'DELETE',
                headers: getHeaders(),
                credentials: 'include'
            });

            const data = await response.json();

            if (response.ok && data.success) {
                // Refresh warehouses list
                await fetchWarehouses({ page: pagination.value.current_page });
                return { success: true };
            } else {
                return { success: false, error: data.message || 'Failed to delete warehouse' };
            }
        } catch (err) {
            console.error('Delete warehouse error:', err);
            return { success: false, error: 'An error occurred while deleting warehouse' };
        } finally {
            loading.value = false;
        }
    };

    const setCurrentWarehouse = (warehouse) => {
        currentWarehouse.value = warehouse ? { ...warehouse } : null;
    };

    const clearCurrentWarehouse = () => {
        currentWarehouse.value = null;
    };

    return {
        // State
        warehouses,
        loading,
        currentWarehouse,
        pagination,
        // Actions
        fetchWarehouses,
        createWarehouse,
        updateWarehouse,
        deleteWarehouse,
        setCurrentWarehouse,
        clearCurrentWarehouse
    };
});

