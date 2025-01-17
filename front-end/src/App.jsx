import Home from './Home';
import Layout from './Layout';
import { BrowserRouter, Route, Routes } from 'react-router-dom';
import NewGame from './NewGame';
import GameList from './GameList';
import GameDetail from './GameDetail';
import { useState } from 'react';
import axios from 'axios';
import { useEffect } from 'react';

function App() {
  const [games, setGames] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);

  useEffect(() => {
    const fetchGames = async () => {
      try {
        const response = await axios.get('/api/v1/games');
        setGames(response.data);
      } catch (err) {
        setError('Failed to fetch games.');
        console.error(err);
      } finally {
        setLoading(false);
      }
    };

    fetchGames();
  }, []);

  if (loading) {
    return <div>Loading...</div>;
  }

  if (error) {
    return <div>{error}</div>;
  }

  return (
    <BrowserRouter>
      <Routes>
        <Route path="/" element={<Layout />}>
          <Route index element={<Home games={games}/>} />
          <Route path='/list' element={<GameList games={games} setGames={setGames}/>}/>
          <Route path="/game" element={<NewGame />} />
          <Route path="/game/:uuid" element={<GameDetail />} />
        </Route>
      </Routes>
    </BrowserRouter>
  );
}

export default App;
