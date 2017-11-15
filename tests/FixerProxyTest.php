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

namespace PhpCsFixer\Tests;

use PhpCsFixer\FixerProxy;
use PhpCsFixer\Tests\Fixtures\FixerProxy\DeprecatedFixer;
use PHPUnit\Framework\TestCase;

/**
 * @author Kuba Werłos <werlos@gmail.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\FixerProxy
 */
final class FixerProxyTest extends TestCase
{
    public function testUserDeprecatedErrorIsSilenced()
    {
        $proxy = new FixerProxy(DeprecatedFixer::class);

        $this->assertInstanceOf(DeprecatedFixer::class, $proxy->retrieveFixer());
    }
}
