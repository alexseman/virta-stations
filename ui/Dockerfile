FROM node:18-alpine

WORKDIR /index
COPY ./package.json ./package-lock.json /index/
RUN npm install
COPY . /index

CMD ["npm", "run", "dev"]
EXPOSE 3000
