#!/bin/sh
mkdir -p app/tmp/cache/persistent
mkdir -p app/tmp/cache/models
mkdir -p app/tmp/tests
mkdir -p app/tmp/logs
touch app/tmp/logs/error.log
chmod -R a+rwx app/tmp