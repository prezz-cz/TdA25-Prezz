import Game from "./Game";
import { useState } from "react";
import CreateGame from './service/CreateGame';
import { useNavigate } from 'react-router-dom';

const NewGame = () => {
    const [initialBoard, setInitialBoard] = useState(Array(15).fill(Array(15).fill("")));
    const navigate = useNavigate();
    const [winner, setWinner] = useState(null);
    const [gameData, setGameData] = useState({
        name: '',
        difficulty: '',
        board: null,
    });

    const [error, setError] = useState(null);
    const [successMessage, setSuccessMessage] = useState(null);

    const handleInputChange = (e) => {
        const { name, value } = e.target;
        setGameData((prevData) => ({
            ...prevData,
            [name]: value,
        }));
    };

    const handleSubmit = async (e) => {
        e.preventDefault();
        gameData.board = initialBoard;
        const result = await CreateGame(gameData, navigate);
        if (result.success) {
            setSuccessMessage(result.message);
            setError(null);
        } else {
            setSuccessMessage(null);
            setError(result.message);
        }
    };

    const resetGame = () => {
        setWinner(null);
        setGameData({
            name: '',
            difficulty: '',
            board: Array(15).fill(Array(15).fill("")),
        });
    };

    return (
        <div className="newGame">
            <Game initialBoard={initialBoard} winner={winner} setWinner={setWinner} setInitialBoard={setInitialBoard}/>
            <div className="info">
                <h1>Nová hra</h1>
                {winner && (
                    <div className="win">
                        <p className="vitez">
                            Vítěz: {winner} - <button className="restart" onClick={resetGame}>Restartovat</button>
                        </p>
                    </div>
                )}
                <div>
                    <h2>Jak hrát?</h2>
                    <p>Je to jednoduché, prostě se střídejte v tazích za X a 0. Pokud je pět stejných polí hra končí a vypíše se vítěz. Nezapomeňte si hru uložit, pokud jste jí nedohráli či se k ní chcete vrátit.</p>
                </div>
                <form onSubmit={handleSubmit}>
                    <div>
                        <label htmlFor="name">Název hry</label>
                        <input
                            type="text"
                            id="name"
                            name="name"
                            value={gameData.name}
                            placeholder="Něco jednoduchého"
                            onChange={handleInputChange}
                            required
                        />
                    </div>
                    <div>
                        <label htmlFor="difficulty">Obtížnost</label>
                        <select
                            id="difficulty"
                            name="difficulty"
                            value={gameData.difficulty}
                            onChange={handleInputChange}
                            required
                        >
                            <option value="" disabled>Vyberte obtížnost</option>
                            <option value="beginner">Začátečník</option>
                            <option value="easy">Jednoduchá</option>
                            <option value="medium">Pokročilá</option>
                            <option value="hard">Těžká</option>
                            <option value="extreme">Nejtěžší</option>
                        </select>
                    </div>
                    <button className="btn" type="submit">Uložit hru</button>
                    {error && <div style={{ color: 'red' }} aria-live="assertive">{error}</div>}
                    {successMessage && <div style={{ color: 'green' }} aria-live="polite">{successMessage}</div>}
                </form>
            </div>
        </div>
    );
};

export default NewGame;