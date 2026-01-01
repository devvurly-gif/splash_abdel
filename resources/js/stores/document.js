import { defineStore } from 'pinia';
import { ref } from 'vue';

export const useDocumentStore = defineStore('document', () => {
    // State
    const documents = ref([]);
    const loading = ref(false);
    const currentDocument = ref(null);
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
            const response = await fetch('/sanctum/csrf-cookie', {
                method: 'GET',
                credentials: 'include',
                headers: {
                    'Accept': 'application/json'
                }
            });
            
            if (!response.ok) {
                console.warn('CSRF cookie fetch returned non-OK status:', response.status);
            }
            
            // Small delay to ensure cookie is set
            await new Promise(resolve => setTimeout(resolve, 100));
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
            'X-CSRF-TOKEN': csrfToken || '',
            'X-Requested-With': 'XMLHttpRequest'
        };
    };

    // Actions
    const fetchDocuments = async (params = {}) => {
        loading.value = true;

        try {
            const queryParams = new URLSearchParams({
                per_page: params.per_page || 15,
                page: params.page || 1,
                ...(params.search && { search: params.search }),
                ...(params.domain && { domain: params.domain }),
                ...(params.type && { type: params.type }),
                ...(params.status && { status: params.status }),
                ...(params.warehouse_id && { warehouse_id: params.warehouse_id }),
                ...(params.partner_id && { partner_id: params.partner_id }),
                ...(params.date_from && { date_from: params.date_from }),
                ...(params.date_to && { date_to: params.date_to })
            });

            const response = await fetch(`/api/documents?${queryParams}`, {
                method: 'GET',
                headers: getHeaders(),
                credentials: 'include'
            });

            const data = await response.json();

            if (response.ok && data.success) {
                const responseData = data.data;
                
                if (responseData && Array.isArray(responseData.data)) {
                    documents.value = responseData.data;
                    pagination.value = {
                        current_page: responseData.current_page || 1,
                        last_page: responseData.last_page || 1,
                        per_page: responseData.per_page || 15,
                        total: responseData.total || 0
                    };
                } else if (Array.isArray(responseData)) {
                    documents.value = responseData;
                    pagination.value = {
                        current_page: 1,
                        last_page: 1,
                        per_page: responseData.length,
                        total: responseData.length
                    };
                } else {
                    documents.value = [];
                }
                
                console.log('Documents fetched successfully:', documents.value.length, 'items');
                return { success: true, data: responseData };
            } else {
                console.error('Failed to fetch documents:', data);
                documents.value = [];
                return { success: false, error: data.message || 'Failed to fetch documents' };
            }
        } catch (err) {
            console.error('Fetch documents error:', err);
            documents.value = [];
            return { success: false, error: 'An error occurred while fetching documents' };
        } finally {
            loading.value = false;
        }
    };

    const createDocument = async (documentData) => {
        loading.value = true;

        try {
            await ensureCsrfCookie();
            
            const response = await fetch('/api/documents', {
                method: 'POST',
                headers: getHeaders(),
                credentials: 'include',
                body: JSON.stringify(documentData)
            });

            const data = await response.json();

            if (response.ok && data.success) {
                // Refresh documents list
                await fetchDocuments({ page: pagination.value.current_page });
                return { success: true, data: data.data };
            } else {
                return { success: false, error: data.message || 'Failed to create document', errors: data.errors };
            }
        } catch (err) {
            console.error('Create document error:', err);
            return { success: false, error: 'An error occurred while creating document' };
        } finally {
            loading.value = false;
        }
    };

    const updateDocument = async (id, documentData) => {
        loading.value = true;

        try {
            await ensureCsrfCookie();
            
            const response = await fetch(`/api/documents/${id}`, {
                method: 'PUT',
                headers: getHeaders(),
                credentials: 'include',
                body: JSON.stringify(documentData)
            });

            const data = await response.json();

            if (response.ok && data.success) {
                // Refresh documents list
                await fetchDocuments({ page: pagination.value.current_page });
                return { success: true, data: data.data };
            } else {
                return { success: false, error: data.message || 'Failed to update document', errors: data.errors };
            }
        } catch (err) {
            console.error('Update document error:', err);
            return { success: false, error: 'An error occurred while updating document' };
        } finally {
            loading.value = false;
        }
    };

    const deleteDocument = async (id) => {
        loading.value = true;

        try {
            await ensureCsrfCookie();
            
            const response = await fetch(`/api/documents/${id}`, {
                method: 'DELETE',
                headers: getHeaders(),
                credentials: 'include'
            });

            const data = await response.json();

            if (response.ok && data.success) {
                // Refresh documents list
                await fetchDocuments({ page: pagination.value.current_page });
                return { success: true };
            } else {
                return { success: false, error: data.message || 'Failed to delete document' };
            }
        } catch (err) {
            console.error('Delete document error:', err);
            return { success: false, error: 'An error occurred while deleting document' };
        } finally {
            loading.value = false;
        }
    };

    const validateDocument = async (id) => {
        loading.value = true;

        try {
            // Ensure CSRF cookie is set and get fresh token
            await ensureCsrfCookie();
            
            // Get fresh CSRF token from meta tag
            const csrfToken = getCsrfToken();
            if (!csrfToken) {
                console.error('CSRF token not found');
                return { success: false, error: 'CSRF token not found. Please refresh the page.' };
            }
            
            const response = await fetch(`/api/documents/${id}/validate`, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                credentials: 'include'
            });

            const data = await response.json();

            if (response.ok && data.success) {
                // Refresh documents list
                await fetchDocuments({ page: pagination.value.current_page });
                return { success: true, data: data.data };
            } else {
                // If CSRF error, try to refresh token and retry once
                if (response.status === 419 || (data.message && data.message.includes('CSRF'))) {
                    console.warn('CSRF token expired, refreshing...');
                    await ensureCsrfCookie();
                    const newCsrfToken = getCsrfToken();
                    
                    const retryResponse = await fetch(`/api/documents/${id}/validate`, {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': newCsrfToken,
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        credentials: 'include'
                    });
                    
                    const retryData = await retryResponse.json();
                    if (retryResponse.ok && retryData.success) {
                        await fetchDocuments({ page: pagination.value.current_page });
                        return { success: true, data: retryData.data };
                    }
                }
                return { success: false, error: data.message || 'Failed to validate document' };
            }
        } catch (err) {
            console.error('Validate document error:', err);
            return { success: false, error: 'An error occurred while validating document' };
        } finally {
            loading.value = false;
        }
    };

    const cancelDocument = async (id) => {
        loading.value = true;

        try {
            await ensureCsrfCookie();
            
            const response = await fetch(`/api/documents/${id}/cancel`, {
                method: 'POST',
                headers: getHeaders(),
                credentials: 'include'
            });

            const data = await response.json();

            if (response.ok && data.success) {
                // Refresh documents list
                await fetchDocuments({ page: pagination.value.current_page });
                return { success: true, data: data.data };
            } else {
                return { success: false, error: data.message || 'Failed to cancel document' };
            }
        } catch (err) {
            console.error('Cancel document error:', err);
            return { success: false, error: 'An error occurred while cancelling document' };
        } finally {
            loading.value = false;
        }
    };

    const getDocument = async (id) => {
        loading.value = true;

        try {
            const response = await fetch(`/api/documents/${id}`, {
                method: 'GET',
                headers: getHeaders(),
                credentials: 'include'
            });

            const data = await response.json();

            if (response.ok && data.success) {
                return { success: true, data: data.data };
            } else {
                return { success: false, error: data.message || 'Failed to fetch document' };
            }
        } catch (err) {
            console.error('Get document error:', err);
            return { success: false, error: 'An error occurred while fetching document' };
        } finally {
            loading.value = false;
        }
    };

    const setCurrentDocument = (document) => {
        currentDocument.value = document ? { ...document } : null;
    };

    const clearCurrentDocument = () => {
        currentDocument.value = null;
    };

    // Helper function to create a new document structure
    const createNewDocument = (domain, type) => {
        return {
            domain: domain, // 'sale', 'purchase', 'stock'
            type: type, // 'invoice', 'delivery_note', 'return', 'receipt', 'adjustment', etc.
            warehouse_id: null,
            partner_id: null,
            document_date: new Date().toISOString().split('T')[0],
            due_date: null,
            notes: '',
            reference: '',
            lines: []
        };
    };

    // Helper function to add a line to current document
    const addLine = (line) => {
        if (!currentDocument.value) {
            return;
        }
        if (!currentDocument.value.lines) {
            currentDocument.value.lines = [];
        }
        currentDocument.value.lines.push({
            product_id: null,
            quantity: 1,
            unit_price: 0,
            unit_cost: 0,
            discount_percent: 0,
            tax_percent: 0,
            description: '',
            notes: '',
            ...line
        });
    };

    // Helper function to remove a line
    const removeLine = (index) => {
        if (!currentDocument.value || !currentDocument.value.lines) {
            return;
        }
        currentDocument.value.lines.splice(index, 1);
    };

    // Helper function to calculate totals
    const calculateTotals = () => {
        if (!currentDocument.value || !currentDocument.value.lines) {
            return;
        }

        let subtotal = 0;
        let totalTax = 0;
        let totalDiscount = 0;

        currentDocument.value.lines.forEach(line => {
            const lineSubtotal = line.quantity * line.unit_price;
            const lineDiscount = lineSubtotal * (line.discount_percent || 0) / 100;
            const afterDiscount = lineSubtotal - lineDiscount;
            const lineTax = afterDiscount * (line.tax_percent || 0) / 100;
            const lineTotal = afterDiscount + lineTax;

            subtotal += lineSubtotal;
            totalDiscount += lineDiscount;
            totalTax += lineTax;
        });

        currentDocument.value.subtotal = subtotal;
        currentDocument.value.discount_amount = totalDiscount;
        currentDocument.value.tax_amount = totalTax;
        currentDocument.value.total_amount = subtotal - totalDiscount + totalTax;
    };

    return {
        // State
        documents,
        loading,
        currentDocument,
        pagination,
        // Actions
        fetchDocuments,
        createDocument,
        updateDocument,
        deleteDocument,
        validateDocument,
        cancelDocument,
        getDocument,
        setCurrentDocument,
        clearCurrentDocument,
        // Helpers
        createNewDocument,
        addLine,
        removeLine,
        calculateTotals
    };
});

