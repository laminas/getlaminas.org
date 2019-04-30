# DOCKER-VERSION        1.3.2

# Build UI assets stage
FROM node:8-alpine as assets
RUN apk add git
RUN npm install -g gulp-cli
RUN mkdir -p /work
COPY bootstrap /work/
WORKDIR /work
RUN npm install && npm rebuild node-sass && gulp sass
# Created asset have to be moved in a not mapped folder to be maintained
RUN mkdir -p /assets && mv /work/css /assets


# Build the PHP container
FROM mwop/phly-docker-php-swoole:7.2-alpine

# System dependencies
RUN echo 'http://dl-cdn.alpinelinux.org/alpine/v3.6/community' >> /etc/apk/repositories
RUN apk update && \
    apk add --no-cache \
    php7-bcmath \
    php7-bz2 \
    php7-dom \
    php7-intl \
    php7-opcache \
    php7-pcntl \
    php7-sockets \
    php7-xsl \
    php7-zip

# PHP configuration
COPY .docker/php/getlaminas.ini /usr/local/etc/php/conf.d/999-getlaminas.ini

# Overwrite  entrypoint
COPY .docker/bin/php-entrypoint /usr/local/bin/entrypoint

# Project files
COPY bin /var/www/bin
COPY composer.json /var/www/
COPY composer.lock /var/www/
COPY templates /var/www/templates
COPY config /var/www/config
COPY src /var/www/src
COPY data /var/www/data
COPY public /var/www/public

# Copy assets from docker assets stage in a not mapped folder to be maintained
RUN mkdir -p /assets && mkdir -p /assets/css
COPY --from=assets /assets/css/*.css /assets/css/

# Reset "local"/development config files
RUN rm -f /var/www/config/development.config.php && \
    rm -f /var/www/config/autoload/*.local.php && \
    mv /var/www/config/autoload/local.php.dist /var/www/config/autoload/local.php

# Build project
WORKDIR /var/www
