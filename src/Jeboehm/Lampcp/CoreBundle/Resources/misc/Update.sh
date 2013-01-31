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

pwd=`pwd`

cd /var/www/lampcp/htdocs

git pull
rm -rf app/cache/*
rm app/logs/*
./composer.phar install
chown -R lampcp:lampcp .

cd $pwd

exit 0
