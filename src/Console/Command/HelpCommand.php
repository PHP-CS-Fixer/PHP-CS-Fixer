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
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\HelpCommand as BaseHelpCommand;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author Fabien Potencier <fabien@symfony.com>
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 */
#[AsCommand(name: 'help')]
final class HelpCommand extends BaseHelpCommand
{
    /**
     * @var string
     */
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
     *
     * @return null|list<AllowedValueSubset|mixed>
     */
    public static function getDisplayableAllowedValues(FixerOptionInterface $option): ?array
    {
        $allowed = $option->getAllowedValues();

        if (null !== $allowed) {
            $allowed = array_filter($allowed, static function ($value): bool {
                return !$value instanceof \Closure;
            });

            usort($allowed, static function ($valueA, $valueB): int {
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
     * @param mixed $value
     */
    private static function scalarToString($value): string
    {
        $str = var_export($value, true);

        return Preg::replace('/\bNULL\b/', 'null', $str);
    }

    /**
     * @param array<mixed> $value
     */
    private static function arrayToString(array $value): string
    {
        if (0 === \count($value)) {
            return '[]';
        }

        $isHash = !array_is_list($value);
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
}
