FROM node:alpine

WORKDIR /var/www

COPY package.json angular.json package-lock.json ./

RUN npm cache clean --force

RUN npm install

RUN npm install --global @angular/cli

ENV PATH="/var/www/node_modules/.bin:${PATH}"

EXPOSE 4200

COPY start.sh .

RUN chmod +x start.sh

CMD ["./start.sh"]