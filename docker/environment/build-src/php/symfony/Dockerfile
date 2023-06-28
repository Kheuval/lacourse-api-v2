ARG CONTAINER_REGISTRY_URL=emagma

FROM ${CONTAINER_REGISTRY_URL}/php/8.2:latest as base

### prod
FROM base as prod

ADD ./config/z-10-prod.ini /usr/local/etc/php/conf.d/
ADD ./config/zzz-10-prod.conf /usr/local/etc/php-fpm.d/

### staging
FROM base as staging

ADD ./config/z-10-prod.ini /usr/local/etc/php/conf.d/
ADD ./config/zzz-10-prod.conf /usr/local/etc/php-fpm.d/

### dev
FROM base as dev

ADD ./config/z-10-dev.ini /usr/local/etc/php/conf.d/
ADD ./config/zzz-10-dev.conf /usr/local/etc/php-fpm.d/

# Renew arguments wherever it is needed
ARG SHELL_USER_ID=1000
ARG SHELL_USER_NAME="anonymous"
ARG APPS_DIR="/apps"

USER root

# Install dev utilities
RUN apt-get update \
  && apt-get install -y git graphviz vim curl wget mariadb-client \
  && apt-get clean && apt-get autoremove

# Install xdebug
RUN pecl install xdebug
COPY xdebug/xdebug-enable.sh /usr/local/bin/xdebug-enable
COPY xdebug/xdebug-disable.sh /usr/local/bin/xdebug-disable

# Add a friendly user
RUN adduser ${SHELL_USER_NAME} --uid=${SHELL_USER_ID} --gecos=",,,," --disabled-password --shell=/bin/bash;

USER $UID

RUN echo 'alias sf="bin/console"' >> ~/.bashrc

WORKDIR $APPS_DIR
