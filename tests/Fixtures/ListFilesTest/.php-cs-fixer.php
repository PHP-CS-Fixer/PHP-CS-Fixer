<?php

$finder = PhpCsFixer\Finder::create()
    ->in([
        __DIR__.'/needs-fixing/',
    ])
    ->exclude([
        __DIR__.'/excluded/',
    ])
;

$config = PhpCsFixer\Config();
return $config
    ->setUsingCache(false)
    ->setRules([
        '@Symfony' => true,
    ])
    ->setRiskyAllowed(true)
    ->setFinder($finder)
    ;
