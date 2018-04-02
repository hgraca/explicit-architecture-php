FROM hgraca/explicit-architecture:app.sfn.prd

ENV ENV="ci"

COPY ./tests /opt/app/tests

COPY ./phpunit.xml.dist /opt/app/
COPY ./.php_cs.dist     /opt/app/

WORKDIR /opt/app

RUN make dep-install-ci-guest && \
    chmod -R 777 /opt/app/var && \
    make dep-clearcache-guest
