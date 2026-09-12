import './Translator.css'
import React, { useEffect, useRef, type ChangeEvent } from 'react'
import LanguagePicker from '../LanguagePicker/LanguagePicker'
import TranslatorArea from '../TranslatorArea/TranslatorArea'
import type { LanguageOption, TranslationResponse, TranslationVariant } from '../../types/types'
import { translateText } from '../../API/api/translator'
import TranslationCard from './TranslationCard/TranslationCard'
import { useDictionaryStore } from '../../store/dictionaryStore'
import { LANGUAGES } from '../../types/types'

interface TranslatorProps{
    inputValue: string;
    onInputChange: (e: ChangeEvent<HTMLTextAreaElement | HTMLInputElement>) => void;
    onClearInput: () => void
    maxLength?: number;
}

const Translator: React.FC<TranslatorProps> = ({
    inputValue,
    onInputChange,
    onClearInput,
    maxLength = 2000
}) => {
    const [sourceLanguage, setSourceLanguage] = React.useState(LANGUAGES[0]);
    const [targetLanguage, setTargetLanguage] = React.useState(LANGUAGES[1]);
    const [translationResponse, setTranslationResponse] = React.useState<TranslationResponse | null>(null);
    const [isTranslating, setIsTranslating] = React.useState(false);

    const [selectedWord, setSelectedWord] = React.useState('');
    const [selectedTranslation, setSelectedTranslation] = React.useState('');
    const [showListModal, setShowListModal] = React.useState(false);

    const { lists, addItemToList } = useDictionaryStore();

    const favoritesList = lists.find(l => l.type === 'favorites');
    const historyList = lists.find(l => l.type === 'history');

    const lastSavedRef = useRef<{ word: string; translation: string } | null>(null);

    const handleAddToFavorites = async (word: string, translation: string) => {
        if (!favoritesList) return;
        await addItemToList({
            listId: favoritesList.id!,
            word,
            translation,
            allowDuplicate: false
        });
    };

    const handleAddToList = (word: string, translation: string) => {
        setSelectedWord(word);
        setSelectedTranslation(translation);
        setShowListModal(true);
    };

    const performTranslation = async (text: string, from: string, to: string) => {
        if(!text.trim()){
            setTranslationResponse(null);
            return;
        }

        setIsTranslating(true);

        try{
            const res = await translateText({
                word: text,
                sourceLang: from.toLowerCase(),
                targetLang: to.toLowerCase()
            });
            const data = res as any;

            if (!data.success) {
                setTranslationResponse(null);
                return;
            }

            if (data.source === 'yandex') {
                setTranslationResponse({
                    success: true,
                    source: 'yandex',
                    word: data.word,
                    transcription: data.transcription,
                    translations: data.translations.map((t: any) => ({
                        text: t.text,
                        partOfSpeech: t.pos,
                        gender: t.gender,
                        examples: t.examples
                    }))
                });
            } else {
                setTranslationResponse({
                    success: true,
                    source: 'mymemory',
                    word: data.word,
                    translation: data.translation
                });
            }
        } catch(error){
            console.error(error);
            setTranslationResponse(null);
        } finally {
            setIsTranslating(false);
        }
    };

    useEffect(() => {
        const handler = setTimeout(() => {
            performTranslation(inputValue, sourceLanguage.code, targetLanguage.code);   
        }, 600);
        return () => clearTimeout(handler);
    }, [inputValue, sourceLanguage, targetLanguage]);

    useEffect(() => {
        if (inputValue.trim()) {
            performTranslation(inputValue, sourceLanguage.code, targetLanguage.code);
        }
    }, [sourceLanguage, targetLanguage]);

    useEffect(() =>{
        if (!translationResponse?.success) return;

        const sourceWord = translationResponse.word;
        let firstTranslation: string | null = null;
        if (translationResponse.source === 'yandex' && translationResponse.translations?.length) {
            firstTranslation = translationResponse.translations[0].text;
        } else if (translationResponse.source === 'mymemory' && translationResponse.translation) {
            firstTranslation = translationResponse.translation;
        }
        if (!sourceWord || !firstTranslation) return;

        if (
            lastSavedRef.current?.word === sourceWord &&
            lastSavedRef.current?.translation === firstTranslation
        ) {
            return;
        }

        if (historyList) {
            addItemToList({
                listId: historyList.id!,
                word: sourceWord,
                translation: firstTranslation,
                allowDuplicate: true,
            });
            lastSavedRef.current = { word: sourceWord, translation: firstTranslation };
        }
    }, [translationResponse, historyList, addItemToList]);

    const handleSwapLanguages = () => {
        const temp = sourceLanguage;
        setSourceLanguage(targetLanguage);
        setTargetLanguage(temp);
    };

    const changeLanguage = (lang : LanguageOption, type: "source" | "target") => {
        const isSameAsOpposite =
            type === "source"
                ? lang.code === targetLanguage.code
                : lang.code === sourceLanguage.code;

        if (isSameAsOpposite) {
            handleSwapLanguages();
            return;
        }

        if (type === "source") {
            setSourceLanguage(lang);
        }
        if (type === "target") {
            setTargetLanguage(lang);
        }
    };

    const renderTranslation = () => {
        if (!translationResponse) return null;

        if (translationResponse.source === 'yandex' && translationResponse.translations) {
            return translationResponse.translations
            .slice(0, 3)
            .map((variant, idx) => (
                <TranslationCard
                    key={idx}
                    variant={variant}
                    sourceWord={translationResponse.word}
                    onAddToFavorites={handleAddToFavorites}
                    onAddToList={handleAddToList}
                />
            ));
        }

        if (translationResponse.source === 'mymemory' && translationResponse.translation) {
            const variant: TranslationVariant = {
                text: translationResponse.translation,
                partOfSpeech: null,
            };
            return (
                <TranslationCard
                    variant={variant}
                    sourceWord={translationResponse.word}
                    onAddToFavorites={handleAddToFavorites}
                    onAddToList={handleAddToList}
                />
            );
        }

        return <div className="no-translation">Перевод не найден</div>;
    }

    return (
        <div className="contentWraper">
            <div className="translator_container">
                    <div className="translator_container_langs">
                        <LanguagePicker
                            language = {sourceLanguage}
                            languages= {LANGUAGES}
                            onLanguageClick = {(lang) => {changeLanguage(lang, "source");}}
                        
                        />

                        <button
                            className="translator_container_langs_swap_btn"
                            onClick={handleSwapLanguages}
                        >
                            <img src="../sources/icons/swaps.svg" alt="swap language" className="header_container_burgerMenu_img"/>
                        </button>

                        <LanguagePicker
                            language = {targetLanguage}
                            languages= {LANGUAGES}
                            onLanguageClick = {(lang) => {changeLanguage(lang, "target");}}
                        />
                    </div>
                    <div className="translator_container_translatorTexts">
                        <div className="translator_side">
                            <TranslatorArea
                                value={inputValue}
                                onChange={onInputChange}
                                onClear={onClearInput}
                                placeholder='Введите текст для перевода'
                                maxLength={maxLength}
                            />

                            <p className='translator_container_wordsLeng'> 
                                {inputValue.length} / {maxLength} 
                            </p>
                        </div>

                        <div className="translator_container_translatorTexts_splitor" />

                        <div className="translator_side translator_side_output">
                            {isTranslating && <div className="translating-indicator">Переводим...</div>}
                            <div className="translations-list">
                                {renderTranslation()}
                            </div>
                        </div>
                    </div>

                {showListModal && (
                    <div className="showListModal_overlay" onClick={() => setShowListModal(false)}>
                        <div className="showListModal_content" onClick={(e) => e.stopPropagation()}>
                            <h3 className="showListModal_content_title">Выберите список</h3>
                            {(() => {
                                const compatibleLists = lists.filter(
                                    (l) =>
                                        l.type === 'custom' &&
                                        l.sourceLang?.code.toLowerCase() === sourceLanguage.code.toLowerCase() &&
                                        l.targetLang?.code.toLowerCase() === targetLanguage.code.toLowerCase()
                                );

                                if (compatibleLists.length === 0) {
                                    return (
                                        <p className="showListModal_noListsMessage">
                                            Нет подходящих списков с языками {sourceLanguage.name} → {targetLanguage.name}
                                        </p>
                                    );
                                }

                                return compatibleLists.map((list) => (
                                    <button
                                        key={list.id}
                                        className="showListModal_listChoiceBtn"
                                        onClick={async () => {
                                            await addItemToList({
                                                listId: list.id!,
                                                word: selectedWord,
                                                translation: selectedTranslation,
                                                allowDuplicate: false,
                                            });
                                            setShowListModal(false);
                                        }}
                                    >
                                        {list.name}
                                    </button>
                                ));
                            })()}
                            <button className="showListModal_cancelBtn" onClick={() => setShowListModal(false)}>
                                Отмена
                            </button>
                        </div>
                    </div>
                )}
                    
            </div>
        </div>
    )
}

export default Translator 
