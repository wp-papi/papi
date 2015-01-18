#!/usr/bin/env bash

DB_USER=root
DB_PASS=root

mysql --user="$DB_USER" --password="$DB_PASS" -e"DROP DATABASE IF EXISTS wordpress_test"
/vagrant/bin/install-wp-tests.sh wordpress_test $DB_USER $DB_PASS localhost latest

mkdir -p /tmp/wordpress/wp-content/plugins/papi
cp -rf /vagrant/* /tmp/wordpress/wp-content/plugins/papi/
cd /tmp/wordpress/wp-content/plugins/papi/
mkdir /tmp/wordpress/wp-content/uploads
mkdir /tmp/wordpress/wp-content/plugins/papi/tmp
chmod 777 /tmp/wordpress/wp-content/plugins/papi/tmp

# Set start path
cd /vagrant
echo cd \/tmp\/wordpress\/wp-content\/plugins\/papi/ > /home/vagrant/.bashrc
rm -rf /etc/motd
