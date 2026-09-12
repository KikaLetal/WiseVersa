import React from 'react'
import './Dictionary.css'
import DictionaryList from '../DictionaryList/DictionaryList'
import { useDictionaryStore } from '../../store/dictionaryStore';
import ListDetail from '../ListDetail/ListDetail';
import Flashcards from '../Flashcards/Flashcards';

interface DictionaryProps {
    onCreateClick : () => void;
}

const Dictionary : React.FC<DictionaryProps> = ({onCreateClick}) => {
    const { lists } = useDictionaryStore();
    const [selectedList, setSelectedList] = React.useState<{ id: number; name: string } | null>(null);
    const [selectedCards, setSelectedCards] = React.useState<{ id: number; name: string } | null>(null);

    if (selectedCards) {
        return (
            <Flashcards
                listId={selectedCards.id}
                listName={selectedCards.name}
                onBack={() => setSelectedCards(null)}
            />
        );
    }
    
    if (selectedList) {
        return (
            <ListDetail
                listId={selectedList.id}
                listName={selectedList.name}
                onBack={() => setSelectedList(null)}
                onOpenCards={() => setSelectedCards({ id: selectedList.id, name: selectedList.name })}
            />
        );
    }

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
                                    <DictionaryList 
                                        key={list.id} 
                                        dictionaryList={list} 
                                        onClick={() => setSelectedList({ id: list.id!, name: list.name })}
                                        onStartCards={() => {
                                            setSelectedCards({ id: list.id!, name: list.name });
                                        }}
                                    />
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