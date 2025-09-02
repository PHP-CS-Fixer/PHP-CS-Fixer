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

namespace PhpCsFixer\Tokenizer;

/**
 * @author Gregor Harlan
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 */
final class IndexNotFoundException extends \RuntimeException
{
    private function __construct(string $message)
    {
        parent::__construct($message);
    }

    public static function fromMethod(string $methodName): self
    {
        return new self(\sprintf('No matching index found by method "%s".', $methodName));
    }
}
