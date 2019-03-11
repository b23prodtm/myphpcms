

<!-- toc -->

- [CakePHP Sample App on OpenShift](#cakephp-sample-app-on-openshift)
    + [Source repository layout](#source-repository-layout)
    + [Compatibility](#compatibility)
    + [Local Built-in Server](#local-built-in-server)
    + [PHP Unit Test](#php-unit-test)
    + [Server-side environment](#server-side-environment)
    + [Database configuration](#database-configuration)
    + [Common Issues](#common-issues)
    + [License](#license)

<!-- tocstop -->

CakePHP for [PHP-CMS Pohse](https://sourceforge.net/projects/pohse/) on OpenShift [![Build Status](https://travis-ci.org/b23prodtm/myphpcms.svg?branch=development)](https://travis-ci.org/b23prodtm/myphpcms)
===============================

This is a quickstart CakePHP application for OpenShift v3 that you ''can'' use as a starting point to develop your own application and deploy it on an [OpenShift](https://github.com/openshift/origin) cluster.

If you'd like to install it, follow [these directions](https://github.com/openshift/cakephp-ex/blob/master/README.md#installation).  

It includes a link to [PHP-CMS Pohse](https://sourceforge.net/projects/pohse/) and its [GIT cake-php release](https://bitbucket.org/b23prodtm/php-cms/branch/cake-php). The latter PHP CMS is featuring well-known functionalities as cool as posting some web contents with pictures stored in a database. More features do come thank to the powerful [Cake PHP framework](http://www.cakephp.org).

### Source repository layout

You do not need to change anything in your existing PHP project's repository.
However, if these files exist they will affect the behavior of the build process:

* **download from git**

  Use a git client to download the project. It will stay in the myphpcms/ folder :
  ```git checkout https://github.com/b23prodtm/myphpcms.git```

* **submodules**

  The myphpcms folder includes modules that need to be pulled in order to install locally.
  After the first checkout browse to myphpcms folder and do
  ```git submodule update --init --recursive```
  You'll see modules populating the subfolder app/webroot/... If something goes wrong, erase the myphpcms folder and start over.

* **composer.json**

  List of dependencies to be installed with `composer`. The format is documented
  [here](https://getcomposer.org/doc/04-schema.md).
  Plugins are registered in both _git submodule_ and _composer.json_. To allow a plugin to accept ```composer update```, edit _composer.json_ according to the available released tags. In the plugin's home repository (app/Plugin/<plugin-name>/), call```git tag``` or  ``git log``` for more information.
  >_DEVELOPER TIP:_ To push tags : ```git tag`<version> && git push --tags```.   

* **.htaccess**

  To allow Apache server to browse directly to the app/webroot folder on server-side, use mod_rewrite rules, as provided by .htaccess files.

  >/.htaccess

      <IfModule mod_rewrite.c>
        RewriteEngine on
        # Uncomment if you have a .well-known directory in the root folder, e.g. for the Let's Encrypt challenge
        # https://tools.ietf.org/html/rfc5785
        #RewriteRule ^(\.well-known/.*)$ $1 [L]
        RewriteRule ^$ app/webroot/ [L]
        RewriteRule (.*) app/webroot/$1 [L]
      </IfModule>

  >/app/.htaccess

      <IfModule mod_rewrite.c>
         RewriteEngine on
         RewriteBase /app/
         RewriteRule    ^$    webroot/    [L]
         RewriteRule    (.*) webroot/$1    [L]
      </IfModule>

### Compatibility

This repository is compatible with PHP 5.6 and higher, excluding any alpha or beta versions.

### Local built-in server
>for local test only

Open a Terminal window:

    ./start-cake.sh -p=<sql-root-password>

>Ctrl-click the URLs to open them in the browser. To get more help about the command line interface :

    ./start-cake.sh --help

### PHP Unit Test

Open a Terminal window:

    ./test-cake.sh -p=<sql-root-password> -t=<test-password>

See [below](#common-issues) to allow access on the built-in local server.

### Server-side environment

When deployment happens on server-side or is triggered by a git push event, 'source-to-image (s2i)', the httpd-server  or pod needs proper environment variables to be set ready. Otherwise the scripts will fail with an error state, unable to connect to the database

The following variables must be set up as server environment, provided by your **database administrator**:

    # SqliteCms, PostgresCms
    DATABASE_ENGINE:MysqlCms

>Note: DB Engine selects CakePhp Model/Datasource/Database DBOSource class to configure SQL connections.    

    DATABASE_NAME:default
    DATABASE_SERVICE_NAME:MYSQL
    # a host alias or IP address
    MYSQL_SERVICE_HOST:mysql

>Note: Prefixed with *TEST_* they are used by the index.php?test=1 URLs and ./test-cake.sh (--travis)

The following additional variables must be set up as server secrets environment, provided by your database administrator:

    #(optional)
    WEBHOOK_URL:<discordapp-url>
    # Persistent connection credentials
    DATABASE_USER:<provided-user>
    DATABASE_PASSWORD:<provided-password>
    # Just add TEST_DATABASE_USER and TEST_DATABASE_PASSWORD
    TEST_DATABASE_USER:<test-user>
    TEST_DATABASE_PASSWORD:<test-password>
    # CakePHP generated
    CAKEPHP_SECRET_TOKEN:<secret-token>
    CAKEPHP_SECRET_SALT:<secret-salt>
    CAKEPHP_SECURITY_CIPHER_SEED:<cipher-seed>
    # Generated by ./configure.sh -h
    GET_HASH_PASSWORD:<hashed-password>

### Database Configuration

An SQL server (must match remote server version) must be reachable by hostname or via its socket. If it's the 1st time you use this connection,

Configure it as a service and configure the login ACL with the user shell.
* __Mysql__ mysql installation:

    mysql_secure_installation

* __Optional__ Edit `./app/Config/database.cms.php` if you wish to modify the DATABASE_CONFIG class.

* __Optional__ Edit `./app/Model/Datasources/Database` if you wish to modify the DBOSource driver.

* Edit `./Scripts/bootargs.sh` to change default environment settings (host, port, login, database name)

* Run the configuration script:

    ./configure.sh -d -p=<root-password> -u

* More about configuration:

    ./configure.sh --help && ./migrate-database.sh --help

* More [common issues](#common-issues)

* The following command resets SQL users `${DATABASE_USER}` and `${TEST_DATABASE_USER}` password :

    ./migrate-database.sh -p -i -p --test-sql-password


### Common Issues

1. How to fix the following error?

Index page displays:

    errno : 1146
    sqlstate : 42S02
    error : Table 'phpcms.info' doesn't exist

Try the following to migrate (update) all database tables, answer 'y' when prompted:

    ./migrate-database.sh -u

2. ACCESS DENIED appears with other information complaining about database connection, what does that mean ?

You probably have modified user privileges on your server:

    mysql -u root
    use mysql;
    grant all on $TEST_DATABASE_NAME.* to '$TEST_DATABASE_USER'@'$TEST_MYSQL_SERVICE_HOST';
    exit
    ./configure.sh -c

This will reset the connection profile in ..etc/ properties file with the template.
More about environment variables are located in the remote pod (OpenShift) settings and locally in ./Scripts/bootargs.sh.  

>Note:

    ./configure.sh --mig-database -p -i -p -t

to do a reset with environment root and user password.

3. ACCESS DENIED for root@'127.0.0.1' or root@'localhost' appears with other information complaining about database connection, what does that mean ?

This looks like a first installation of mysql. You have to secure or reset your mysql root access:

    sudo rm -rf /usr/local/var/mysql
    mysqld --initialize | grep "temporary password" | cut -f4  -d ":" | cut -c 2-  > app/tmp/nupwd

>Note: A temporary password is generated for root@localhost. Now import identities.

    brew services restart mysql@5.7
    ./configure.sh --mig-database -p=$(cat app/tmp/nupwd) -i -p -t
>You have now configured a new SQL root password and a test password. Local SQL access and server is ready to run tests:

    ./test-cake.sh -p -t=<test-password>

Go on to development phase with the [Local Built-in server](#local-built-in-server).

4. My mysql server's upgraded to another version, what should I do ?

Upgrade your phpcms database within a (secure)shell:

    mysql_upgrade -u root --password=<password>

4. I've made changes to mysql database tables, I've made changes to Config/Schema/myschema.php, as Config/database.php defines it, what should I do ?

Migrate all your tables:

    ./migrate-database.sh -u

Answer 'y' when prompted.

5. How to fix up 'Database connection "Mysql" or "Pgsql" is missing, or could not be created' ?

Log in with root privileges should work:

    mysql -u root --password=<password> cakephp_test

Postgres has its own command:

    pgsql -U root --password=<password> -d cakephp_test

If not, retry the [secure_installation](#database-configuration).

Check your environment variables (`./Scripts/bootargs.sh` or Docker/Pod settings). Use one or the other, and see which works for you:

    MYSQL_SERVICE_HOST=localhost (Unix/OSX platforms)
            or
    MYSQL_SERVICE_HOST=127.0.0.1
    ..

    MYSQL_SERVICE_PORT=3306

Debug the local configuration with verbosity level information (add `-o` if you are in a remote shell):

    ./configure.sh --verbose -d -u


6. How to fix up ERROR 2002 (HY000): Can't connect to local MySQL server through socket '/var/mysql/mysql.sock' (2) ?

Run the socket fixup script with arguments:

    ./migrate-database.sh -y
    brew services restart mysql@5.7

7. I'm testing with ./start_cake.sh and I cannot add any new post on Updates section, what should I do ?

With the CLI, you may ctrl-X ctrl-C to exit server and migrate your database:

    ./migrate-database.sh -u
    ./start_cake.sh

Answer 'y' when prompted.

8. I cannot upload any picture, why ?

The Mysql.php Datasource must define binary and mediumbinary storage types. Please look at the file  __app/Model/Datasource/Mysql_cms.php__ if it exists and if you experienced the following error:

    errno : 1054
    sqlstate : 42S22
    error : Unknown column 'image' in 'field list'

Add the *__mediumbinary__* storage, extending the original Datasource class:

```
<?php
App::uses('Mysql', 'Model/Datasource/Database');

class Mysql_cms extends Mysql
{
	public function __construct()
	{
		parent::__construct();
		$this->columns['mediumbinary'] = array('name' => 'mediumblob');
	}

	/**
	 * Converts database-layer column types to basic types
	 *
	 * @param string $real Real database-layer column type (i.e. "varchar(255)")
	 * @return string Abstract column type (i.e. "string")
	 */
		public function column($real) {
			$s = parent::column($real);
			if($s === "text") {
				$col = str_replace(')', '', $real);
				$limit = $this->length($real);
				if (strpos($col, '(') !== false) {
					list($col, $vals) = explode('(', $col);
				}
				if (strpos($col, 'mediumblob') !== false || $col === 'mediumbinary') {
					return 'mediumbinary';
				}
			}
			return $s;
		}
}
?>
```
Ensure it is set as DATABASE_ENGINE in `app/Config/database.cms.php`,`./Scripts/bootargs.sh`, `.travis.yml` and update the database schema:

    ./migrate-database.sh -u

9. It looks like submodule folders have disappeared, why ?

A recent `git checkout ` made the submodule disappear from disk, that can happen on master/development branch.  Recall or add the shell configure script to your workflow:

    ./configure.sh -m

10. SHA1 signature couldn't be verified with the file composer.phar, how is that possible ?

The composer binary hasn't been downloaded on this machine, and must be downloaded again. By default, git shouldn't keep track on this file. Delete it and launch composer installer again.

    rm bin/composer.phar
    ./composer.sh

If you are working on a git remote tracked branch, it is recommended to remove from file tracking and add to `.gitignore`.

    git rm bin/composer.phar
    echo "/bin/composer.phar" >> .gitignore

### License
   Copyright 2016 www.b23prodtm.info

   Licensed under the Apache License, Version 2.0 (the "License");
   you may not use this file except in compliance with the License.
   You may obtain a copy of the License at

   * [Apache License, Version 2.0](http://www.apache.org/licenses/LICENSE-2.0)

   Unless required by applicable law or agreed to in writing, software
   distributed under the License is distributed on an "AS IS" BASIS,
   WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
   See the License for the specific language governing permissions and
   limitations under the License.
