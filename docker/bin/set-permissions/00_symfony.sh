#!/bin/bash

set -e

function check_vars {
    for var in $@;
    do
		# @see bash indirection http://mywiki.wooledge.org/BashFAQ/006#Indirection
		if [ -z "${!var}" ]
		then
			echo "Variable $var is not defined aborting."
			return 1
		fi
    done
}

check_vars DOCKER_DIR SHELL_USER_NAME SHELL_USER_ID

chmod -R 751 $DOCKER_DIR/mount/apps/symfony/
chown -R $SHELL_USER_NAME: $DOCKER_DIR/mount/apps/symfony/
setfacl -Rd -m u:101:rX -m u:33:rX $DOCKER_DIR/mount/apps/symfony/
setfacl -R -m u:101:rX -m u:33:rX $DOCKER_DIR/mount/apps/symfony/

cd $DOCKER_DIR/mount/apps/symfony/ \
  && mkdir -p var public/media public/build \
  && setfacl -Rd -m u:33:rwX -m u:$SHELL_USER_NAME:rwX var public/media public/build \
  && setfacl -R -m u:33:rwX -m u:$SHELL_USER_NAME:rwX var public/media public/build
