import { create } from "zustand";
import { getLists, createList } from "../API/api/dictionary";
import type { DictionaryListType } from "../types";

interface DictionaryStore {
    lists: DictionaryListType[];
    loading: boolean;

    fetchLists: () => Promise<void>;
    addList: (data: {
        name: string;
        source_lang: string;
        target_lang: string;
    }) => Promise<void>;
}

export const useDictionaryStore = create<DictionaryStore>((set) => ({
    lists: [],
    loading: false,

    fetchLists: async () => {
        set({ loading: true });

        const data = await getLists();

        set({ 
            lists: data.data ?? data, 
            loading: false 
        });
    },

    addList: async (data) => {
        const res = await createList(data);

        const newList = {
            id: res.id ?? res.data?.id,
            name: data.name,
            source_lang: data.source_lang,
            target_lang: data.target_lang,
            image: "../sources/icons/default.svg"
        };

        set((state) => ({
            lists: [...state.lists, newList]
        }));
    }
}));