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
use PHPUnit\Framework\TestCase;

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
        $setLocation = __DIR__.'/../.php_cs.dist';

        /** @var \PhpCsFixer\Config $config */
        $config = require $setLocation;

        $this->assertInstanceOf('PhpCsFixer\Config', $config);
        $this->assertEmpty($config->getCustomFixers());
        $this->assertNotEmpty($config->getRules());

        $resolver = new ConfigurationResolver($config, array(), __DIR__);
        $resolver->getFixers();
    }
}
