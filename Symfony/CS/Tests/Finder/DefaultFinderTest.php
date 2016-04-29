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

namespace Symfony\CS\Tests\Finder;

use Symfony\CS\Finder\DefaultFinder;

class DefaultFinderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException \LogicException
     * "You must call one of in() or append() methods before iterating over a Finder."
     */
    public function testThatDefaultFinderDoesNotSpecifyAnyDirectory()
    {
        $finder = DefaultFinder::create();
        $finder->getIterator();
    }
}
