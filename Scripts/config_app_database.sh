#!/usr/bin/env bash
sqlversion="5.7"
source ./Scripts/lib/logging.sh
source ./Scripts/lib/parsing.sh
docker=$(parse_arg_exists "--docker" $*)
if [ $docker 2> /dev/null ]; then
	./Scripts/start_daemon.sh ${docker}
else
	if [ ! $(which brew) 2> /dev/null ]; then echo "Missing homebrew... aborted mysql check."; elif [ ! $(which mysql) 2> /dev/null ]; then
		slogger -st $0 "Missing MySQL ${sqlversion} database service."
		brew outdated mysql@${sqlversion} | brew upgrade
		slogger -st $0 "Installing with Homebrew..."
		brew install mysql@${sqlversion}
		slogger -st $0 "Starting the service thread..."
		brew services start mysql@${sqlversion}
		slogger -st $0 "Performing some checks..."
		mysql_upgrade -u root &
	fi
fi
while [[ "$#" > 0 ]]; do case $1 in
  *.php)
    dbfile=$1
    wd="app/Config"
    source ./Scripts/cp_bkp_old.sh $wd $dbfile "database.php"
    ;;
	-[yY]*)
		if [ $(which mysql) 2> /dev/null ]; then
			mysql --version
			#; symlink mysql socket with php
	    echo "Please allow the super-user to link mysql socket to php ..."
	    mkdir -p /var/run/mysqld
	    if [ -h /var/run/mysqld/mysqld.sock ]; then
					ls -al /var/run/mysqld/mysqld.sock
		 	else
				 ln -vs /tmp/mysqld.sock /var/run/mysqld/mysqld.sock
			fi
		fi;;
  *)
    ;;
esac; shift; done
if [ $(which mysql) 2> /dev/null ] && [ ! -h /var/run/mysqld/mysqld.sock ]; then
	slogger -st $0 "${orange}Warning:${nc}/var/run/mysqld/mysqld.sock symlink not found."
	export -p | grep MYSQL_ &
else
	slogger -st $0 "${green}Notice: mysqld.sock symlink was found.${nc}"
fi
