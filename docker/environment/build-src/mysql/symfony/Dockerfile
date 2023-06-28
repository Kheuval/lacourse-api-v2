ARG CONTAINER_REGISTRY_URL=emagma

FROM ${CONTAINER_REGISTRY_URL}/mysql/8.0:latest as base

### prod
FROM base as prod

### staging
FROM base as staging

### dev
FROM base as dev

RUN apt-get update && apt-get install -y --no-install-recommends vim && apt-get clean
