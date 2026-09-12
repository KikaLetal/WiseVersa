import { apiClient } from '../client';
import type { BlogPost, CreatePostData, UpdatePostData } from '../../types/types'; 

export const getPosts = async (page: number = 1, limit: number = 20): Promise<{ data: BlogPost[]; pagination: { page: number; limit: number } }> => {
    const res = await apiClient(`/blog/posts?page=${page}&limit=${limit}`);
    return res as any;
};

export const getPost = async (postId: number): Promise<{ data: BlogPost }> => {
    const res = await apiClient(`/blog/posts/${postId}`);
    return res as any;
};

export const createPost = async (formData: FormData): Promise<{ data: BlogPost }> => {
    const res = await apiClient('/blog/posts', {
        method: 'POST',
        body: formData 
    });
    return res as any;
};

export const updatePost = async (postId: number, formData: FormData): Promise<{ success: boolean }> => {
    const res = await apiClient(`/blog/posts/${postId}`, {
        method: 'POST', 
        body: formData
    });
    return res as any;
};

export const deletePost = async (postId: number): Promise<{ success: boolean }> => {
    const res = await apiClient(`/blog/posts/${postId}`, { method: 'DELETE' });
    return res as any;
};

export const toggleLike = async (postId: number): Promise<{ data: { liked: boolean; likes_count: number } }> => {
    const res = await apiClient(`/blog/posts/${postId}/like`, { method: 'POST' });
    return res as any;
};