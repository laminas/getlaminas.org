# DOCKER-VERSION        1.3.2

# Build UI assets
FROM node:22-alpine as assets
RUN apk add git
RUN npm install --location=global gulp-cli
RUN mkdir -p /work
COPY bootstrap /work/
WORKDIR /work
RUN npm install && gulp deploy
# Created assets need to be in an unmapped folder to persist
RUN mkdir -p /assets && mv /work/build/* /assets


# Build the PHP container
FROM php:8.3-fpm-alpine

RUN apk update && \
    apk add --no-cache bash

ADD https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/
RUN chmod +x /usr/local/bin/install-php-extensions && \
    install-php-extensions \
    bcmath \
    bz2 \
    dom \
    intl \
    opcache \
    pcntl \
    sockets \
    xsl \
    zip

# PHP configuration
COPY .docker/php/getlaminas.ini /usr/local/etc/php/conf.d/999-getlaminas.ini

# Overwrite entrypoint
COPY .docker/php/php-entrypoint /usr/local/bin/php-entrypoint

# Copy assets
COPY --from=assets /assets/assets.json /assets/assets.json
COPY --from=assets /assets/css /assets/css/
COPY --from=assets /assets/js /assets/js/

# Build project
WORKDIR /var/www
ENTRYPOINT ["php-entrypoint"]
CMD ["php-fpm"]
