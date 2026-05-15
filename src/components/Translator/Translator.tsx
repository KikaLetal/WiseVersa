import './Translator.css'
import React, { type ChangeEvent } from 'react'
import LanguagePicker from '../LanguagePicker/LanguagePicker'
import TranslatorArea from '../TranslatorArea/TranslatorArea'
import type { LanguageOption } from '../../types/types'

interface TranslatorProps{
    inputValue: string;
    onInputChange: (e: ChangeEvent<HTMLTextAreaElement | HTMLInputElement>) => void;
    outputValue: string;
    onClearInput: () => void

    maxLength?: number;
}

const languages: LanguageOption[] = [
    { code: "Ru", name: "Русский", flagSrc: "../sources/flags/image 1.png" },
    { code: "En", name: "Английский", flagSrc: "../sources/flags/image 2.png" },
];

const Translator: React.FC<TranslatorProps> = ({
    inputValue,
    onInputChange,
    outputValue,
    onClearInput,
    maxLength = 2000
}) => {
    const [sourceLanguage, setSourceLanguage] = React.useState(languages[0])

    const [targetLanguage, setTargetLanguage] = React.useState(languages[1])

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

    return (
        <div className="contentWraper">
            <div className="translator_container">
                    <div className="translator_container_langs">
                        <LanguagePicker
                            language = {sourceLanguage}
                            languages= {languages}
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
                            languages= {languages}
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

                        <div className="translator_side">
                            <TranslatorArea
                                value={outputValue}
                                onChange={() => {}}
                                placeholder='Перевод'
                                readOnly={true}
                            />
                        </div>
                    </div>

                    
                    
            </div>
        </div>
    )
}

export default Translator 
