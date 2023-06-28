#!/bin/bash
set -e

source $(dirname $(realpath "$0"))/commons.sh

# Get docker path
DOCKER_DIR=$(get_path "../..")

# https://gist.github.com/lukechilds/a83e1d7127b78fef38c2914c4ececc3c
get_latest_release() {
  curl --silent "https://api.github.com/repos/$1/releases/latest" | # Get latest release from GitHub api
    grep '"tag_name":' |                                            # Get tag line
    sed -E 's/.*"([^"]+)".*/\1/'                                    # Pluck JSON value
}

MKCERT_VERSION=$(get_latest_release "FiloSottile/mkcert")

# Install latest version of mkcert
if [ ! "$(which mkcert)" ] || [ "$(mkcert --version)" != "$MKCERT_VERSION" ]; then
  echo "Installing mkcert $MKCERT_VERSION"

  sudo apt -qqq update && sudo apt -qqq install libnss3-tools -y
  curl -sL https://github.com/FiloSottile/mkcert/releases/download/$MKCERT_VERSION/mkcert-$MKCERT_VERSION-linux-amd64 -o ~/bin/mkcert
  mkdir -p ~/bin/
  chmod +x ~/bin/mkcert
fi

# Install root cert
mkcert -install &> /dev/null

# Get dev domain name
cd $DOCKER_DIR
check_file_exists $DOCKER_DIR/.project.local

set -a
source $DOCKER_DIR/.project.local
set +a

if [ "$ENV" != "dev" ]; then
  echo "ENV is not dev, aborting"
  exit 1
fi

check_file_exists $DOCKER_DIR/config/project/project.dev

set -a
source $DOCKER_DIR/config/project/project.dev
set +a

check_vars PROJECT_DOMAIN_NAME SHELL_USER_NAME

# Prepare pathes
CURRENT_CERTS_DIR=$DOCKER_DIR/mount/certificates/
CERTS_DIR=$DOCKER_DIR/mount/certificates/dev
TMPCERT_DIR=/tmp/mkcert.local

# Generate certificate in a temporary dir
rm -rf $TMPCERT_DIR
mkdir -p $TMPCERT_DIR
cd $TMPCERT_DIR
mkcert $PROJECT_DOMAIN_NAME "*."$PROJECT_DOMAIN_NAME &> /dev/null

# Move certificates to final destination
mkdir -p $CERTS_DIR
echo '*' > $CERTS_DIR/.gitignore
echo '*' > $CURRENT_CERTS_DIR/.gitignore
mv $TMPCERT_DIR/$PROJECT_DOMAIN_NAME+1.pem $CERTS_DIR/cert.pem
mv $TMPCERT_DIR/$PROJECT_DOMAIN_NAME+1-key.pem $CERTS_DIR/key.pem
rm -rf $TMPCERT_DIR
cp $CERTS_DIR/*pem $CURRENT_CERTS_DIR/
chown $SHELL_USER_NAME:$SHELL_USER_NAME $CURRENT_CERTS_DIR/*pem

echo "Dev certificates stored at $CERTS_DIR and copied for $SHELL_USER_NAME at $CURRENT_CERTS_DIR"
