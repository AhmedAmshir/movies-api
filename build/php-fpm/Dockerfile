
FROM php:7.3.1-fpm-stretch

RUN apt-get update && apt-get install -y libmemcached-dev zlib1g-dev libssl-dev wget \
    && pecl install xdebug-2.7.0beta1 \
    && docker-php-ext-enable xdebug

RUN apt-get install -y libpng-dev

RUN docker-php-ext-install gd
RUN docker-php-ext-install bcmath
RUN docker-php-ext-install pdo pdo_mysql

WORKDIR /usr/local/etc/php/conf.d/

RUN echo "date.timezone = UTC" > timezone.ini
RUN echo "memory_limit = 256M" > memorylimit.ini
RUN rm docker-php-ext-xdebug.ini

RUN curl -sS https://getcomposer.org/installer | php && mv composer.phar /usr/local/bin/composer

RUN apt-get install -y git zip unzip gnupg2

WORKDIR /var/www/html/
