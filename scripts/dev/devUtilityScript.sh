#!/usr/bin/env bash

while true; do
    rsync -av /node-modules-volume/ /var/www/node_modules --delete;
    sleep 15;
done
