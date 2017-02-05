#!/bin/bash

if [ -z "$1" ]
  then
    echo "Please specify installation path"
fi

echo "Updating MSV Website at $1"

git pull

echo "Copy files"

cp -R src/include/* $1/include/

cp -R src/content/* $1/content/

cp -R src/templates/* $1/templates/

cp -R src/index.php $1/index.php

cp -R src/load.php $1/load.php

rm -R $1/include/module/install/