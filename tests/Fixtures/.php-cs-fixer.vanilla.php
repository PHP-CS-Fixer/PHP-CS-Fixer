<?php

$config = (new \PhpCsFixer\Config())
    ->setFinder(
        \PhpCsFixer\Finder::create()
            ->in(__DIR__ . '/../../')
    )
;

if (PHP_VERSION_ID >= 7_06_00) {
    $config->setUnsupportedPhpVersionAllowed(true);
}

return $config;
