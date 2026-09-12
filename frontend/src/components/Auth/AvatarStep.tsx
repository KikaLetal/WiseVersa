import React from 'react'
import './AvatarStep.css'
import AvatarUploader from '../AvatarUploader/AvatarUploader';

interface AvatarStepProps {
    onNext: (avatar: File | null) => void;
}

const AvatarStep : React.FC<AvatarStepProps> = ({ onNext }) => {
    const [avatar, setAvatar] = React.useState<File | null>(null);

    return (
        <>
            <div className="avatarStep_wraper">
                <div className="avatarStep_container">
                    <h2 className='avatarStep_container_title'>Загрузите фотографию</h2>

                    <div className='avatarStep_container_form'>
                        <div className="avatarStep_form_areas_inputs">
                            <AvatarUploader
                                value={avatar}
                                onChange={setAvatar}
                            />
                        </div>
                        <button 
                            className='avatarStep_Enter' 
                            onClick={() => onNext(avatar)}
                        >
                            Загрузить
                        </button>
                    </div>

                    <button 
                        className="avatarStep_switchToReg"
                        onClick={() => onNext(null)}
                    >
                        Пропустить
                    </button>
                </div>
            </div>
        </>
    )
}

export default AvatarStep