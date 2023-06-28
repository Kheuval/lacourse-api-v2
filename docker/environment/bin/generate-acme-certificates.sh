#!/bin/bash

set -e

source $(dirname $(realpath "$0"))/commons.sh

# Get docker path
DOCKER_DIR=$(get_path "../..")

set -a
source $DOCKER_DIR/.project
set +a

check_vars SHELL_USER_NAME PROJECT_DOMAIN_NAME

DOMAINS="-d ${PROJECT_DOMAIN_NAME}"

if [ "${WWW}" ]; then
  DOMAINS=${DOMAINS}" -d www."${PROJECT_DOMAIN_NAME}
fi

if [ "${PROJECT_DOMAIN_NAME_ALT}" ]; then
  DOMAINS=${DOMAINS}" -d "${PROJECT_DOMAIN_NAME_ALT}
fi

# Remove current certs
CERTIFICATES_DIR=$DOCKER_DIR/mount/certificates
if [ -f "${CERTIFICATES_DIR}/cert.pem" ]; then
  cp ${CERTIFICATES_DIR}/cert.pem ${CERTIFICATES_DIR}/cert.pem.bak
fi

if [ -f "${CERTIFICATES_DIR}/key.pem" ]; then
  cp ${CERTIFICATES_DIR}/key.pem ${CERTIFICATES_DIR}/key.pem.bak
fi

chown root:${SHELL_USER_NAME} ${CERTIFICATES_DIR}
chmod 770 ${CERTIFICATES_DIR}

# Generate Acme/LE certificates

cd $DOCKER_DIR
# Install acme.sh
COMMAND="cd ~ && curl https://get.acme.sh | sh -s email=hosting@emagma.fr"
docker-compose exec -u ${SHELL_USER_NAME} ssh bash -c "$COMMAND"

# Generate certificates
COMMAND="~/.acme.sh/acme.sh --issue $DOMAINS -w /apps/wellknown/ --fullchain-file /apps/certificates/cert.pem --key-file /apps/certificates/key.pem --server letsencrypt --force"
docker-compose exec -u ${SHELL_USER_NAME} ssh bash -c "$COMMAND"

# Reload nginx
docker-compose exec nginx nginx -s reload
docker-compose kill nginx
docker-compose rm -f nginx
docker-compose up -d nginx
