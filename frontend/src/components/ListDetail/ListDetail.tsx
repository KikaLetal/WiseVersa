import React from 'react'
import WordCard from './WordCard/WordCard';
import './ListDetail.css'
import { getListItems, deleteListItem } from '../../API/api/dictionary';
import { useDictionaryStore } from '../../store/dictionaryStore';
import { isEmoji } from '../../utils/checker';

interface ListDetailProps {
    listId: number;
    listName: string;
    onBack: () => void;
    onOpenCards: () => void;
}

const ListDetail : React.FC<ListDetailProps> = ({
    listId, 
    listName, 
    onBack,
    onOpenCards
}) => {
    const [items, setItems] = React.useState<any[]>([]);
    const [loading, setLoading] = React.useState(true);
    const { lists } = useDictionaryStore();
    const list = lists.find(l => l.id === listId);

    const fetchItems = async () => {
        try {
            const res = await getListItems(listId);
            const data = res as any;
            if (data.success) {
                setItems(data.data);
            }
        } catch (error) {
            console.error(error);
        } finally {
            setLoading(false);
        }
    };

    React.useEffect(() => {
        fetchItems();
    }, [listId]);

    const handleDelete = async (itemId: number) => {
    try {
        await deleteListItem(itemId, listId);
        setItems(prev => prev.filter(item => item.id !== itemId));
    } catch (error) {
        console.error('Ошибка удаления', error);
    }
};

    if (loading) return <div>Загрузка...</div>;

    return (
        <div className="listDetail_wraper">
            <div className="listDetail_content">
                <button className="listDetail_backBtn" onClick={onBack}>
                    <div className="listDetail_backBtn_container">
                        <img src="../../../public/sources/icons/ArrowBack.svg" alt="" className="listDetail_backBtn_arrow" />
                        <p className="listDetail_backBtn_text">все списки</p>
                    </div>
                </button>
                <div className="listDetail_listInfo">
                    {list?.image && isEmoji(list.image) ?
                        <span className="listDetail_listInfo_IconEmoji">{list.image}</span> : 
                        <img src={`../sources/icons/${list?.image}`} alt="dictionaryList_container_top_img" className="listDetail_listInfo_IconImg" />
                    }
                    <div className="listDetail_listInfo_right">
                        <h2 className='listDetail_listInfo_right_title'>{listName}</h2>
                        {list?.itemsCounts !== undefined && (
                            <p className="listDetail_listInfo_right_listCount">записей: {list.itemsCounts ?? 0}
                                {list.type === 'custom' &&
                                list.sourceLang?.code &&
                                list.targetLang?.code && (
                                <> · {list.sourceLang.code} ➞ {list.targetLang.code}</>)}
                            </p>)}
                        {list?.id && (
                            <div className="listDetail_listInfo_right_cards">
                                <button 
                                    className="listDetail_listInfo_right_cards_btn"
                                    onClick={onOpenCards}
                                >
                                    <div className="listDetail_listInfo_right_cards_btn_container">
                                        <img src="../../../public/sources/icons/cards.svg" alt="" className="listDetail_listInfo_right_cards_btn_container_img" />
                                        <p className="listDetail_listInfo_right_cards_btn_container_text">Карточки</p>
                                    </div>
                                </button>
                            </div>
                        )}
                    </div>
                </div>
            </div>
            <div className="listDetail_elements">
                {items.length === 0 ? (
                    <p className="empty-message">В этом списке пока нет слов. Добавьте их через переводчик.</p>
                ) : (
                    <div className="words-list">
                        {items.map(item => (
                            <WordCard
                                key={item.id}
                                word={item.word}
                                translation={item.translation}
                                partOfSpeech={item.part_of_speech}
                                onDelete={() => handleDelete(item.id)}
                            />
                        ))}
                    </div>
                )}
            </div>
        </div>
    );
}

export default ListDetail