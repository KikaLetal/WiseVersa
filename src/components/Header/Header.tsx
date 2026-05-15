import type React from 'react'
import './Header.css'
import type { NavItem } from '../../types'

interface LogoProps {
    logoSrc?: string
    companyName?: string
}

const Logo : React.FC<LogoProps> = ({
    logoSrc = '../sources/icons/logo.svg', 
    companyName = 'WiseVersa' 
}) => {
    return (
        <div className="header_container_leftPart_name">
            <img src={logoSrc} alt="logo" className="header_container_leftPart_name_logo"/>
            <h1 className="header_container_leftPart_name_name">{companyName}</h1>
        </div>
    )
}

interface NavigationProps {
    items: NavItem[]
    onChangePage : (page : NavItem) => void 
}

const Navigation : React.FC<NavigationProps> = ({ items, onChangePage }) => {
    const handleClick = (e:React.MouseEvent<HTMLAnchorElement>, item : NavItem) => {
        e.preventDefault();
        onChangePage(item)
    }

    return(
        <div className="header_container_leftPart_navPanel">
            {items.map((item, index) => (
                <a
                key={index}
                href={item.href}
                onClick={(e) => handleClick(e, item)}
                className={item.isActive 
                    ? 'header_container_leftPart_navPanel_link_active'
                    : 'header_container_leftPart_navPanel_link'}
                >
                    {item.text}
                </a>
            ))}
        </div>
    )
}

interface HeaderProps {
    onChangePage : (page : NavItem) => void 
    burgerMenuSrc?: string
    navItems?: NavItem[]
}

const Header : React.FC<HeaderProps> = ({
    onChangePage,
    burgerMenuSrc = '../sources/icons/burgerMenu.svg',
    navItems,
}) => {
    return (
        <>
        <header className="header">
            <div className="header_container">
                <div className="header_container_leftPart">
                    <Logo/>
                    <Navigation items={navItems} onChangePage={onChangePage}/>
                </div>
                <button className="header_container_burgerMenu_btn">
                    <img src={burgerMenuSrc} alt="burger menu" className="header_container_burgerMenu_img"/>
                </button>
            </div>
        </header>
        </>
    )
}

export default Header
export { Logo, Navigation }