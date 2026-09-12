export interface LanguageOption {
    code: string
    name: string
    flagSrc: string
}

export interface TranslationVariant {
    text: string;
    partOfSpeech: string | null;    
    gender?: string | null;          
}

export interface TranslationResponse {
    success: boolean;
    source: 'yandex' | 'mymemory';
    word: string;                     
    transcription?: string | null;   
    translations?: TranslationVariant[]; 
    translation?: string;        
}

export interface TranslationState {
    sourceLanguage: LanguageOption
    targetLanguage: LanguageOption
    sourceText: string
    translatedText: string
    isLoading: boolean
    error: string | null
}

export interface BlogPost {
    id: number;
    author_id: number;
    username: string;
    profile_picture: string;
    content: string;
    preview: string | null;
    created_at: string;
    updated_at: string;
    likes_count: number;
    user_liked: boolean;
}

export interface CreatePostData {
    content: string;
    preview?: string;
}

export interface UpdatePostData {
    content: string;
    preview?: string;
}

export interface CardItem {
    id: number;
    word: string;
    translation: string;
    part_of_speech?: string;
    mastered: boolean;
}

export interface User {
    id: number;
    username: string;
    profilePicture?: string;
    role?: 'user' | 'admin' | 'moderator';
}

export const LANGUAGES: LanguageOption[] = [
    { code: 'ru', name: 'Русский', flagSrc: '/sources/flags/image 1.png' },
    { code: 'en', name: 'Английский', flagSrc: '/sources/flags/image 2.png' },
    { code: 'de', name: 'Немецкий', flagSrc: '/sources/flags/image 3.png' },
    { code: 'fr', name: 'Французский', flagSrc: '/sources/flags/image 4.png' },
]  
