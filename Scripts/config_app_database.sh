#!/bin/bash
sqlversion="5.7"
if [ ! $(which brew) 2> /dev/null ]; then echo "Missing homebrew... aborted mysql check."; else if [ ! $(which mysql) 2> /dev/null ]; then
	echo -e "Missing MySQL ${sqlversion} database service."
	brew outdated mysql@${sqlversion} | brew upgrade
	echo -e "Installing with Homebrew..."
	brew install mysql@${sqlversion}
	echo -e "Starting the service thread..."
	brew services start mysql@${sqlversion}
	echo -e "Performing some checks..."
	mysql_upgrade -u root &
else
  mysql --version
fi; fi
while [[ "$#" > 0 ]]; do case $1 in
  *.php)
    dbfile=$1
    wd="app/Config"
    source ./Scripts/cp_bkp_old.sh $wd $dbfile "database.php"
    ;;
  -[yY]*)
    #; symlink mysql socket with php
    echo "Please allow the super-user to link mysql socket to php ..."
    sudo mkdir -p /var/mysql
    if [ -h /var/mysql/mysql.sock ]; then
				ls -al /var/mysql/mysql.sock
	 	else
			 sudo ln -vs /tmp/mysql.sock /var/mysql/mysql.sock
		fi;;
  *)
    ;;
esac; shift; done
if [ ! -h /var/mysql/mysql.sock ]; then
	echo -e "${orange}Warning:${nc}/var/mysql/mysql.sock symlink not found."
else
	echo -e "${green}Notice: mysql.sock symlink was found.${nc}"
	#export MYSQL_SERVICE_HOST="127.0.0.1"
	#export TEST_MYSQL_SERVICE_HOST="127.0.0.1"
fi
