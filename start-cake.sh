#!/bin/bash
while [[ "$#" > 0 ]]; do case $1 in
  --help )
    echo "./start-cake.sh [options]
        All options are arguments passed to the command lib/Cake/Console/cake server -p 8080
        "
        exit 0;;
  *);;
esac; shift; done
source ./Scripts/bootstrap.sh
url="http://localhost:8080"
echo -e "Welcome homepage ${cyan}${url}${nc}"
echo -e "Debugging echoes ${cyan}${url}/admin/index.php${green}?debug=1&verbose=1${nc}"
echo -e "Alternate local tests ${cyan}${url}/admin/index.php${green}?local=1${nc}"
echo -e "Unit tests ${cyan}${url}/test.php${nc}"
echo -e "Turnoff flags ${cyan}${url}/admin/logoff.php${nc}"
echo -e "==============================================="
lib/Cake/Console/cake server -p 8080 $*
