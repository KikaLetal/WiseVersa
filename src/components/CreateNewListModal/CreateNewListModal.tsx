import React from 'react'
import './CreateNewListModal.css'
import LanguagePicker from '../LanguagePicker/LanguagePicker';
import { LANGUAGES, type LanguageOption } from '../../types/types';
import { useDictionaryStore } from '../../store/dictionaryStore';

interface CreateNewListModalProps {
    isOpen : boolean
    onClose: () => void;
}

const CreateNewListModal : React.FC<CreateNewListModalProps> = ({isOpen, onClose}) => {
    if (!isOpen) return null;

    const { addList } = useDictionaryStore();
    const [name, setName] = React.useState("");
    const [sourceLanguage, setSourceLanguage] = React.useState(LANGUAGES[0])

    const [targetLanguage, setTargetLanguage] = React.useState(LANGUAGES[1])

    const isValid = name.trim().length > 0;

    const handleCreate = async () => {
        if (!name.trim()) return;
        
        await addList({
            name: name,
            source_lang: sourceLanguage.code,
            target_lang: targetLanguage.code
        });

        setName('');
        onClose();
    }

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
            <div className="modalOverlay" onClick={onClose}>
                <div className="modal_content" onClick={(e) => e.stopPropagation()}>
                    <h2 className="modal_content_title">Создать новый список</h2>

                    <div className="modal_content_name">
                        <h3 className="modal_content_name_title">Название</h3>
                        <input 
                            className='modal_content_name_input'
                            value={name}
                            onChange={(e) => setName(e.target.value)}
                            placeholder='Введите название списка'
                            maxLength={45}
                        />
                    </div>

                    <div className="modal_content_languages">
                        <div className="modal_content_languages_btn">
                            <h3 className="modal_content_languages_btn_title">Исходный язык</h3>
                            <div className="modal_content_languages_btn_wraper">
                                <LanguagePicker
                                language = {sourceLanguage}
                                languages= {LANGUAGES}
                                onLanguageClick = {(lang) => {changeLanguage(lang, "source");}}
                                className='modal_language_picker'
                                />
                            </div>
                        </div>

                        <button
                            className="translator_container_langs_swap_btn"
                            onClick={handleSwapLanguages}
                        >
                            <img src="../sources/icons/swaps.svg" alt="swap language" className="header_container_burgerMenu_img"/>
                        </button>

                        <div className="modal_content_languages_btn">
                            <h3 className="modal_content_languages_btn_title">Язык перевода</h3>
                            <div className="modal_content_languages_btn_wraper">
                                <LanguagePicker
                                    language = {targetLanguage}
                                    languages= {LANGUAGES}
                                    onLanguageClick = {(lang) => {changeLanguage(lang, "target");}}
                                    className='modal_language_picker'
                                />
                            </div>
                        </div>
                    </div>

                    <div className="modal_content_footer">
                        <button className="modal_content_footer_btn modal_content_footer_btn_cancel" onClick={onClose}>Отменить</button>
                        <button className="modal_content_footer_btn modal_content_footer_btn_create" onClick={handleCreate} disabled={!isValid}>Создать список</button>
                    </div>
                </div>
            </div>
    )
}

export default CreateNewListModal