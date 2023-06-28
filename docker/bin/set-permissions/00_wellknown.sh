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

check_vars DOCKER_DIR SHELL_USER_NAME

setfacl -d -m u:www-data:rwX -m u:$SHELL_USER_NAME:rwX $DOCKER_DIR/mount/wellknown
setfacl -m u:www-data:rwX -m u:$SHELL_USER_NAME:rwX $DOCKER_DIR/mount/wellknown
