import "./index.css";
import { Link } from "react-router-dom";
import { useState } from "react";
import GameEditDialog from "./GameEditDialog";

const GameListComponent = ({ games, admin, setGames }) => {
    const [openDialogGameUuid, setOpenDialogGameUuid] = useState(null);

    const deleteGame = async (uuid) => {
        try {
            const response = await fetch(`/api/v1/games/${uuid}`, {
                method: "DELETE",
            });

            if (response.status === 204) {
                setGames((prevGames) => prevGames.filter(game => game.uuid !== uuid));
            } else if (response.status === 404) {
                alert("Game not found!");
            } else {
                alert("An error occurred while deleting the game.");
            }
        } catch (error) {
            console.error("Error deleting game:", error);
            alert("Unable to delete the game. Please try again later.");
        }
    };

    if (!Array.isArray(games)) {
        return <div>No games available</div>;
    }

    const diff = (difficulty) => {
        switch (difficulty) {
            case 'beginner':
                return 'Začátečník';
            case 'easy':
                return 'Jednoduchá';
            case 'medium':
                return 'Pokročilá';
            case 'hard':
                return 'Těžká';
            case 'extreme':
                return 'Nejtěžší';
            default:
                return 'Neznámá';
        }
    };

    const state = (state) => {
        switch (state) {
            case 'opening':
                return 'Zahájení';
            case 'midgame':
                return 'Middle game';
            case 'endgame':
                return 'Koncovka';
            default:
                return 'Neznámá';
        }
    }

    return (
        <div className="gameListComponent">
            {games.map((game) => (
                <div className="card" key={game.uuid}>
                    <h3>{game.name}</h3>
                    <hr />
                    <div>
                        <img src="/trophy.png" alt="Trophy icon" />
                        {diff(game.difficulty)}
                    </div>
                    <div>
                        <img src="/date.png" alt="Date icon" />
                        {new Date(game.updatedAt).toLocaleDateString("cs-CZ")}
                    </div>
                    <div className="ikonka">
                        <img src="/state.png" alt="Trophy icon" />
                        {state(game.gameState)}
                    </div>
                    {admin && (
                        <div className="ikonka">
                            <button onClick={() => setOpenDialogGameUuid(game.uuid)}>Upravit</button>
                            <button
                                className="btnImage"
                                onClick={() => deleteGame(game.uuid)}
                            >
                                <img src="/delete.png" alt="Delete icon" />
                                Smazat
                            </button>
                        </div>
                    )}
                    <div>
                        <Link to={`/game/${game.uuid}`}>Detail</Link>
                    </div>
                    {openDialogGameUuid === game.uuid && (
                        <GameEditDialog
                            isOpen={true}
                            onClose={
                                () => {setOpenDialogGameUuid(null);
                                window.location.reload();}
                            }
                            game={game}
                        />
                    )}
                </div>
            ))}
        </div>
    );
};

export default GameListComponent;