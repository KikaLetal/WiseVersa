import React, { useState } from 'react';
import './BlogModal.css';

interface BlogModalProps {
    initialContent?: string;
    initialImage?: string | null;
    onSubmit: (content: string, image?: File | null) => Promise<void>;
    onCancel: () => void;
    title: string;
}

const BlogModal: React.FC<BlogModalProps> = ({
    initialContent = '',
    initialImage = null,
    onSubmit,
    onCancel,
    title
}) => {
    const [content, setContent] = useState(initialContent);
    const [imageFile, setImageFile] = useState<File | null>(null);
    const [imagePreview, setImagePreview] = useState<string | null>(initialImage);
    const [isSubmitting, setIsSubmitting] = useState(false);
    const fileInputRef = React.useRef<HTMLInputElement>(null);


    const handleImageChange = (e: React.ChangeEvent<HTMLInputElement>) => {
        const file = e.target.files?.[0];
        if (file) {
            setImageFile(file);
            const reader = new FileReader();
            reader.onloadend = () => {
                setImagePreview(reader.result as string);
            };
            reader.readAsDataURL(file);
        }
    };

    const handleRemoveImage = () => {
        setImageFile(null);
        setImagePreview(null);
        if (fileInputRef.current) {
            fileInputRef.current.value = '';
        }
    };

    const handleSubmit = async () => {
        if (!content.trim()) return;
        setIsSubmitting(true);
        await onSubmit(content, imageFile || undefined);
        setIsSubmitting(false);
    };

    return (
        <div className="blog-editor-overlay" onClick={onCancel}>
            <div className="blog-editor" onClick={e => e.stopPropagation()}>
                <h3>{title}</h3>
                
                <div className="blog-editor__image">
                    <label className="image-label">
                        <span>📷 Картинка превью</span>
                        <input
                            type="file"
                            accept="image/*"
                            onChange={handleImageChange}
                            ref={fileInputRef}
                            style={{ display: 'none' }}
                        />
                        <button 
                            type="button" 
                            className="upload-btn"
                            onClick={() => fileInputRef.current?.click()}
                        >
                            Выбрать фото
                        </button>
                    </label>
                    
                    {imagePreview && (
                        <div className="image-preview">
                            <img src={imagePreview} alt="Preview" />
                            <button type="button" onClick={handleRemoveImage} className="remove-image">
                                ✕
                            </button>
                        </div>
                    )}
                </div>

                <textarea
                    className="blog-editor__content"
                    value={content}
                    onChange={(e) => setContent(e.target.value)}
                    placeholder="Введите текст поста..."
                    rows={6}
                />
                
                <div className="blog-editor__actions">
                    <button onClick={onCancel} className="cancel">Отмена</button>
                    <button onClick={handleSubmit} disabled={isSubmitting || !content.trim()} className="submit">
                        {isSubmitting ? 'Публикация...' : 'Опубликовать'}
                    </button>
                </div>
            </div>
        </div>
    );
};

export default BlogModal;