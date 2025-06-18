FROM php:8.0.0-apache
ARG DEBIAN_FRONTEND=noninteractive
RUN docker-php-ext-install mysqli
RUN apt update \
    && apt install libzip-dev zlib1g-dev -y \
    && rm -rf /var/lib/apt/lists/* \
    && docker-php-ext-install zip

RUN a2enmod rewrite
RUN docker-php-ext-install pdo pdo_mysql mysqli


COPY ./public /var/www/html/
RUN apt update \
    && apt install -y cmatrix \
    && rm -rf /var/lib/apt/lists/*