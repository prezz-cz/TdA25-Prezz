import { useState, useEffect } from "react";
import "./index.css";

const GameEditDialog = ({ isOpen, onClose, game }) => {
    const [error, setError] = useState(null);
    const [name, setName] = useState("");
    const [difficulty, setDifficulty] = useState("");

    let board = game.board;

    useEffect(() => {
        if (game) {
            setName(game.name || "");
            setDifficulty(game.difficulty || "");
        }
    }, [game]);

    if (!isOpen) return null;

    const handleSubmit = async (e) => {
        e.preventDefault();
        if (!name.trim()) {
            setError("Název nemůže být prázdný.");
            return;
        }
        try {
            const response = await fetch(`http://localhost:8080/api/v1/games/${game.uuid}`, {
                method: "PUT",
                headers: {
                    "Content-Type": "application/json",
                },
                body: JSON.stringify({ name, difficulty, board }),
            });
            if (response.ok) {
                onClose();
            } else {
                setError("Došlo k chybě při aktualizaci hry.");
            }
        } catch (err) {
            setError("Došlo k síťové chybě.");
        }
    };

    return (
        <section className="dialog-backdrop">
            <section className="dialog">
                <div className="dialog-header">
                    <h2>Úprava úlohy</h2>
                    <button className="dialog-close" onClick={onClose}>
                        <img src="/xmain.png" alt="Zavřít" />
                    </button>
                </div>
                <div className="dialog-body">
                    {error && <p className="error" style={{color: '#E31837'}}>{error}</p>}
                    {game ? (
                        <form onSubmit={handleSubmit}>
                            <div>
                                <label htmlFor="name">Název:</label>
                                <input
                                    id="name"
                                    type="text"
                                    value={name}
                                    onChange={(e) => setName(e.target.value)}
                                />
                            </div>
                            <div>
                                <label htmlFor="difficulty">Obtížnost:</label>
                                <select
                                    id="difficulty"
                                    value={difficulty}
                                    onChange={(e) => setDifficulty(e.target.value)}
                                    required
                                >
                                    <option value="" disabled>
                                        Vyberte obtížnost
                                    </option>
                                    <option value="beginner">Začátečník</option>
                                    <option value="easy">Jednoduchá</option>
                                    <option value="medium">Pokročilá</option>
                                    <option value="hard">Těžká</option>
                                    <option value="extreme">Nejtěžší</option>
                                </select>
                            </div>
                            <button type="submit" className="btn">
                                Uložit změny
                            </button>
                        </form>
                    ) : (
                        <p>Načítání dat...</p>
                    )}
                </div>
            </section>
        </section>
    );
};

export default GameEditDialog;