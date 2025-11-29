FROM php:8.2-fpm-alpine AS app_php_core

RUN apk update; \
    apk upgrade;

#RUN apk add --no-cache ${PHPIZE_DEPS} \
		#acl \
		#wget \
		#fcgi \
		#file \
		#gettext \
		#git \
#	;

COPY --from=mlocati/php-extension-installer:latest --link /usr/bin/install-php-extensions /usr/local/bin/
RUN install-php-extensions  \
        intl \
        opcache \
        zip \
        pdo_pgsql \
        xsl \
        sockets \
        imap \
        gd \
        redis \
        apcu \
        amqp \
    ;

FROM app_php_core AS app_php
WORKDIR /app
VOLUME /app/var/
VOLUME /app/fs/

# --- PHP configs ---
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"
COPY --link docker/php/conf.d/app.ini $PHP_INI_DIR/conf.d/
COPY --link docker/php/conf.d/app.prod.ini $PHP_INI_DIR/conf.d/
COPY --link docker/php/php-fpm.d/zz-docker.conf /usr/local/etc/php-fpm.d/zz-docker.conf

ENV COMPOSER_ALLOW_SUPERUSER=1
ENV PATH="${PATH}:/root/.composer/vendor/bin"
COPY --from=composer/composer:2-bin --link /composer /usr/bin/composer

COPY --link ./../../composer.* ./../../symfony.* ./

ENV APP_ENV=prod
RUN set -eux; \
    if [ -f composer.json ]; then \
		composer install --prefer-dist --no-dev --no-autoloader --no-scripts --no-progress; \
		composer clear-cache; \
    fi

COPY --link ./../../ ./
RUN rm -Rf docker/

RUN set -eux; \
	mkdir -p var/cache var/log var/tmp/zip fs; \
    if [ -f composer.json ]; then \
		composer dump-autoload --classmap-authoritative --no-dev; \
		composer dump-env prod; \
		COMPOSER_MEMORY_LIMIT=-1 composer run-script --no-dev post-install-cmd; \
		chmod +x bin/console; sync; \
    fi

# --- Node.js & npm ---
RUN apk add --no-cache nodejs npm
COPY package.json package-lock.json ./
RUN npm install


COPY --link docker/php/healthcheck.sh /usr/local/bin/healthcheck
RUN chmod +x bin/phpunit; sync;
RUN chmod +x healthcheck; sync;
RUN chmod +wr var -R
RUN chmod +wr fs -R

HEALTHCHECK --interval=10s --timeout=3s --retries=3 CMD ["healthcheck"]
CMD ["php-fpm"]

FROM app_php AS app_php_dev
ENV APP_ENV=dev XDEBUG_MODE=off

RUN rm "$PHP_INI_DIR/conf.d/app.prod.ini"; \
	mv "$PHP_INI_DIR/php.ini" "$PHP_INI_DIR/php.ini-production"; \
	mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"

COPY --link docker/php/conf.d/app.dev.ini $PHP_INI_DIR/conf.d/

RUN composer install --prefer-dist --no-autoloader --no-scripts --no-progress
RUN composer --no-ansi --no-interaction dump-autoload --optimize --no-scripts && \
      COMPOSER_MEMORY_LIMIT=-1 composer --no-ansi --no-interaction run-script post-install-cmd \
    ;
RUN composer dump-env dev;

RUN install-php-extensions  \
      xdebug \
    ;
