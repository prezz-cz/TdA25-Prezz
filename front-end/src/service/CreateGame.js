import axios from 'axios';

const CreateGame = async (gameData, navigate) => {
    try {
        const response = await axios.post('/api/v1/games', gameData);
        if (response.status === 201) {
            navigate(`/game/${response.data.uuid}`);
        }
    } catch (error) {
        if (error.response) {
            if (error.response.status === 400) {
                return { success: false, message: `Bad request: ${error.response.data.reason}` };
            } else if (error.response.status === 422) {
                return { success: false, message: `Semantic error: ${error.response.data.reason}` };
            } else {
                return { success: false, message: `Error: ${error.message}` };
            }
        } else {
            return { success: false, message: 'Error connecting to the server.' };
        }
    }
};

export default CreateGame;