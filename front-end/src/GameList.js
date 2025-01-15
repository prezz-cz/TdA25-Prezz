import { useState } from "react";
import GameListComponent from "./GameListComponent";
import "./index.css";

const GameList = ({games, setGames}) => {
    const [filterName, setFilterName] = useState("");
    const [filterDifficulty, setFilterDifficulty] = useState("none");
    const [filterTime, setFilterTime] = useState("none");

    const filteredGames = games.filter((game) => {
        const matchesName = game.name.toLowerCase().includes(filterName.toLowerCase());
        const matchesDifficulty =
            filterDifficulty === "none" || game.difficulty === filterDifficulty;

        const matchesTime = (() => {
            if (filterTime === "none") return true;
            const updatedAt = new Date(game.updatedAt);
            const now = new Date();

            switch (filterTime) {
                case "hours":
                    return now - updatedAt <= 24 * 60 * 60 * 1000;
                case "days":
                    return now - updatedAt <= 7 * 24 * 60 * 60 * 1000;
                case "month":
                    return now - updatedAt <= 30 * 24 * 60 * 60 * 1000;
                case "monthThree":
                    return now - updatedAt <= 3 * 30 * 24 * 60 * 60 * 1000;
                default:
                    return true;
            }
        })();

        return matchesName && matchesDifficulty && matchesTime;
    });

    return (
        <div className="gameList">
            <div className="heading">
                <h2>Seznam úloh</h2>
                <nav>
                    <form action="">
                        <select
                            name="diff"
                            value={filterDifficulty}
                            onChange={(e) => setFilterDifficulty(e.target.value)}
                        >
                            <option value="none">Vyberte obtížnost</option>
                            <option value="beginner">Začátečník</option>
                            <option value="easy">Jednoduchá</option>
                            <option value="medium">Pokročilá</option>
                            <option value="hard">Těžká</option>
                            <option value="extreme">Nejtěžší</option>
                        </select>
                    </form>
                    <form action="">
                        <select
                            name="time"
                            value={filterTime}
                            onChange={(e) => setFilterTime(e.target.value)}
                        >
                            <option value="none">Čas poslední úpravy</option>
                            <option value="hours">Před 24 hodinami</option>
                            <option value="days">Před 7 dny</option>
                            <option value="month">Před měsícem</option>
                            <option value="monthThree">Před 3 měsíci</option>
                        </select>
                    </form>
                </nav>
                <form action="">
                    <div>
                        <label htmlFor="name">Název hry</label>
                        <input
                            type="text"
                            id="name"
                            name="name"
                            placeholder="Vyhledat"
                            value={filterName}
                            onChange={(e) => setFilterName(e.target.value)}
                            required
                        />
                    </div>
                </form>
            </div>
            <GameListComponent games={filteredGames} admin={true} setGames={setGames}/>
        </div>
    );
};

export default GameList;
