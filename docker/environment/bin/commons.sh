#!/bin/bash

GREEN='\033[0;32m'
NOCOLOR='\033[0m'

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

function check_file_exists {
  if [ ! -f "$1" ]; then
    echo "File \"$1\" not found, aborting"
    exit 1
  fi
}

get_path() {
  local CURRENT_DIR=$(dirname $(realpath "$0"))
  echo $(realpath "$CURRENT_DIR/$1")
}

