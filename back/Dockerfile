FROM php:8.2-apache

RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

RUN apt-get update && apt-get install -y --no-install-recommends \
    locales apt-utils git libicu-dev g++ libpng-dev libxml2-dev libzip-dev libonig-dev libxslt-dev unzip libpq-dev nodejs npm wget \
    apt-transport-https lsb-release ca-certificates

RUN echo "en_US.UTF-8 UTF-8" > /etc/locale.gen && \
    echo "fr_FR.UTF-8 UTF-8" >> /etc/locale.gen && \
    locale-gen

RUN curl -sS https://getcomposer.org/installer | php -- \
    &&  mv composer.phar /usr/local/bin/composer

RUN curl -sS https://get.symfony.com/cli/installer | bash && \
    mv /root/.symfony5/bin/symfony /usr/local/bin

RUN docker-php-ext-configure intl
RUN docker-php-ext-install \
    pdo pdo_mysql gd opcache intl zip calendar dom mbstring xsl
RUN pecl install apcu && docker-php-ext-enable apcu

RUN npm install --global yarn

RUN git config --global user.email "adrien.le.pober@gmail.com" \
    &&  git config --global user.name "Adrien-Le-Pober"

WORKDIR /var/www/

CMD ["sh", "-c", "tail -f /dev/null"]