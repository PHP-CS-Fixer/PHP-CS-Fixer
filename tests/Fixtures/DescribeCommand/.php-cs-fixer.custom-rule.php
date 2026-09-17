<?php

use PhpCsFixer\Config;
use PhpCsFixer\Finder;
use PhpCsFixer\Tests\Fixtures\DescribeCommand\DescribeFixtureFixer;

return (new Config())
    ->setFinder((new Finder())->in(__DIR__))
    ->registerCustomFixers([new DescribeFixtureFixer()]);
