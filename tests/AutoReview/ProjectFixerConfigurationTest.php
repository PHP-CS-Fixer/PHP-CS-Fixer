<?php

/*
 * This file is part of PHP CS Fixer.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *     Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace PhpCsFixer\Tests\AutoReview;

use PhpCsFixer\Config;
use PhpCsFixer\Console\ConfigurationResolver;
use PhpCsFixer\Tests\TestCase;
use PhpCsFixer\ToolInfo;

/**
 * @author SpacePossum
 *
 * @internal
 *
 * @coversNothing
 * @group auto-review
 */
final class ProjectFixerConfigurationTest extends TestCase
{
    /**
     * @var Config
     */
    private $config;

    protected function setUp()
    {
        $file = __DIR__.'/../../.php_cs.dist';
        $this->config = require $file;
    }

    public function testCreate()
    {
        $this->assertInstanceOf('PhpCsFixer\Config', $this->config);
        $this->assertEmpty($this->config->getCustomFixers());
        $this->assertNotEmpty($this->config->getRules());

        // call so the fixers get configured to reveal issue (like deprecated configuration used etc.)
        $resolver = new ConfigurationResolver(
            $this->config,
            array(),
            __DIR__,
            new ToolInfo()
        );

        $resolver->getFixers();
    }

    public function testRuleDefinedAlpha()
    {
        $rules = $rulesSorted = array_keys($this->config->getRules());
        sort($rulesSorted);
        $this->assertSame($rulesSorted, $rules, 'Please sort the "rules" in `.php_cs.dist` of this project.');
    }
}
