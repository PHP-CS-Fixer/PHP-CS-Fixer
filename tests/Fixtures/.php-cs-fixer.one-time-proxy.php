<?php

declare(strict_types=1);

putenv('PHP_CS_FIXER_TESTS_ALLOW_ONE_TIME_SELF_CONFIG_USAGE=1');

$config = require __DIR__.'/../../.php-cs-fixer.dist.php';

putenv('PHP_CS_FIXER_TESTS_ALLOW_ONE_TIME_SELF_CONFIG_USAGE=0');


return $config;
