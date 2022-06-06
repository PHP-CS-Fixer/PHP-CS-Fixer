FROM php:8.1-cli-alpine3.16

ARG DOCKER_USER_ID
ARG DOCKER_GROUP_ID

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
    xdebug-3.1.2 \
  # composer
  && curl --output composer-setup.php https://getcomposer.org/installer \
  && php composer-setup.php --install-dir=/usr/local/bin --filename=composer \
  && rm composer-setup.php \
  # xdebug command
  && curl --location --output /usr/local/bin/xdebug https://github.com/julienfalque/xdebug/releases/download/v2.0.0/xdebug \
  && chmod +x /usr/local/bin/xdebug

COPY xdebug.ini /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini
