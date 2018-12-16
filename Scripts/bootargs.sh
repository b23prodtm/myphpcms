#!/bin/bash
source ./Scripts/lib/parsing.sh
set -e
#; colorize shell script
nc="\033[0m"
red="\033[0;31m"
green="\033[0;32m"
orange="\033[0;33m"
cyan="\033[0;36m"
#; To change  Model/Datasource/Database
[[ ("${DB}" == "Mysql") || -z $DB ]] && export DB="Mysql" && export DATABASE_ENGINE="MysqlCms" && export DATABASE_SERVICE_NAME="MYSQL"
[ "${DB}" == "Pgsql" ] && export DATABASE_ENGINE="PostgresCms" && export DATABASE_SERVICE_NAME="PGSQL"
[ "${DB}" == "Sqlite" ] && export DATABASE_ENGINE="SqliteCms" && export DATABASE_SERVICE_NAME="SQLITE"
echo -e "DB : ${green}${DB}${nc}"
#; Host name (unix) 'localhost' generally replaces '127.0.0.1' (macOS).
export PGSQL_SERVICE_HOST="localhost"
#;export MYSQL_SERVICE_HOST="127.0.0.1"
export MYSQL_SERVICE_HOST="localhost"
export MYSQL_SERVICE_PORT="3306"
#; To override, use shell parameter -dbase=<name> instead
export DATABASE_NAME="phpcms"
export DATABASE_USER="root"
#; To override, shell parameter -p=<password> instead
#;export DATABASE_PASSWORD=""
#. Test configuration ?test=1, ./test_cake.sh
export TEST_PGSQL_SERVICE_HOST="localhost"
#;export TEST_MYSQL_SERVICE_HOST="127.0.0.1"
export TEST_MYSQL_SERVICE_HOST="localhost"
export TEST_MYSQL_SERVICE_PORT="3306"
#; To override, use shell parameter -tbase=<name> instead
export TEST_DATABASE_NAME="cakephp_test"
export TEST_DATABASE_USER="test"
#; To override, use shell parameter -t=<password> instead
#;export TEST_DATABASE_PASSWORD=""
export FTP_SERVICE_HOST="localhost"
export FTP_SERVICE_USER="test"
export FTP_SERVICE_PASSWORD="mypassword"
export PHP_CMS_DIR="./app/webroot/php_cms/"
#; More about default environment app/Config/core.php
#; Openshift Online secure keys (default_keys)
export CAKEPHP_SECURITY_SALT="Word"
export CAKEPHP_SECURITY_CIPHER_SEED="01234"
#; 0, 1, 2 the higher the more debug data
export CAKEPHP_DEBUG_LEVEL=2
#; Read -p pass -s word <- bootstrap.sh exports
#; export GET_HASH_PASSWORD=wokUd0mccQD2s
if [[ $(parse_arg_exists "-[vV]*|--verbose" $*) ]]; then
  echo "DATABASE_PASSWORD=${DATABASE_PASSWORD}"
  echo "TEST_DATABASE_PASSWORD=${TEST_DATABASE_PASSWORD}"
fi;
