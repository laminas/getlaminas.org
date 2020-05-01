# DOCKER-VERSION        1.3.2

# Build UI assets
FROM node:8-alpine as assets
RUN apk add git
RUN npm install -g gulp-cli
RUN mkdir -p /work
COPY bootstrap /work/
WORKDIR /work
RUN npm install && npm rebuild node-sass && gulp deploy
# Created assets need to be in an unmapped folder to persist
RUN mkdir -p /assets && mv /work/build/* /assets


# Build the PHP container
FROM php:7.4-fpm-alpine

# System dependencies
# RUN mkdir -p /etc/apk && echo 'http://dl-cdn.alpinelinux.org/alpine/v3.6/community' >> /etc/apk/repositories
RUN apk update && \
  apk add --no-cache \
    bash \
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
