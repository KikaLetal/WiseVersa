import React from 'react'
import './AuthPage.css'
import Autorization from '../components/Auth/Autorization.tsx'
import Registration from '../components/Auth/Registration.tsx'
import AvatarStep from '../components/Auth/AvatarStep.tsx'
import { useAuthStore } from '../store/authStore.ts'

const AuthPage : React.FC = () => {
    const { register, login } = useAuthStore();

    const [authMode, setAuthMode] = React.useState<"login" | "register">("login");

    const [step, setStep] = React.useState<1 | 2>(1);

    const [username, setUsername] = React.useState("");
    const [password, setPassword] = React.useState("");

    if (authMode === "login") {
        return (
            <Autorization
                onSwitch={() => setAuthMode("register")}
                onLogin={login}
            />
        );
    }

    return step === 1 ? (
        <Registration
            onSwitch={() => setAuthMode("login")}
            onNext={(data) => {
                setUsername(data.username);
                setPassword(data.password);
                setStep(2);
            }}
        />
    ) : (
        <AvatarStep
            onNext={async (avatar) => {
                try {
                    await register({
                        username,
                        password,
                        avatar
                    });

                    setAuthMode("login");
                    setStep(1); 

                } catch (e: any) {
                    alert(e.message);
                }
            }}
        />
    );
};

export default AuthPage