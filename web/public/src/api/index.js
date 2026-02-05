import axios from 'axios';

const api = axios.create({
  baseURL: 'http://localhost:6080/',
  headers: {
    'Content-Type': 'application/json',
  }
});

export default api;
