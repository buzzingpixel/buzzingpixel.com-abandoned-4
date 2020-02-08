FROM php:7.4.2-fpm

# update bash profile
COPY .profile /root/.profile
COPY .profile /root/.bashrc

RUN pecl install xdebug-2.9.2;
RUN docker-php-ext-enable xdebug;
