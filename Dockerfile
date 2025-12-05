ARG PHP_VERSION=8.5
ARG ALPINE_VERSION=3.22

FROM alpine:3.23 AS sphinx-lint

RUN apk add python3 py3-pip git \
    && pip install --break-system-packages sphinx-lint

# This must be the same AS in CI's job, but `--null` must be changed to `-0` (Alpine)
CMD git ls-files --cached -z -- '*.rst' \
    | xargs -0 -- python3 -m sphinxlint --enable all --disable trailing-whitespace --max-line-length 2000

FROM php:${PHP_VERSION}-cli-alpine${ALPINE_VERSION} AS base

RUN curl --location --output /usr/local/bin/install-php-extensions https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions \
    && chmod +x /usr/local/bin/install-php-extensions \
    && install-php-extensions pcntl

FROM base AS base-dev

# https://blog.codito.dev/2022/11/composer-binary-only-docker-images/
# https://github.com/composer/docker/pull/250
COPY --from=composer/composer:2-bin /composer /usr/local/bin/composer

FROM base-dev AS vendor
COPY composer.json /fixer/composer.json
WORKDIR /fixer
RUN composer remove --dev infection/infection --no-update \
    && composer install --prefer-dist --no-dev --optimize-autoloader --no-scripts

FROM base AS dist

RUN mkdir /code
WORKDIR /code
COPY src /fixer/src
COPY php-cs-fixer /fixer/php-cs-fixer
# Only take the dependencies (not composer itself) into the container
COPY --from=vendor /fixer/vendor /fixer/vendor
RUN ln -s /fixer/php-cs-fixer /usr/local/bin/php-cs-fixer
ENTRYPOINT ["/usr/local/bin/php-cs-fixer"]

FROM base-dev AS dev
ARG DOCKER_USER_ID
ARG DOCKER_GROUP_ID
ARG PHP_XDEBUG_VERSION

RUN if [ ! -z "$DOCKER_GROUP_ID" ] && [ ! getent group "${DOCKER_GROUP_ID}" > /dev/null ]; \
        then addgroup -S -g "${DOCKER_GROUP_ID}" devs; \
    fi \
    && if [ ! -z "$DOCKER_USER_ID" ] && [ ! -z "$DOCKER_GROUP_ID" ] && [ ! getent passwd "${DOCKER_USER_ID}" > /dev/null ]; \
        then adduser -S -u "${DOCKER_USER_ID}" -G "$(getent group "${DOCKER_GROUP_ID}" | awk -F: '{printf $1}')" dev; \
    fi \
    && apk add git \
    && sync \
    && if [ ! -z "$PHP_XDEBUG_VERSION" ] ; then install-php-extensions pcov xdebug-${PHP_XDEBUG_VERSION}; fi \
    && curl --location --output /usr/local/bin/xdebug https://github.com/julienfalque/xdebug/releases/download/v2.0.0/xdebug \
    && chmod +x /usr/local/bin/xdebug

COPY docker/php/* /usr/local/etc/php/conf.d/
