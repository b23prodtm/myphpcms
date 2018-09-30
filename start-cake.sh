#!/bin/sh
#;
#;
#; this is configuration for development phase and runtime
#;
#;
#; colorful shell
nc='\033[0m'
red="\033[0;31m"
green="\033[0;32m"
orange="\033[0;33m"
cyan="\033[0;36m"
#;
#;
#;
#; Host name (unix) 'localhost' generally replaces '127.0.0.1' (macOS).
#;
export DATABASE_ENGINE="Mysql"
export DATABASE_SERVICE_NAME="mysql"
export TEST_MYSQL_SERVICE_HOST="127.0.0.1"
#;export TEST_MYSQL_SERVICE_HOST="localhost"
export TEST_MYSQL_SERVICE_PORT="3306"
export TEST_DATABASE_NAME="phpcms"
export TEST_DATABASE_USER="test"
export TEST_DATABASE_PASSWORD="mypassword"
export FTP_SERVICE_HOST="localhost"
export FTP_SERVICE_USER="test"
export FTP_SERVICE_PASSWORD="mypassword"
export PHP_CMS_DIR="./app/webroot/php_cms/"
echo "

${red}                ///// MySQL HOWTO: connect to the database${nc}

 A MySQL@5.6 server (must match remote server version)
 must be reachable locally. If it's the 1st time you use this connection,
 Configure it as a service and log in with super or admin user shell:${green}mysql -u root${nc}
 These SQL statements initializes the database, replaced with ${orange}environment variables${nc} :

        create database ${orange}${TEST_DATABASE_NAME}${nc};
        use mysql;
        create user '${cyan}${TEST_DATABASE_USER}${nc}'@'${TEST_MYSQL_SERVICE_HOST}';
        alter user '${cyan}${TEST_DATABASE_USER}${nc}'@'${TEST_MYSQL_SERVICE_HOST}' identified by '${orange}${TEST_DATABASE_PASSWORD}${nc}';
        select * from user where user = '${cyan}${TEST_DATABASE_USER}${nc}';
        ${orange}grant all${nc} on ${TEST_DATABASE_NAME}.* to '${cyan}${TEST_DATABASE_USER}${nc}'@'${TEST_MYSQL_SERVICE_HOST}';

${nc}
 The values of CakePHP DB VARIABLES available at ${cyan}app/Config/database.php${nc}.
 Don't forget to grant all privileges.
 Type in shell to login ${green}mysqld ${nc}local daemon as above should give the following results :
${orange}
        mysql -u root
        create database \$TEST_DATABASE_NAME;
        use mysql;
        create user '\$TEST_DATABASE_USER'@'\$TEST_MYSQL_SERVICE_HOST';
        ${green}
        > Query OK, 0 row affected, ...
        ${orange}
        alter user '\$TEST_DATABASE_USER'@'127.0.0.1' identified by '\$TEST_DATABASE_PASSWORD';
        ${green}
        > Query OK, 0 row affected, ...
        ${orange}
        grant all on \$TEST_DATABASE_NAME.* to '\$TEST_DATABASE_USER'@'\$TEST_MYSQL_SERVICE_HOST';
        ${green}
        > Query OK, 0 row affected, ...
        ${nc}

${red}                        ///// FAQ${nc} :

                                        1.
        errno : 1146
        sqlstate : 42S02
        error : Table 'phpcms.info' doesn't exist

Run again ${green}./migrate_database.sh${nc}, to create or update database tables.

                                        2.
If ACCESS DENIED appears, please verify the user name and localhost values then
${cyan}
        grant all on phpcms.* to this user as above.
${nc}

                                        3.
${green}Whenever mysql server changes to another version${nc}, try an upgrade of phpcms database within a (secure)shell ${green}mysql_upgrade -u root${nc}

                                        4.
${green}Make changes to SQL database structure (table-models)${nc}, by modifying Config/Schema/myschema.php, as Config/database.php defines it.
Run ${green}./migrate-database.sh${nc}, answer ${cyan}Y${nc}es when prompted, which may not display any ${red}SQLSTATE [error]${nc}.

If the ${red}Error: 'Database connection \"Mysql\" is missing, or could not be created'${nc}
 shows up, please check up your ${cyan}TEST_DATABASE_NAME=$TEST_DATABASE_NAME${nc} environment variable (set up is above in this shell script or in web node settings).
 Log into the SQL shell (${green}mysql -u root${nc}) and check if you can do : ${green}use $TEST_DATABASE_NAME${nc}.
"
#;
#;
#; this development phase, don't use the same values for production (no setting means no debugger)!
#;
#;
export CAKEPHP_DEBUG_LEVEL=2
#;
#; check if file etc/constantes_local.properties exist (~ ./configure.sh was run once)
#;
if [ ! -f ${PHP_CMS_DIR}/e13/etc/constantes.properties ]; then
        echo "${red}PLEASE RUN ./configure.sh -Y -N -N FIRST !${nc}"
        exit
fi
#;
#;
#; hash file that is stored in webroot to allow administrator privileges
#;
#;
echo "Configuration begins...${green}"
hash="${PHP_CMS_DIR}/e13/etc/export_hash_password.sh"
if [ ! -f $hash ]; then
        echo "${red}PLEASE RUN ./configure.sh -N -Y -N FIRST !${nc}"
        exit
fi
source $hash
echo "${nc}Password ${green}$GET_HASH_PASSWORD${nc}"
#; update plugins and dependencies
sh ./composer.sh
#;
#;
#; PHPUnit performs unit tests
#; The website must pass health checks in order to be deployed
#;
#;
phpunit="vendors/bin/phpunit"
if [ ! -f $phpunit ]; then
        echo "Composer will download the PHPUnit framework"
        version=3
        vcs=3
#        CakePHP 2.X compatible with PHPUnit 3.7
#        PHPUnit 4+ needs CakePHP 3+.
        if [ `expr "\`php --version\`" : 'PHP\ 5\.[0-3]\.'` -gt 0 ]; then
                version=3
                vcs=1
        fi
#        if [ `expr "\`php --version\`" : 'PHP\ 5\.[4-9]\.'` -gt 0 ]; then
#                version=3
#                vcs=3
#        fi
#        if [ `expr "\`php --version\`" : 'PHP\ 7\.0\.'` -gt 0 ]; then
#                version=3
#                vcs=3
#        fi
#        if [ `expr "\`php --version\`" : 'PHP\ 7\.[1-9]\.'` -gt 0 ]; then
#                version=3
#                vcs=3
#        fi
        echo " version $version...\n"
        if [ ! -f bin/composer.phar ]; then
          composer.sh
        fi
        php bin/composer.phar require --prefer-dist --update-with-dependencies --dev phpunit/phpunit ^$version cakephp/cakephp-codesniffer ^$vcs
else
        echo "PHPUnit ${green}[OK]${nc}"
fi
echo `$phpunit --version`
echo "Welcome homepage ${cyan}http://localhost:8080${nc}"
echo "Debugging echoes ${cyan}/admin/index.php${green}?debug=1&verbose=1${nc}"
echo "Alternate local tests ${cyan}/admin/index.php${green}?local=1${nc}"
echo "Turnoff flags ${cyan}/admin/logoff.php${nc}"
echo "==============================================="
lib/Cake/Console/cake server -p 8080 $*
