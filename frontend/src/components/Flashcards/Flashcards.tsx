import React, { useEffect } from 'react'
import './Flashcards.css'
import { getListItems, resetListProgress, updateItemMastered } from '../../API/api/dictionary';
import { type CardItem } from '../../types/types';

interface FlashcardsProps {
    listId: number;
    listName: string;
    onBack: () => void;
    onUpdate?: () => void;
}

const Flashcards : React.FC<FlashcardsProps> = ({
    listId, 
    listName, 
    onBack,
    onUpdate
}) => {
    const [allCards, setAllCards] = React.useState<CardItem[]>([]);
    const [cards, setCards] = React.useState<CardItem[]>([]);
    const [currentIndex, setCurrentIndex] = React.useState(0);
    const [flipped, setFlipped] = React.useState(false);
    const [loading, setLoading] = React.useState(true);
    const [completed, setCompleted] = React.useState(false);
    const [highlight, setHighlight] = React.useState<'know' | 'notknow' | null>(null);
    const [knownCount, setKnownCount] = React.useState(0);
    const [unknownCount, setUnknownCount] = React.useState(0);

    const loadAllCards = async () => {
        try {
            const res = await getListItems(listId);
            const data = res as any;
            if (data.success) {
                const all = data.data;
                setAllCards(all);
                const notMastered = all.filter((c: CardItem) => !c.mastered);
                setCards(notMastered);
                if (notMastered.length === 0 && all.length > 0) {
                    setCompleted(true);
                } else {
                    setCompleted(false);
                }
            }
        } catch (error) {
            console.error(error);
        } finally {
            setLoading(false);
        }
    };

    useEffect(() => {
        loadAllCards();
        setKnownCount(0);
        setUnknownCount(0);
    }, [listId]);

    const resetProgress = async () => {
        await resetListProgress(listId);
        await loadAllCards(); 
        setCurrentIndex(0);
        setFlipped(false);
        setCompleted(false);
        setKnownCount(0);
        setUnknownCount(0);
        onUpdate?.();
    };

    const handleKnow = async () => {
        setHighlight('know');
        setTimeout(() => setHighlight(null), 300);
        setKnownCount(prev => prev + 1);

        const currentCard = cards[currentIndex];
        if (!currentCard) return;

        await updateItemMastered(currentCard.id, true);

        const newCards = cards.filter((_, idx) => idx !== currentIndex);
        setCards(newCards);
        setFlipped(false);

        if (newCards.length === 0) {
            if (allCards.length > 0) {
                setCompleted(true);
            }
            onUpdate?.();
        } else if (currentIndex >= newCards.length) {
            setCurrentIndex(newCards.length - 1);
        } else {
            setCurrentIndex(currentIndex);
        }
    };

    const handleNotKnow = async () => {
        setHighlight('notknow');
        setTimeout(() => setHighlight(null), 300);

        const currentCard = cards[currentIndex];
        if (!currentCard) return;

        await updateItemMastered(currentCard.id, false);

        if (cards.length === 1) {
            setFlipped(false);
            setUnknownCount(prev => prev + 1);
            return;
        }

        setUnknownCount(prev => prev + 1);

        const newCards = [...cards];
        const removed = newCards.splice(currentIndex, 1)[0];
        newCards.push(removed);
        setCards(newCards);
        setFlipped(false);
    };

    const handleFlip = () => setFlipped(!flipped);

    if (loading) return <div className="flashcardsLoading">Загрузка карточек...</div>;

    if (allCards.length === 0) {
        return (
            <div className="flashcardsEmpty">
                <p>В этом списке пока нет слов. Добавьте их через переводчик.</p>
                <button onClick={onBack}>Вернуться к списку</button>
            </div>
        );
    }

    if (completed || (cards.length === 0 && allCards.length > 0)) {
        return (
            <div className="flashcards_Completed_wraper">
                <div className="flashcards_Completed">
                    <h2>Поздравляем! 🎉</h2>
                    <p>Вы выучили все {allCards.length} слов(о) из списка «{listName}».</p>
                    <div className="flashcards_Completed_actions">
                        <button onClick={onBack}>Вернуться к списку</button>
                        <button onClick={resetProgress}
                        className="flashcards_Completed_actions_resetBtn">
                            Пройти заново
                        </button>
                    </div>
                </div>
            </div>
        );
    }

    const currentCard = cards[currentIndex];
    if (!currentCard) {
        console.error('currentCard is undefined', cards, currentIndex);
        return <div className="flashcardsError">Ошибка загрузки карточки</div>;
    }

    return (
        <>
            <div className="flashcards_progressBar_container">
                <div className="flashcards_topBar">
                    <button className="flashcardsBack" onClick={onBack}>✕</button>
                    <div className="flashcards_progressBarWrapper">
                        <div className="flashcards_progressBar">
                            <div 
                                className="flashcards_progressFill" 
                                style={{ width: `${((currentIndex + 1) / cards.length) * 100}%` }}
                            />
                        </div>
                    </div>
                </div>

                <div className="flashcardsStats">
                    <div className="stat statCorrect">
                        <span className="statDotGreen">{knownCount}</span>
                    </div>
                    <div className="stat statWrong">
                        <span className="statDotRed">{unknownCount}</span>
                    </div>
                </div>
            </div>

            <div className="flashcards_container">
                <div className="flashcardsProgress">
                    {currentIndex + 1} / {cards.length}
                </div>
                <div className={`flashcard ${flipped ? 'flipped' : ''} ${highlight === 'know' ? 'flashcard-know' : ''} ${highlight === 'notknow' ? 'flashcard-notknow' : ''}`} onClick={handleFlip}>
                    <div className="flashcard_inner">
                        <div className="flashcard_inner_front">
                            <p className="flashcard_inner_front_word">{currentCard.word}</p>
                            {currentCard.part_of_speech && (
                                <span className="flashcard_inner_front_pos">{currentCard.part_of_speech}</span>
                            )}
                            <div className="flashcard_inner_front_flipHint">(нажмите, чтобы увидеть перевод)</div>
                        </div>
                        <div className="flashcard_inner_back">
                            <p className="flashcard_inner_back_translation">{currentCard.translation}</p>
                        </div>
                    </div>
                </div>
                <div className="flashcard_actions">
                    <button 
                        className={`flashcard_actions_Btn flashcard_actions_notKnowBtn`} 
                        onClick={handleNotKnow}
                    >
                        ❌ Не знаю
                        </button>
                    <button 
                        className={`flashcard_actions_Btn flashcard_actions_knowBtn`} 
                        onClick={handleKnow}
                    >
                        ✅ Знаю
                    </button>
                </div>
            </div>
        </>
    )
}

export default Flashcards