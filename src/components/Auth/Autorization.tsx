import React from 'react'
import './Autorization.css'

interface AutorizationProps {
    onSwitch: () => void;
    onLogin: (data: { username: string; password: string, rememberMe: boolean }) => Promise<void>;
}

const Autorization : React.FC<AutorizationProps> = ({ onSwitch, onLogin }) => {
    const [showPassword, setShowPassword] = React.useState(false);

    const [rememberMe, setRememberMe] = React.useState(false);

    const [username, setUsername] = React.useState("");
    const [password, setPassword] = React.useState("");

    const [errors, setErrors] = React.useState<{ username?: string; password?: string }>({});
    const [isLoading, setIsLoading] = React.useState(false);

    const validateForm = (): boolean => {
        const newErrors: {username?: string, password?: string} = {};

        if (!username.trim()) {
            newErrors.username = "Введите имя пользователя";
        } else if (username.length < 3) {
            newErrors.username = "Минимум 3 символа";
        } else if (username.length > 50) {
            newErrors.username = "Максимум 50 символов";
        }
        
        if (!password) {
            newErrors.password = "Введите пароль";
        } else if (password.length < 6) {
            newErrors.password = "Минимум 6 символов";
        }
        
        setErrors(newErrors);
        return Object.keys(newErrors).length === 0;
    };

    const handleBlur = (field: 'username' | 'password') => {
        if (field === 'username') {
            if (!username.trim()) {
                setErrors(prev => ({ ...prev, username: "Введите имя пользователя" }));
            } else if (username.length < 3) {
                setErrors(prev => ({ ...prev, username: "Минимум 3 символа" }));
            } else if (username.length > 50) {
                setErrors(prev => ({ ...prev, username: "Максимум 50 символов" }));
            } else {
                setErrors(prev => ({ ...prev, username: undefined }));
            }
        }
        
        if (field === 'password') {
            if (!password) {
                setErrors(prev => ({ ...prev, password: "Введите пароль" }));
            } else if (password.length < 6) {
                setErrors(prev => ({ ...prev, password: "Минимум 6 символов" }));
            } else {
                setErrors(prev => ({ ...prev, password: undefined }));
            }
        }
    };

    const handleSubmit = async (e: React.FormEvent) => {
        e.preventDefault();
        
        if (!validateForm()) return;
        
        setIsLoading(true);
        try {
            await onLogin({ username, password, rememberMe });
        } catch (error:any) {

            console.error('Login error:', error);

            let errorMessage = "Неверное имя пользователя или пароль";
            
            if (error?.message === "Wrong username or password") {
                errorMessage = "Неверное имя пользователя или пароль";
            } else if (typeof error === 'string') {
                errorMessage = error;
            } else if (error?.message) {
                errorMessage = error.message;
            }
            
            setErrors({ password: errorMessage });
        } finally {
            setIsLoading(false);
        }
    };

    return (
        <>
            <div className="autorization_wraper">
                <div className="autorization_container">
                    <h2 className='autorization_container_title'>Вход</h2>

                    <form 
                        className='autorization_container_form'
                        onSubmit={handleSubmit}
                    >
                        <div className="autorization_form_areas">
                            <div className="autorization_form_areas_inputs">
                                <div className="autorization_form_areas_inputs_input_and_error">
                                    <input 
                                        value={username}
                                        onChange={(e) => setUsername(e.target.value)}
                                        onBlur={() => handleBlur("username")}
                                        type="text" 
                                        placeholder="Имя пользователя" 
                                        className='autorization_form_areas_inputs_block autorization_form_areas_inputs_input autorization_login'
                                        maxLength={50}
                                    />
                                    {errors.username && (
                                        <span className="error-message" style={{ color: 'red', fontSize: '12px', marginTop: '5px', display: 'block' }}>
                                            {errors.username}
                                        </span>
                                    )}
                                </div>
                                <div className="autorization_form_areas_inputs_input_and_error">
                                    <div className="autorization_form_areas_inputs_password">
                                        <input 
                                            type={showPassword ? "text" : "password"} 
                                            placeholder="Пароль" 
                                            value={password}
                                            onChange={(e) => setPassword(e.target.value)}
                                            onBlur={() => handleBlur('password')}
                                            className='autorization_form_areas_inputs_input autorization_password'
                                        />
                                            <button 
                                                type="button" 
                                                className='autorization_form_areas_inputs_password_showBtn'
                                                onClick={() => setShowPassword(prev => !prev)}
                                            >
                                                <img 
                                                    src={
                                                        showPassword
                                                            ? "../sources/icons/opened_eye.svg"
                                                            : "../sources/icons/closed_eye.svg"
                                                    } 
                                                    alt="show password" 
                                                    className='autorization_form_areas_inputs_password_showBtn_img'
                                                />
                                            </button>
                                    
                                    </div>
                                    {errors.password && (
                                            <span className="error-message" style={{ color: 'red', fontSize: '12px', marginTop: '5px', display: 'block' }}>
                                                {errors.password}
                                            </span>
                                    )}
                                </div>
                            </div>
                            <div className="autorization_form_areas_rememberme">
                                <label className="autorization_form_areas_rememberme_label" >
                                    <input 
                                    type="checkbox" 
                                    className="autorization_form_areas_rememberme_btn" 
                                        checked={rememberMe}
                                        onChange={(e) => setRememberMe(e.target.checked)}
                                />
                                Запомнить меня</label>
                            </div>
                        </div>
                        <button type="submit" className='autorization_Enter'>Войти</button>
                    </form>

                    <button className="autorization_switchToReg" onClick={onSwitch}>
                        Нет аккаунта?
                    </button>
                </div>
            </div>
        </>
    )
}

export default Autorization