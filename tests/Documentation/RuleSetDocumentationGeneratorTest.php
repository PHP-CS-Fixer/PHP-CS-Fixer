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
use PhpCsFixer\Documentation\RuleSetDocumentationGenerator;
use PhpCsFixer\FixerFactory;
use PhpCsFixer\RuleSet\RuleSets;
use PhpCsFixer\RuleSet\Sets\PERSet;
use PhpCsFixer\RuleSet\Sets\SymfonySet;
use PhpCsFixer\Tests\TestCase;

/**
 * @internal
 *
 * @covers \PhpCsFixer\Documentation\RuleSetDocumentationGenerator
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 */
final class RuleSetDocumentationGeneratorTest extends TestCase
{
    /**
     * @dataProvider provideGenerateRuleSetsDocumentationCases
     */
    public function testGenerateRuleSetsDocumentation(string $ruleSetName): void
    {
        $locator = new DocumentationLocator();
        $generator = new RuleSetDocumentationGenerator($locator);

        $fixerFactory = new FixerFactory();
        $fixerFactory->registerBuiltInFixers();
        $fixers = $fixerFactory->getFixers();

        self::assertSame(
            file_get_contents($locator->getRuleSetsDocumentationFilePath($ruleSetName)),
            $generator->generateRuleSetsDocumentation(RuleSets::getSetDefinition($ruleSetName), $fixers),
        );
    }

    /**
     * @return iterable<int, array{string}>
     */
    public static function provideGenerateRuleSetsDocumentationCases(): iterable
    {
        yield ['@PER'];

        yield ['@PhpCsFixer:risky'];
    }

    public function testGenerateRuleSetsDocumentationIndex(): void
    {
        $locator = new DocumentationLocator();
        $generator = new RuleSetDocumentationGenerator($locator);

        self::assertSame(
            <<<'RST'
                ===========================
                List of Available Rule sets
                ===========================
                - `@PER <./PER.rst>`_ *(deprecated)*
                - `@Symfony <./Symfony.rst>`_

                RST,
            $generator->generateRuleSetsDocumentationIndex([
                $locator->getRuleSetsDocumentationFilePath('@PER') => new PERSet(),
                $locator->getRuleSetsDocumentationFilePath('@Symfony') => new SymfonySet(),
            ]),
        );
    }
}
