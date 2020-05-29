incBOOT_ARGS=${incBOOT_ARGS:-0}; if [ $incBOOT_ARGS -eq 0 ]; then
  export incBOOT_ARGS=1
  [[ ! -e .env || ! -e common.env ]] && printf "Missing environment configuration, please run ./deploy.sh %s --nobuild first." $(arch) && exit 1
  eval $(cat .env common.env | awk 'BEGIN{ FS="\n" }{ print "export " $1 }')
fi
