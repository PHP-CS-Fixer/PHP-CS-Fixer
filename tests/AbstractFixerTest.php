<?php

/*
 * This file is part of the PHP CS Fixer.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *     Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace PhpCsFixer\Tests;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 */
final class AbstractFixerTest extends \PHPUnit_Framework_TestCase
{
    public function testSupports()
    {
        $mockup = $this->getMockForAbstractClass('\\PhpCsFixer\\AbstractFixer');
        $this->assertTrue($mockup->supports(new \SplFileInfo(__FILE__)));
    }
}
