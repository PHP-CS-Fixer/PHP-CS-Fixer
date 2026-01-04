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

namespace PhpCsFixer\Fixer\Strict;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 */
final class StrictComparisonFixer extends AbstractFixer
{
    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(
            'Comparisons should be strict.',
            [new CodeSample("<?php\n\$a = 1== \$b;\n")],
            null,
            'Changing comparisons to strict might change code behaviour.',
        );
    }

    /**
     * {@inheritdoc}
     *
     * Must run before BinaryOperatorSpacesFixer, ModernizeStrposFixer.
     */
    public function getPriority(): int
    {
        return 38;
    }

    public function isCandidate(Tokens $tokens): bool
    {
        return $tokens->isAnyTokenKindsFound([\T_IS_EQUAL, \T_IS_NOT_EQUAL]);
    }

    public function isRisky(): bool
    {
        return true;
    }

    protected function applyFix(\SplFileInfo $file, Tokens $tokens): void
    {
        foreach ($tokens as $index => $token) {
            $newToken = [
                \T_IS_EQUAL => [
                    'id' => \T_IS_IDENTICAL,
                    'content' => '===',
                ],
                \T_IS_NOT_EQUAL => [
                    'id' => \T_IS_NOT_IDENTICAL,
                    'content' => '!==',
                ],
            ][$token->getId()] ?? null;

            if (null !== $newToken) {
                $tokens[$index] = new Token([$newToken['id'], $newToken['content']]);
            }
        }
    }
}
