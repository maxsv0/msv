#!/bin/bash

if [ -z "$1" ]
  then
    echo "Please specify module name"
	exit 0
fi


if [ -z "$2" ]
  then
    echo "Please specify repository KEY"
	exit 0
fi


if [ -z "$3" ]
  then
    echo "Please specify Module Title"
	exit 0
fi


if [ -z "$4" ]
  then
    echo "Please specify Module Version"
	exit 0
fi


if [ -z "$5" ]
  then
    echo "Please specify Module Released Date"
	exit 0
fi


if [ -z "$6" ]
  then
    echo "Please specify Module Description"
	exit 0
fi


echo "Push to MSV repository"
echo "========> $1 (key :  $2)"

mkdir src-temp

cp -a src/. src-temp

cp config-sample.php src-temp/config.php

echo "Creating archive.tar.gz.."
tar -zcvf archive.tar.gz ./src-temp
echo "Done!"

echo $1
echo $2
echo $3
echo $4
echo $5
echo $6

echo "Sending file to repository.."
curl -F "file=@archive.tar.gz" -F "module=$1" -F "key=$2" -F "title=$3" -F "version=$4" -F "released=$5" -F "description=$6" http://rep.msvhost.com/api/import/
echo "Done!"

echo "Removing temp files.."
rm -R src-temp
rm archive.tar.gz
echo "Done!"