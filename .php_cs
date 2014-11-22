<?php

return Symfony\CS\Config\Config::create()
    // use default SYMFONY_LEVEL and extra fixers:
    ->fixers(array(
        'ordered_use',
        'strict',
        'strict_param',
    ))
    ->finder(
        Symfony\CS\Finder\DefaultFinder::create()
            ->exclude('Symfony/CS/Tests/Fixtures')
            ->notName('ShortTagFixerTest.php')
            ->in(__DIR__)
    )
;
