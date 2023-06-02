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
 * @author Alexey Polyvanyi <aleksey.polyvanyi@eonx.com>
 *
 * @internal
 */
abstract class AbstractOrderFixer extends AbstractFixer
{
    /**
     * @final
     */
    protected const OPTION_DIRECTION = 'direction';

    /**
     * @final
     */
    protected const DIRECTION_ASCEND = 'ascend';

    /**
     * @final
     */
    protected const DIRECTION_DESCEND = 'descend';

    /**
     * @final
     */
    protected const OPTION_CASE_SENSITIVE = 'case_sensitive';

    /**
     * @final
     */
    protected const SORT_ORDER_ALPHA = 'alpha';

    /**
     * @final
     */
    protected const SORT_ORDER_LENGTH = 'length';

    /**
     * @final
     */
    protected const SORT_ORDER_NONE = 'none';

    /**
     * Array of supported directions in configuration.
     *
     * @var string[]
     *
     * @final
     */
    protected const SUPPORTED_DIRECTION_OPTIONS = [
        self::DIRECTION_ASCEND,
        self::DIRECTION_DESCEND,
    ];

    abstract protected function getSortOrderOptionName(): string;

    protected function getScoreWithSortAlgorithm(
        string $element1,
        string $element2,
        bool $useAlphaSortWhenEqualLength = false
    ): int {
        $sortOrder = $this->configuration[$this->getSortOrderOptionName()];
        $score = 0;

        if (self::SORT_ORDER_ALPHA === $sortOrder) {
            $score = $this->configuration[self::OPTION_CASE_SENSITIVE]
                ? strcmp($element1, $element2)
                : strcasecmp($element1, $element2);
        }

        if (self::SORT_ORDER_LENGTH === $sortOrder) {
            $score = \strlen($element1) - \strlen($element2);

            if (0 === $score && $useAlphaSortWhenEqualLength) {
                $score = $this->configuration[self::OPTION_CASE_SENSITIVE]
                    ? strcmp($element1, $element2)
                    : strcasecmp($element1, $element2);
            }
        }

        return $this->getScoreWithDirection($score);
    }

    protected function getScoreWithDirection(int $score): int
    {
        if (self::DIRECTION_DESCEND === $this->configuration[self::OPTION_DIRECTION]) {
            $score *= -1;
        }

        return $score;
    }
}
