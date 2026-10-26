FROM php:8.5-fpm

RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        libpng-dev \
        libzip-dev \
        fonts-dejavu-core \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j"$(nproc)" gd mysqli pdo_mysql zip \
    && rm -rf /var/lib/apt/lists/*

COPY zz-clear-env.conf /usr/local/etc/php-fpm.d/zz-clear-env.conf
COPY prepend.php /usr/local/etc/php/prepend.php
COPY docker-entrypoint.sh /usr/local/bin/local-entrypoint.sh

RUN chmod +x /usr/local/bin/local-entrypoint.sh

WORKDIR /var/www/html

ENTRYPOINT ["local-entrypoint.sh"]
CMD ["php-fpm"]
