export interface LanguageOption {
    code: string
    name: string
    flagSrc: string
}

export interface TranslationState {
    sourceLanguage: LanguageOption
    targetLanguage: LanguageOption
    sourceText: string
    translatedText: string
    isLoading: boolean
    error: string | null
}

export interface User {
    id: number;
    username: string;
    profilePicture?: string;
}

export const LANGUAGES: LanguageOption[] = [
    { code: 'ru', name: 'Русский', flagSrc: '/sources/flags/image 1.png' },
    { code: 'en', name: 'Английский', flagSrc: '/sources/flags/image 2.png' },
    { code: 'de', name: 'Немецкий', flagSrc: '/sources/flags/germany.png' },
    { code: 'fr', name: 'Французский', flagSrc: '/sources/flags/france.png' },
]  
