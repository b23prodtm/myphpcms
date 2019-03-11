#!/bin/bash
#;
#;
#; Composer simplifies the process to add features like plugins
#;
#;
#; colorful shell
source ./Scripts/lib/parsing.sh
nc='\033[0m'
red="\033[0;31m"
green="\033[0;32m"
orange="\033[0;33m"
cyan="\033[0;36m"
composer="bin/composer.phar"
if [ $(which composer) 2> /dev/null ]; then
        composer="composer"
else if [ ! -f $composer ]; then
        echo -e "Composer setup...\n"
        mkdir -p bin
        cd bin
        curl -sS https://getcomposer.org/installer | php
        cd ..
fi;fi
echo -e "Composer ${green}[OK]${nc}"
bash -c "${composer} --version"
#; update plugins and dependencies (composer install is good enough to check for updates)
bash -c "${composer} install $*"
