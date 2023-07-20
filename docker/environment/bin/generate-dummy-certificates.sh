#!/bin/bash
set -e

source $(dirname $(realpath "$0"))/commons.sh

# Get docker path
DOCKER_DIR=$(get_path "../..")

check_file_exists $DOCKER_DIR/.project

set -a
source $DOCKER_DIR/.project
set +a

check_vars PROJECT_DOMAIN_NAME SHELL_USER_NAME

# Check openssl configuration exists
OPENSSL_CONF=$DOCKER_DIR/environment/bin/generate-dummy-certificates/certificate.conf
check_file_exists $OPENSSL_CONF

# Prepare pathes
CURRENT_CERTS_DIR=$DOCKER_DIR/mount/certificates/
CERTS_DIR=$DOCKER_DIR/mount/certificates/dummy/
TMPCERT_DIR=/tmp/certs-dummy

# Generate cert in temporary dir
rm -rf $TMPCERT_DIR
mkdir -p $TMPCERT_DIR
cd $TMPCERT_DIR
OPENSSL_CONF=$OPENSSL_CONF openssl req -x509 -nodes -newkey rsa:2048 -keyout key.pem -out cert.pem 2>/dev/null

# Move certificates to final destination
mkdir -p $CERTS_DIR
echo '*' > $CERTS_DIR/.gitignore
cp $TMPCERT_DIR/cert.pem $CERTS_DIR/cert.pem
cp $TMPCERT_DIR/key.pem $CERTS_DIR/key.pem
rm -rf $TMPCERT_DIR
cp $CERTS_DIR/*pem $CURRENT_CERTS_DIR/
chown $SHELL_USER_NAME:$SHELL_USER_NAME $CURRENT_CERTS_DIR/*pem

echo "Dummy certificates stored at $CERTS_DIR and copied for $SHELL_USER_NAME at $CURRENT_CERTS_DIR"
