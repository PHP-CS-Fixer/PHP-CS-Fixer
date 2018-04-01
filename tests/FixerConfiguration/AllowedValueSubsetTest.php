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

namespace PhpCsFixer\Tests\FixerConfiguration;

use PhpCsFixer\FixerConfiguration\AllowedValueSubset;
use PhpCsFixer\Tests\TestCase;

/**
 * @internal
 */
final class AllowedValueSubsetTest extends TestCase
{
    public function testGetValues()
    {
        $values = array('foo', 'bar');

        $subset = new AllowedValueSubset($values);

        $this->assertSame($values, $subset->getValues());
    }
}
