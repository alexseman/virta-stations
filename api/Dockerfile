FROM serversideup/php:8.2-fpm

ENV AUTORUN_ENABLED="false"
ENV PHP_DISPLAY_STARTUP_ERRORS="on"
ENV PHP_ERROR_REPORTING="32767"
ENV SSL_MODE="off"

WORKDIR /var/www/html/
COPY . .

RUN apt-get update \
    && apt-get install -y --no-install-recommends php8.2-pgsql \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* /usr/share/doc/*
