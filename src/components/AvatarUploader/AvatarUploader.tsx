import React from 'react'
import './AvatarUploader.css'

interface AvatarUploaderProps {
    value?: File | null;
    onChange?: (file: File | null) => void;
}

const AvatarUploader : React.FC<AvatarUploaderProps> = ({ value, onChange }) => {
    const [preview, setPreview] =
        React.useState<string | null>(null);

    React.useEffect(() => {
        return () => {
            if (preview) {
                URL.revokeObjectURL(preview);
            }
        };
    }, [preview]);

    const handleChangeAvatar = (e: React.ChangeEvent<HTMLInputElement>) => {
        const file = e.target.files?.[0] ?? null;

        if(!file) return;

        const imageURL = URL.createObjectURL(file);

        setPreview(imageURL);

        onChange?.(file);
    }

    return (
        <>
            <label className="avatarUploader">
                <input
                    type="file"
                    accept="image/*"
                    onChange={handleChangeAvatar}
                    hidden
                />

                <img
                    src={
                        preview ??
                        "../../../public/sources/icons/default_avatar.svg"
                    }
                    alt="avatar"
                    className="avatarUploader_img"
                />

                <div className="avatarUploader_overlay">
                    Изменить
                </div>
            </label>
        </>
    )
}

export default AvatarUploader