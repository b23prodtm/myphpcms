#!/usr/bin/env bash
source ./Scripts/lib/parsing.sh
bootargs=""
docker=""
saved=("$*")
usage="[-dns=<domainname>] [-p|--sql-password=<password>] [-t,--test-sql-password=<password>] [other-args]"
[ $# -eq 0 ] && echo "Usage: $0 ${usage}" && exit 1
while [[ "$#" > 0 ]]; do case $1 in
    -[pP]*|--sql-password*)
      parse_sql_password "$1" "DATABASE_PASSWORD" "user ${DATABASE_USER}";;
    -[tT]*|--test-sql-password*)
      parse_sql_password "$1" "TEST_DATABASE_PASSWORD" "test user ${TEST_DATABASE_USER}";;
    -[vV]*|--verbose )
      echo "Passed params :  $0 ${saved}";;
    -[oO]*|--openshift )
      bootargs=$saved;;
    -[S]*|-submodule )
      git submodule update --init --recursive;;
    -dns*|-DNS*)
      parse_dns_host "$1" "SERVER_NAME" "Domain Server Name";;
    -[hH]*|--help )
      echo "Usage: $0 ${usage}
        -dns=<domainname>
	    Apache ServerName global directive
	-p, --sql-password=<password>
            Exports DATABASE_PASSWORD
        -t, --test-sql-password=<password>
            Exports TEST_DATABASE_PASSWORD
        -S, --submodule
            Update Git submodules
        -o, --openshift
            Use environment variables from real pod or current shell. Also calls composer update (heavy load task).
        -v
            Verbosity enabled
	[other-args]
	    Passed to docker-compose
        "
        exit 0;;
    *) docker="${docker} $1";;
esac; shift; done
export DB=Mysql
source Scripts/bootstrap.sh $bootargs
if [ ! $(which docker-compose) 2> /dev/null ]; then Scripts/install-docker-compose.sh; fi
if [ ! -z $SERVER_NAME ]; then
    source Scripts/docker_site_conf.sh $SERVER_NAME
else
    cp -v docker/apache/site-default.conf docker/apache/site.conf
fi
docker-compose $docker
sudo cp index-redirect-8000.php /var/www/html/index.php
