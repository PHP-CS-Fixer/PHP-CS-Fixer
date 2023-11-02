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

namespace PhpCsFixer\Fixer\Casing;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\FixerDefinition\VersionSpecification;
use PhpCsFixer\FixerDefinition\VersionSpecificCodeSample;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @author Torben Nordtorp <torben.nordtorp@icloud.com>
 *
 * Fixer for uppercase enum case names.
 */
final class UppercaseCaseNameFixer extends AbstractFixer
{
    public function isRisky(): bool
    {
        return true;
    }

    public function isCandidate(Tokens $tokens): bool
    {
        return $tokens->isTokenKindFound(T_CASE);
    }

    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(
            'PHP enums case names must be uppercased.',
            [
                new VersionSpecificCodeSample(
                    '<?php
enum Example
{
    case element1;
    case element2;
}
'
                    , new VersionSpecification(8_01_00)
                ),
            ],
            null,
            'Since this changes variable names, it might break references.'
        );
    }

    protected function applyFix(\SplFileInfo $file, Tokens $tokens): void
    {
        foreach ($tokens as $index => $token) {
            if (
                $this->isEnumCaseName($tokens, $index)
            ) {
                $tokens[$index] = new Token([$token->getId(), strtoupper($token->getContent())]);
            }
        }
    }

    private function isEnumCaseName(Tokens $tokens, int $index): bool
    {
        if (!\defined('T_ENUM') || !$tokens->isTokenKindFound(T_ENUM)) { // @TODO: drop condition when PHP 8.1+ is required
            return false;
        }

        $prevIndex = $tokens->getPrevMeaningfulToken($index);

        if (null === $prevIndex || !$tokens[$prevIndex]->isGivenKind(T_CASE)) {
            return false;
        }

        if (!$tokens->isTokenKindFound(T_SWITCH)) {
            return true;
        }

        $prevIndex = $tokens->getPrevTokenOfKind($prevIndex, [[T_ENUM], [T_SWITCH]]);

        return null !== $prevIndex && $tokens[$prevIndex]->isGivenKind(T_ENUM);
    }
}
