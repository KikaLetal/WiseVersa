import React from 'react'
import './Registration.css'

interface RegistrationProps {
    onSwitch: () => void;
    onNext: (data: {
        username: string;
        password: string;
    }) => void;
}

const Registration : React.FC<RegistrationProps> = ({ onSwitch, onNext }) => {
    const [showPassword, setShowPassword] = React.useState(false);

    const [username, setUsername] = React.useState("");
    const [password, setPassword] = React.useState("");
    const [repeatPassword, setRepeatPassword] = React.useState("");

    return (
        <>
            <div className="registration_wraper">
                <div className="registration_container">
                    <h2 className='registration_container_title'>Регистрация</h2>

                    <form 
                        className='registration_container_form'
                        onSubmit={(e) => {
                            e.preventDefault();

                            if (!username.trim()) {
                                alert("Введите имя");
                                return;
                            }

                            if (password.length < 6) {
                                alert("Пароль должен содержать не менее 6 символов");
                                return;
                            }

                            if (password !== repeatPassword) {
                                alert("Пароли не совпадают");
                                return;
                            }

                            onNext({
                                username,
                                password
                            });
                        }}
                    >
                        <div className="registration_form_areas">
                            <div className="registration_form_areas_inputs">
                                <input 
                                    value={username}
                                    onChange={(e) => setUsername(e.target.value)}
                                    type="text" 
                                    placeholder="Имя пользователя" 
                                    className='registration_form_areas_inputs_block registration_form_areas_inputs_input registration_login'
                                    maxLength={50}
                                />
                                <div className="registration_form_areas_inputs_password">
                                    <input 
                                        value={password}
                                        onChange={(e) => setPassword(e.target.value)}
                                        type={showPassword ? "text" : "password"} 
                                        placeholder="Пароль" 
                                        className='registration_form_areas_inputs_input registration_password'
                                    />
                                        <button 
                                            type="button" 
                                            className='registration_form_areas_inputs_password_showBtn'
                                            onClick={() => setShowPassword(prev => !prev)}
                                        >
                                            <img 
                                                src={
                                                    showPassword
                                                        ? "../sources/icons/opened_eye.svg"
                                                        : "../sources/icons/closed_eye.svg"
                                                } 
                                                alt="show password" 
                                                className='registration_form_areas_inputs_password_showBtn_img'
                                            />
                                        </button>
                                </div>
                                <input 
                                    value={repeatPassword}
                                    onChange={(e) => setRepeatPassword(e.target.value)}
                                    type="password"
                                    placeholder="Повторите пароль" 
                                    className='registration_form_areas_inputs_block registration_form_areas_inputs_input registration_login'
                                />
                            </div>
                        </div>
                        <button type="submit" className='registration_Enter'>Продолжить</button>
                    </form>

                    <button className="registration_switchToReg" onClick={onSwitch}>
                        Уже есть аккаунт?
                    </button>
                </div>
            </div>
        </>
    )
}

export default Registration