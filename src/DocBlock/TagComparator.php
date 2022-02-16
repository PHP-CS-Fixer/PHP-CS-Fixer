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
 */
final class TagComparator
{
    /**
     * Groups of tags that should be allowed to immediately follow each other.
     */
    private static array $groups = [
        ['deprecated', 'link', 'see', 'since'],
        ['author', 'copyright', 'license'],
        ['category', 'package', 'subpackage'],
        ['property', 'property-read', 'property-write'],
    ];

    /**
     * Should the given tags be kept together, or kept apart?
     */
    public static function shouldBeTogether(Tag $first, Tag $second): bool
    {
        $firstName = $first->getName();
        $secondName = $second->getName();

        if ($firstName === $secondName) {
            return true;
        }

        foreach (self::$groups as $group) {
            if (\in_array($firstName, $group, true) && \in_array($secondName, $group, true)) {
                return true;
            }
        }

        return false;
    }
}
