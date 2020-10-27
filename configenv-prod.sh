#!/bin/bash
echo "configurando ambiente"
bin/console cache:clear --env prod

chmod -R 775 var/cache/
chmod -R 775 var/log/

chown -R root:apache var/cache/
chown -R root:apache var/log/