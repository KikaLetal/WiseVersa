import { create } from "zustand";
import { getLists, createList, addItemToDictList } from "../API/api/dictionary";
import type { DictionaryListType } from "../types";
import { LANGUAGES, type LanguageOption } from "../types/types";

interface DictionaryStore {
    lists: DictionaryListType[];
    loading: boolean;
    error: string | null;

    fetchLists: () => Promise<void>;
    addList: (data: {
        name: string;
        source_lang: LanguageOption;
        target_lang: LanguageOption;
    }) => Promise<void>;
    addItemToList: (data: {
        listId: number; 
        word: string; 
        translation: string; 
        allowDuplicate?: boolean 
    }) => Promise<void>;

}

export const useDictionaryStore = create<DictionaryStore>((set, get) => ({
    lists: [],
    loading: false,
    error: null,

    fetchLists: async () => {
        set({ loading: true, error: null});
        
        try {
            const res = await getLists();
            const listsData = (res as { data?: any[] }).data ?? [];

            const lists: DictionaryListType[] = listsData.map(list => {
                const sourceLangObj = LANGUAGES.find(l => l.code === list.source_lang);
                const targetLangObj = LANGUAGES.find(l => l.code === list.target_lang);

                return {
                    id: list.id,
                    name: list.name,
                    type: list.type,      
                    sourceLang: sourceLangObj ?? { code: list.source_lang, name: list.source_lang, flagSrc: '' }, 
                    targetLang: targetLangObj ?? { code: list.target_lang, name: list.target_lang, flagSrc: '' },
                    image: list.icon,
                    itemsCounts: list.itemsCounts ?? 0
                };
            });

            set({
                lists: lists,
                loading: false,
            });
        } catch (error) {
            set({ 
                loading: false,
                error: error instanceof Error ? error.message : 'Failed to fetch lists'
            });
        }
    },

    addList: async (data) => {
        const res = await createList({
            name: data.name,
            source_lang: data.source_lang.code,
            target_lang: data.target_lang.code
        });
        const responseData = (res as any).data;

        const newList: DictionaryListType = {
            id: responseData.id,
            name: data.name,
            type: 'custom',
            sourceLang: data.source_lang,
            targetLang: data.target_lang,
            image: responseData.icon,
            itemsCounts: 0
        };

        set((state) => ({
            lists: [...state.lists, newList]
        }));
    },

    addItemToList: async (data) => {
    try {
        await addItemToDictList(data);
        const { lists } = get();
        const updatedLists = lists.map((list: DictionaryListType) =>
            list.id === data.listId
                ? { ...list, itemsCounts: (list.itemsCounts || 0) + 1 }
                : list
        );
        set({ lists: updatedLists });
    } catch (error) {
        console.error('Failed to add item', error);
    }
}
}));