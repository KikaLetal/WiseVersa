import type { LanguageOption } from "./types"

export interface NavItem {
    href: string
    text: string
    isActive?: boolean
}

export interface DictionaryListType{
    id?: number
    name : string
    type?: 'custom' | 'favorites' | 'history'
    itemsCounts?: number
    sourceLang?: LanguageOption
    targetLang?: LanguageOption
    image: string
}
  
export type Language = 'Русский' | 'Английский' | 'Немецкий' | 'Французский'