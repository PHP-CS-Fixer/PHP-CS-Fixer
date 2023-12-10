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

use PhpCsFixer\Tokenizer\Analyzer\Analysis\AbstractControlCaseStructuresAnalysis;
use PhpCsFixer\Tokenizer\Analyzer\Analysis\CaseAnalysis;
use PhpCsFixer\Tokenizer\Analyzer\Analysis\DefaultAnalysis;
use PhpCsFixer\Tokenizer\Analyzer\Analysis\EnumAnalysis;
use PhpCsFixer\Tokenizer\Analyzer\Analysis\MatchAnalysis;
use PhpCsFixer\Tokenizer\Analyzer\Analysis\SwitchAnalysis;
use PhpCsFixer\Tokenizer\Tokens;

final class ControlCaseStructuresAnalyzer
{
    /**
     * @param list<int> $types Token types of interest of which analyzes must be returned
     *
     * @return \Generator<int, AbstractControlCaseStructuresAnalysis>
     */
    public static function findControlStructures(Tokens $tokens, array $types): \Generator
    {
        if (\count($types) < 1) {
            return; // quick skip
        }

        $typesWithCaseOrDefault = self::getTypesWithCaseOrDefault();

        foreach ($types as $type) {
            if (!\in_array($type, $typesWithCaseOrDefault, true)) {
                throw new \InvalidArgumentException(sprintf('Unexpected type "%d".', $type));
            }
        }

        if (!$tokens->isAnyTokenKindsFound($types)) {
            return; // quick skip
        }

        $depth = -1;

        /**
         * @var list<array{
         *     kind: int|null,
         *     index: int,
         *     brace_count: int,
         *     cases: list<array{index: int, open: int}>,
         *     default: array{index: int, open: int}|null,
         *     alternative_syntax: bool,
         * }> $stack
         */
        $stack = [];
        $isTypeOfInterest = false;

        foreach ($tokens as $index => $token) {
            if ($token->isGivenKind($typesWithCaseOrDefault)) {
                ++$depth;

                $stack[$depth] = [
                    'kind' => $token->getId(),
                    'index' => $index,
                    'brace_count' => 0,
                    'cases' => [],
                    'default' => null,
                    'alternative_syntax' => false,
                ];

                $isTypeOfInterest = \in_array($stack[$depth]['kind'], $types, true);

                if ($token->isGivenKind(T_SWITCH)) {
                    $index = $tokens->getNextMeaningfulToken($index);
                    $index = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $index);

                    $stack[$depth]['open'] = $tokens->getNextMeaningfulToken($index);
                    $stack[$depth]['alternative_syntax'] = $tokens[$stack[$depth]['open']]->equals(':');
                } elseif (\defined('T_MATCH') && $token->isGivenKind(T_MATCH)) { // @TODO: drop condition when PHP 8.0+ is required
                    $index = $tokens->getNextMeaningfulToken($index);
                    $index = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $index);

                    $stack[$depth]['open'] = $tokens->getNextMeaningfulToken($index);
                } elseif (\defined('T_ENUM') && $token->isGivenKind(T_ENUM)) {
                    $stack[$depth]['open'] = $tokens->getNextTokenOfKind($index, ['{']);
                }

                continue;
            }

            if ($depth < 0) {
                continue;
            }

            if ($token->equals('{')) {
                ++$stack[$depth]['brace_count'];

                continue;
            }

            if ($token->equals('}')) {
                --$stack[$depth]['brace_count'];

                if (0 === $stack[$depth]['brace_count']) {
                    if ($stack[$depth]['alternative_syntax']) {
                        continue;
                    }

                    if ($isTypeOfInterest) {
                        $stack[$depth]['end'] = $index;

                        yield $stack[$depth]['index'] => self::buildControlCaseStructureAnalysis($stack[$depth]);
                    }

                    array_pop($stack);
                    --$depth;

                    if ($depth < -1) { // @phpstan-ignore-line
                        throw new \RuntimeException('Analysis depth count failure.');
                    }

                    if (isset($stack[$depth]['kind'])) {
                        $isTypeOfInterest = \in_array($stack[$depth]['kind'], $types, true);
                    }
                }

                continue;
            }

            if ($tokens[$index]->isGivenKind(T_ENDSWITCH)) {
                if (!$stack[$depth]['alternative_syntax']) {
                    throw new \RuntimeException('Analysis syntax failure, unexpected "T_ENDSWITCH".');
                }

                if (T_SWITCH !== $stack[$depth]['kind']) {
                    throw new \RuntimeException('Analysis type failure, unexpected "T_ENDSWITCH".');
                }

                if (0 !== $stack[$depth]['brace_count']) {
                    throw new \RuntimeException('Analysis count failure, unexpected "T_ENDSWITCH".');
                }

                $index = $tokens->getNextTokenOfKind($index, [';', [T_CLOSE_TAG]]);

                if ($isTypeOfInterest) {
                    $stack[$depth]['end'] = $index;

                    yield $stack[$depth]['index'] => self::buildControlCaseStructureAnalysis($stack[$depth]);
                }

                array_pop($stack);
                --$depth;

                if ($depth < -1) { // @phpstan-ignore-line
                    throw new \RuntimeException('Analysis depth count failure ("T_ENDSWITCH").');
                }

                if (isset($stack[$depth]['kind'])) {
                    $isTypeOfInterest = \in_array($stack[$depth]['kind'], $types, true);
                }
            }

