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

namespace PhpCsFixer\Console\Command;

use PhpCsFixer\FixerConfiguration\AllowedValueSubset;
use PhpCsFixer\FixerConfiguration\FixerOptionInterface;
use PhpCsFixer\Preg;
use Symfony\Component\Console\Command\HelpCommand as BaseHelpCommand;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author Fabien Potencier <fabien@symfony.com>
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 * @author SpacePossum
 *
 * @internal
 */
final class HelpCommand extends BaseHelpCommand
{
    protected static $defaultName = 'help';

    /**
     * @param mixed $value
     */
    public static function toString($value): string
    {
        return \is_array($value)
            ? static::arrayToString($value)
            : static::scalarToString($value)
        ;
    }

    /**
     * Returns the allowed values of the given option that can be converted to a string.
     */
    public static function getDisplayableAllowedValues(FixerOptionInterface $option): ?array
    {
        $allowed = $option->getAllowedValues();

        if (null !== $allowed) {
            $allowed = array_filter($allowed, static function ($value) {
                return !($value instanceof \Closure);
            });

            usort($allowed, static function ($valueA, $valueB) {
                if ($valueA instanceof AllowedValueSubset) {
                    return -1;
                }

                if ($valueB instanceof AllowedValueSubset) {
                    return 1;
                }

                return strcasecmp(
                    self::toString($valueA),
                    self::toString($valueB)
                );
            });

            if (0 === \count($allowed)) {
                $allowed = null;
            }
        }

        return $allowed;
    }

    /**
     * {@inheritdoc}
     */
    protected function initialize(InputInterface $input, OutputInterface $output): void
    {
        $output->getFormatter()->setStyle('url', new OutputFormatterStyle('blue'));
    }

    /**
     * Wraps a string to the given number of characters, ignoring style tags.
     *
     * @return string[]
     */
    private static function wordwrap(string $string, int $width): array
    {
        $result = [];
        $currentLine = 0;
        $lineLength = 0;
        foreach (explode(' ', $string) as $word) {
            $wordLength = \strlen(Preg::replace('~</?(\w+)>~', '', $word));
            if (0 !== $lineLength) {
                ++$wordLength; // space before word
            }

            if ($lineLength + $wordLength > $width) {
                ++$currentLine;
                $lineLength = 0;
            }

            $result[$currentLine][] = $word;
            $lineLength += $wordLength;
        }

        return array_map(static function (array $line) {
            return implode(' ', $line);
        }, $result);
    }

    /**
     * @param mixed $value
     */
    private static function scalarToString($value): string
    {
        $str = var_export($value, true);

        return Preg::replace('/\bNULL\b/', 'null', $str);
    }

    private static function arrayToString(array $value): string
    {
        if (0 === \count($value)) {
            return '[]';
        }

        $isHash = static::isHash($value);
        $str = '[';

        foreach ($value as $k => $v) {
            if ($isHash) {
                $str .= static::scalarToString($k).' => ';
            }

            $str .= \is_array($v)
                ? static::arrayToString($v).', '
                : static::scalarToString($v).', '
            ;
        }

        return substr($str, 0, -2).']';
    }

    private static function isHash(array $array): bool
    {
        $i = 0;

        foreach ($array as $k => $v) {
            if ($k !== $i) {
                return true;
            }

            ++$i;
        }

        return false;
    }
}
