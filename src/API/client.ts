const API_BASE_URL = 'http://127.0.0.1:8000';  

export const apiClient = async(
    endpoint: string,
    options?: RequestInit
) => {
    const isFormData = options?.body instanceof FormData;

    const response = await fetch(`${API_BASE_URL}${endpoint}`, {
        ...options,
        headers: {
            ...(isFormData ? {} : {
                "Content-Type": "application/json"
            }),
            ...options?.headers
        }
    });

    const data = await response.json().catch(() => null);

    if(!response.ok) {
        return {
            success: false,
            error: data?.error || "Server error"
        }
    }

    return {
        success: true,
        data
    };
}