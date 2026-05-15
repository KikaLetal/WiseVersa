import '../styles/App.css'
import Header from "../components/Header/Header"
import Translator from "../components/Translator/Translator"
import React from 'react'
import type { NavItem, DictionaryListType } from '../types/index.ts'
import Dictionary from '../components/Dictionary/Dictionary.tsx'
import CreateNewListModal from '../components/CreateNewListModal/CreateNewListModal.tsx'
import { useDictionaryStore } from '../store/dictionaryStore.ts'
import AuthPage from '../pages/AuthPage.tsx'
import { useAuthStore } from '../store/authStore.ts'
import AuthGate from '../components/AuthGate/AuthGate.tsx'

const App : React.FC = () =>{
    const defaultItems: NavItem[] = [
        { href: 'translator', text: 'Перевод', isActive: true },
        { href: 'blog', text: 'Блог' },
        { href: 'dictionary', text: 'Мой словарь' },
        { href: 'history', text: 'История' }
    ]

    const [navItems, setNavItems] = React.useState<NavItem[]>(defaultItems);
    const [currentPage, setCurrentPage] = React.useState<string>('Перевод');

    const { isAuth, loading, hydrate } = useAuthStore();
    const [page, setPage] = React.useState("translator");

    const { fetchLists } = useDictionaryStore();

    React.useEffect(() => {
        hydrate();
    }, [hydrate]);

    React.useEffect(() => {
        if (isAuth) {
            fetchLists();
        }
    }, [isAuth]);
    
    const [inputText, setInputText] = React.useState('');
    const [outputText, setOutputText] = React.useState('');

    const onChangePage = (page : NavItem) => { 
        const updatedItems = navItems.map(item => ({
            ...item,
            isActive: item.text === page.text
        }));
        setNavItems(updatedItems);
        setCurrentPage(page.href);
    }

    const onClearInput = () => setInputText("");

    // модалки
    const [isCreateModalOpen, setIsCreateModalOpen] = React.useState(false);

    const renderContent = () => {
        switch(currentPage) {
            case 'translator':
                return (
                    <Translator 
                        inputValue={inputText}
                        onInputChange={(e: React.ChangeEvent<HTMLTextAreaElement | HTMLInputElement>) =>
                            setInputText(e.target.value)}
                        outputValue={outputText}
                        onClearInput={onClearInput}
                    />
                );
            case 'blog':
                return (
                    <Translator 
                        inputValue={inputText}
                        onInputChange={(e: React.ChangeEvent<HTMLTextAreaElement | HTMLInputElement>) =>
                            setInputText(e.target.value)}
                        outputValue={outputText}
                        onClearInput={onClearInput}
                    />
                );
            case 'dictionary':
                return (
                    <>
                        <Dictionary 
                            onCreateClick={ () => setIsCreateModalOpen(true)}
                        />

                        {isCreateModalOpen && (
                            <CreateNewListModal 
                                isOpen={isCreateModalOpen}
                                onClose={ () => setIsCreateModalOpen(false)}
                            />
                        )}
                    </>
                    // <Dictionary history={historyList} favourite={favouriteList}/>
                );
            case 'history':
                return (
                    <Translator 
                        inputValue={inputText}
                        onInputChange={(e: React.ChangeEvent<HTMLTextAreaElement | HTMLInputElement>) =>
                            setInputText(e.target.value)}
                        outputValue={outputText}
                        onClearInput={onClearInput}
                    />
                );
            default:
                return (
                    <Translator 
                        inputValue={inputText}
                        onInputChange={(e: React.ChangeEvent<HTMLTextAreaElement | HTMLInputElement>) =>
                            setInputText(e.target.value)}
                        outputValue={outputText}
                        onClearInput={onClearInput}
                    />
                );
        }
    }

    if (loading) {
        return <div>Loading...</div>;
    }

    if (!isAuth) {
        return <AuthPage />;
    }

    return (
        <>
            <Header navItems={navItems} onChangePage={onChangePage}/>
            {renderContent()}
        </>
    )
}

export default App
