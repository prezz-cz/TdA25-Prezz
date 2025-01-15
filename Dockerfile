FROM node:16

WORKDIR /front-end

COPY front-end/package*.json ./

RUN npm install

RUN npm install react-router-dom axios

COPY front-end /front-end

EXPOSE 3000

CMD ["npm", "start"]
