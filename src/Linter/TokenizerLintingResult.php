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

namespace PhpCsFixer\Linter;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 */
final class TokenizerLintingResult implements LintingResultInterface
{
    /**
     * @var \ParseError|null
     */
    private $error;

    /**
     * @param \ParseError|null $error
     */
    public function __construct(\ParseError $error = null)
    {
        $this->error = $error;
    }

    /**
     * {@inheritdoc}
     */
    public function check()
    {
        if (null === $this->error) {
            return;
        }

        $message = $this->error->getMessage();
        $detail = end($this->error->getTrace());

        throw new LintingException(
            "PHP Parse error:  {$message} in {$detail['file']} on line {$detail['line']}",
            $this->error->getCode(),
            $this->error
        );
    }
}
