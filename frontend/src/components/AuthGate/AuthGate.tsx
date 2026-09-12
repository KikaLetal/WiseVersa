import React from "react";
import { useAuthStore } from "../../store/authStore";
import AuthPage from "../../pages/AuthPage";
import Header from "../Header/Header";

const AuthGate: React.FC = () => {
    const { isAuth } = useAuthStore();

    const [page, setPage] = React.useState("translator");

    if (!isAuth) {
        return <AuthPage />;
    }

    return (
        <>
            <Header
                navItems={[
                    { href: "translator", text: "Перевод", isActive: true },
                    { href: "dictionary", text: "Словарь" },
                ]}
                onChangePage={(p) => setPage(p.href)}
            />

            {page === "translator" && <Translator />}
            {page === "dictionary" && <Dictionary />}
        </>
    );
}

export default AuthGate