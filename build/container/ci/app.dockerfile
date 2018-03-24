FROM hgraca/explicit-architecture:app.sfn.prd

ENV ENV="ci"

COPY ./build/container/ci/php.ini /usr/local/etc/php/php.ini

WORKDIR /opt/app

RUN make dep-install-ci-guest && \
    chmod -R 777 /opt/app/var && \
    chmod -R a+rw /opt/app/vendor/autoload.php && \
    chmod -R a+rw /opt/app/vendor/composer && \
    chmod -R a+rw /opt/app/vendor/bin && \
    make dep-clearcache-guest
