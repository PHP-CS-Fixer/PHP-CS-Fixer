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

namespace PhpCsFixer\FixerDefinition;

/**
 * @author Andreas Möller <am@localheinz.com>
 */
final class VersionSpecification implements VersionSpecificationInterface
{
    private ?int $minimum;

    private ?int $maximum;

    /**
     * @throws \InvalidArgumentException
     */
    public function __construct(?int $minimum = null, ?int $maximum = null)
    {
        if (null === $minimum && null === $maximum) {
            throw new \InvalidArgumentException('Minimum or maximum need to be specified.');
        }

        if (null !== $minimum && 1 > $minimum) {
            throw new \InvalidArgumentException('Minimum needs to be either null or an integer greater than 0.');
        }

        if (null !== $maximum) {
            if (1 > $maximum) {
                throw new \InvalidArgumentException('Maximum needs to be either null or an integer greater than 0.');
            }

            if (null !== $minimum && $maximum < $minimum) {
                throw new \InvalidArgumentException('Maximum should not be lower than the minimum.');
            }
        }

        $this->minimum = $minimum;
        $this->maximum = $maximum;
    }

    /**
     * {@inheritdoc}
     */
    public function isSatisfiedBy(int $version): bool
    {
        if (null !== $this->minimum && $version < $this->minimum) {
            return false;
        }

        if (null !== $this->maximum && $version > $this->maximum) {
            return false;
        }

        return true;
    }
}
