import React, { useEffect, useState } from 'react';
import { useBlogStore } from '../store/blogStore';
import { useAuthStore } from '../store/authStore';
import BlogPost from '../components/Blog/Blog';
import BlogModal from '../components/Blog/BlogModal/BlogModal';
import './BlogPage.css';

const BlogPage: React.FC = () => {
    const { posts, loading, error, fetchPosts, createPost, updatePost, deletePost, toggleLike, reset } = useBlogStore();
    const { user } = useAuthStore();
    const [showModal, setShowModal] = useState(false);
    const [editingPost, setEditingPost] = useState<any>(null);

    useEffect(() => {
        reset();
        fetchPosts(true);
        
        return () => {
            reset();
        };
    }, []);
    
    const handleCreatePost = async (content: string, image?: File | null) => {
        await createPost(content, image);
        setShowModal(false);
    };
    
    const handleEditPost = (post: any) => {
        setEditingPost(post);
    };

    const handleUpdatePost = async (content: string, image?: File | null) => {
        if (editingPost) {
            const success = await updatePost(editingPost.id, content, image);
            if (success) {
                setEditingPost(null);
            }
        }
    };
    
    const handleDeletePost = async (postId: number) => {
        if (confirm('Удалить этот пост?')) {
            await deletePost(postId);
        }
    };
    
    const handleScroll = (e: React.UIEvent<HTMLDivElement>) => {
        const target = e.target as HTMLDivElement;
        if (target.scrollTop + target.clientHeight >= target.scrollHeight - 200) {
            fetchPosts();
        }
    };
    
    const isAdmin = user?.role === 'admin' || user?.role === 'moderator';
    
    return (
        <div className="blog-page">
            <div className="blog-header">
                <h1>Блог</h1>
                {isAdmin && (
                    <button onClick={() => setShowModal(true)} className="create-post-btn">
                        + Написать пост
                    </button>
                )}
            </div>
            
            <div className="blog-content" onScroll={handleScroll}>
                {loading && posts.length === 0 && (
                    <div className="blog-loading">Загрузка...</div>
                )}
                
                {error && (
                    <div className="blog-error">{error}</div>
                )}
                
                {posts.length === 0 && !loading && (
                    <div className="blog-empty">
                        <p>Пока нет постов.</p>
                    </div>
                )}
                
                <div className="blog-posts">
                    {posts.map(post => (
                        <BlogPost
                            key={post.id}
                            post={post}
                            onDelete={handleDeletePost}
                            onEdit={handleEditPost}
                            onLike={toggleLike}
                            isOwner={user?.id === post.author_id || isAdmin}
                        />
                    ))}
                </div>
                
                {loading && posts.length > 0 && (
                    <div className="blog-loading-more">Загрузка...</div>
                )}
            </div>
            
            {showModal && (
                <BlogModal
                    title="Создать пост"
                    initialContent=""
                    initialImage={null}
                    onSubmit={handleCreatePost}
                    onCancel={() => setShowCreateModal(false)}
                />
            )}
            
            {editingPost && (
                <BlogModal
                    title="Редактировать пост"
                    initialContent={editingPost.content}
                    initialImage={editingPost.preview}
                    onSubmit={handleUpdatePost}
                    onCancel={() => setEditingPost(null)}
                />
            )}
        </div>
    );
};

export default BlogPage;