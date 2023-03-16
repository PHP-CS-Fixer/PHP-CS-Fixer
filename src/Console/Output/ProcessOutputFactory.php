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

namespace PhpCsFixer\Console\Output;

final class ProcessOutputFactory
{
    public static function create(string $outputType, OutputContext $context): ProcessOutputInterface
    {
        if (null === $context->getOutput()) {
            $outputType = ProcessOutputInterface::OUTPUT_TYPE_NONE;
        }

        switch ($outputType) {
            case ProcessOutputInterface::OUTPUT_TYPE_NONE:
                return new NullOutput();

            case ProcessOutputInterface::OUTPUT_TYPE_DOTS:
                return new DotsOutput($context);

            default:
                throw new \RuntimeException(
                    sprintf(
                        'Something went wrong, "%s" output type is not supported',
                        $outputType
                    )
                );
        }
    }
}
