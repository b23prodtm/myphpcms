#!/bin/bash
set -e
source ./Scripts/lib/shell_prompt.sh
source ./Scripts/lib/parsing.sh
openshift=$(parse_arg_exists "-[oO]*|--openshift" $*)
if [ $openshift > /dev/null ]; then
  echo "Real environment bootargs..."
else
  echo "Provided local/test bootargs..."
  source ./Scripts/bootargs.sh $*
fi
dbfile=database.cms.php
fix_socket="-Y"
config_app_checked=""
update_checked=0
import_identities=0
identities=app/Config/database.sql
new_pass=""
new_test_pass=""
saved=("$*")
while [[ "$#" > 0 ]]; do case $1 in
  -[uU]* )
      update_checked=1
      ;;
  -[yY]* );;
  -[nN]* )
      fix_socket="-N"
      dbfile=""
      config_app_checked="-N"
      ;;
  -[iI]* )
      import_identities=1
      new_pass=$2
      new_test_pass=$3
      shift;shift;;
  -[vV]*|--verbose )
    [ -f $identities ] && cat $identities
    # Reset passed args (shift reset)
    echo "Passed params : $0 ${saved}
    and environment VARIABLES:"
    export -p | grep "DATABASE\|MYSQL";;
  -[hH]*|--help )
    echo "Usage: $0 [-u] [-y|n] [-o] [-p|--sql-password=<password>] [-t,--test-sql-password=<password>] [-i] [-p|--new-sql-password=<password>] [-t,--new-test-sql-password=<password>]
        -u
            Update the database in app/Config/Schema/
        -y
            Reset ${dbfile} and default socket file
        -n
            Doesn't reset ${dbfile} and socket
        -i -p=<new-password> -t=<new-password>
            Import SQL identities
        -o, --openshift
            Resets ${dbfile}, keep socket and update the database
        -p, --sql-password=<password>
            Exports DATABASE_PASSWORD
        -t,--test-sql-password=<password>
            Exports TEST_DATABASE_PASSWORD
        -dbase=<name>
            Exports DATABASE_NAME
        -tbase=<name>
            Exports TEST_DATABASE_NAME
        -v, --verbose
            Outputs more debug information
        -h, --help
            Displays this help"
        exit 0;;
  -[oO]*|--openshift )
    fix_socket="-N"
    update_checked=1;;
  -[pP]*|--sql-password*)
    parse_sql_password "$1" "DATABASE_PASSWORD" "current ${DATABASE_USER}";;
  -[tT]*|--test-sql-password*)
    parse_sql_password "$1" "TEST_DATABASE_PASSWORD" "current ${TEST_DATABASE_USER}";;
  -dbase*|-DBASE*)
    parse_arg_export "$1" "-dbase*|-DBASE*" "DATABASE_NAME" "${DATABASE_USER} database";;
  -tbase*|-TBASE*)
    parse_arg_export "$1" "-tbase*|-TBASE*" "TEST_DATABASE_NAME" "${TEST_DATABASE_USER} database";;
  *) echo "Unknown parameter passed: $0 $1"; exit 1;;
  esac
shift; done
#; import identities
[ ! -z $DATABASE_NAME ] && [ ! -z $DATABASE_USER ] && [ ! -z $DATABASE_PASSWORD ] && [ ! -z $MYSQL_SERVICE_HOST ] && [ ! -z $MYSQL_SERVICE_PORT ] || (echo -e "${red}ERROR : Missing Database VARIABLES.${nc}\n" && export -p | grep " DATABASE\| MYSQL");
[ ! -z $TEST_DATABASE_NAME ] && [ ! -z $TEST_DATABASE_USER ] && [ ! -z $TEST_DATABASE_PASSWORD ] && [ ! -z $TEST_MYSQL_SERVICE_HOST ] && [ ! -z $TEST_MYSQL_SERVICE_PORT ] || (echo -e "${red}ERROR : Missing Test Database VARIABLES.${nc}\n" && export -p | grep "TEST_DATABASE\|TEST_MYSQL");
if [[ -f $identities ]]; then source ./Scripts/cp_bkp_old.sh . $identities ${identities}.old; fi
# configure user application database and eventually alter user database access
[ -z $dbfile ] && [ $fix_socket == "-N" ] && [ -f app/Config/database.php ] || config_app_checked="-Y"
shell_prompt "./Scripts/config_app_database.sh ${dbfile} ${fix_socket}" "${cyan}Setup ${dbfile} connection and socket\n${nc}" $config_app_checked
if [[ $import_identities -eq 1 ]]; then
  echo -e "Importing the mysql ${cyan}${DATABASE_USER}${nc} and ${cyan}${TEST_DATABASE_USER}${nc} users SQL identities..."
  echo -e "\r${red}WARNING: You will modify SQL ${DATABASE_USER} password !${nc}" &&
  parse_sql_password "$new_pass" "set_DATABASE_PASSWORD" "new ${DATABASE_USER}" &&
  echo -e "\r${red}WARNING: You will modify SQL ${TEST_DATABASE_USER} password !${nc}" &&
  parse_sql_password "$new_test_pass" "set_TEST_DATABASE_PASSWORD" "new ${TEST_DATABASE_USER}" &&
  echo -e "# WARNING: You will alter SQL users access rights\r
  create database if not exists ${DATABASE_NAME};\r
  use mysql;\r
  create user if not exists '${DATABASE_USER}'@'${MYSQL_SERVICE_HOST}';\r
  # set_DATABASE_PASSWORD
  alter user '${DATABASE_USER}'@'${MYSQL_SERVICE_HOST}' identified by '${set_DATABASE_PASSWORD}';\r
  select * from user where user = '${DATABASE_USER}';\r
  grant all on ${DATABASE_NAME}.* to '${DATABASE_USER}'@'${MYSQL_SERVICE_HOST}';\r

  create database if not exists ${TEST_DATABASE_NAME};\r
  use mysql;\r
  create user if not exists '${TEST_DATABASE_USER}'@'${TEST_MYSQL_SERVICE_HOST}';\r
  # set_TEST_DATABASE_PASSWORD
  alter user '${TEST_DATABASE_USER}'@'${TEST_MYSQL_SERVICE_HOST}' identified by '${set_TEST_DATABASE_PASSWORD}';\r
  select * from user where user = '${TEST_DATABASE_USER}';\r
  grant all on ${TEST_DATABASE_NAME}.* to '${TEST_DATABASE_USER}'@'${TEST_MYSQL_SERVICE_HOST}';\r
  " > $identities
  echo "source ${identities}" | mysql -u $DATABASE_USER --password=$DATABASE_PASSWORD
  export DATABASE_PASSWORD=$set_DATABASE_PASSWORD
  export TEST_DATABASE_PASSWORD=$set_TEST_DATABASE_PASSWORD
fi
if [[ $update_checked -eq 1 ]]; then
  show_password_status "$DATABASE_USER" "$DATABASE_PASSWORD" "is updating cake schemas"
  #; update plugins and dependencies
  source ./Scripts/composer.sh "-o"
  #; cakephp shell
  if [ ! -f app/Config/Schema/schema.php ]; then
    echo "Generating database schema 'cake schema generate'"
    ./lib/Cake/Console/cake schema generate -f s
  fi
  if [ ! -f app/Config/Schema/sessions.php ]; then
      echo "Generating default Sessions table"
      ./lib/Cake/Console/cake schema create Sessions -y
  fi
  echo "Migrating database 'cake schema update' ..."
  ./lib/Cake/Console/cake schema update --file myschema.php -y
fi
