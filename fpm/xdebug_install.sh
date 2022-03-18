apk add --no-cache $PHPIZE_DEPS
pecl install xdebug
docker-php-ext-enable xdebug

