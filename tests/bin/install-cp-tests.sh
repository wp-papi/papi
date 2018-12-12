#!/usr/bin/env bash

if [ $# -lt 3 ]; then
	echo "usage: $0 <db-name> <db-user> <db-pass> [db-host] [wp-version] [skip-database-creation]"
	exit 1
fi

DB_NAME=$1
DB_USER=$2
DB_PASS=$3
DB_HOST=${4-localhost}
WP_VERSION=${5-master}
SKIP_DB_CREATE=${6-false}

TMPDIR=${TMPDIR-/tmp}
TMPDIR=$(echo $TMPDIR | sed -e "s/\/$//")
WP_TESTS_DIR=${WP_TESTS_DIR-$TMPDIR/classicpress-tests-lib}
WP_CORE_DIR=${WP_CORE_DIR-$TMPDIR/classicpress/}

download() {
    if [ `which curl` ]; then
        curl -L -s "$1" > "$2";
    elif [ `which wget` ]; then
        wget -nv -O "$2" "$1"
    fi
}
set -ex

install_wp() {
  if WP_VERSION == "latest"; then
    WP_VERSION = "master"
  fi

  echo "download and install ClassicPress ${WP_VERSION}"
  if [ ! -d $WP_CORE_DIR  ]
  then
    mkdir -p $WP_CORE_DIR
    download https://github.com/ClassicPress/ClassicPress-release/archive/${WP_VERSION}.tar.gz $TMPDIR/cp.tar.gz
    tar --strip-components=1 -zxmf $TMPDIR/cp.tar.gz -C $WP_CORE_DIR
  fi
}

install_test_suite() {
  echo "download test-suite"
	# portable in-place argument for both GNU sed and Mac OSX sed
	if [[ $(uname -s) == 'Darwin' ]]; then
		local ioption='-i .bak'
	else
		local ioption='-i'
	fi

	# set up testing suite if it doesn't yet exist
	if [ ! -d $WP_TESTS_DIR ]; then
		# set up testing suite
		mkdir -p $WP_TESTS_DIR
    TMP_STR=$$;
    git clone --depth 1 --branch $WP_VERSION https://github.com/ClassicPress/ClassicPress.git /tmp/cp-core-${TMP_STR}
    mv -v /tmp/cp-core-${TMP_STR}/tests/phpunit/includes $WP_TESTS_DIR/includes
    mv -v /tmp/cp-core-${TMP_STR}/tests/phpunit/data $WP_TESTS_DIR/data
	fi

	if [ ! -f wp-tests-config.php ]; then
		download https://raw.githubusercontent.com/ClassicPress/ClassicPress/${WP_VERSION}/wp-tests-config-sample.php "$WP_TESTS_DIR"/wp-tests-config.php
		# remove all forward slashes in the end
		WP_CORE_DIR=$(echo $WP_CORE_DIR | sed "s:/\+$::")
		sed $ioption "s:dirname( __FILE__ ) . '/src/':'$WP_CORE_DIR/':" "$WP_TESTS_DIR"/wp-tests-config.php
		sed $ioption "s/youremptytestdbnamehere/$DB_NAME/" "$WP_TESTS_DIR"/wp-tests-config.php
		sed $ioption "s/yourusernamehere/$DB_USER/" "$WP_TESTS_DIR"/wp-tests-config.php
		sed $ioption "s/yourpasswordhere/$DB_PASS/" "$WP_TESTS_DIR"/wp-tests-config.php
		sed $ioption "s|localhost|${DB_HOST}|" "$WP_TESTS_DIR"/wp-tests-config.php
	fi

}

install_db() {
	if [ ${SKIP_DB_CREATE} = "true" ]; then
		return 0
	fi
  echo "initializing DB"

	# parse DB_HOST for port or socket references
	local PARTS=(${DB_HOST//\:/ })
	local DB_HOSTNAME=${PARTS[0]};
	local DB_SOCK_OR_PORT=${PARTS[1]};
	local EXTRA=""

	if ! [ -z $DB_HOSTNAME ] ; then
		if [ $(echo $DB_SOCK_OR_PORT | grep -e '^[0-9]\{1,\}$') ]; then
			EXTRA=" --host=$DB_HOSTNAME --port=$DB_SOCK_OR_PORT --protocol=tcp"
		elif ! [ -z $DB_SOCK_OR_PORT ] ; then
			EXTRA=" --socket=$DB_SOCK_OR_PORT"
		elif ! [ -z $DB_HOSTNAME ] ; then
			EXTRA=" --host=$DB_HOSTNAME --protocol=tcp"
		fi
	fi

	# create database
	mysqladmin create $DB_NAME --user="$DB_USER" --password="$DB_PASS"$EXTRA
}

install_wp
install_test_suite
install_db
