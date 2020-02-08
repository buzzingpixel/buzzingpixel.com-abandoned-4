FROM php:7.4.2-fpm

# update bash profile
COPY .profile /root/.profile
COPY .profile /root/.bashrc

RUN apt-get update
RUN apt-get install -my --no-install-recommends libmagick++-dev
RUN apt-get install -my --no-install-recommends libmagickwand-dev
RUN apt-get install -my --no-install-recommends libpq-dev
RUN apt-get install -my --no-install-recommends libonig-dev
RUN apt-get install -my --no-install-recommends libssl-dev
RUN apt-get install -my --no-install-recommends libwebp-dev
RUN apt-get install -my --no-install-recommends libxml2
RUN apt-get install -my --no-install-recommends libxml2-dev
RUN apt-get install -my --no-install-recommends libxpm-dev
RUN apt-get install -my --no-install-recommends libzip-dev
RUN apt-get install -my --no-install-recommends openssl
RUN apt-get install -my --no-install-recommends postgresql
RUN apt-get install -my --no-install-recommends postgresql-contrib
RUN apt-get install -my --no-install-recommends unzip
RUN apt-get clean
RUN docker-php-ext-configure gd --with-jpeg --with-freetype --with-xpm --with-webp
RUN docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql
RUN docker-php-ext-install bcmath
RUN docker-php-ext-install bz2
RUN docker-php-ext-install exif
RUN docker-php-ext-install gd
RUN docker-php-ext-install iconv
RUN docker-php-ext-install mbstring
RUN docker-php-ext-install opcache
RUN docker-php-ext-install pgsql
RUN docker-php-ext-install pdo
RUN docker-php-ext-install pdo_pgsql
RUN docker-php-ext-install xmlrpc
RUN docker-php-ext-install zip
RUN pecl install imagick
RUN docker-php-ext-enable imagick
RUN pecl install xdebug-2.9.2;
RUN docker-php-ext-enable xdebug;
