#!/bin/bash
set -e

source $(dirname $(realpath "$0"))/commons.sh

# Get docker path
DOCKER_DIR=$(get_path "../..")

DOTPROJECT_LOCAL=$DOCKER_DIR/.project.local
if [ -f "$DOTPROJECT_LOCAL" ]; then
  set -a
  source $DOTPROJECT_LOCAL
  set +a
  echo -e "➡️  Load base variables from $GREEN""$DOTPROJECT_LOCAL$NOCOLOR"
fi

# Check we have all variables
check_vars ENV SHELL_USER_NAME

HOME_DIR=$(getent passwd "$SHELL_USER_NAME" | cut -d: -f6)

cd $DOCKER_DIR/mount/ssh \
  && rm -f .profile .bashrc .ssh \
  && ln -s $HOME_DIR/.profile \
  && ln -s $HOME_DIR/.bashrc \
  && ln -s $HOME_DIR/.ssh \
  && mkdir -p home && chown -R $SHELL_USER_NAME:$SHELL_USER_NAME home && chmod 750 home
