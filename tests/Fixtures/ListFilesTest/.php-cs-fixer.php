<?php

$finder = PhpCsFixer\Finder::create()
    ->in([
        __DIR__.'/needs-fixing/'
    ])
    ->exclude([
        __DIR__.'/excluded/'
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
