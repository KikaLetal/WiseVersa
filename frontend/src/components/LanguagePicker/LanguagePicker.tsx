import './LanguagePicker.css'
import React from 'react'
import type { LanguageOption } from '../../types/types'

interface LanguageOprionProps{
    language : LanguageOption,
    languages : LanguageOption[],
    onLanguageClick?: (lang: LanguageOption) => void;
    arrowIconSrc? : string

    className?: string;
}

const LanguagePicker : React.FC<LanguageOprionProps> = ({
    language, 
    languages,
    onLanguageClick,
    arrowIconSrc = '/sources/icons/arrow_down.svg',
    className = ''
}) => {
    const wrapperRef = React.useRef<HTMLDivElement>(null);

    const [isOpen, setIsOpen] = React.useState(false);

    const handleSelect = (lang : LanguageOption) =>{
        onLanguageClick?.(lang);
        setIsOpen(false);
    }

    React.useEffect(() => {
        const handleClickOutside = (e: MouseEvent) => {
            if (wrapperRef.current && !wrapperRef.current.contains(e.target as Node)) {
                setIsOpen(false);
            }
        };

        document.addEventListener("mousedown", handleClickOutside);
        return () => {
            document.removeEventListener("mousedown", handleClickOutside);
        }

    }, [])

    return (
        <>
            <div className={`language_picker_wrapper ${className}`} ref={wrapperRef}>
                <button 
                    className={`translator_container_langs_Btn ${className}`}
                    onClick={() => setIsOpen(!isOpen)}
                >
                    <div className={`translator_container_langs_Btn_content ${className}`}>
                        <div className={`translator_container_langs_Btn_content_leftPart ${className}`}>
                            <img
                                src={language.flagSrc} 
                                alt={`${language.name} flag`} 
                                className={`translator_container_langs_Btn_content_img ${className}`}
                            />
                            <p className={`translator_container_langs_Btn_content_text ${className}`}>{language.name}</p>
                        </div>
                        <img 
                            src={arrowIconSrc} 
                            alt="language arrow down" 
                            className={`translator_container_langs_Btn_content_langImg ${className}`}
                        />
                    </div>
                </button>

                {isOpen && (
                    <div className={`language_dropdown ${className}`}>
                        <div className={`language_dropdown_list ${className}`}>
                            {languages.map(lang => (
                                <div 
                                    className={`language_dropdown_list_element ${
                                        lang.code === language.code ? "active" : ""
                                    }`}
                                    key={lang.code}
                                    onClick={() => handleSelect(lang)}
                                >
 
                                    {lang.name}
                                </div>
                            ))}
                        </div>
                    </div>
                )}
            </div>
        </>
    )
}

export default LanguagePicker 
