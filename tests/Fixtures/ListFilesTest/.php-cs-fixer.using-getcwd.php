<?php

/** @var PhpCsFixer\Config $config */
$config = require __DIR__.'/.php-cs-fixer.php';

return $config
    ->setFinder((new PhpCsFixer\Finder())->in([__DIR__.'/using-getcwd']))
;
