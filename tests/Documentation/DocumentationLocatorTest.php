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

namespace PhpCsFixer\Tests\Documentation;

use PhpCsFixer\Documentation\DocumentationLocator;
use PhpCsFixer\Fixer\Casing\ConstantCaseFixer;
use PhpCsFixer\Tests\TestCase;

/**
 * @internal
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 *
 * @covers \PhpCsFixer\Documentation\DocumentationLocator
 */
final class DocumentationLocatorTest extends TestCase
{
    public function testFixersDocumentationDirectoryPath(): void
    {
        self::assertSame(
            realpath(__DIR__.'/../..').'/doc/rules',
            (new DocumentationLocator())->getFixersDocumentationDirectoryPath()
        );
    }

    public function testFixersDocumentationIndexFilePath(): void
    {
        self::assertSame(
            realpath(__DIR__.'/../..').'/doc/rules/index.rst',
            (new DocumentationLocator())->getFixersDocumentationIndexFilePath()
        );
    }

    public function testFixerDocumentationFilePath(): void
    {
        self::assertSame(
            realpath(__DIR__.'/../..').'/doc/rules/casing/constant_case.rst',
            (new DocumentationLocator())->getFixerDocumentationFilePath(new ConstantCaseFixer())
        );
    }

    public function testFixerDocumentationFileRelativePath(): void
    {
        self::assertSame(
            'casing/constant_case.rst',
            (new DocumentationLocator())->getFixerDocumentationFileRelativePath(new ConstantCaseFixer())
        );
    }

    public function testRuleSetsDocumentationDirectoryPath(): void
    {
        self::assertSame(
            realpath(__DIR__.'/../..').'/doc/ruleSets',
            (new DocumentationLocator())->getRuleSetsDocumentationDirectoryPath()
        );
    }

    public function testRuleSetsDocumentationIndexFilePath(): void
    {
        self::assertSame(
            realpath(__DIR__.'/../..').'/doc/ruleSets/index.rst',
            (new DocumentationLocator())->getRuleSetsDocumentationIndexFilePath()
        );
    }

    public function testRuleSetsDocumentationFilePath(): void
    {
        self::assertSame(
            realpath(__DIR__.'/../..').'/doc/ruleSets/PhpCsFixerRisky.rst',
            (new DocumentationLocator())->getRuleSetsDocumentationFilePath('@PhpCsFixer:risky')
        );
    }

    public function testUsageFilePath(): void
    {
        self::assertSame(
            realpath(__DIR__.'/../..').'/doc/usage.rst',
            (new DocumentationLocator())->getUsageFilePath()
        );
    }
}
