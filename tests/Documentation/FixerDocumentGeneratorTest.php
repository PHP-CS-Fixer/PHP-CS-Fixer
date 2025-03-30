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
use PhpCsFixer\Documentation\FixerDocumentGenerator;
use PhpCsFixer\Fixer\Basic\BracesFixer;
use PhpCsFixer\Fixer\ClassNotation\VisibilityRequiredFixer;
use PhpCsFixer\Fixer\Comment\HeaderCommentFixer;
use PhpCsFixer\Fixer\FixerInterface;
use PhpCsFixer\Fixer\LanguageConstruct\ClassKeywordFixer;
use PhpCsFixer\Fixer\Strict\StrictParamFixer;
use PhpCsFixer\FixerFactory;
use PhpCsFixer\Tests\TestCase;

/**
 * @internal
 *
 * @covers \PhpCsFixer\Documentation\FixerDocumentGenerator
 */
final class FixerDocumentGeneratorTest extends TestCase
{
    /**
     * @dataProvider provideGenerateRuleSetsDocumentationCases
     */
    public function testGenerateRuleSetsDocumentation(FixerInterface $fixer): void
    {
        $locator = new DocumentationLocator();
        $generator = new FixerDocumentGenerator($locator);

        self::assertSame(
            file_get_contents($locator->getFixerDocumentationFilePath($fixer)),
            $generator->generateFixerDocumentation($fixer),
        );
    }

    /**
     * @return iterable<int, array{FixerInterface}>
     */
    public static function provideGenerateRuleSetsDocumentationCases(): iterable
    {
        yield [new BracesFixer()];

        yield [new ClassKeywordFixer()];

        yield [new HeaderCommentFixer()];

        yield [new StrictParamFixer()];

        yield [new VisibilityRequiredFixer()];
    }

    public function testGenerateFixersDocumentationIndex(): void
    {
        $locator = new DocumentationLocator();
        $generator = new FixerDocumentGenerator($locator);

        $fixerFactory = new FixerFactory();
        $fixerFactory->registerBuiltInFixers();
        $fixers = $fixerFactory->getFixers();

        self::assertSame(
            file_get_contents($locator->getFixersDocumentationIndexFilePath()),
            $generator->generateFixersDocumentationIndex($fixers),
        );
    }
}
