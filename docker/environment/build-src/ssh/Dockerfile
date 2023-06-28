ARG CONTAINER_REGISTRY_URL=emagma

FROM ${CONTAINER_REGISTRY_URL}/ssh/php-8.1:latest as base

### prod
FROM base as prod

### staging
FROM base as staging

### dev
FROM base as dev

RUN apt-get update \
  && apt-get install -y vim curl wget \
  && apt-get clean && apt-get autoremove
