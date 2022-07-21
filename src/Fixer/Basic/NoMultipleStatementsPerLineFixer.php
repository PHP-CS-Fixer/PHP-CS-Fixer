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

namespace PhpCsFixer\Fixer\Basic;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\Fixer\Indentation;
use PhpCsFixer\Fixer\WhitespacesAwareFixerInterface;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Preg;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * Fixer for rules defined in PSR2 ¶2.3 Lines: There must not be more than one statement per line.
 */
final class NoMultipleStatementsPerLineFixer extends AbstractFixer implements WhitespacesAwareFixerInterface
{
    use Indentation;

    /**
     * {@inheritdoc}
     */
    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(
            'There must not be more than one statement per line.',
            [new CodeSample("<?php\nfoo(); bar();\n")]
        );
    }

    /**
     * {@inheritdoc}
     *
     * Must run after NoEmptyStatementFixer.
     */
    public function getPriority(): int
    {
        return 39;
    }

    /**
     * {@inheritdoc}
     */
    public function isCandidate(Tokens $tokens): bool
    {
        return $tokens->isTokenKindFound(';');
    }

    /**
     * {@inheritdoc}
     */
    protected function applyFix(\SplFileInfo $file, Tokens $tokens): void
    {
        for ($index = 1, $max = \count($tokens) - 1; $index < $max; ++$index) {
            if ($tokens[$index]->isGivenKind(T_FOR)) {
                $index = $tokens->findBlockEnd(
                    Tokens::BLOCK_TYPE_PARENTHESIS_BRACE,
                    $tokens->getNextTokenOfKind($index, ['('])
                );

                continue;
            }

            if (!$tokens[$index]->equals(';')) {
                continue;
            }

            for ($nextIndex = $index + 1; $nextIndex < $max; ++$nextIndex) {
                $token = $tokens[$nextIndex];

                if ($token->isWhitespace() || $token->isComment()) {
                    if (1 === Preg::match('/\R/', $token->getContent())) {
                        break;
                    }

                    continue;
                }

                if (!$token->equalsAny(['}', [T_CLOSE_TAG], [T_ENDIF], [T_ENDFOR], [T_ENDSWITCH], [T_ENDWHILE], [T_ENDFOREACH]])) {
                    $whitespaceIndex = $index;
                    do {
                        $token = $tokens[++$whitespaceIndex];
                    } while ($token->isComment());

                    $newline = $this->whitespacesConfig->getLineEnding().$this->getLineIndentation($tokens, $index);

                    if ($tokens->ensureWhitespaceAtIndex($whitespaceIndex, 0, $newline)) {
                        ++$max;
                    }
                }

                break;
            }
        }
    }
}
