<?php

return (new PhpCsFixer\Config())
    ->setRules([
        'invalid/////rule    ' => true
    ])
    ->setFinder(
        PhpCsFixer\Finder::create()
            ->in(__DIR__)
    )
;
