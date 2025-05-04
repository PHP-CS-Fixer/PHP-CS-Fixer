# syntax=docker/dockerfile:1

ARG ALPINE_VERSION=3.21
ARG PHP_VERSION=8.4

FROM python:3-alpine AS sphinx-lint
SHELL ["/bin/ash", "-eo", "pipefail", "-c"]
WORKDIR /fixer
RUN --mount=type=cache,target=/var/cache/apk \
    --mount=type=bind,target=.,rw <<EOF
    apk add 'git>=2.47'
    pip install --no-cache-dir 'sphinx-lint>=1'
    mkdir /out
    git ls-files --cached -z -- '*.rst' | cpio -pdm -p /out
    python -m sphinxlint --enable all --disable trailing-whitespace --max-line-length 2000 /out
EOF

FROM scratch AS sphinx-lint-update
COPY --from=sphinx-lint /out /

# hadolint ignore=DL3007
FROM registry.gitlab.com/pipeline-components/markdownlint:latest AS markdown-lint
WORKDIR /fixer
SHELL ["/bin/ash", "-eo", "pipefail", "-c"]
RUN --mount=type=bind,target=.,rw <<EOF
    mdl --git-recurse -v .
    mkdir /out
    find . -name '*.md' | cpio -pdm /out
EOF

FROM scratch AS markdown-lint-update
COPY --from=markdown-lint /out /

FROM php:${PHP_VERSION}-cli-alpine${ALPINE_VERSION} AS base

ADD --chmod=0755 https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/install-php-extensions
RUN install-php-extensions pcntl

FROM base AS base-dev
COPY --from=composer:2 --chmod=0755 /usr/bin/composer /usr/local/bin/composer

FROM base-dev AS vendor
COPY --link --chmod=0644 composer.json /fixer/composer.json
WORKDIR /fixer
RUN <<EOF
    composer remove --dev infection/infection --no-update
    composer install --prefer-dist --no-dev --optimize-autoloader --no-scripts
EOF

FROM base AS dist
WORKDIR /fixer
# Only take the dependencies (not composer itself) into the container
COPY --from=vendor /fixer/vendor ./vendor
COPY --link src ./src
COPY --link --chmod=0755 php-cs-fixer ./
RUN ln -s /fixer/php-cs-fixer /usr/local/bin/php-cs-fixer
WORKDIR /code
ENTRYPOINT ["/usr/local/bin/php-cs-fixer"]

FROM base-dev AS dev
ARG DOCKER_USER_ID=1000
ARG DOCKER_GROUP_ID=1000
ARG PHP_XDEBUG_VERSION=3.4.2
ARG XDEBUG_MODE
ENV PHP_CS_FIXER_ALLOW_XDEBUG=1
ENV PHP_IDE_CONFIG=serverName=php-cs-fixer
ENV XDEBUG_MODE=${XDEBUG_MODE}

EXPOSE 9003

RUN if [ ! -z "$DOCKER_GROUP_ID" ] && [ ! getent group "${DOCKER_GROUP_ID}" > /dev/null ]; \
        then addgroup -S -g "${DOCKER_GROUP_ID}" devs; \
    fi \
    && if [ ! -z "$DOCKER_USER_ID" ] && [ ! -z "$DOCKER_GROUP_ID" ] && [ ! getent passwd "${DOCKER_USER_ID}" > /dev/null ]; \
        then adduser -S -u "${DOCKER_USER_ID}" -G "$(getent group "${DOCKER_GROUP_ID}" | awk -F: '{printf $1}')" dev; \
    fi \
    && apk add git \
    && sync \
    && install-php-extensions pcov xdebug-${PHP_XDEBUG_VERSION} \
    && curl --location --output /usr/local/bin/xdebug https://github.com/julienfalque/xdebug/releases/download/v2.0.0/xdebug \
    && chmod +x /usr/local/bin/xdebug

COPY --link --chmod=0644 docker/php/* /usr/local/etc/php/conf.d/
