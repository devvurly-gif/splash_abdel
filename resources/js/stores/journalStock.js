import { defineStore } from 'pinia';
import { ref } from 'vue';

export const useJournalStockStore = defineStore('journalStock', () => {
    // State
    const movements = ref([]);
    const loading = ref(false);
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
    const fetchMovements = async (params = {}) => {
        loading.value = true;

        try {
            const queryParams = new URLSearchParams({
                per_page: params.per_page || 15,
                page: params.page || 1,
                ...(params.search && { search: params.search }),
                ...(params.warehouse_id && { warehouse_id: params.warehouse_id }),
                ...(params.product_id && { product_id: params.product_id }),
                ...(params.movement_type && { movement_type: params.movement_type }),
                ...(params.entry_type && { entry_type: params.entry_type }),
                ...(params.date_from && { date_from: params.date_from }),
                ...(params.date_to && { date_to: params.date_to })
            });

            const response = await fetch(`/api/journal-stock?${queryParams}`, {
                method: 'GET',
                headers: getHeaders(),
                credentials: 'include'
            });

            const data = await response.json();

            if (response.ok && data.success) {
                const responseData = data.data;
                
                if (responseData && Array.isArray(responseData.data)) {
                    movements.value = responseData.data;
                    pagination.value = {
                        current_page: responseData.current_page || 1,
                        last_page: responseData.last_page || 1,
                        per_page: responseData.per_page || 15,
                        total: responseData.total || 0
                    };
                } else if (Array.isArray(responseData)) {
                    movements.value = responseData;
                    pagination.value = {
                        current_page: 1,
                        last_page: 1,
                        per_page: responseData.length,
                        total: responseData.length
                    };
                } else {
                    movements.value = [];
                }
                
                console.log('Stock movements fetched successfully:', movements.value.length, 'items');
                return { success: true, data: responseData };
            } else {
                console.error('Failed to fetch stock movements:', data);
                movements.value = [];
                return { success: false, error: data.message || 'Failed to fetch stock movements' };
            }
        } catch (err) {
            console.error('Fetch stock movements error:', err);
            movements.value = [];
            return { success: false, error: 'An error occurred while fetching stock movements' };
        } finally {
            loading.value = false;
        }
    };

    const getHistory = async (warehouseId, productId, dateFrom = null, dateTo = null) => {
        loading.value = true;

        try {
            const queryParams = new URLSearchParams({
                warehouse_id: warehouseId,
                product_id: productId,
                ...(dateFrom && { date_from: dateFrom }),
                ...(dateTo && { date_to: dateTo })
            });

            const response = await fetch(`/api/journal-stock/history?${queryParams}`, {
                method: 'GET',
                headers: getHeaders(),
                credentials: 'include'
            });

            const data = await response.json();

            if (response.ok && data.success) {
                return { success: true, data: data.data };
            } else {
                return { success: false, error: data.message || 'Failed to fetch stock history' };
            }
        } catch (err) {
            console.error('Get stock history error:', err);
            return { success: false, error: 'An error occurred while fetching stock history' };
        } finally {
            loading.value = false;
        }
    };

    return {
        // State
        movements,
        loading,
        pagination,
        // Actions
        fetchMovements,
        getHistory
    };
});

