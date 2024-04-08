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

namespace PhpCsFixer\Fixer\PhpUnit;

use PhpCsFixer\Fixer\AbstractPhpUnitFixer;
use PhpCsFixer\Fixer\WhitespacesAwareFixerInterface;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Tokens;
use PhpCsFixer\Tokenizer\TokensAnalyzer;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
final class PhpUnitTestClassRequiresCoversFixer extends AbstractPhpUnitFixer implements WhitespacesAwareFixerInterface
{
    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(
            'Adds a default `@coversNothing` annotation to PHPUnit test classes that have no `@covers*` annotation.',
            [
                new CodeSample(
                    '<?php
final class MyTest extends \PHPUnit_Framework_TestCase
{
    public function testSomeTest()
    {
        $this->assertSame(a(), b());
    }
}
'
                ),
            ]
        );
    }

    /**
     * {@inheritdoc}
     *
     * Must run before PhpdocSeparationFixer.
     */
    public function getPriority(): int
    {
        return 0;
    }

    protected function applyPhpUnitClassFix(Tokens $tokens, int $startIndex, int $endIndex): void
    {
        $classIndex = $tokens->getPrevTokenOfKind($startIndex, [[T_CLASS]]);

        $tokensAnalyzer = new TokensAnalyzer($tokens);
        $modifiers = $tokensAnalyzer->getClassyModifiers($classIndex);

        if (isset($modifiers['abstract'])) {
            return; // don't add `@covers` annotation for abstract base classes
        }

        $this->ensureIsDocBlockWithAnnotation(
            $tokens,
            $classIndex,
            'coversNothing',
            [
                'covers',
                'coversDefaultClass',
                'coversNothing',
            ],
            [
                'phpunit\\framework\\attributes\\coversclass',
                'phpunit\\framework\\attributes\\coversnothing',
            ],
        );
    }
}
