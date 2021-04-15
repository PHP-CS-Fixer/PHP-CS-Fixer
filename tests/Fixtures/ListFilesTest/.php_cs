<?php

$finder = PhpCsFixer\Finder::create()
    ->in([
        __DIR__
    ])
    ->exclude([
    ])
;

return PhpCsFixer\Config::create()
    ->setUsingCache(false)
    ->setRules([
            '@Symfony' => true,
    ])
    ->setRiskyAllowed(true)
    ->setFinder($finder)
    ;
