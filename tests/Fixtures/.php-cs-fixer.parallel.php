<?php

return (new \PhpCsFixer\Config())
    ->setFinder(
        \PhpCsFixer\Finder::create()
            ->in(__DIR__ . '/../../')
    )
    ->setParallelConfig(\PhpCsFixer\Runner\Parallel\ParallelConfigFactory::detect()) // @TODO 4.0 no need to call this manually
;
