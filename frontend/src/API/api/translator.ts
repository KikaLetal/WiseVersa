import { apiClient } from "../client";

export const translateText = async(data: {
    word: string;
    sourceLang: string;
    targetLang: string;
}) => {
    return apiClient("/translate", {
        method: 'POST',
        body: JSON.stringify(data)
    });
};

