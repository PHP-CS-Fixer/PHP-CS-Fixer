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
final class PhpUnitDoesNotPerformAssertionsAnnotationFixer extends AbstractPhpUnitFixer implements WhitespacesAwareFixerInterface
{
    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(
            'Usages of the `@doesNotPerformAssertions` annotation MUST be replaced by the `->expectNotToPerformAssertions()` method.',
            [
                new CodeSample(
                    <<<'PHP'
                        <?php
                        final class MyTest extends \PHPUnit\Framework\TestCase
                        {
                            /**
                             * @doesNotPerformAssertions
                             */
                            public function testFoo(): void
                            {
                                foo();
                            }
                        }

                        PHP,
                ),
            ],
            'The method was added in PHPUnit 7.2.',
            'Risky when PHPUnit classes are overridden or not accessible, or when project has PHPUnit incompatibilities.',
        );
    }

    public function isCandidate(Tokens $tokens): bool
    {
        return parent::isCandidate($tokens) && $tokens->isTokenKindFound(\T_DOC_COMMENT);
    }

    /**
     * {@inheritdoc}
     *
     * Must run before NoEmptyPhpdocFixer, PhpUnitAttributesFixer.
     */
    public function getPriority(): int
    {
        return 10;
    }

    public function isRisky(): bool
    {
        return true;
    }

    protected function applyPhpUnitClassFix(Tokens $tokens, int $startIndex, int $endIndex): void
    {
        $tokensAnalyzer = new TokensAnalyzer($tokens);
        $slices = [];

        for ($index = $endIndex - 1; $index > $startIndex; --$index) {
            if (!$tokens[$index]->isGivenKind(\T_FUNCTION) || $tokensAnalyzer->isLambda($index)) {
                continue;
            }

            // ignore abstract methods
            $braceIndex = $tokens->getNextTokenOfKind($index, [';', '{']);
            if (!$tokens[$braceIndex]->equals('{')) {
                continue;
            }

            // static methods are skipped too, as `$this` is not available in them
            $docBlockIndex = $this->getDocBlockIndex($tokens, $index);
            if (!$tokens[$docBlockIndex]->isGivenKind(\T_DOC_COMMENT)) {
                continue;
            }

            $doc = new DocBlock($tokens[$docBlockIndex]->getContent());
            $annotations = $doc->getAnnotationsOfType('doesNotPerformAssertions');
            if ([] === $annotations) {
                continue;
            }

            foreach ($annotations as $annotation) {
                $annotation->remove();
            }

            $docContent = $doc->getContent();
            $tokens[$docBlockIndex] = new Token([\T_DOC_COMMENT, '' === $docContent ? '/** */' : $docContent]);

            $closeBraceIndex = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_BRACE, $braceIndex);
            if (!$this->callsExpectNotToPerformAssertions($tokens, $braceIndex, $closeBraceIndex)) {
                $slices[$braceIndex + 1] = $this->createCall($tokens, $braceIndex, WhitespacesAnalyzer::detectIndent($tokens, $docBlockIndex));
            }

            $index = $docBlockIndex;
        }

        $tokens->insertSlices($slices);
    }

    private function callsExpectNotToPerformAssertions(Tokens $tokens, int $openBraceIndex, int $closeBraceIndex): bool
    {
        for ($index = $openBraceIndex + 1; $index < $closeBraceIndex; ++$index) {
            if ($tokens[$index]->equals([\T_STRING, 'expectNotToPerformAssertions'], false)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Returns the tokens to insert after the opening brace, and adjusts the whitespace that follows it.
     *
     * @return list<Token>
     */
    private function createCall(Tokens $tokens, int $openBraceIndex, string $methodIndent): array
    {
        $lineEnding = $this->whitespacesConfig->getLineEnding();
        $bodyIndent = $methodIndent.$this->whitespacesConfig->getIndent();

        $nextIndex = $openBraceIndex + 1;
        $nextMeaningfulIndex = $tokens->getNextNonWhitespace($openBraceIndex);
        \assert(null !== $nextMeaningfulIndex);

        if ($tokens[$nextMeaningfulIndex]->equals('}')) {
            // empty body: the call takes the whole body
            $separator = $lineEnding.$methodIndent;
        } elseif ($tokens[$nextIndex]->isWhitespace() && str_contains($tokens[$nextIndex]->getContent(), "\n")) {
            // keep the indentation of the body, with a blank line after the call
            $separator = $lineEnding.$tokens[$nextIndex]->getContent();
        } else {
            $separator = $lineEnding.$lineEnding.$bodyIndent;
        }

        $call = [
            new Token([\T_WHITESPACE, $lineEnding.$bodyIndent]),
            new Token([\T_VARIABLE, '$this']),
            new Token([\T_OBJECT_OPERATOR, '->']),
            new Token([\T_STRING, 'expectNotToPerformAssertions']),
            new Token('('),
            new Token(')'),
            new Token(';'),
        ];

        if ($tokens[$nextIndex]->isWhitespace()) {
            $tokens[$nextIndex] = new Token([\T_WHITESPACE, $separator]);
        } else {
            $call[] = new Token([\T_WHITESPACE, $separator]);
        }

        return $call;
    }
}
