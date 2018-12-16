#!/bin/bash
#;
#;
#; Composer simplifies the process to add features like plugins
#;
#;
#; colorful shell
nc='\033[0m'
red="\033[0;31m"
green="\033[0;32m"
orange="\033[0;33m"
cyan="\033[0;36m"
composer="bin/composer.phar"
if [ ! -f $composer ]; then
        echo -e "Composer setup...\n"
        mkdir -p bin
        cd bin
        curl -sS https://getcomposer.org/installer | php
        cd ..
else
        echo -e "Composer ${green}[OK]${nc}"
fi
bin/composer.phar --version
#; update plugins and dependencies
bin/composer.phar update --with-dependencies --apcu-autoloader $*
