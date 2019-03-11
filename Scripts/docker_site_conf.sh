#!/usr/bin/env bash
if [ $# -lt 1 ]; then echo "Usage: $0 <ServerName>"; fi
touch site.conf
echo -e "
<VirtualHost *:80>
    ServerName $1
    DocumentRoot /var/www/html/app/webroot/

    <Directory /var/www/html/app/webroot/>
        Options FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>" >> site.conf
cat site.conf
mv site.conf docker/apache/.
