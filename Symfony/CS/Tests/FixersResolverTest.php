<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Tests;

use Symfony\CS\Config\Config;
use Symfony\CS\Fixer;
use Symfony\CS\FixersResolver;

class FixersResolverTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        $this->fixer = new Fixer();
        $this->fixer->registerBuiltInFixers();
        $this->fixer->registerBuiltInConfigs();

        $this->config = new Config();

        $this->resolver = new FixersResolver($this->fixer->getFixers(), $this->config);
    }

    public function testResolveWithIncludeAndExcludeNames()
    {
        $fixers = $this->resolver->resolve('psr1', '-encoding,php_closing_tag');

        $enabledEncoding = false;
        $enabledPhpClosingTag = false;

        foreach ($fixers as $fixer) {
            switch ($fixer->getName()) {
                case 'encoding':        // psr1
                    $enabledEncoding = true;
                    break;
                case 'php_closing_tag': // psr2
                    $enabledPhpClosingTag = true;
                    break;
            }
        }

        $this->assertFalse($enabledEncoding);
        $this->assertTrue($enabledPhpClosingTag);
    }
}
