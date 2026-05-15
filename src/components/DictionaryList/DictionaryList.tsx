import type React from 'react'
import './DictionaryList.css'
import { type DictionaryListType } from '../../types'

interface DictionaryListProps {
    dictionaryList : DictionaryListType
    onClick?: () => void;
}

const DictionaryList : React.FC<DictionaryListProps> = ({dictionaryList, onClick}) => {
    return (
        <>
            <div className={`dictionaryList_container ${onClick ? "cursor_pointer" : ""}`} onClick={onClick}>
                <div className="dictionaryList_container_top">
                    <img src={dictionaryList.image} alt="dictionaryList_container_top_img" className="dictionaryList_container_img" />
                    <div className="dictionaryList_container_top_info">
                        <p className="dictionaryList_container_top_info_listName"> {dictionaryList.name} </p>

                        {dictionaryList.itemsCounts !== undefined && (
                            <p className="dictionaryList_container_top_info_listCount">записей: {dictionaryList.itemsCounts && dictionaryList.itemsCounts.toString()}
                            {dictionaryList.sourceLang && dictionaryList.targetLang && (<> · {dictionaryList.sourceLang.code} ➞ {dictionaryList.targetLang.code}</>)}
                        </p>)}
                    </div>
                    
                </div>
                {dictionaryList.id && (
                    <div className="dictionaryList_container_bottom">
                        <button className="dictionaryList_container_bottom_button">Карточки</button>
                        <button className="dictionaryList_container_bottom_button">Открыть список</button>
                    </div>
                )}
            </div>
        </>
    )
}

export default DictionaryList