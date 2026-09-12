import React, { useState } from 'react';
import type { BlogPost as BlogPostType } from '../../types/types';
import { useAuthStore } from '../../store/authStore';
import './Blog.css';

interface BlogPostProps {
    post: BlogPostType;
    onDelete?: (postId: number) => void;
    onEdit?: (post: BlogPostType) => void;
    onLike?: (postId: number) => void;
    isOwner?: boolean;
}

const BlogPost: React.FC<BlogPostProps> = ({ post, onDelete, onEdit, onLike, isOwner }) => {
    const [showFullContent, setShowFullContent] = useState(false);
    const { user } = useAuthStore();
    
    const formatDate = (dateString: string) => {
        const date = new Date(dateString);
        const now = new Date();
        const diff = Math.floor((now.getTime() - date.getTime()) / 1000);
        
        if (diff < 60) return 'только что';
        if (diff < 3600) return `${Math.floor(diff / 60)} мин назад`;
        if (diff < 86400) return `${Math.floor(diff / 3600)} ч назад`;
        return date.toLocaleDateString('ru-RU');
    };
    
    const content = showFullContent || !post.preview ? post.content : (post.preview || post.content.slice(0, 200) + '...');
    const needsExpand = post.content.length > 200 && !showFullContent;
    
    return (
        <div className="blog-post">
            <div className="blog-post__header">
                <div className="blog-post__author">
                    <img 
                        src={post.profile_picture ? `/uploads/${post.profile_picture}` : '/sources/icons/default-avatar.svg'} 
                        alt={post.username}
                        className="blog-post__avatar"
                    />
                    <div>
                        <span className="blog-post__username">{post.username}</span>
                        <span className="blog-post__date">{formatDate(post.created_at)}</span>
                    </div>
                </div>
                {isOwner && (
                    <div className="blog-post__actions">
                        <button onClick={() => onEdit?.(post)} className="blog-post__edit">✏️</button>
                        <button onClick={() => onDelete?.(post.id)} className="blog-post__delete">🗑️</button>
                    </div>
                )}
            </div>
            
            <div className="blog-post__content">
                <img src={`${post.preview}`} alt="" className="blog-post__content_img" />
                <p>{content}</p>
                {needsExpand && (
                    <button onClick={() => setShowFullContent(true)} className="blog-post__expand">
                        Читать далее
                    </button>
                )}
            </div>
            
            <div className="blog-post__footer">
                <button 
                    className={`blog-post__like ${post.user_liked ? 'liked' : ''}`}
                    onClick={() => onLike?.(post.id)}
                >
                    <span className="like-icon">{post.user_liked ? '❤️' : '🤍'}</span>
                    <span className="like-count">{post.likes_count}</span>
                </button>
            </div>
        </div>
    );
};

export default BlogPost;