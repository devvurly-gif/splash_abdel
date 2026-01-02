import { defineStore } from 'pinia';
import { ref } from 'vue';

export const useProductStore = defineStore('product', () => {
    // State
    const products = ref([]);
    const loading = ref(false);
    const currentProduct = ref(null);
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
    const fetchProducts = async (params = {}) => {
        loading.value = true;

        try {
            const queryParams = new URLSearchParams({
                per_page: params.per_page || 10,
                page: params.page || 1,
                ...(params.search && { search: params.search }),
                ...(params.isactive !== undefined && { isactive: params.isactive ? 1 : 0 }),
                ...(params.category_id && { category_id: params.category_id }),
                ...(params.brand_id && { brand_id: params.brand_id })
            });

            const response = await fetch(`/api/products?${queryParams}`, {
                method: 'GET',
                headers: getHeaders(),
                credentials: 'include'
            });

            const data = await response.json();

            if (response.ok && data.success) {
                const responseData = data.data;
                
                if (responseData && Array.isArray(responseData.data)) {
                    products.value = responseData.data;
                    pagination.value = {
                        current_page: responseData.current_page || 1,
                        last_page: responseData.last_page || 1,
                        per_page: responseData.per_page || 10,
                        total: responseData.total || 0
                    };
                } else if (Array.isArray(responseData)) {
                    products.value = responseData;
                    pagination.value = {
                        current_page: 1,
                        last_page: 1,
                        per_page: responseData.length,
                        total: responseData.length
                    };
                } else {
                    products.value = [];
                }
                
                return { success: true, data: responseData };
            } else {
                console.error('Failed to fetch products:', data);
                products.value = [];
                return { success: false, error: data.message || 'Failed to fetch products' };
            }
        } catch (err) {
            console.error('Fetch products error:', err);
            products.value = [];
            return { success: false, error: 'An error occurred while fetching products' };
        } finally {
            loading.value = false;
        }
    };

    const fetchProduct = async (id) => {
        loading.value = true;

        try {
            const response = await fetch(`/api/products/${id}`, {
                method: 'GET',
                headers: getHeaders(),
                credentials: 'include'
            });

            const data = await response.json();

            if (response.ok && data.success) {
                return { success: true, data: data.data };
            } else {
                return { success: false, error: data.message || 'Failed to fetch product' };
            }
        } catch (err) {
            console.error('Fetch product error:', err);
            return { success: false, error: 'An error occurred while fetching product' };
        } finally {
            loading.value = false;
        }
    };

    const createProduct = async (productData) => {
        loading.value = true;

        try {
            await ensureCsrfCookie();
            
            const response = await fetch('/api/products', {
                method: 'POST',
                headers: getHeaders(),
                credentials: 'include',
                body: JSON.stringify(productData)
            });

            const data = await response.json();

            if (response.ok && data.success) {
                await fetchProducts({ page: pagination.value.current_page });
                return { success: true, data: data.data };
            } else {
                return { 
                    success: false, 
                    error: data.message || 'Failed to create product',
                    errors: data.errors || {}
                };
            }
        } catch (err) {
            console.error('Create product error:', err);
            return { success: false, error: 'An error occurred while creating product' };
        } finally {
            loading.value = false;
        }
    };

    const updateProduct = async (id, productData) => {
        loading.value = true;

        try {
            await ensureCsrfCookie();
            
            const response = await fetch(`/api/products/${id}`, {
                method: 'PUT',
                headers: getHeaders(),
                credentials: 'include',
                body: JSON.stringify(productData)
            });

            const data = await response.json();

            if (response.ok && data.success) {
                await fetchProducts({ page: pagination.value.current_page });
                return { success: true, data: data.data };
            } else {
                return { 
                    success: false, 
                    error: data.message || 'Failed to update product',
                    errors: data.errors || {}
                };
            }
        } catch (err) {
            console.error('Update product error:', err);
            return { success: false, error: 'An error occurred while updating product' };
        } finally {
            loading.value = false;
        }
    };

    const deleteProduct = async (id) => {
        loading.value = true;

        try {
            await ensureCsrfCookie();
            
            const response = await fetch(`/api/products/${id}`, {
                method: 'DELETE',
                headers: getHeaders(),
                credentials: 'include'
            });

            const data = await response.json();

            if (response.ok && data.success) {
                await fetchProducts({ page: pagination.value.current_page });
                return { success: true };
            } else {
                return { success: false, error: data.message || 'Failed to delete product' };
            }
        } catch (err) {
            console.error('Delete product error:', err);
            return { success: false, error: 'An error occurred while deleting product' };
        } finally {
            loading.value = false;
        }
    };

    const setCurrentProduct = (product) => {
        currentProduct.value = product ? { ...product } : null;
    };

    const clearCurrentProduct = () => {
        currentProduct.value = null;
    };

    return {
        // State
        products,
        loading,
        currentProduct,
        pagination,
        // Actions
        fetchProducts,
        fetchProduct,
        createProduct,
        updateProduct,
        deleteProduct,
        setCurrentProduct,
        clearCurrentProduct
    };
});

