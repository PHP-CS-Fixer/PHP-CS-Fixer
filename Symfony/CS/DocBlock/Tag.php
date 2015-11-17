<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\DocBlock;

/**
 * This represents a tag, as defined by the proposed PSR PHPDoc standard.
 *
 * @author Graham Campbell <graham@mineuk.com>
 */
class Tag
{
    /**
     * All the tags defined by the proposed PSR PHPDoc standard.
     *
     * @var string[]
     */
    private static $tags = array(
        'api', 'author', 'category', 'copyright', 'deprecated', 'example',
        'global', 'internal', 'license', 'link', 'method', 'package', 'param',
        'property', 'property-read', 'property-write', 'return', 'see',
        'since', 'struct', 'subpackage', 'throws', 'todo', 'typedef', 'uses',
        'var', 'version',
    );

    /**
     * All the tags with types.
     *
     * @var string[]
     */
    private static $tagsWithTypes = array(
        'method', 'param', 'property', 'property-read', 'property-write',
        'return', 'throws', 'type', 'var',
    );

    /**
     * The line containing the tag.
     *
     * @var Line
     */
    private $line;

    /**
     * The cached tag name.
     *
     * @var string|null
     */
    private $name;

    /**
     * The cached types content.
     *
     * @var string|null
     */
    private $typesContent;

    /**
     * Create a new tag instance.
     *
     * @param Line $line
     */
    public function __construct(Line $line)
    {
        $this->line = $line;
    }

    /**
     * Is the tag a known tag.
     *
     * This is defined by if it exists in the proposed PSR PHPDoc standard.
     *
     * @return bool
     */
    public function valid()
    {
        return in_array($this->getName(), self::$tags, true);
    }

    /**
     * Get the tag name.
     *
     * This may be "param", or "return", etc.
     *
     * @return string
     */
    public function getName()
    {
        if (null === $this->name) {
            preg_match_all('/@[a-zA-Z0-9_-]+(?=\s|$)/', $this->line->getContent(), $matches);

            if (isset($matches[0][0])) {
                $this->name = ltrim($matches[0][0], '@');
            } else {
                $this->name = 'other';
            }
        }

        return $this->name;
    }

    /**
     * Set the tag name.
     *
     * This will also be persisted to the upsteam line and annotation.
     *
     * @param string $name
     */
    public function setName($name)
    {
        $current = $this->getName();

        if ('other' === $current) {
            throw new \RuntimeException('Cannot set name on unknown tag');
        }

        $this->line->setContent(preg_replace("/@$current/", "@$name", $this->line->getContent(), 1));

        $this->name = $name;
    }

    /**
     * Get the types associated with this tag.
     *
     * @return string[]
     */
    public function getTypes()
    {
        return explode('|', $this->getTypesContent());
    }

    /**
     * Set the types associated with this tag.
     *
     * @param string[] $types
     */
    public function setTypes(array $types)
    {
        $pattern = '/'.preg_quote($this->getTypesContent()).'/';

        $this->line->setContent(preg_replace($pattern, implode('|', $types), $this->line->getContent(), 1));

        $this->typesContent = null;
    }

    /**
     * Get the current types content.
     *
     * Be careful modifying the underlying line as that won't flush the cache.
     *
     * @return string
     */
    private function getTypesContent()
    {
        if (null === $this->typesContent) {
            $name = $this->getName();

            if (!in_array($this->getName(), self::$tagsWithTypes, true)) {
                throw new \RuntimeException('This tag does not support types');
            }

            $tagSplit = preg_split('/\s*\@'.$name.'\s*/', $this->line->getContent(), 2);
            $spaceSplit = preg_split('/\s/', $tagSplit[1], 2);

            $this->typesContent = $spaceSplit[0];
        }

        return $this->typesContent;
    }
}
