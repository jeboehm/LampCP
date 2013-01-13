#!/bin/bash
##########################################
#
# LampCP
# https://github.com/jeboehm/LampCP
#
# Licensed under the GPL Version 2 license
# http://www.gnu.org/licenses/gpl-2.0.txt
#
##########################################

set -e
set -u

if [ `id -u` != 0 ]; then
    echo "Please run this script as root!"
    exit 1
fi

mkdir -p /var/www/lampcp
cd /var/www/lampcp

if ! [ -r "htdocs/app/console" ]; then
    git clone git@ares:lampcp.git htdocs
fi

cd htdocs

if ! [ -r "app/config/parameters.yml" ]; then
    cp app/config/parameters.yml.dist app/config/parameters.yml

    echo "Now you have to edit the configuration! Press enter to continue.."
    read -n 1 -s
    editor app/config/parameters.yml
fi

if ! [ -x "composer.phar" ]; then
    curl -s https://getcomposer.org/installer | php
fi

./composer.phar install

ln -sf /var/www/lampcp/htdocs/app/console /usr/bin/lampcp

# Fixtures
if ! [ -r "schema_created" ]; then
    lampcp doctrine:schema:create
    lampcp doctrine:fixtures:load
    lampcp lampcp:postfix -c
    touch schema_created
fi

# Add lampcp user for FCGI
if ! grep -q lampcp:x: /etc/passwd; then
    adduser --home /var/www/lampcp --no-create-home --gecos ,,,, --disabled-password lampcp
    adduser www-data lampcp
fi

chown -R lampcp:lampcp /var/www/lampcp

echo "Specify the address (vhost) for LampCP. E.g.: lampcp.ressourcenkonflikt.de"
read -p "Address: " -a VHOST
echo $VHOST

lampcp lampcp:loadusers
lampcp lampcp:apache:generatelampcpconfig $VHOST lampcp

/etc/init.d/apache2 restart

echo "LampCP is now installed."
exit 0
