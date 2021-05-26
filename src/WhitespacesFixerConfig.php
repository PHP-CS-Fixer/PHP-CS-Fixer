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

namespace PhpCsFixer;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
final class WhitespacesFixerConfig
{
    /**
     * @var string
     */
    private $indent;

    /**
     * @var string
     */
    private $lineEnding;

    public function __construct(string $indent = '    ', string $lineEnding = "\n")
    {
        if (!\in_array($indent, ['  ', '    ', "\t"], true)) {
            throw new \InvalidArgumentException('Invalid "indent" param, expected tab or two or four spaces.');
        }

        if (!\in_array($lineEnding, ["\n", "\r\n"], true)) {
            throw new \InvalidArgumentException('Invalid "lineEnding" param, expected "\n" or "\r\n".');
        }

        $this->indent = $indent;
        $this->lineEnding = $lineEnding;
    }

    public function getIndent(): string
    {
        return $this->indent;
    }

    public function getLineEnding(): string
    {
        return $this->lineEnding;
    }
}
