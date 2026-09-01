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

namespace PhpCsFixer\Differ;

use PhpCsFixer\Preg;
use Symfony\Component\Console\Formatter\OutputFormatter;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @readonly
 *
 * @internal
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 */
final class DiffConsoleFormatter
{
    /**
     * Matches ANSI/VT100 escape sequences: CSI (e.g. colours, cursor movement),
     * OSC (e.g. window title) terminated by BEL or ST, and other two-byte escapes.
     */
    private const ESCAPE_SEQUENCES_PATTERN = '#\x1B(?:\[[0-?]*[ -/]*[@-~]|][^\x07\x1B]*(?:\x07|\x1B\x5C)|[\x40-\x5A\x5C\x5F])#';

    private bool $isDecoratedOutput;

    private string $template;

    public function __construct(bool $isDecoratedOutput, string $template = '%s')
    {
        $this->isDecoratedOutput = $isDecoratedOutput;
        $this->template = $template;
    }

    public function format(string $diff, string $lineTemplate = '%s'): string
    {
        $isDecorated = $this->isDecoratedOutput;

        $template = $isDecorated
            ? $this->template
            : Preg::replace('/<[^<>]+>/', '', $this->template);

        return \sprintf(
            $template,
            implode(
                \PHP_EOL,
                array_map(
                    static function (string $line) use ($isDecorated, $lineTemplate): string {
                        $line = Preg::replace(self::ESCAPE_SEQUENCES_PATTERN, '', $line);

                        if ($isDecorated) {
                            $count = 0;
                            $line = Preg::replaceCallback(
                                '/^([+\-@].*)/',
                                static function (array $matches): string {
                                    if ('+' === $matches[0][0]) {
                                        $colour = 'green';
                                    } elseif ('-' === $matches[0][0]) {
                                        $colour = 'red';
                                    } else {
                                        $colour = 'cyan';
                                    }

                                    return \sprintf('<fg=%s>%s</fg=%s>', $colour, OutputFormatter::escape($matches[0]), $colour);
                                },
                                $line,
                                1,
                                $count,
                            );

                            if (0 === $count) {
                                $line = OutputFormatter::escape($line);
                            }
                        }

                        return \sprintf($lineTemplate, $line);
                    },
                    Preg::split('#\R#u', $diff),
                ),
            ),
        );
    }
}
