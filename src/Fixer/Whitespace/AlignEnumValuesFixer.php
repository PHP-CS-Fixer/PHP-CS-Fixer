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

namespace PhpCsFixer\Fixer\Whitespace;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use PhpCsFixer\Tokenizer\TokensAnalyzer;

final class AlignEnumValuesFixer extends AbstractFixer
{
    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(
            'Align enum values on the `=` operator.',
            [
                new CodeSample("<?php enum MyEnum: string
{
    case AB = 'ab';
    case C = 'c';
    case DEFGH = 'defgh';
}
"),
                new CodeSample('<?php enum MyEnum: int
{
    case BENJI          = 1;
    case ELIZABETH      = 2;
    case CORA           = 4;
}
'),
            ],
        );
    }

    public function isCandidate(Tokens $tokens): bool
    {
        // @TODO: drop condition when PHP 8.1+ is required
        if (!\defined('T_ENUM')) {
            return false;
        }

        return $tokens->isTokenKindFound(T_ENUM);
    }

    protected function applyFix(\SplFileInfo $file, Tokens $tokens): void
    {
        $tokenAnalyzer = new TokensAnalyzer($tokens);

        // Ignore first & last indexes.
        for ($index = 1; $index < \count($tokens); ++$index) {
            /** @var Token $token */
            $token = $tokens[$index];
            if ($token->isGivenKind(T_ENUM)) {
                $enumNameTokenIndex = $tokens->getNextMeaningfulToken($index);
                $nextToEnumNameTokenIndex = $tokens->getNextMeaningfulToken($enumNameTokenIndex);

                /** @var Token $nextToEnumNameToken */
                $nextToEnumNameToken = $tokens[$nextToEnumNameTokenIndex];
                if (!$nextToEnumNameToken->isGivenKind(CT::T_TYPE_COLON)) {
                    // This is NOT a backed enum, jump to the end of the block.
                    $blockType = Tokens::detectBlockType($nextToEnumNameToken);
                    $index = $tokens->findBlockEnd($blockType['type'], $nextToEnumNameTokenIndex);

                    continue;
                }
                $enumTypeTokenIndex = $tokens->getNextMeaningfulToken($nextToEnumNameTokenIndex);
                $startBlockTokenIndex = $tokens->getNextMeaningfulToken($enumTypeTokenIndex);
                if (!$tokenAnalyzer->isBlockMultiline($tokens, $startBlockTokenIndex)) {
                    // This enum is a single line enum, jump to the end of the block.
                    $blockType = Tokens::detectBlockType($tokens[$startBlockTokenIndex]);
                    $index = $tokens->findBlockEnd($blockType['type'], $startBlockTokenIndex);

                    continue;
                }
                $blockType = Tokens::detectBlockType($tokens[$startBlockTokenIndex]);
                $endBlockTokenIndex = $tokens->findBlockEnd($blockType['type'], $startBlockTokenIndex);

                $this->handleEnumBlock($tokens, $startBlockTokenIndex, $endBlockTokenIndex);
                $index = $endBlockTokenIndex;
            }
        }
    }

    private function handleEnumBlock(Tokens $tokens, int $startIndex, int $endIndex): void
    {
        $maxLength = 0;
        $enumCases = [];

        // Ignore block indexes and first & last indexes within the block.
        for ($index = $startIndex + 2; $index < $endIndex - 1; ++$index) {
            if ('=' === $tokens[$index]->getContent()) {
                $caseNameTokenIndex = $tokens->getPrevMeaningfulToken($index);

                /** @var Token $caseNameToken */
                $caseNameToken = $tokens[$caseNameTokenIndex];
                if (!$caseNameToken->isGivenKind(T_STRING)) {
                    throw new \LogicException('Should be a case value token.');
                }
                $caseNameLength = mb_strlen($caseNameToken->getContent());
                if ($caseNameLength > $maxLength) {
                    $maxLength = $caseNameLength;
                }

                $enumCases[] = [
                    'length' => $caseNameLength,
                    'spacingIndex' => $index - 1,
                ];
            }
        }

        foreach ($enumCases as $enumCase) {
            self::replaceSpacingToken($tokens, $enumCase['spacingIndex'], $maxLength - $enumCase['length'] + 1);
        }
    }

    private static function replaceSpacingToken(Tokens $tokens, int $index, int $spacingLength): void
    {
        $spacingContent = str_repeat(' ', $spacingLength);
        if ($tokens[$index]->isGivenKind(T_WHITESPACE) && $tokens[$index]->getContent() === $spacingContent) {
            // Spacing token is already well formatted. Avoid changing it.
            return;
        }

        $tokens[$index] = new Token([T_WHITESPACE, $spacingContent]);
    }
}
