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

namespace Symfony\CS\Tests;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
class AbstractFixerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException        \LogicException
     * @expectedExceptionMessage Can not determine Fixer level
     */
    public function testLevelThatNotExists()
    {
        $mockup = $this->getMockForAbstractClass('\\Symfony\\CS\\AbstractFixer');
        $mockup->getLevel();
    }

    public function testSupports()
    {
        $mockup = $this->getMockForAbstractClass('\\Symfony\\CS\\AbstractFixer');
        $this->assertTrue($mockup->supports(new \SplFileInfo(__FILE__)));
    }
}
