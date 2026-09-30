export const API_BASE_URL =
    import.meta.env.VITE_API_URL ?? "http://127.0.0.1:8000";

type ApiSuccess<T> = T & {
    success: true;
};

type ApiError = {
    success: false;
    error: string;
};

export type ApiResponse<T> = ApiSuccess<T> | ApiError;

function getStoredToken(): string | null {
    return localStorage.getItem("token") ?? sessionStorage.getItem("token");
}

export const apiClient = async <T>(
    endpoint: string,
    options?: RequestInit & { auth?: boolean },
): Promise<ApiResponse<T>> => {
    const isFormData = options?.body instanceof FormData;
    const useAuth = options?.auth !== false;
    const token = useAuth ? getStoredToken() : null;

    const { auth: _auth, ...fetchOptions } = options ?? {};
    const url = `${API_BASE_URL}${endpoint}`;

    let response: Response;
    try {
        response = await fetch(url, {
            ...fetchOptions,
            headers: {
                ...(isFormData
                    ? {}
                    : { "Content-Type": "application/json" }),
                ...(token ? { Authorization: `Bearer ${token}` } : {}),
                ...fetchOptions.headers,
            },
        });
    } catch {
        throw new Error(
            `Не удалось подключиться к ${API_BASE_URL}. Запустите: npm run backend`,
        );
    }

    const text = await response.text();
    const trimmed = text.trim();

    let data: Record<string, unknown>;
    try {
        data = trimmed ? JSON.parse(trimmed) : {};
    } catch {
        throw new Error(
            trimmed.slice(0, 200) ||
                `Сервер вернул не-JSON ответ (${response.status})`,
        );
    }

    if (!response.ok || data.success === false) {
        throw new Error(String(data.error ?? `Ошибка сервера (${response.status})`));
    }

    return data as ApiResponse<T>;
};
