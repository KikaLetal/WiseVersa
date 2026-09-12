import type React from 'react'
import './DictionaryList.css'
import { type DictionaryListType } from '../../types'
import { isEmoji } from '../../utils/checker'

interface DictionaryListProps {
    dictionaryList : DictionaryListType
    onClick?: () => void;
    onStartCards?: () => void;
}

const DictionaryList : React.FC<DictionaryListProps> = ({dictionaryList, onClick, onStartCards}) => {
    return (
        <>
            <div className={`dictionaryList_container ${onClick ? "cursor_pointer" : ""}`}>
                <div className="dictionaryList_container_top">
                    {dictionaryList.image && isEmoji(dictionaryList.image) ?
                        <span className="dictionaryList_container_emoji">{dictionaryList.image}</span> : 
                        <img src={`../sources/icons/${dictionaryList.image}`} alt="dictionaryList_container_top_img" className="dictionaryList_container_img" />
                    }
                    <div className="dictionaryList_container_top_info">
                        <p className="dictionaryList_container_top_info_listName"> {dictionaryList.name} </p>

                        {dictionaryList.itemsCounts !== undefined && (
                            <p className="dictionaryList_container_top_info_listCount">записей: {dictionaryList.itemsCounts && dictionaryList.itemsCounts.toString()}
                            {dictionaryList.type === 'custom' &&
                            dictionaryList.sourceLang?.code &&
                             dictionaryList.targetLang?.code && (
                             <> · {dictionaryList.sourceLang.code} ➞ {dictionaryList.targetLang.code}</>)}
                        </p>)}
                    </div>
                    
                </div>
                {dictionaryList.id && (
                    <div className="dictionaryList_container_bottom">
                        <button
                            className="dictionaryList_container_bottom_button"
                            onClick={(e) => { 
                                e.stopPropagation(); 
                                onStartCards?.(); 
                            }}
                        >
                            Карточки
                        </button>
                        <button
                            className="dictionaryList_container_bottom_button"
                            onClick={(e) => { 
                                e.stopPropagation(); 
                                onClick?.(); 
                            }}
                        >
                            Открыть список
                        </button>
                    </div>
                )}
            </div>
        </>
    )
}

export default DictionaryList