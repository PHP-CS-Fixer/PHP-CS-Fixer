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

namespace PhpCsFixer\DocBlock;

/**
 * This class is responsible for comparing tags to see if they should be kept
 * together, or kept apart.
 *
 * @author Graham Campbell <hello@gjcampbell.co.uk>
 * @author Jakub Kwaśniewski <jakub@zero-85.pl>
 */
final class TagComparator
{
    /**
     * Groups of tags that should be allowed to immediately follow each other.
     *
     * @internal
     */
    public const DEFAULT_GROUPS = [
        ['deprecated', 'link', 'see', 'since'],
        ['author', 'copyright', 'license'],
        ['category', 'package', 'subpackage'],
        ['property', 'property-read', 'property-write'],
    ];

    /**
     * @var string[][]
     */
    private array $groups = [];

    /**
     * @param null|string[][] $groups
     *
     * @return $this
     */
    public static function configure(?array $groups = null): self
    {
        $comparator = new self();
        $comparator->groups = $groups ?? self::DEFAULT_GROUPS;

        return $comparator;
    }

    /**
     * Should the given tags be kept together, or kept apart?
     * Check against configured groups.
     */
    public function shouldBeGroupedTogether(Tag $first, Tag $second): bool
    {
        return self::whetherShouldBeTogether($first, $second, $this->groups);
    }

    /**
     * Should the given tags be kept together, or kept apart?
     * Check against DEFAULT_GROUPS.
     */
    public static function shouldBeTogether(Tag $first, Tag $second): bool
    {
        return self::whetherShouldBeTogether($first, $second, self::DEFAULT_GROUPS);
    }

    /**
     * Should the given tags be kept together, or kept apart?
     * It would be much prettier if all method and function names rhyme.
     *
     * @param string[][] $groups
     */
    private static function whetherShouldBeTogether(Tag $first, Tag $second, array $groups): bool
    {
        $firstName = $first->getName();
        $secondName = $second->getName();

        if ($firstName === $secondName) {
            return true;
        }

        foreach ($groups as $group) {
            if (\in_array($firstName, $group, true) && \in_array($secondName, $group, true)) {
                return true;
            }
        }

        return false;
    }
}
