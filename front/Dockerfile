FROM node:alpine

WORKDIR /var/www

COPY package.json package-lock.json angular.json ./

RUN npm install

RUN npm install --global @angular/cli

ENV PATH="/var/www/node_modules/@angular/cli/bin:${PATH}"

EXPOSE 4200

COPY start.sh .

RUN chmod +x start.sh

CMD ["./start.sh"]