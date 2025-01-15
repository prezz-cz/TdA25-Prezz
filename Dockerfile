FROM node:16

WORKDIR /frontend

COPY frontend/package*.json ./

RUN npm install

RUN npm install react-router-dom axios

COPY frontend /frontend

EXPOSE 3000

CMD ["npm", "start"]
