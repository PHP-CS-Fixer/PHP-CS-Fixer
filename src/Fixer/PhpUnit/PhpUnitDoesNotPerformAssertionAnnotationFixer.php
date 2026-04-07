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

use PhpCsFixer\DocBlock\DocBlock;
use PhpCsFixer\Fixer\AbstractPhpUnitFixer;
use PhpCsFixer\Fixer\WhitespacesAwareFixerInterface;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Analyzer\WhitespacesAnalyzer;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use PhpCsFixer\Tokenizer\TokensAnalyzer;

/**
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 */
final class PhpUnitDoesNotPerformAssertionAnnotationFixer extends AbstractPhpUnitFixer implements WhitespacesAwareFixerInterface
{
    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(
            'Use PHPUnit assertion `expectNotToPerformAssertion` instead of `@doesNotPerformAssertions` annotation.',
            [
                new CodeSample(
                    <<<'PHP'
                        <?php
                        final class MyTest extends \PHPUnit_Framework_TestCase
                        {
                            /**
                             * @doesNotPerformAssertions
                             */
                            public function testFix(): void
                            {
                                foo();
                            }
                        }

                        PHP,
                ),
            ],
        );
    }

    protected function applyPhpUnitClassFix(Tokens $tokens, int $startIndex, int $endIndex): void
    {
        $tokensAnalyzer = new TokensAnalyzer($tokens);
        $slices = [];

        for ($i = $endIndex - 1; $i > $startIndex; --$i) {
            if (!$tokens[$i]->isGivenKind(\T_FUNCTION) || $tokensAnalyzer->isLambda($i)) {
                continue;
            }

            $functionIndex = $i;
            $docBlockIndex = $i;

            // ignore abstract functions
            $braceIndex = $tokens->getNextTokenOfKind($functionIndex, [';', '{']);
            if (!$tokens[$braceIndex]->equals('{')) {
                continue;
            }

            do {
                $docBlockIndex = $tokens->getPrevNonWhitespace($docBlockIndex);
            } while ($tokens[$docBlockIndex]->isGivenKind([\T_PUBLIC, \T_PROTECTED, \T_PRIVATE, \T_FINAL, \T_ABSTRACT, \T_COMMENT]));

            if (!$tokens[$docBlockIndex]->isGivenKind(\T_DOC_COMMENT)) {
                continue;
            }

            $doc = new DocBlock($tokens[$docBlockIndex]->getContent());
            $found = false;

            foreach ($doc->getAnnotationsOfType([
                'doesNotPerformAssertions',
            ]) as $annotation) {
                $annotation->remove();
                $found = true;
            }

            if (!$found) {
                continue;
            }

            $originalIndent = WhitespacesAnalyzer::detectIndent($tokens, $docBlockIndex);

            $newMethods = [new Token([
                \T_WHITESPACE,
                $this->whitespacesConfig->getLineEnding().$originalIndent.$this->whitespacesConfig->getIndent(),
            ]), new Token([\T_VARIABLE, '$this']), new Token([\T_OBJECT_OPERATOR, '->']), new Token([\T_STRING, 'expectNotToPerformAssertions']), new Token('('), new Token(')'), new Token(';')];

            // apply changes
            $docContent = $doc->getContent();
            if ('' === $docContent) {
                $docContent = '/** */';
            }
            $tokens[$docBlockIndex] = new Token([\T_DOC_COMMENT, $docContent]);
            $slices[$braceIndex + 1] = $newMethods;

            if ('}' === $tokens[$braceIndex + 1]->getContent()) {
                continue;
            }

            $whitespaceIndex = $braceIndex + 1;
            $tokens[$whitespaceIndex] = new Token([
                \T_WHITESPACE,
                $this->whitespacesConfig->getLineEnding().$tokens[$whitespaceIndex]->getContent(),
            ]);

            $i = $docBlockIndex;
        }
        $tokens->insertSlices($slices);
    }
}
