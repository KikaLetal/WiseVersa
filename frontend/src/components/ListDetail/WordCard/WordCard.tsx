import type React from 'react'
import './WordCard.css'

interface WordCardProps {
    word: string;
    translation: string;
    partOfSpeech?: string | null;
    onDelete?: () => void;
    onCopy?: (text: string) => void;
    onAddToFavorites?: (word: string, translation: string) => void;
    onAddToList?: (word: string, translation: string) => void;
}

const WordCard : React.FC<WordCardProps> = ({
    word, 
    translation, 
    partOfSpeech, 
    onDelete,
    onCopy,
    onAddToFavorites,
    onAddToList
}) => {
    const handleCopy = () => {
        navigator.clipboard.writeText(translation);
        onCopy?.(translation);
    };
    const handleFavorite = () => onAddToFavorites?.(word, translation);
    const handleList = () => onAddToList?.(word, translation);

    return (
        <div className="wordCard">
            <div className="wordCard_info">
                <div className="wordCard_info_translation">
                    <p className="wordCard_info_translation">{translation}</p>
                    {partOfSpeech && (<p className="wordCard_info_translation_partOfSpeech">{partOfSpeech}</p>)}
                </div>
                <p className="wordCard_info_original">{word}</p>
            </div>
            <div className="wordCard_actions">
                <button onClick={handleCopy} title="Копировать">📋</button>
                <button onClick={handleFavorite} title="В избранное">⭐</button>
                <button onClick={handleList} title="Добавить в список">📚</button>
                <button onClick={onDelete}>🗑️</button>
            </div>
        </div>
    )
}

export default WordCard