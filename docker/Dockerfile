FROM php:7.1-apache

RUN apt-get update && apt-get install zlib1g-dev && \
    docker-php-ext-install zip  && a2enmod rewrite
COPY . /dspstats
COPY docker/parameters.yml /dspstats/app/config/parameters.yml
RUN rmdir /var/www/html && ln -s /dspstats/web /var/www/html
WORKDIR /dspstats
RUN sh docker/get-composer.sh
RUN php composer.phar -q install
RUN chown -R www-data: /dspstats
