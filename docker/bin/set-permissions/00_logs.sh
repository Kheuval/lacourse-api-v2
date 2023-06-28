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

setfacl -Rd -m u:${SHELL_USER_NAME}:rX $DOCKER_DIR/mount/logs
setfacl -R -m u:${SHELL_USER_NAME}:rX $DOCKER_DIR/mount/logs
