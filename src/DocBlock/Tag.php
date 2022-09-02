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

use PhpCsFixer\Preg;

/**
 * This represents a tag, as defined by the proposed PSR PHPDoc standard.
 *
 * @author Graham Campbell <hello@gjcampbell.co.uk>
 * @author Jakub Kwaśniewski <jakub@zero-85.pl>
 */
final class Tag
{
    /**
     * All the tags defined by the proposed PSR PHPDoc standard.
     */
    public const PSR_STANDARD_TAGS = [
        'api', 'author', 'category', 'copyright', 'deprecated', 'example',
        'global', 'internal', 'license', 'link', 'method', 'package', 'param',
        'property', 'property-read', 'property-write', 'return', 'see',
        'since', 'subpackage', 'throws', 'todo', 'uses', 'var', 'version',
    ];

    /**
     * The line containing the tag.
     */
    private Line $line;

    private ?string $name = null;

    public function __construct(Line $line)
    {
        $this->line = $line;
    }

    public function getName(): string
    {
        if (null === $this->name) {
            Preg::matchAll('/@[a-zA-Z0-9_-]+(?=\s|$)/', $this->line->getContent(), $matches);

            if (isset($matches[0][0])) {
                $this->name = ltrim($matches[0][0], '@');
            } else {
                $this->name = 'other';
            }
        }

        return $this->name;
    }

    /**
     * @param list<string>|string $names
     */
    public function nameEquals($names, bool $allowWildcards = false): bool
    {
        if (!$allowWildcards) {
            return \in_array($this->getName(), (array) $names, true);
        }

        foreach ((array) $names as $name) {
            $name = str_replace('\*', '.*', preg_quote($name, '/'));

            if (1 === Preg::match("/^{$name}$/", $this->getName())) {
                return true;
            }
        }

        return false;
    }

    /**
     * New name will also be persisted to the upstream line and annotation.
     */
    public function setName(string $name): void
    {
        $current = $this->getName();

        if ('other' === $current) {
            throw new \RuntimeException('Cannot set name on unknown tag.');
        }

        $this->line->setContent(Preg::replace("/@{$current}/", "@{$name}", $this->line->getContent(), 1));

        $this->name = $name;
    }

    /**
     * Is the tag a known tag?
     *
     * This is defined by if it exists in the proposed PSR PHPDoc standard.
     */
    public function valid(): bool
    {
        return $this->nameEquals(self::PSR_STANDARD_TAGS);
    }
}
