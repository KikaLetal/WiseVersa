import { create } from "zustand";
import { login as loginApi, register as registerApi, me as meApi} from "../API/api/auth";
import { type User } from "../types/types";

interface AuthState {
    user: User | null;
    token: string | null;
    isAuth: boolean;
    loading: boolean;

    login: (data: {username: string, password: string, rememberMe?: boolean}) => Promise<void>;
    register: (data: {
        username: string;
        password: string;
        avatar: File | null;
    }) => Promise<{ success: boolean; error?: string }>;

    logout: () => void;
    hydrate: () => Promise<void>;
    setAuth: (token: string, user: User, rememberMe: boolean) => void;
}

export const useAuthStore = create<AuthState>((set, get) => ({
    user: null,
    token: null,
    isAuth: false,
    loading: true,

    login: async (data:{
        username: string, 
        password: string, 
        rememberMe?: boolean
    }) => {
        const res = await loginApi(data);
        const { token, user } = res;

        get().setAuth(token, user, data.rememberMe || false);
    },

    register: async (data) => {
        const res = await registerApi(data);
        const { token, user } = res;

        get().setAuth(token, user, false);

        return res;
    },

    logout: () => {
        localStorage.removeItem('token');
        sessionStorage.removeItem('token');

        set({ 
            token: null,
            user: null, 
            isAuth: false
        });
    },

    hydrate: async () => {
        const token = localStorage.getItem('token')||sessionStorage.getItem('token');

        if(!token) {
            set({ 
                isAuth: false,
                token: null,
                user: null,
                loading: false
            });
            return;
        }

        try{
            const res = await meApi(token);
            const responseData = res as any;
            
            if (!responseData.success) {
                throw new Error(responseData.error || 'Failed to get user');
            }

            const userData = responseData.user;

            const userWithRole: User = {
                id: userData.id,
                username: userData.username,
                profilePicture: userData.profile_picture,
                role: userData.role || 'user'
            };
            
            set({ 
                token, 
                user: userWithRole,
                isAuth: true,
                loading: false
            });

        } catch(e) {
            localStorage.removeItem('token');   
            sessionStorage.removeItem('token');
            
            set({ 
                token: null,
                user: null,
                isAuth: false,
                loading: false
            });
        }
    },
    
    setAuth: (token: string, user: User, rememberMe: boolean) => {
        if(rememberMe){
            localStorage.setItem('token', token);
            sessionStorage.removeItem('token');
        } else {
            sessionStorage.setItem('token', token);
            localStorage.removeItem('token');
        }

        set({
            token,
            user,
            isAuth: true,
            loading: false,
        });
    },
}));
