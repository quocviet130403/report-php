FROM php:8.1.22-fpm-alpine

RUN docker-php-ext-install pdo pdo_mysql

COPY crontab /etc/crontabs/root

CMD ["crond", "-f"]