FROM hgraca/explicit-architecture:app.sfn.prd

ENV ENV="dev"

RUN apk --update add --no-cache --virtual build-dependencies autoconf g++ && \
    apk add --no-cache openssh && \
    pecl install xdebug && \
    apk del build-dependencies && \
    rm -rf /var/cache/apk/* && \
    pecl clear-cache

COPY build/container/dev/xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini
