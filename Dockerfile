ARG PHP_VERSION
ARG ALPINE_VERSION=3.18

FROM php:${PHP_VERSION}-cli-alpine${ALPINE_VERSION}

ARG DOCKER_USER_ID
ARG DOCKER_GROUP_ID
ARG PHP_XDEBUG_VERSION

# https://blog.codito.dev/2022/11/composer-binary-only-docker-images/
# https://github.com/composer/docker/pull/250
COPY --from=composer/composer:2-bin /composer /usr/local/bin/composer

RUN if ! getent group "${DOCKER_GROUP_ID}" > /dev/null; \
    then addgroup -S -g "${DOCKER_GROUP_ID}" devs; \
  fi \
  && if ! getent passwd "${DOCKER_USER_ID}" > /dev/null; \
    then adduser -S -u "${DOCKER_USER_ID}" -G "$(getent group "${DOCKER_GROUP_ID}" | awk -F: '{printf $1}')" dev; \
  fi \
  # php extensions
  && curl --location --output /usr/local/bin/install-php-extensions https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions \
  && chmod +x /usr/local/bin/install-php-extensions \
  && sync \
  && install-php-extensions \
    pcntl \
    xdebug-${PHP_XDEBUG_VERSION} \
  # xdebug command
  && curl --location --output /usr/local/bin/xdebug https://github.com/julienfalque/xdebug/releases/download/v2.0.0/xdebug \
  && chmod +x /usr/local/bin/xdebug

COPY docker/xdebug.ini /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini
