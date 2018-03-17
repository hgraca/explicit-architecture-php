FROM hgraca/explicit-architecture:app.sfn.base

ARG APP_ENV
ARG DATABASE_URL

ENV ENV='prd'

COPY ./build/container/prd/php.ini /usr/local/etc/php/php.ini

COPY ./assets       /opt/app
COPY ./bin          /opt/app
COPY ./config       /opt/app
COPY ./public       /opt/app
COPY ./src          /opt/app
COPY ./templates    /opt/app
COPY ./translations /opt/app

COPY ./composer.json        /opt/app
COPY ./composer.lock        /opt/app
COPY ./Makefile             /opt/app
COPY ./package.json         /opt/app
COPY ./symfony.lock         /opt/app
COPY ./webpack.config.js    /opt/app
COPY ./yarn.lock            /opt/app

WORKDIR /opt/app

RUN make dep-install-prd-guest && \
    mkdir -p /opt/app/var/data && \
    make db-setup-guest && \
    chmod -R 777 /opt/app/var && \
    make dep-clearcache-guest

EXPOSE 8000

CMD ["php", "bin/console", "server:run", "0.0.0.0:8000"]
