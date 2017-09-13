#!/bin/sh

# Remove the default wp-config.php file because it doesn't set SCRIPT_DEBUG
rm /wp-core/wp-config.php

# Add the custom wp-config-php file
cp /project/tests/acceptance-tests/wp-config-codeception.php /wp-core/wp-config.php

# Make sure the database is up and running
while ! mysqladmin ping -hmysql --silent; do
    echo 'Waiting for the database'
    sleep 1
done
echo 'The database is ready'

exec "/repo/vendor/bin/codecept" "$@"
