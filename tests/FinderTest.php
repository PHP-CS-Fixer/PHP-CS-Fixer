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

use PhpCsFixer\Finder;

/**
 * @internal
 */
final class FinderTest extends \PHPUnit_Framework_TestCase
{
    public function testThatDefaultFinderDoesNotSpecifyAnyDirectory()
    {
        $this->setExpectedExceptionRegExp(
            'LogicException',
            '/^You must call (?:the in\(\) method)|(?:one of in\(\) or append\(\)) methods before iterating over a Finder\.$/'
        );

        $finder = Finder::create();
        $finder->getIterator();
    }
}
