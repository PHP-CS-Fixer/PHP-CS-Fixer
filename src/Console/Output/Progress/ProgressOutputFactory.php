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

/**
 * @internal
 */
final class ProgressOutputFactory
{
    /**
     * @var array<string, class-string<ProgressOutputInterface>>
     */
    private static array $outputTypeMap = [
        ProgressOutputType::NONE => NullOutput::class,
        ProgressOutputType::DOTS => DotsOutput::class,
        ProgressOutputType::BAR => PercentageBarOutput::class,
    ];

    public function create(string $outputType, OutputContext $context): ProgressOutputInterface
    {
        if (null === $context->getOutput()) {
            $outputType = ProgressOutputType::NONE;
        }

        if (!$this->isBuiltInType($outputType)) {
            throw new \InvalidArgumentException(
                \sprintf(
                    'Something went wrong, "%s" output type is not supported',
                    $outputType
                )
            );
        }

        return new self::$outputTypeMap[$outputType]($context);
    }

    private function isBuiltInType(string $outputType): bool
    {
        return \in_array($outputType, ProgressOutputType::all(), true);
    }
}
