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

namespace PhpCsFixer\Tokenizer\Analyzer;

use PhpCsFixer\Preg;
use PhpCsFixer\Tokenizer\Analyzer\Analysis\AttributeAnalysis;
use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @internal
 */
final class AttributeAnalyzer
{
    private const TOKEN_KINDS_NOT_ALLOWED_IN_ATTRIBUTE = [
        ';',
        '{',
        [T_ATTRIBUTE],
        [T_FUNCTION],
        [T_OPEN_TAG],
        [T_OPEN_TAG_WITH_ECHO],
        [T_PRIVATE],
        [T_PROTECTED],
        [T_PUBLIC],
        [T_RETURN],
        [T_VARIABLE],
        [CT::T_ATTRIBUTE_CLOSE],
    ];

    /**
     * Check if given index is an attribute declaration.
     */
    public static function isAttribute(Tokens $tokens, int $index): bool
    {
        if (
            !\defined('T_ATTRIBUTE') // attributes not available, PHP version lower than 8.0
            || !$tokens[$index]->isGivenKind(T_STRING) // checked token is not a string
            || !$tokens->isAnyTokenKindsFound([T_ATTRIBUTE]) // no attributes in the tokens collection
        ) {
            return false;
        }

        $attributeStartIndex = $tokens->getPrevTokenOfKind($index, self::TOKEN_KINDS_NOT_ALLOWED_IN_ATTRIBUTE);
        if (!$tokens[$attributeStartIndex]->isGivenKind(T_ATTRIBUTE)) {
            return false;
        }

        // now, between attribute start and the attribute candidate index cannot be more "(" than ")"
        $count = 0;
        for ($i = $attributeStartIndex + 1; $i < $index; ++$i) {
            if ($tokens[$i]->equals('(')) {
                ++$count;
            } elseif ($tokens[$i]->equals(')')) {
                --$count;
            }
        }

        return 0 === $count;
    }

    /**
     * Find all consecutive elements that start with #[ and end with ] and the attributes inside.
     *
     * @return list<AttributeAnalysis>
     */
    public static function collectFor(Tokens $tokens, int $index): array
    {
        while (!$tokens[$index]->isGivenKind(T_ATTRIBUTE)) {
            if (null === $index = $tokens->getPrevNonWhitespace($index)) {
                return [];
            }
        }

        /** @var list<AttributeAnalysis> $elements */
        $elements = [];

        $openingIndex = $index;
        do {
            $startIndex = $index;
            $closingIndex = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_ATTRIBUTE, $openingIndex);
            $index = $tokens->getNextNonWhitespace($closingIndex);

            $elements[] = new AttributeAnalysis(
                $startIndex,
                $index - 1,
                self::collectAttributes($tokens, $startIndex, $closingIndex),
            );

            $openingIndex = $tokens->getNextMeaningfulToken($closingIndex);
        } while ($tokens[$openingIndex]->isGivenKind(T_ATTRIBUTE));

        return $elements;
    }

    /**
     * @return list<array{start: int, end: int, name: string}>
     */
    private static function collectAttributes(Tokens $tokens, int $index, int $closingIndex): array
    {
        /** @var list<array{start: int, end: int, name: string}> $elements */
        $elements = [];

        do {
            $attributeStartIndex = $index + 1;

            $nameStartIndex = $tokens->getNextTokenOfKind($index, [[T_STRING], [T_NS_SEPARATOR]]);
            $index = $tokens->getNextTokenOfKind($attributeStartIndex, ['(', ',', [CT::T_ATTRIBUTE_CLOSE]]);
            $attributeName = $tokens->generatePartialCode($nameStartIndex, $tokens->getPrevMeaningfulToken($index));

            // Find closing parentheses, we need to do this in case there's a comma inside the parentheses
            if ($tokens[$index]->equals('(')) {
                $index = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $index);
                $index = $tokens->getNextTokenOfKind($index, [',', [CT::T_ATTRIBUTE_CLOSE]]);
            }

            $elements[] = [
                'start' => $attributeStartIndex,
                'end' => $index - 1,
                'name' => $attributeName,
            ];

            $nextIndex = $index;

            // In case there's a comma right before T_ATTRIBUTE_CLOSE
            if ($nextIndex < $closingIndex) {
                $nextIndex = $tokens->getNextMeaningfulToken($index);
            }
        } while ($nextIndex < $closingIndex);

        // End last element at newline if it exists and there's no trailing comma
        --$index;
        while ($tokens[$index]->isWhitespace()) {
            if (Preg::match('/\R/', $tokens[$index]->getContent())) {
                $lastElementKey = array_key_last($elements);
                $elements[$lastElementKey]['end'] = $index - 1;

                break;
            }
            --$index;
        }

        return $elements;
    }
}
