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
    public function testCreate()
    {
        /** @var \PhpCsFixer\Config $config */
        $config = require __DIR__.'/../../.php_cs.dist';

        $this->assertInstanceOf(\PhpCsFixer\Config::class, $config);
        $this->assertEmpty($config->getCustomFixers());
        $this->assertNotEmpty($config->getRules());

        // call so the fixers get configured to reveal issue (like deprecated configuration used etc.)
        $resolver = new ConfigurationResolver(
            $config,
            [],
            __DIR__,
            new ToolInfo()
        );

        $resolver->getFixers();
    }
}
