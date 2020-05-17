incBOOT_ARGS=${incBOOT_ARGS:-0}; if [ $incBOOT_ARGS -eq 0 ]; then
  export incBOOT_ARGS=1
  [[ ! -e .env || ! -e common.env ]] && printf "Missing environment configuration, please run ./deploy.sh %s --nobuild first." $(arch) && exit 1
  eval $(cat .env common.env | awk 'BEGIN{ FS="$" }{ print "export " $1 }')
  export MYPHPCMS_DIR=${MYPHPCMS_DIR:-'app/webroot/php_cms'}
  export MYPHPCMS_LOG=${MYPHPCMS_LOG:-'app/tmp/logs'}
  printf "MYPHPCMS_DIR=%s in ~/.bash_profile or as environment variable." "${MYPHPCMS_DIR}"
fi
