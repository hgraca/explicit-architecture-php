FROM hgraca/explicit-architecture:app.sfn.base

ARG APP_ENV
ARG DATABASE_URL

ENV ENV='prd'

COPY . /opt/app

WORKDIR /opt/app

RUN make dep-install-prd-guest && \
    mkdir -p /opt/app/var/data && \
    make db-setup-guest && \
    chmod -R 777 /opt/app/var && \
    make dep-clearcache-guest

EXPOSE 8000

CMD ["php", "bin/console", "server:run", "0.0.0.0:8000"]
