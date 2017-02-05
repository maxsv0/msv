#!/bin/bash

if [ -z "$1" ]
  then
    echo "Please specify installation path"
fi

echo "Installing MSV Website to $1"

cp -ar src/. $1

find $1 -type d -exec chmod 775 {} \;

find $1 -type f -exec chmod 664 {} \;

chmod 777 $1/include/custom/smarty/cache