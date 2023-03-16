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

namespace PhpCsFixer\Console\Output\Progress;

use PhpCsFixer\Console\Output\OutputContext;

final class ProgressOutputFactory
{
    public static function create(string $outputType, OutputContext $context): ProgressOutputInterface
    {
        if (null === $context->getOutput()) {
            $outputType = ProgressOutputInterface::OUTPUT_TYPE_NONE;
        }

        switch ($outputType) {
            case ProgressOutputInterface::OUTPUT_TYPE_NONE:
                return new NullOutput();

            case ProgressOutputInterface::OUTPUT_TYPE_DOTS:
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
