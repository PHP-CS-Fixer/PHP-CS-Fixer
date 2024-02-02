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
use PhpCsFixer\Fixer\ConfigurableFixerInterface;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolver;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolverInterface;
use PhpCsFixer\FixerConfiguration\FixerOptionBuilder;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Preg;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * Let's you add underscores to numeric literals.
 *
 * Inspired by:
 * - {@link https://github.com/kubawerlos/php-cs-fixer-custom-fixers/blob/main/src/Fixer/NumericLiteralSeparatorFixer.php}
 * - {@link https://github.com/sindresorhus/eslint-plugin-unicorn/blob/main/rules/numeric-separators-style.js}
 *
 * @author Marvin Heilemann <marvin.heilemann+github@googlemail.com>
 * @author Greg Korba <greg@codito.dev>
 */
final class NumericLiteralSeparatorFixer extends AbstractFixer implements ConfigurableFixerInterface
{
    public const STRATEGY_USE_SEPARATOR = 'use_separator';
    public const STRATEGY_NO_SEPARATOR = 'no_separator';

    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(
            'Adds separators to numeric literals of any kind.',
            [
                new CodeSample(
                    <<<'PHP'
                        <?php
                        $integer = 1234567890;

                        PHP
                    ,
                ),
                new CodeSample(
                    <<<'PHP'
                        <?php
                        $integer = 1234_5678;
                        $octal = 01_234_56;
                        $binary = 0b00_10_01_00;
                        $hexadecimal = 0x3D45_8F4F;

                        PHP
                    ,
                    ['strategy' => self::STRATEGY_NO_SEPARATOR],
                ),
                new CodeSample(
                    <<<'PHP'
                        <?php
                        $integer = 12345678;
                        $octal = 0123456;
                        $binary = 0b0010010011011010;
                        $hexadecimal = 0x3D458F4F;

                        PHP
                    ,
                    ['strategy' => self::STRATEGY_USE_SEPARATOR],
                ),
                new CodeSample(
                    "<?php \$var = 24_40_21;\n",
                    ['override_existing' => true]
                ),
            ]
        );
    }

    public function isCandidate(Tokens $tokens): bool
    {
        return $tokens->isAnyTokenKindsFound([T_DNUMBER, T_LNUMBER]);
    }

    protected function createConfigurationDefinition(): FixerConfigurationResolverInterface
    {
        return new FixerConfigurationResolver([
            (new FixerOptionBuilder(
                'override_existing',
                'Whether literals already containing underscores should be reformatted.'
            ))
                ->setAllowedTypes(['bool'])
                ->setDefault(false)
                ->getOption(),
            (new FixerOptionBuilder(
                'strategy',
                'Whether numeric literal should be separated by underscores or not.'
            ))
                ->setAllowedValues([self::STRATEGY_USE_SEPARATOR, self::STRATEGY_NO_SEPARATOR])
                ->setDefault(self::STRATEGY_USE_SEPARATOR)
                ->getOption(),
        ]);
    }

    protected function applyFix(\SplFileInfo $file, Tokens $tokens): void
    {
        foreach ($tokens as $index => $token) {
            if (!$token->isGivenKind([T_DNUMBER, T_LNUMBER])) {
                continue;
            }

            $content = $token->getContent();

            $newContent = $this->formatValue($content);

            if ($content === $newContent) {
                // Skip Token override if its the same content, like when it
                // already got a valid literal separator structure.
                continue;
            }

            $tokens[$index] = new Token([$token->getId(), $newContent]);
        }
    }

    private function formatValue(string $value): string
    {
        if (self::STRATEGY_NO_SEPARATOR === $this->configuration['strategy']) {
            return str_contains($value, '_') ? str_replace('_', '', $value) : $value;
        }

        if (true === $this->configuration['override_existing']) {
            $value = str_replace('_', '', $value);
        } elseif (str_contains($value, '_')) {
            // Keep already underscored literals untouched.
            return $value;
        }

        $lowerValue = strtolower($value);

        if (str_starts_with($lowerValue, '0b')) {
            // Binary
            return $this->insertEveryRight($value, 8, 2);
        }

        if (str_starts_with($lowerValue, '0x')) {
            // Hexadecimal
            return $this->insertEveryRight($value, 2, 2);
        }

        if (str_starts_with($lowerValue, '0o')) {
            // Octal
            return $this->insertEveryRight($value, 3, 2);
        }
        if (str_starts_with($lowerValue, '0') && !str_contains($lowerValue, '.')) {
            // Octal notation prior PHP 8.1 but still valid
            return $this->insertEveryRight($value, 3, 1);
        }

        // All other types

        /** If its a negative value we need an offset */
        $negativeOffset = static fn ($v) => str_contains($v, '-') ? 1 : 0;

        Preg::matchAll('/([0-9-_]+)?((\.)([0-9_]*))?((e)([0-9-_]+))?/i', $value, $result);

        $integer = $result[1][0];
        $joinedValue = $this->insertEveryRight($integer, 3, $negativeOffset($integer));

        $dot = $result[3][0];
        if ('' !== $dot) {
            $integer = $result[4][0];
            $decimal = $this->insertEveryLeft($integer, 3, $negativeOffset($integer));
            $joinedValue = $joinedValue.$dot.$decimal;
        }

        $tim = $result[6][0];
        if ('' !== $tim) {
            $integer = $result[7][0];
            $times = $this->insertEveryRight($integer, 3, $negativeOffset($integer));
            $joinedValue = $joinedValue.$tim.$times;
        }

        return $joinedValue;
    }

    private function insertEveryRight(string $value, int $length, int $offset = 0): string
    {
        $position = $length * -1;
        while ($position > -(\strlen($value) - $offset)) {
            $value = substr_replace($value, '_', $position, 0);
            $position -= $length + 1;
        }

        return $value;
    }

    private function insertEveryLeft(string $value, int $length, int $offset = 0): string
    {
        $position = $length;
        while ($position < \strlen($value)) {
            $value = substr_replace($value, '_', $position, $offset);
            $position += $length + 1;
        }

        return $value;
    }
}
