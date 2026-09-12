import type React from 'react'
import './Dictionary.css'
import DictionaryList from '../DictionaryList/DictionaryList'
import { useDictionaryStore } from '../../store/dictionaryStore';


interface DictionaryProps {
    onCreateClick : () => void;
}

const Dictionary : React.FC<DictionaryProps> = ({onCreateClick}) => {
    const { lists } = useDictionaryStore();

    return (
        <>
            <div className="BackgroundWraper">
                <div className="contentWraper">
                    <div className="dictionaryNavigation">
                        <button className="dictionaryNavigation_createNewButton" onClick={onCreateClick}>
                            <img src="../sources/icons/plusList.svg" alt="plus new list"  className='dictionaryNavigation_createNewButton_img'/>
                            <p className='dictionaryNavigation_createNewButton_text'>Создать список</p>
                        </button>
                    </div>
                    
                    <div className="dictionaryList">
                        {lists.length > 0 ? (
                            lists.map((list) =>
                                list.id ? (
                                    <DictionaryList key={list.id.toString()} dictionaryList={list}/>
                                ) : null
                            )
                        ) : (                        
                            <DictionaryList
                                dictionaryList= {{name: 'Создать новый список', image: "../sources/icons/addList.svg"}}
                                onClick={onCreateClick}
                            />
                        )}
                    </div>
                </div>
            </div>
        </>
    )
}

export default Dictionary