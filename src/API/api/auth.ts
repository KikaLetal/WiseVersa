import { apiClient } from "../client";

export const register = async (data: {
    username: string;
    password: string;
    avatar: File | null;
}) => {
    const formData = new FormData();

    formData.append("username", data.username);
    formData.append("password", data.password);
    
    if(data.avatar) {
        formData.append("avatar", data.avatar);
    }

    return apiClient("/auth/register",{
        method: "POST",
        body: formData
    });
};

export const login = async (data: {
    username: string;
    password: string;
}) => {
    const res = await apiClient("/auth/login",{
        method: "POST",
        headers: {
            "Content-Type": "application/json",
        },
        body: JSON.stringify(data)
    });

    return res;
};

export const me = async (token: string) => {
    const res = await apiClient("/auth/me", {
        method: "GET",
        headers: {
            Authorization: `Bearer ${token}`,
        },
    });

    return res;
};