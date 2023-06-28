#!/bin/bash

NGINX_DIR=$(dirname $(realpath $0))
NGINX_CONFIG_PREFIX="NGINX_CONFIG"
ENV=$(realpath "$NGINX_DIR/../../.env")

if [ ! -f "$ENV" ]; then
  echo "File $ENV not found."
  exit 1
fi

# Load environment vars from .env file
CONFIG_VARS=$(grep $NGINX_CONFIG_PREFIX $ENV)
if [ ! "$CONFIG_VARS" ]; then
  echo "Could not find $NGINX_CONFIG_PREFIX in $ENV, aborting."
  exit 1
fi

SRC_DIR="$NGINX_DIR/src/"
SKEL_DIR="$NGINX_DIR/skel/"

# Reset src/
rm -rf $SRC_DIR/*
mkdir -p $SRC_DIR/conf.d

# Copy main config file
cp $SKEL_DIR/nginx.conf $SRC_DIR/nginx.conf

# Parse NGINX_CONFIG_* and copy enabled configurations
for NAME in $(grep $NGINX_CONFIG_PREFIX $ENV|grep '="1"'|sed -n 's/NGINX_CONFIG_\(.\+\)=.*$/\1/p'); do
  NAME_LOWER=$(echo $NAME | awk '{ print tolower($0) }')
  SCRIPT_PATTERN="/conf.d/$NAME_LOWER"

  if [ -f "$SKEL_DIR$SCRIPT_PATTERN.conf" ]; then
    cp $SKEL_DIR$SCRIPT_PATTERN.conf $SRC_DIR$SCRIPT_PATTERN.conf
  fi

  if [ -d "$SKEL_DIR$SCRIPT_PATTERN" ]; then
    cp -r $SKEL_DIR$SCRIPT_PATTERN $SRC_DIR$SCRIPT_PATTERN
  fi
done;

