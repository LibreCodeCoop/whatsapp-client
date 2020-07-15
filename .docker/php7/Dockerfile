FROM php:7.4-fpm

RUN apt-get update

RUN apt-get install -y \
        git \
        libzip-dev \
        unzip \
        rsync \
        && docker-php-ext-install opcache \
        && docker-php-ext-install zip

RUN pecl install xdebug-2.9.6 \
    && docker-php-ext-enable xdebug

RUN apt-get install -y tzdata

RUN cp /usr/share/zoneinfo/America/Sao_Paulo /etc/localtime
RUN echo "America/Sao_Paulo" >  /etc/timezone

RUN curl https://getcomposer.org/composer.phar --output /usr/bin/composer
RUN chmod +x /usr/bin/composer

COPY config/php.ini /usr/local/etc/php/conf.d/
WORKDIR /whatsapp
COPY entrypoint.sh /usr/local/bin/entrypoint.sh
ENTRYPOINT ["sh", "/usr/local/bin/entrypoint.sh"]
