import { Link } from "react-router-dom";
import GameListComponent from "./GameListComponent";
import "./index.css";

const Home = ({ games }) => {
    const limitedGames = games.slice(0, 3);

    return (
        <div className="home">
            <section className="hero">
                <div className="text">
                    <h1>Vyzkoušejte nový level piškvorek</h1>
                    <Link to={"/game"} className="btn">Zahrát si</Link>
                </div>
                <div className="images">
                    <img src="/xmain.png" alt="" className="first" />
                    <img src="/omain.png" alt="" className="second" />
                </div>
            </section>
            <hr />
            <section className="list">
                <h2>Vaše předchozí hry</h2>
                <GameListComponent games={limitedGames} />
            </section>
            <hr />
            <section className="us">
                <h2>Kdo jsme</h2>
                <h3>Tým <span className="red">Pre</span><span className="blue">zz</span></h3>
                <article>
                    <div className="desk">
                        Richard, <br /> <span className="absoluteBlue">back-end a komunikace</span>
                    </div>
                    <img src="/risa.png" alt="" />
                </article>
                <article>
                    <div className="desk">
                        Ondřej, <br /> <span className="absoluteWhite">back-end a prezentace</span>
                    </div>
                    <img src="/kuca.png" alt="" />
                </article>
                <article>
                    <div className="desk">
                        Petr, <br /> <span className="absoluteRed">front-end a design</span>
                    </div>
                    <img src="/ja.png" alt="" />
                </article>
            </section>
        </div>
    );
}

export default Home;