import { useState, useEffect } from "react";
import "./index.css";

const Game = ({ initialBoard, setInitialBoard, setWinner, winner }) => {
  const [board, setBoard] = useState(initialBoard);
  const [isXNext, setIsXNext] = useState(true);
  const [winningCells, setWinningCells] = useState([]);

  const rows = board.length;
  const cols = board[0].length;

  useEffect(() => {
    setBoard(initialBoard);
  }, [initialBoard]);

  const handleClick = (rowIndex, colIndex) => {
    if (winner || board[rowIndex][colIndex]) return;

    const newBoard = board.map((row, r) =>
      row.map((cell, c) => (r === rowIndex && c === colIndex ? (isXNext ? "X" : "O") : cell))
    );

    setBoard(newBoard);
    setInitialBoard(newBoard);
    setIsXNext(!isXNext);

    const winningLine = checkWinner(newBoard, rowIndex, colIndex);
    if (winningLine) {
      setWinner(isXNext ? "X" : "O");
      setWinningCells(winningLine);
    }
  };

  const checkWinner = (board, row, col) => {
    const player = board[row][col];
    if (!player) return false;

    const directions = [
      [0, 1],
      [1, 0],
      [1, 1],
      [1, -1],
    ];

    for (const [rowOffset, colOffset] of directions) {
      let line = [[row, col]];
      line = line.concat(findInDirection(board, player, row, col, rowOffset, colOffset));
      line = line.concat(findInDirection(board, player, row, col, -rowOffset, -colOffset));
      if (line.length >= 5) {
        return line;
      }
    }

    return null;
  };

  const findInDirection = (board, player, startRow, startCol, rowOffset, colOffset) => {
    let positions = [];
    let row = startRow + rowOffset;
    let col = startCol + colOffset;

    while (
      row >= 0 &&
      row < rows &&
      col >= 0 &&
      col < cols &&
      board[row][col] === player
    ) {
      positions.push([row, col]);
      row += rowOffset;
      col += colOffset;
    }

    return positions;
  };

  const renderCell = (value, rowIndex, colIndex) => {
    const isWinningCell = winningCells.some(
      ([winRow, winCol]) => winRow === rowIndex && winCol === colIndex
    );

    return (
      <button
        key={`${rowIndex}-${colIndex}`}
        onClick={() => handleClick(rowIndex, colIndex)}
        className={`cell ${isWinningCell ? "winning-cell" : ""}`}
        style={
          value === "X"
            ? { backgroundColor: "rgba(0, 112, 187, 0.24)" }
            : value === "O"
            ? { backgroundColor: "rgba(227, 24, 55, 0.42)" }
            : null
        }
      >
        {value}
      </button>
    );
  };

  const renderRow = (row, rowIndex) => {
    return (
      <div key={rowIndex} style={{ display: "flex" }}>
        {row.map((cell, colIndex) => renderCell(cell, rowIndex, colIndex))}
      </div>
    );
  };

  return (
    <div className="game">
      <div>
        {board.map((row, rowIndex) => renderRow(row, rowIndex))}
      </div>
    </div>
  );
};

export default Game;