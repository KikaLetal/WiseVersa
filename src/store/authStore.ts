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
        set({ loading: true });

        try {
            const res = await loginApi(data);

            if (!res.success) throw new Error(res.error);

            const { token, user } = res.data;

            get().setAuth(token, user, data.rememberMe || false);

        } catch (e) {
            throw e;
        } finally {
            set({ loading: false });
        }
    },

    register: async (data) => {
        set({ loading: true });

        try{
            const res = await registerApi(data);

            if(!res.success) throw new Error(res.error);

            const  {token, user } = res.data;

            get().setAuth(token, user, false);

            return res;
        } finally {
            set({ loading: false });
        }
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

            if (!res.success || !res.data.user) throw new Error();
            
            set({ 
                token, 
                user: res.data.user,
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
