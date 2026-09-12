import { create } from "zustand";
import { getPosts, createPost as createPostApi, updatePost as updatePostApi, deletePost, toggleLike } from "../API/api/blog";
import type { BlogPost } from "../types/types";

interface BlogStore {
    posts: BlogPost[];
    loading: boolean;
    error: string | null;
    page: number;
    hasMore: boolean;
    
    fetchPosts: (reset?: boolean) => Promise<void>;
    createPost: (content: string, image?: File | null) => Promise<BlogPost | null>;
    updatePost: (postId: number, content: string, image?: File | null) => Promise<boolean>;
    deletePost: (postId: number) => Promise<boolean>;
    toggleLike: (postId: number) => Promise<void>;
    reset: () => void;
}

export const useBlogStore = create<BlogStore>((set, get) => ({
    posts: [],
    loading: false,
    error: null,
    page: 1,
    hasMore: true,
    
    fetchPosts: async (reset = false) => {
        const { page, hasMore, loading } = get();
        
        if (loading || !hasMore) return;
        
        const currentPage = reset ? 1 : page;
        set({ loading: true, error: null });
        
        try {
            const res = await getPosts(currentPage, 20);
            const newPosts = res.data;
            
            set(state => ({
                posts: reset ? newPosts : [...state.posts, ...newPosts],
                page: currentPage + 1,
                hasMore: newPosts.length === 20,
                loading: false
            }));
        } catch (error) {
            set({ 
                loading: false, 
                error: error instanceof Error ? error.message : 'Failed to fetch posts'
            });
        }
    },
    
    createPost: async (content: string, image?: File | null) => {
        set({ loading: true, error: null });
        try {
            const formData = new FormData();
            formData.append('content', content);
            if (image) formData.append('preview', image);
            
            const res = await createPostApi(formData);
            const newPost = res.data;
            set(state => ({
                posts: [newPost, ...state.posts],
                loading: false
            }));
            return newPost;
        } catch (error) {
            set({ 
                loading: false, 
                error: error instanceof Error ? error.message : 'Failed to create post'
            });
            return null;
        }
    },

    updatePost: async (postId: number, content: string, image?: File | null) => {
        set({ loading: true });
        try {
            const formData = new FormData();
            formData.append('content', content);
            formData.append('_method', 'PUT');  // для Laravel-style, если нужно
            if (image) formData.append('preview', image);

            const existingPost = get().posts.find(p => p.id === postId);
            if (!image && existingPost?.preview) {
                formData.append('existing_preview', existingPost.preview);
            }
            
            const res = await updatePostApi(postId, formData);
            if (res.success) {
                set(state => ({
                    posts: state.posts.map(post =>
                        post.id === postId
                            ? { ...post, content, preview: image ? URL.createObjectURL(image) : post.preview, updated_at: new Date().toISOString() }
                            : post
                    ),
                    loading: false
                }));
                return true;
            }
            return false;
        } catch (error) {
            set({ loading: false, error: 'Failed to update post' });
            return false;
        }
    },
    
    deletePost: async (postId: number) => {
        set({ loading: true });
        try {
            await deletePost(postId);
            set(state => ({
                posts: state.posts.filter(p => p.id !== postId),
                loading: false
            }));
            return true;
        } catch (error) {
            set({ loading: false, error: 'Failed to delete post' });
            return false;
        }
    },
    
    toggleLike: async (postId: number) => {
        try {
            const res = await toggleLike(postId);
            const { liked, likes_count } = res.data;
            set(state => ({
                posts: state.posts.map(post =>
                    post.id === postId
                        ? { ...post, user_liked: liked, likes_count }
                        : post
                )
            }));
        } catch (error) {
            console.error('Failed to toggle like', error);
        }
    },
    
    reset: () => {
        set({ posts: [], page: 1, hasMore: true, error: null });
    }
}));