            if (!$isTypeOfInterest) {
                continue; // don't bother to analyze stuff that caller is not interested in
            }

            if ($token->isGivenKind(T_CASE)) {
                $stack[$depth]['cases'][] = ['index' => $index, 'open' => self::findCaseOpen($tokens, $stack[$depth]['kind'], $index)];
            } elseif ($token->isGivenKind(T_DEFAULT)) {
                if (null !== $stack[$depth]['default']) {
                    throw new \RuntimeException('Analysis multiple "default" found.');
                }

                $stack[$depth]['default'] = ['index' => $index, 'open' => self::findDefaultOpen($tokens, $stack[$depth]['kind'], $index)];
            }
        }
    }

    /**
     * @param array{
     *     kind: int,
     *     index: int,
     *     open: int,
     *     end: int,
     *     cases: list<array{index: int, open: int}>,
     *     default: null|array{index: int, open: int},
     * } $analysis
     */
    private static function buildControlCaseStructureAnalysis(array $analysis): AbstractControlCaseStructuresAnalysis
    {
        $default = null === $analysis['default']
            ? null
            : new DefaultAnalysis($analysis['default']['index'], $analysis['default']['open']);

        $cases = [];

        foreach ($analysis['cases'] as $case) {
            $cases[$case['index']] = new CaseAnalysis($case['index'], $case['open']);
        }

        sort($cases);

        if (T_SWITCH === $analysis['kind']) {
            return new SwitchAnalysis(
                $analysis['index'],
                $analysis['open'],
                $analysis['end'],
                $cases,
                $default
            );
        }

        if (\defined('T_ENUM') && T_ENUM === $analysis['kind']) {
            return new EnumAnalysis(
                $analysis['index'],
                $analysis['open'],
                $analysis['end'],
                $cases
            );
        }

        if (\defined('T_MATCH') && T_MATCH === $analysis['kind']) { // @TODO: drop condition when PHP 8.0+ is required
            return new MatchAnalysis(
                $analysis['index'],
                $analysis['open'],
                $analysis['end'],
                $default
            );
        }

        throw new \InvalidArgumentException(sprintf('Unexpected type "%d".', $analysis['kind']));
    }

    private static function findCaseOpen(Tokens $tokens, int $kind, int $index): int
    {
        if (T_SWITCH === $kind) {
            $ternariesCount = 0;

            do {
                if ($tokens[$index]->equalsAny(['(', '{'])) { // skip constructs
                    $type = Tokens::detectBlockType($tokens[$index]);
                    $index = $tokens->findBlockEnd($type['type'], $index);

                    continue;
                }

                if ($tokens[$index]->equals('?')) {
                    ++$ternariesCount;

                    continue;
                }

                if ($tokens[$index]->equalsAny([':', ';'])) {
                    if (0 === $ternariesCount) {
                        break;
                    }

                    --$ternariesCount;
                }
            } while (++$index);

            return $index;
        }

        if (\defined('T_ENUM') && T_ENUM === $kind) {
            return $tokens->getNextTokenOfKind($index, ['=', ';']);
        }

        throw new \InvalidArgumentException(sprintf('Unexpected case for type "%d".', $kind));
    }

    private static function findDefaultOpen(Tokens $tokens, int $kind, int $index): int
    {
        if (T_SWITCH === $kind) {
            return $tokens->getNextTokenOfKind($index, [':', ';']);
        }

        if (\defined('T_MATCH') && T_MATCH === $kind) { // @TODO: drop condition when PHP 8.0+ is required
            return $tokens->getNextTokenOfKind($index, [[T_DOUBLE_ARROW]]);
        }

        throw new \InvalidArgumentException(sprintf('Unexpected default for type "%d".', $kind));
    }

    /**
     * @return list<int>
     */
    private static function getTypesWithCaseOrDefault(): array
    {
        $supportedTypes = [T_SWITCH];

        if (\defined('T_MATCH')) { // @TODO: drop condition when PHP 8.0+ is required
            $supportedTypes[] = T_MATCH;
        }

        if (\defined('T_ENUM')) { // @TODO: drop condition when PHP 8.1+ is required
            $supportedTypes[] = T_ENUM;
        }

        return $supportedTypes;
    }
}
