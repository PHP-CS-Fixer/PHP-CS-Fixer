<?php

return PhpCsFixer\Config::create()
    ->setFinder(
        PhpCsFixer\Finder::create()
            ->filter(function ($x) { return true; })
    )
;
