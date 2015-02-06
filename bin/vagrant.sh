#!/usr/bin/env bash

DB_USER=root
DB_PASS=root

mysql --user="$DB_USER" --password="$DB_PASS" -e"DROP DATABASE IF EXISTS wordpress_test"
/tmp/wordpress/wp-content/plugins/papi/bin/install-wp-tests.sh wordpress_test $DB_USER $DB_PASS localhost latest

cd /tmp/wordpress/wp-content/plugins/papi/
mkdir /tmp/wordpress/wp-content/uploads
mkdir -p /tmp/wordpress/wp-content/plugins/papi/tmp

# Set start path
echo cd \/tmp\/wordpress\/wp-content\/plugins\/papi/ > /home/vagrant/.bashrc
rm -rf /etc/motd
