#!/bin/sh

DIR1="storage/framework"
DIR2="storage/framework/cache"
DIR3="storage/framework/cache/data"
DIR4="storage/framework/sessions"
DIR5="storage/framework/views"


if [ ! -d "$DIR1" ]; then
    mkdir "$DIR1"
    echo "folder $DIR1 created"
fi
if [ ! -d "$DIR2" ]; then
    mkdir "$DIR2"
    echo "folder $DIR2 created"
fi
if [ ! -d "$DIR3" ]; then
    mkdir "$DIR3"
    echo "folder $DIR3 created"
fi
if [ ! -d "$DIR4" ]; then
    mkdir "$DIR4"
    echo "folder $DIR4 created"
fi
if [ ! -d "$DIR5" ]; then
    mkdir "$DIR5"
    echo "folder $DIR5 created"
fi

chmod -R 777 storage
echo "permissions set on storage"