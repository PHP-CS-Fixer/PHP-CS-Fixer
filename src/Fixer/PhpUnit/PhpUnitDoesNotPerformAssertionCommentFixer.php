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
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 */
final class PhpUnitDoesNotPerformAssertionCommentFixer extends AbstractPhpUnitFixer
{
    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(
            'Use PHPUnit assertion `expectNotToPerformAssertion` instead of `@doesNotPerformAssertions` comment.',
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
        $changes = [];
        for ($index = $startIndex; $index < $endIndex; ++$index) {
            $content = $tokens[$index]->getContent();
            $tokenId = $tokens[$index]->getId();
            if (\T_DOC_COMMENT === $tokenId && str_contains($content, '* @doesNotPerformAssertions')) {
                $newString = preg_replace('(\n\s+\* @doesNotPerformAssertions)', '', $content);
                // Delete comment if empty
                if (preg_match_all('(/\*\*\s*\*/)', $newString)) {
                    if ($tokens[$index - 1]->isWhitespace()) {
                        $tokens->clearAt($index - 1);
                    }
                    $tokens->clearAt($index);
                } else {
                    $tokens[$index] = new Token([\T_DOC_COMMENT, $newString]);
                }
                $index = $tokens->getNextMeaningfulToken($index);
                // If the next two keywords aren't a function skip it
                if (\T_FUNCTION !== $tokens[$index]->getId()) {
                    $index = $tokens->getNextMeaningfulToken($index);
                    if (\T_FUNCTION !== $tokens[$index]->getId()) {
                        continue;
                    }
                }
                $index = $tokens->getNextTokenOfKind($index, ['{']);
                $newTokens = [new Token([\T_VARIABLE, '$this']), new Token([\T_OBJECT_OPERATOR, '->']), new Token([\T_STRING, 'expectNotToPerformAssertions']), new Token('('), new Token(')'), new Token(';')];
                if ($tokens[$index + 1]->isWhitespace()) {
                    ++$index;
                    array_unshift($newTokens, new Token([$tokens[$index]->getId(), $tokens[$index]->getContent()]));
                }
                $changes[$index] = $newTokens;
            }
        }
        if (\count($changes) > 0) {
            $tokens->insertSlices($changes);
        }
    }
}
