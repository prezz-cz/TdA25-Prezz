import { Outlet } from "react-router-dom";
import './index.css';
import { Link } from "react-router-dom";
import { useState } from "react";

const Layout = () => {
    const [isSidenavOpen, setIsSidenavOpen] = useState(false);

    const toggleSidenav = () => {
        setIsSidenavOpen(!isSidenavOpen);
    };

    const closeSidenav = () => {
        setIsSidenavOpen(false);
    };

    return ( 
        <div className="page">
            <div className="body">
                <header>
                    <img src="/logo.png" alt="" />
                    <nav className="desk">
                        <Link to={"/"}>Domů</Link>
                        <Link to={"/list"}>Seznam úloh</Link>
                    </nav>
                    <Link to={"/game"} className="btn desk">Zahrát si</Link>
                    <img src="/menu.png" alt="menu" className="mob" onClick={toggleSidenav}/>
                </header>

                <div className={`sidenav ${isSidenavOpen ? 'open' : ''}`}>
                    <div className="head">
                        <img src="/logo.png" alt="" className="logo"/>
                        <img src="/menu.png" alt="menu" onClick={closeSidenav}/>
                    </div>
                    <Link to={"/"} onClick={closeSidenav}>Domů</Link>
                    <Link to={"/list"} onClick={closeSidenav}>Seznam úloh</Link>
                    <Link to={"/game"} onClick={closeSidenav}>Zahrát si</Link>
                </div>

                <Outlet></Outlet>
                <footer>
                    Pro soutěž Tour De App, Tým Prezz | 2025
                </footer>
            </div>
        </div>
    );
}
 
export default Layout;