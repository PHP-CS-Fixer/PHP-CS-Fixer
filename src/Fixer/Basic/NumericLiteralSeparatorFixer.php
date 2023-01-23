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
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * Let's you add underscores to numeric literals.
 *
 * Inspired by:
 * - {@link https://github.com/kubawerlos/php-cs-fixer-custom-fixers/blob/main/src/Fixer/NumericLiteralSeparatorFixer.php}
 * - {@link https://github.com/sindresorhus/eslint-plugin-unicorn/blob/main/rules/numeric-separators-style.js}
 *
 * @author Marvin Heilemann <11534760+muuvmuuv@users.noreply.github.com>
 */
final class NumericLiteralSeparatorFixer extends AbstractFixer
{
    private string $separator = '_';

    /**
     * {@inheritdoc}
     */
    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(
            'Adds separators to numeric literals of any kind.',
            [
                new CodeSample("<?php \$var = 123456;\n"),
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function isCandidate(Tokens $tokens): bool
    {
        if (\PHP_VERSION_ID < 70400) {
            // Syntax since PHP 7.4.0
            return false;
        }

        return $tokens->isAnyTokenKindsFound([T_DNUMBER, T_LNUMBER]);
    }

    /**
     * {@inheritdoc}
     */
    protected function applyFix(\SplFileInfo $file, Tokens $tokens): void
    {
        foreach ($tokens as $index => $token) {
            if (!$token->isGivenKind([T_DNUMBER, T_LNUMBER])) {
                continue;
            }

            $content = $token->getContent();

            // Already has literal separator, but may be incomplete
            // e.g.: 1_412412.
            $content = str_replace('_', '', $content);

            $newContent = $this->formatValue($content);

            if ($content === $newContent) {
                // Skip Token override if its the same content, like when it
                // already got a valid literal separator structure.
                continue;
            }

            $token = new Token([$token->getId(), $newContent]);

            $tokens[$index] = $token;
        }
    }

    private function formatValue(string $value): string
    {
        if (str_starts_with(strtolower($value), '0b')) {
            // Binary
            return $this->insertEveryRight($value, 4, 2);
        }

        if (str_starts_with(strtolower($value), '0x')) {
            // Hexadecimal
            return $this->insertEveryRight($value, 2, 2);
        }

        // All other types

        /** If its a negative value we need an offset */
        $negative_offset = fn ($v) => str_contains($v, '-') ? 1 : 0;

        preg_match_all('/([0-9-_]+)((\.)([0-9_]+))?((e)([0-9-_]+))?/i', $value, $result);

        $integer = $result[1][0];
        $joinedValue = $this->insertEveryRight($integer, 3, $negative_offset($integer));

        $dot = $result[3][0];
        if ('' !== $dot) {
            $integer = $result[4][0];
            $decimal = $this->insertEveryLeft($integer, 3, $negative_offset($integer));
            $joinedValue = $joinedValue.$dot.$decimal;
        }

        $tim = $result[6][0];
        if ('' !== $tim) {
            $integer = $result[7][0];
            $times = $this->insertEveryRight($integer, 3, $negative_offset($integer));
            $joinedValue = $joinedValue.$tim.$times;
        }

        return $joinedValue;
    }

    private function insertEveryRight($value, $length, $offset = 0)
    {
        $position = $length * -1;
        while ($position > -(\strlen($value) - $offset)) {
            $value = substr_replace($value, $this->separator, $position, 0);
            $position -= $length + 1;
        }

        return $value;
    }

    private function insertEveryLeft($value, $length, $offset = 0)
    {
        $position = $length;
        while ($position < \strlen($value)) {
            $value = substr_replace($value, $this->separator, $position, $offset);
            $position += $length + 1;
        }

        return $value;
    }
}
