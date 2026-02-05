<?php

declare(strict_types=1);

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
use Symfony\Component\Finder\SplFileInfo;

/**
 * @internal
 *
 * @covers \PhpCsFixer\Finder
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 */
final class FinderTest extends TestCase
{
    public function testThatDefaultFinderDoesNotSpecifyAnyDirectory(): void
    {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessageMatches('/^You must call (?:the in\(\) method)|(?:one of in\(\) or append\(\)) methods before iterating over a Finder\.$/');

        $finder = Finder::create();
        $finder->getIterator();
    }

    public function testThatFinderFindsDotFilesWhenConfigured(): void
    {
        $finder = Finder::create()
            ->in(__DIR__.'/..')
            ->depth(0)
            ->ignoreDotFiles(false)
        ;

        self::assertContains(
            realpath(__DIR__.'/../.php-cs-fixer.dist.php'),
            array_map(
                static fn (SplFileInfo $file): string => $file->getRealPath(),
                iterator_to_array($finder->getIterator()),
            ),
        );
    }
}
