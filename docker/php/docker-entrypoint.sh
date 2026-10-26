#!/bin/sh
set -e

if [ -n "${WWW_UID:-}" ] && [ -n "${WWW_GID:-}" ]; then
  usermod -o -u "$WWW_UID" www-data
  groupmod -o -g "$WWW_GID" www-data
fi

display_errors=Off
if [ "${APP_DEBUG:-0}" = "1" ]; then
  display_errors=On
fi

cat > /usr/local/etc/php/conf.d/zz-env.ini <<EOF
memory_limit=${PHP_MEMORY_LIMIT:-256M}
upload_max_filesize=${PHP_UPLOAD_MAX_FILESIZE:-32M}
post_max_size=${PHP_POST_MAX_SIZE:-32M}
max_execution_time=${PHP_MAX_EXECUTION_TIME:-60}
max_input_time=${PHP_MAX_EXECUTION_TIME:-60}
date.timezone=${PHP_TIMEZONE:-Europe/Berlin}
default_charset=UTF-8
display_errors=${display_errors}
display_startup_errors=${display_errors}
log_errors=On
error_reporting=E_ALL
auto_prepend_file=/usr/local/etc/php/prepend.php
expose_php=Off
EOF

exec docker-php-entrypoint "$@"
