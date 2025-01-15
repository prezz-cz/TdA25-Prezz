FROM node:16

WORKDIR /piskvorky

COPY piskvorky/package*.json ./

RUN npm install

RUN npm install react-router-dom axios

COPY piskvorky /piskvorky

EXPOSE 3000

CMD ["npm", "start"]
