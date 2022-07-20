FROM php:8.1.8-fpm

WORKDIR /home/watchme

RUN apt-get update
RUN apt-get install -y nano mc ffmpeg && apt-get install -y nodejs npm
RUN apt-get -y dist-upgrade
RUN npm install

RUN apt-get install -y libpq-dev \
    libzip-dev  \
    zip \
    libpng-dev \
    libonig-dev \
    libxml2-dev

RUN docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql

RUN docker-php-ext-install pgsql \
    pdo_pgsql  \
    zip \
    gd

COPY ./composer-setup-2.1.9.php /
RUN cd / \
  && php composer-setup-2.1.9.php \
  && php -r "unlink('composer-setup-2.1.9.php');" \
  && mv composer.phar /usr/local/bin/composer

RUN pecl install xdebug

RUN useradd -p $(openssl passwd -1 docker) docker

RUN usermod -a -G www-data docker
