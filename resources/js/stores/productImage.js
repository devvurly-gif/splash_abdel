import { defineStore } from 'pinia';
import { ref } from 'vue';

export const useProductImageStore = defineStore('productImage', () => {
    // State
    const images = ref([]);
    const loading = ref(false);

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
    const getHeaders = (includeContentType = true) => {
        const csrfToken = getCsrfToken();
        if (!csrfToken) {
            console.warn('CSRF token not found in meta tag');
        }
        const headers = {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        };
        if (includeContentType) {
            headers['Content-Type'] = 'application/json';
        }
        return headers;
    };

    // Actions
    const fetchImages = async (productId) => {
        loading.value = true;

        try {
            const response = await fetch(`/api/products/${productId}/images`, {
                method: 'GET',
                headers: getHeaders(),
                credentials: 'include'
            });

            const data = await response.json();

            if (response.ok && data.success) {
                images.value = data.data || [];
                return { success: true, data: data.data };
            } else {
                console.error('Failed to fetch product images:', data);
                images.value = [];
                return { success: false, error: data.message || 'Failed to fetch product images' };
            }
        } catch (err) {
            console.error('Fetch product images error:', err);
            images.value = [];
            return { success: false, error: 'An error occurred while fetching product images' };
        } finally {
            loading.value = false;
        }
    };

    const createImage = async (productId, imageData, file = null) => {
        loading.value = true;

        try {
            await ensureCsrfCookie();
            
            let body;
            let headers;

            if (file) {
                // Use FormData for file upload
                const formData = new FormData();
                formData.append('image', file);
                if (imageData.title) formData.append('title', imageData.title);
                if (imageData.alt) formData.append('alt', imageData.alt);
                if (imageData.url) formData.append('url', imageData.url);
                if (imageData.isprimary !== undefined) formData.append('isprimary', imageData.isprimary ? '1' : '0');
                if (imageData.order !== undefined) formData.append('order', imageData.order.toString());
                
                body = formData;
                headers = getHeaders(false); // Don't set Content-Type, browser will set it with boundary
            } else {
                // Use JSON for URL only
                body = JSON.stringify(imageData);
                headers = getHeaders(true);
            }
            
            const response = await fetch(`/api/products/${productId}/images`, {
                method: 'POST',
                headers: headers,
                credentials: 'include',
                body: body
            });

            const data = await response.json();

            if (response.ok && data.success) {
                await fetchImages(productId);
                return { success: true, data: data.data };
            } else {
                return { 
                    success: false, 
                    error: data.message || 'Failed to create product image',
                    errors: data.errors || {}
                };
            }
        } catch (err) {
            console.error('Create product image error:', err);
            return { success: false, error: 'An error occurred while creating product image' };
        } finally {
            loading.value = false;
        }
    };

    const updateImage = async (productId, imageId, imageData, file = null) => {
        loading.value = true;

        try {
            await ensureCsrfCookie();
            
            let body;
            let headers;

            if (file) {
                // Use FormData for file upload
                const formData = new FormData();
                formData.append('image', file);
                formData.append('_method', 'PUT'); // Laravel method spoofing
                if (imageData.title) formData.append('title', imageData.title);
                if (imageData.alt) formData.append('alt', imageData.alt);
                if (imageData.url) formData.append('url', imageData.url);
                if (imageData.isprimary !== undefined) formData.append('isprimary', imageData.isprimary ? '1' : '0');
                if (imageData.order !== undefined) formData.append('order', imageData.order.toString());
                
                body = formData;
                headers = getHeaders(false); // Don't set Content-Type, browser will set it with boundary
            } else {
                // Use JSON for URL only
                body = JSON.stringify(imageData);
                headers = getHeaders(true);
            }
            
            const response = await fetch(`/api/products/${productId}/images/${imageId}`, {
                method: 'PUT',
                headers: headers,
                credentials: 'include',
                body: body
            });

            const data = await response.json();

            if (response.ok && data.success) {
                await fetchImages(productId);
                return { success: true, data: data.data };
            } else {
                return { 
                    success: false, 
                    error: data.message || 'Failed to update product image',
                    errors: data.errors || {}
                };
            }
        } catch (err) {
            console.error('Update product image error:', err);
            return { success: false, error: 'An error occurred while updating product image' };
        } finally {
            loading.value = false;
        }
    };

    const deleteImage = async (productId, imageId) => {
        loading.value = true;

        try {
            await ensureCsrfCookie();
            
            const response = await fetch(`/api/products/${productId}/images/${imageId}`, {
                method: 'DELETE',
                headers: getHeaders(),
                credentials: 'include'
            });

            const data = await response.json();

            if (response.ok && data.success) {
                await fetchImages(productId);
                return { success: true };
            } else {
                return { success: false, error: data.message || 'Failed to delete product image' };
            }
        } catch (err) {
            console.error('Delete product image error:', err);
            return { success: false, error: 'An error occurred while deleting product image' };
        } finally {
            loading.value = false;
        }
    };

    const setPrimaryImage = async (productId, imageId) => {
        return await updateImage(productId, imageId, { isprimary: true });
    };

    const uploadMultipleImages = async (productId, files, primaryIndex = null) => {
        loading.value = true;

        try {
            await ensureCsrfCookie();
            
            const formData = new FormData();
            files.forEach((file, index) => {
                formData.append(`images[${index}]`, file);
            });
            if (primaryIndex !== null) {
                formData.append('primary_index', primaryIndex.toString());
            }
            
            const response = await fetch(`/api/products/${productId}/images/upload-multiple`, {
                method: 'POST',
                headers: getHeaders(false),
                credentials: 'include',
                body: formData
            });

            const data = await response.json();

            if (response.ok && data.success) {
                await fetchImages(productId);
                return { success: true, data: data.data };
            } else {
                return { 
                    success: false, 
                    error: data.message || 'Failed to upload images',
                    errors: data.errors || {}
                };
            }
        } catch (err) {
            console.error('Upload multiple images error:', err);
            return { success: false, error: 'An error occurred while uploading images' };
        } finally {
            loading.value = false;
        }
    };

    return {
        // State
        images,
        loading,
        // Actions
        fetchImages,
        createImage,
        updateImage,
        deleteImage,
        setPrimaryImage,
        uploadMultipleImages
    };
});

