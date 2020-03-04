FROM php:7.4.3-alpine3.11

# `apk --update`    updates indexes before installing
# `apk --no-cache`  doesn't put stuff in the cache, so we don't need to remove it at the end
# `apk --virtual`   lets us uninstall temporary dependencies in one go at the end
RUN apk --update add --no-cache --virtual build-dependencies autoconf g++ && \
    apk add --no-cache make libzip-dev curl && \
    mkdir -p /tmp/pear/cache && \
    docker-php-ext-configure pcntl && \
    docker-php-ext-install pcntl && \
    docker-php-ext-configure bcmath && \
    docker-php-ext-install bcmath && \
    docker-php-ext-configure zip && \
    docker-php-ext-install zip && \
    curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin --filename=composer && \
    apk del build-dependencies && \
    rm -rf /var/cache/apk/* && \
    pecl clear-cache
