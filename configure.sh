#!/bin/bash
set -e
source ./Scripts/lib/shell_prompt.sh
source ./Scripts/lib/parsing.sh
openshift=$(parse_arg_exists "-[oO]*|--openshift" $*)
if [[ $openshift -eq 1 ]]; then
  echo "Real environment bootargs..."
else
  echo "Provided local/test bootargs..."
  source ./Scripts/bootargs.sh $*
fi
#; if the full set of the arguments exists, there won't be any prompt in the shell
while [[ "$#" > 0 ]]; do case $1 in
    -[cC]*|--const)
        shell_prompt "./Scripts/config_etc_const.sh" "${cyan}Step 1. Overwrite constantes.properties\n${nc}" "-Y"
        ;;
    -[hH]*|--hash)
    #; get hash password
        shell_prompt "./Scripts/config_etc_pass.sh $*" "${cyan}Step 2. Get a hashed password with encryption, PHP encrypts.\n${nc}" "-Y"
        ;;
    -[dD]*|--mig-database)
        #; Know-How : In Openshift 3, configure a CakePhp-Mysql-persistent docker image. Set automatic deployment with _100%_ unavailability
        #; If it starts a build, it automatically scales deployments down to zero, and deploys and scales up when it's finished to build.
        #; Be sure that lib/Cake/Console/cake test app and Health checks should return gracefullly, or the pods get terminated after a short time.
        #; [[-d|--mig-database] [-uyiohn]] argument fixes up : Error: Database connection "Mysql" is missing, or could not be created.
        shift
        args="$*"
        if [[ $openshift -eq 1 ]]; then args="${args} --openshift"; fi
        shell_prompt "migrate-database.sh $args" "${cyan}Step 3. Migrate database\n${nc}" '-Y'
        ;;
    -[sS]*|-[pP]*|-[fF]*|-[uU]*|-[iI]*)
        #; void source script known args
        shift;;
    -[mM]*|--submodule)
        git submodule update --init --recursive --force;;
    --help )
          echo "Usage: $0 [-m] [-c] [-h [-p password -s salt [-f filename]]] [[-d|--mig-database] [-y]]
              -c,--const
                  Reset to app/webroot/php_cms/etc/constantes-template.properties
              -h,--hash
                  Reset administrator password hash
              -p <password> -s <salt> [-f <save-filename>]
                  Set administrator <password> with md5 <salt>. Optional file to save a shell script export.
              -d, --mig-database [options]
                  Migrate Database (see ./migrate-database.sh --help)
              -m,--submodule
                  Update sub-modules from Git"
              exit 0;;
    -[oO]*|--openshift )
      echo "Called Openshift configuration...";;
    -[vV]*|---verbose );;
    *) echo "Unknown parameter passed: $1"; exit 1;;
esac; shift; done
#; update plugins and dependencies
source ./Scripts/composer.sh "-o"
echo -e "${green}Fixing some file permissions...${nc}"
source ./Scripts/configure_tmp.sh
