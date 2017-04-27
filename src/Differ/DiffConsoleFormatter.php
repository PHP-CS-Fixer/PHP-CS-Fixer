<?php

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

use Symfony\Component\Console\Formatter\OutputFormatter;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 */
final class DiffConsoleFormatter
{
    /**
     * @var bool
     */
    private $isDecoratedOutput;

    /**
     * @var string
     */
    private $template;

    /**
     * @param bool   $isDecoratedOutput
     * @param string $template
     */
    public function __construct($isDecoratedOutput, $template = '%s')
    {
        $this->isDecoratedOutput = $isDecoratedOutput;
        $this->template = $template;
    }

    /**
     * @param string $diff
     * @param string $lineTemplate
     *
     * @return string
     */
    public function format($diff, $lineTemplate = '%s')
    {
        $isDecorated = $this->isDecoratedOutput;

        $template = $isDecorated
            ? $this->template
            : preg_replace('/<[^<>]+>/', '', $this->template);

        return sprintf(
            $template,
            implode(PHP_EOL, array_map(
                function ($string) use ($isDecorated, $lineTemplate) {
                    if ($isDecorated) {
                        $string = preg_replace(
                            array('/^(\+.*)/', '/^(\-.*)/', '/^(@.*)/'),
                            array('<fg=green>\1</fg=green>', '<fg=red>\1</fg=red>', '<fg=cyan>\1</fg=cyan>'),
                            $string
                        );
                    }

                    $templated = sprintf($lineTemplate, $string);

                    if (' ' === $string) {
                        $templated = rtrim($templated);
                    }

                    return $templated;
                },
                preg_split(
                    "#\n\r|\n#",
                    $isDecorated
                        ? OutputFormatter::escape(rtrim($diff))
                        : rtrim($diff)
                )
            ))
        );
    }
}
