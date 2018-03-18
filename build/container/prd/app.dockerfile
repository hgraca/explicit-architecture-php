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

# Heroku starts the app and assigns a random port to it. We need to make our app listen to that random port,
# so we can't just use the usual exec php command.
CMD ./bin/up.sh
