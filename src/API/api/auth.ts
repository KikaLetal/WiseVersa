import { apiClient, type ApiResponse } from "../client";
import { type User } from "../../types/types";

type AuthResponse = {
    token: string;
    user: User;
};

type MeResponse = {
    user: User;
};

export const register = async (data: {
    username: string;
    password: string;
    avatar: File | null;
}): Promise<ApiResponse<AuthResponse>> => {
    const formData = new FormData();

    formData.append("username", data.username);
    formData.append("password", data.password);

    if (data.avatar) {
        formData.append("avatar", data.avatar);
    }

    return apiClient<AuthResponse>("/auth/register", {
        method: "POST",
        body: formData,
        auth: false,
    });
};

export const login = async (data: {
    username: string;
    password: string;
}): Promise<ApiResponse<AuthResponse>> => {
    return apiClient<AuthResponse>("/auth/login", {
        method: "POST",
        body: JSON.stringify(data),
        auth: false,
    });
};

export const me = async (token: string): Promise<ApiResponse<MeResponse>> => {
    return apiClient<MeResponse>("/auth/me", {
        method: "GET",
        headers: {
            Authorization: `Bearer ${token}`,
        },
    });
};
