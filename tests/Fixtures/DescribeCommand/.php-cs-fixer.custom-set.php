<?php

use PhpCsFixer\Config;
use PhpCsFixer\Finder;
use PhpCsFixer\Tests\Fixtures\ExternalRuleSet\ExampleRuleSet;

return (new Config())
    ->setFinder((new Finder())->in(__DIR__))
    ->registerCustomRuleSets([new ExampleRuleSet()]);
