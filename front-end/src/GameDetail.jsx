import "./index.css";
import { useParams } from "react-router-dom";
import { useState, useEffect } from "react";
import Game from "./Game";
import GameEditDialog from "./GameEditDialog";
import { useNavigate } from "react-router-dom";

const GameDetail = () => {
  const { uuid } = useParams();
  const [foundGame, setFoundGame] = useState(null);
  const [error, setError] = useState(null);
  const [winner, setWinner] = useState(null);
  const [isDialogOpen, setIsDialogOpen] = useState(false);
  const [board, setBoard] = useState(null);
  const navigate = useNavigate();

  useEffect(() => {
    const fetchGame = async () => {
      try {
        const response = await fetch(`/api/v1/games/${uuid}`);
        if (response.ok) {
          const data = await response.json();
          setFoundGame(data);
          setBoard(data.board);
        } else if (response.status === 404) {
          setError("Hra nebyla nalezena.");
        } else {
          setError("Došlo k chybě při načítání hry.");
        }
      } catch (err) {
        setError("Došlo k síťové chybě.");
      }
    };

    fetchGame();
  }, [uuid]);

  const deleteGame = async (uuid) => {
    try {
      const response = await fetch(`/api/v1/games/${uuid}`, {
        method: "DELETE",
      });

      if (response.status === 204) {
        navigate(`/list`);
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

  const handleSubmit = async () => {
    if (!foundGame.name.trim()) {
      setError("Název nemůže být prázdný.");
      return;
    }
    try {
      const response = await fetch(`/api/v1/games/${foundGame.uuid}`, {
        method: "PUT",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify({
          name: foundGame.name,
          difficulty: foundGame.difficulty,
          board,
        }),
      });
      if (response.ok) {
        setIsDialogOpen(false)
        setError(null);
      } else {
        setError("Došlo k chybě při aktualizaci hry.");
      }
    } catch (err) {
      setError("Došlo k síťové chybě.");
    }
  };

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

  if (error) {
    return <div className="error">{error}</div>;
  }

  if (!foundGame) {
    return <div className="loading">Načítání...</div>;
  }

  return (
    <div className="newGame">
      <Game initialBoard={board} winner={winner} setWinner={setWinner} setInitialBoard={setBoard} />
      <div className="info">
        <h1>{foundGame.name}</h1>
        {winner && (
          <div className="win">
            <p className="vitez">
              Vítěz: {winner}
            </p>
          </div>
        )}
        <div>
          <h2>Jak hrát?</h2>
          <p>
            Je to jednoduché, prostě se střídejte v tazích za X a 0. Pokud je pět
            stejných polí, hra končí a vypíše se vítěz.
          </p>
        </div>
        <div>
          <div className="ikonka">
            <img src="/trophy.png" alt="Trophy icon" />
            {diff(foundGame.difficulty)}
          </div>
          <div className="ikonka">
            <img src="/date.png" alt="Date icon" />
            {new Date(foundGame.updatedAt).toLocaleDateString("cs-CZ")}
          </div>
          <div className="ikonka">
            <img src="/state.png" alt="Trophy icon" />
            {state(foundGame.gameState)}
          </div>
        </div>
        <div className="ikonka">
          <button onClick={() => setIsDialogOpen(true)}>Upravit</button>
          <button
            onClick={() => {
              deleteGame(uuid)
            }}
          >
            Smazat
          </button>
        </div>
        <div>
          <button className="btn" type="button" onClick={handleSubmit}>
            Uložit progres
          </button>
        </div>
      </div>
      <GameEditDialog
        isOpen={isDialogOpen}
        onClose={() => {
          setIsDialogOpen(false);
          window.location.reload();
        }}
        game={foundGame}
      />
    </div>
  );
};

export default GameDetail;