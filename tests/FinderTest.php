<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace PhpCsFixer\Tests\Finder;

use PhpCsFixer\Finder;

/**
 * @internal
 */
final class FinderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException              \LogicException
     * @expectedExceptionMessageRegExp /^You must call (?:the in\(\) method)|(?:one of in\(\) or append\(\)) methods before iterating over a Finder\.$/
     */
    public function testThatDefaultFinderDoesNotSpecifyAnyDirectory()
    {
        $finder = Finder::create();
        $finder->getIterator();
    }
}
