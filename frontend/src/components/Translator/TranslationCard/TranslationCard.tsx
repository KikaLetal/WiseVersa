import './TranslationCard.css'
import React from 'react';
import { type TranslationVariant } from '../../../types/types';

interface TranslationCardProps{
    variant: TranslationVariant;
    sourceWord: string;
    onCopy?: (text: string) => void;
    onAddToFavorites?: (word: string, translation: string) => void;
    onAddToList?: (word: string, translation: string) => void;
}

const TranslationCard: React.FC<TranslationCardProps> = ({
    variant,
    sourceWord,
    onCopy,
    onAddToFavorites,
    onAddToList,
}) => {
    const handleCopy = () => {
        navigator.clipboard.writeText(variant.text);
        onCopy?.(variant.text);
    };
    const translation = variant.text;
    const handleFavorite = () => onAddToFavorites?.(sourceWord, translation);
    const handleList = () => onAddToList?.(sourceWord, translation);

    return (
        <div className="translationCard_wraper">
            <div className="translationCard_translation">
                <p className="translationCard_content_translation_text">{variant.text}</p>
                {variant.partOfSpeech && (
                    <span className="translationCard_content_translation_pos">{variant.partOfSpeech}</span>
                )}
            </div>
            <div className="translationCard_actions">
                <button onClick={handleCopy} title="Копировать">📋</button>
                <button onClick={handleFavorite} title="В избранное">⭐</button>
                <button onClick={handleList} title="Добавить в список">📚</button>
            </div>
        </div>
    )
}

export default TranslationCard;
