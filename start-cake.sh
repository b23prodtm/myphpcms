#!/bin/bash
source ./Scripts/lib/parsing.sh
source ./Scripts/lib/shell_prompt.sh
command="server -p 8080"
saved=("$*")
while [[ "$#" > 0 ]]; do case $1 in
  --help )
    echo "Usage: $0 [-p|--sql-password=<password>] [--test-sql-password=<password>] [-c <command>] [options]
            Default command is lib/Cake/Console/cake server -p 8080
        -p, --sql-password=<password>
            Exports DATABASE_PASSWORD to bootargs.
        -t,--test-sql-password=<password>
            Exports TEST_DATABASE_PASSWORD
        -c <command> <options> [--help]
            lib/Cake/Console/cake <command> <options>
            E.g. $0 -c server --help
        "
        exit 0;;
  -[pP]*|--sql-password*)
    parse_sql_password "$1" "DATABASE_PASSWORD" "current ${DATABASE_USER}";;
  -[tT]*|--test-sql-password*)
    parse_sql_password "$1" "TEST_DATABASE_PASSWORD" "current ${TEST_DATABASE_USER}";;
  -[cC]*)
    command=$2
    shift; shift; command="${command} $*";;
  *);;
esac; shift; done
source ./Scripts/bootstrap.sh $saved
show_password_status "$DATABASE_USER" "$DATABASE_PASSWORD" "is running development server"
url="http://localhost:8080"
echo -e "Welcome homepage ${cyan}${url}${nc}"
echo -e "Administrator login ${cyan}${url}/admin/index${nc}"
echo -e "Debugging echoes ${cyan}${url}${orange}?debug=1&verbose=1${nc}"
echo -e "Another Test configuration ${cyan}${url}/admin/index.php${orange}?test=1${nc}"
echo -e "Unit tests ${cyan}${url}/test.php${nc}"
echo -e "Turnoff flags (fix captcha)${cyan}${url}/admin/logoff.php${nc}"
echo -e "==============================================="
lib/Cake/Console/cake $command
