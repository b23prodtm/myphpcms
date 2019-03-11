#!/bin/bash
set -e
source ./Scripts/lib/shell_prompt.sh
source ./Scripts/lib/parsing.sh
openshift=$(parse_arg_exists "-[oO]*|--openshift" $*)
if [ -z ${PHP_CMS_DIR} ]; then export PHP_CMS_DIR=app/webroot/php_cms; fi
if [ $openshift 2> /dev/null ]; then
  echo "Real environment bootargs..."
  export CAKEPHP_DEBUG_LEVEL=1
else
  echo "Provided local/test bootargs..."
  export CAKEPHP_DEBUG_LEVEL=2
  source ./Scripts/bootargs.sh $*
fi
#;
#; check if file etc/constantes_local.properties exist (~ ./configure.sh was run once)
#;
if [ ! -f ${PHP_CMS_DIR}/e13/etc/constantes.properties ]; then
        shell_prompt "./configure.sh -c" "configuration"
fi
echo "Configuration begins automatically..."
#; hash file that is stored in webroot to allow administrator privileges
if [[ ! $GET_HASH_PASSWORD ]]; then
  hash="${PHP_CMS_DIR}/e13/etc/export_hash_password.sh"
  if [ ! -f $hash ]; then
          shell_prompt "./configure.sh -c -h" "configuration"
  fi
  source $hash
fi
echo -e "${nc}Password ${green}${GET_HASH_PASSWORD}${nc}"
#; Install PHPUnit, performs unit tests
#; The website must pass health checks in order to be deployed
if [ $openshift 2> /dev/null ]; then
	phpunit="./app/Vendor/bin/phpunit"
	if [ ! -f $phpunit ]; then
                source ./Scripts/composer.sh -o phpunit/phpunit cakephp/cakephp-codesniffer
	else
	        echo -e "PHPUnit ${green}[OK]${nc}"
	fi
	echo `$phpunit --version`
fi
source ./Scripts/config_app_database.sh
