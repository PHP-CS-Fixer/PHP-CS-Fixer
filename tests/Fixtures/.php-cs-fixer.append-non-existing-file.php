<?php

return (new PhpCsFixer\Config())
    ->setFinder(
        PhpCsFixer\Finder::create()
            ->files()
            ->in(__DIR__.'/FinderDirectory')
            ->append(['/root/../../........////////i-do-not-exist'])
    )
    ->setUsingCache(false)
;
