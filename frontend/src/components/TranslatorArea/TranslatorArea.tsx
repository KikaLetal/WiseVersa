import './TranslatorArea.css'
import React, { type ChangeEvent } from 'react'

interface TranslatorAreaProps{
    value: string
    onChange: (e: ChangeEvent<HTMLTextAreaElement>) => void
    placeholder: string
    onClear?: () => void
    readOnly?: boolean
    maxLength?: number
}

const TranslatorArea: React.FC<TranslatorAreaProps> = ({
    value,
    onChange,
    placeholder,
    onClear,
    readOnly = false,
    maxLength = 2000
}) => {
    return (
        <div className="translator_container_translatorTexts_input">

            { value.length > 0 && !readOnly && (
                <button
                    type="button"
                    className='clear_btn'
                    onClick={() => onClear?.()}
                >
                    ✕
                </button>
            )

            }

            <textarea 
                value={value}
                onChange={onChange}
                className="translator_container_translatorTexts_input_inputArea" 
                placeholder={placeholder} 
                readOnly={readOnly}
                maxLength={maxLength}
            />
        </div>
    )
}

export default TranslatorArea 
