#!/bin/bash
set -e
parse_sql_password() {
  [ $# -lt 2 ] && echo "Usage: $0 -p|-t|--*sql-password*|*=<password> <var-name> <name>" && return $FALSE
  pass=$(echo $1 | cut -f 2 -d '=')
  while true; do case "$pass" in
    *sql-password*|-[pPtT]*)
      read -sp "
Please, enter the $3 SQL password now:
" pass;;
    "")
      echo "WARNING: using blank password is unsafe !"
      pass=""
      break;;
    *)
      break;;
  esac; done
  #;echo $pass
  export $2=$pass
}
#; export -f parse_sql_password
parse_arg_export() {
  [ $# -lt 3 ] && echo "Usage: $0 -<arg>=<val> <-pattern*|-PATTERN*> <var-name> <name>" && return $FALSE
  zval=$(echo $1 | cut -f 2 -d '=')
  while true; do case "$zval" in
    $2|"")
      read -sp "
Please, enter the $4 value now:
" zval;;
    *)
      break;;
  esac; done
  export $3=$zval
}
#; export -f parse_arg_export
parse_arg_exists() {
  [ $# -lt 2 ] && echo "Usage: $0 <argument-case> list-or-\$*"
  arg1=$(echo $1 | cut -f 1 -d '|')
  arg2=$(echo $1 | cut -f 2 -d '|')
  shift
  count=1
  while [[ "$#" > 0 ]]; do case $1 in
    $arg1|$arg2 ) echo $count; break;;
    *) ;;
  esac; ((count++)); shift; done
}
#; export -f parse_arg_exists()
parse_dns_host() {
  [ $# -lt 3 ] && echo "Usage: $0 <passed-argument> <export-var> <description>"
  parse_arg_export $1 "-dns*|-DNS*" $2 $3
}
#; export -f parse_dns_host()
