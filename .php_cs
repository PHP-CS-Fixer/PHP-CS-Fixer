<?php

$header = <<<EOF
This file is part of the PHP CS utility.

(c) Fabien Potencier <fabien@symfony.com>

This source file is subject to the MIT license that is bundled
with this source code in the file LICENSE.
EOF;

Symfony\CS\Fixer\Contrib\HeaderCommentFixer::setHeader($header);

return Symfony\CS\Config\Config::create()
    // use default SYMFONY_LEVEL and extra fixers:
    ->fixers(array(
        'header_comment',
        'long_array_syntax',
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
