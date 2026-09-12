import { create } from "zustand";
import { getLists, createList } from "../API/api/dictionary";
import type { DictionaryListType } from "../types";
import type { LanguageOption } from "../types/types";

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
}

export const useDictionaryStore = create<DictionaryStore>((set) => ({
    lists: [],
    loading: false,
    error: null,

    fetchLists: async () => {
        set({ loading: true, error: null});
        
        try {
            const res = await getLists();
            const listsData = (res as { data?: any[] }).data ?? [];

            const lists = listsData.map(list =>({
                ...list,
                image: list.icon,
                itemsCounts: list.itemsCounts ?? 0
            }));

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
            source_lang: data.source_lang.name,
            target_lang: data.target_lang.name
        });
        const responseData = (res as any).data;

        const newList: DictionaryListType = {
            id: responseData.id,
            name: data.name,
            type: 'custom',
            sourceLang: data.source_lang,
            targetLang: data.target_lang,
            image: "../sources/icons/default.svg",
            itemsCounts: 0
        };

        set((state) => ({
            lists: [...state.lists, newList]
        }));
    }
}));