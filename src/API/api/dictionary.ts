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

