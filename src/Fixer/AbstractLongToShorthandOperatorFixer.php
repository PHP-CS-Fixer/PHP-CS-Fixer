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

namespace PhpCsFixer\Fixer;

use PhpCsFixer\Tokenizer\FCT;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use PhpCsFixer\Tokenizer\TokensAnalyzer;

/**
 * Shared logic for the "long to shorthand operator" fixers, split by assignment
 * target: a non-risky fixer that only touches a plain variable target, and a
 * risky one that touches every other target (member access, offsets, ...).
 *
 * @phpstan-import-type _PhpTokenArray from Token
 *
 * @internal
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 */
abstract class AbstractLongToShorthandOperatorFixer extends AbstractShortOperatorFixer
{
    /**
     * @var non-empty-array<string, _PhpTokenArray>
     */
    private const OPERATORS = [
        '+' => [\T_PLUS_EQUAL, '+='],
        '-' => [\T_MINUS_EQUAL, '-='],
        '*' => [\T_MUL_EQUAL, '*='],
        '/' => [\T_DIV_EQUAL, '/='],
        '&' => [\T_AND_EQUAL, '&='],
        '.' => [\T_CONCAT_EQUAL, '.='],
        '%' => [\T_MOD_EQUAL, '%='],
        '|' => [\T_OR_EQUAL, '|='],
        '^' => [\T_XOR_EQUAL, '^='],
    ];

    /**
     * @var non-empty-list<string>
     */
    private array $operatorTypes;

    private TokensAnalyzer $tokensAnalyzer;

    /**
     * {@inheritdoc}
     *
     * Must run before BinaryOperatorSpacesFixer, NoExtraBlankLinesFixer, NoSinglelineWhitespaceBeforeSemicolonsFixer, StandardizeIncrementFixer.
     */
    public function getPriority(): int
    {
        return 17;
    }

    public function isCandidate(Tokens $tokens): bool
    {
        return $tokens->isAnyTokenKindsFound([...array_keys(self::OPERATORS), FCT::T_AMPERSAND_FOLLOWED_BY_VAR_OR_VARARG, FCT::T_AMPERSAND_NOT_FOLLOWED_BY_VAR_OR_VARARG]);
    }

    protected function applyFix(\SplFileInfo $file, Tokens $tokens): void
    {
        $this->operatorTypes = array_keys(self::OPERATORS);
        $this->tokensAnalyzer = new TokensAnalyzer($tokens);

        parent::applyFix($file, $tokens);
    }

    protected function isOperatorTokenCandidate(Tokens $tokens, int $index): bool
    {
        if (!$tokens[$index]->equalsAny($this->operatorTypes)) {
            return false;
        }

        while (null !== $index) {
            $index = $tokens->getNextMeaningfulToken($index);
            $otherToken = $tokens[$index];

            if ($otherToken->equalsAny([';', [\T_CLOSE_TAG]])) {
                return true;
            }

            // fast precedence check
            if ($otherToken->equals('?') || $otherToken->isGivenKind(\T_INSTANCEOF)) {
                return false;
            }

            $blockType = Tokens::detectBlockType($otherToken);

            if (null !== $blockType) {
                if (false === $blockType['isStart']) {
                    return true;
                }

                $index = $tokens->findBlockEnd($blockType['type'], $index);

                continue;
            }

            // precedence check
            if ($this->tokensAnalyzer->isBinaryOperator($index)) {
                return false;
            }
        }

        return false; // unreachable, but keeps SCA happy
    }

    protected function getReplacementToken(Token $token): Token
    {
        \assert(isset(self::OPERATORS[$token->getContent()])); // for PHPStan

        return new Token(self::OPERATORS[$token->getContent()]);
    }

    /**
     * Whether the assignment target is a single plain variable (`$x`).
     *
     * That is the only target for which `$x = $x <op> …` and `$x <op>= …` are
     * guaranteed to behave identically: evaluating a plain variable has no side
     * effect, so doing it once (shorthand) instead of twice (long form) changes
     * nothing. Any `->`, `::`, `[]` or `(…)` in the target may double-evaluate a
     * magic accessor or a side-effecting subscript, so those are handled by the
     * risky fixer instead.
     *
     * @param array{start: int, end: int} $assignRange
     */
    protected function targetIsPlainVariable(Tokens $tokens, array $assignRange): bool
    {
        return $assignRange['start'] === $assignRange['end']
            && $tokens[$assignRange['start']]->isGivenKind(\T_VARIABLE);
    }
}
