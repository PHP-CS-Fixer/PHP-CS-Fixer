php = [
  {
    version = "7.4"
    args = {
      ALPINE_VERSION     = "3.16"
      PHP_XDEBUG_VERSION = "3.1.6"
    }
  },
  {
    version = "8.0"
    args = {
      ALPINE_VERSION = "3.16"
    }
  },
  { version = "8.1" },
  { version = "8.2" },
  { version = "8.3" },
  {
    version = "8.4"
    args = {
      XDEBUG_MODE = "coverage"
    }
  },
]

# Special target: https://github.com/docker/metadata-action#bake-definition
target "docker-metadata-action" {}

target "default" {
  matrix = {
    item = php
  }
  name     = "fixer-php-${sanitize(item.version)}"
  inherits = ["docker-metadata-action"]
  args = merge({
    PHP_VERSION = item.version
  }, lookup(item, "args", {}))
  pull   = true
  tags   = ["fixer:php${item.version}"]
  target = "dev"
  output = ["type=docker"]
}

target "release" {
  matrix = {
    item = [
      for x in php : x if lessthanorequalto(x.version, 8.3)
    ]
  }
  name     = "fixer-release-php-${sanitize(item.version)}"
  inherits = ["fixer-php-${sanitize(item.version)}"]
  target   = "dist"
}

target "_common" {
  args = {
    BUILDKIT_CONTEXT_KEEP_GIT_DIR = 1
  }
}

target "sphinx-lint" {
  inherits = ["_common"]
  target = "sphinx-lint-update"
  output = ["."]
}

target "markdown-lint" {
  inherits = ["_common"]
  target = "markdown-lint-update"
  output = ["."]
}
