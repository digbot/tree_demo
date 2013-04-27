#!/bin/bash
chown digger.digger * -R
find . -type f -exec chmod 644 {} \;; find . -type d -exec chmod 755 {} \;
setfacl -R -m u:apache:rwx -m u:`whoami`:rwx app/cache app/logs
setfacl -dR -m u:apache:rwx -m u:`whoami`:rwx app/cache app/logs
chmod +x fixfileperm.sh
chmod +x clr
