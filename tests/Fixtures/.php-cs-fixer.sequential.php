<?php

return (new \PhpCsFixer\Config())
    ->setFinder(
        \PhpCsFixer\Finder::create()
            ->in(__DIR__ . '/../../')
    )
    ->setParallelConfig(new \PhpCsFixer\Runner\Parallel\ParallelConfig(1))
;
