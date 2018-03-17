FROM hgraca/explicit-architecture:app.sfn.base

ENV ENV="prd"

COPY . /opt/app

WORKDIR /opt/app

RUN make dep-install-prd-guest && \
    mkdir -p /opt/app/var/data && \
    chmod -R 777 /opt/app/var && \
    make dep-clearcache-guest
