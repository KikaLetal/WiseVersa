import { apiClient } from "../client";



export const getLists = async() => {
    return apiClient("/dictionary/lists");
};

export const createList = async(data: {
    name: string;
    source_lang: string;
    target_lang: string;
}) => {
    return apiClient("/dictionary/lists",{
        method: "POST",
        body: JSON.stringify(data)
    });
};

export const addItemToDictList = async (data: {
    listId: number;
    word: string;
    translation: string;
    allowDuplicate?: boolean;
}) => {
    return apiClient('/dictionary/items', {
        method: 'POST',
        body: JSON.stringify({
            list_id: data.listId,
            word: data.word,
            translation: data.translation,
            allow_duplicate: data.allowDuplicate ?? false
        })
    });
};

export const getListItems = async (listId: number) => {
    return apiClient(`/dictionary/lists/${listId}/items`, { method: 'GET' });
};

export const deleteListItem = async (itemId: number, listId: number) => {
    return apiClient(`/dictionary/lists/${listId}/items/${itemId}`, { method: 'DELETE' });
};

export const updateItemMastered = async (itemId: number, mastered: boolean) => {
    return apiClient(`/dictionary/items/${itemId}/master`, {
        method: 'PATCH',
        body: JSON.stringify({ mastered })
    });
};

export const resetListProgress = async (listId: number) => {
    return apiClient(`/dictionary/lists/${listId}/reset`, {
        method: 'POST',
    });
};