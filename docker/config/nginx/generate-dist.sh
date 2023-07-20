#!/bin/bash

DIR=$(realpath $(dirname $(realpath $0)))
ENV=$(realpath "$DIR/../../.env")

if [ ! -f "$ENV" ]; then
  echo "File $ENVnot found."
  exit 1
fi

set -a
source $ENV
export DOLLAR='$'
set +a


rm -rf $DIR/dist
mkdir -p $DIR/dist
echo '*' > $DIR/dist/.gitignore

# Generate nginx configuration

cd $DIR/src
for FILE in $(find -type f); do
  mkdir -p $DIR/dist/$(dirname $FILE)
  envsubst < $FILE > $DIR/dist/$FILE
done
