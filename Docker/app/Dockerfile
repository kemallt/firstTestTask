FROM php:8.0-rc-fpm

RUN apt-get update && apt-get install -y \curl wget git libfreetype6-dev libjpeg62-turbo-dev libxslt-dev libicu-dev libzip-dev libxml2-dev libonig-dev

RUN docker-php-ext-install -j$(nproc) iconv mbstring mysqli pdo_mysql zip
RUN docker-php-ext-configure gd --with-freetype --with-jpeg
RUN docker-php-ext-install -j$(nproc) gd
RUN docker-php-ext-configure intl
RUN docker-php-ext-install intl
RUN docker-php-ext-install xsl
RUN docker-php-ext-install soap

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

ADD php.ini /usr/local/etc/php/conf.d/40-custom.ini

WORKDIR /var/www

CMD ["php-fpm"]

# Add local web-user
ARG WEB_USER_ID=33
ARG WEB_USER_NAME=www-data
RUN useradd -m -u ${WEB_USER_ID} ${WEB_USER_NAME} || echo "Users exists"
RUN sed -i -- "s/user = www-data/user = ${WEB_USER_NAME}/g" /usr/local/etc/php-fpm.d/www.conf
USER ${WEB_USER_ID}
