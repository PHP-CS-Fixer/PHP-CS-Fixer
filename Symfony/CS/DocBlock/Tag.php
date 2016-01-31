<?php

/*
 * This file is part of the PHP CS Fixer.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *     Dariusz Rumiński <dariusz.ruminski@gmail.com>
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
     * The tag name.
     *
     * @var string
     */
    private $name;

    /**
     * Create a new tag instance.
     *
     * @param string $content
     */
    public function __construct($content)
    {
        $this->name = 'other';
        preg_match_all('/@[a-zA-Z0-9_-]+(?=\s|$)/', $content, $matches);

        if (isset($matches[0][0])) {
            $this->name = ltrim($matches[0][0], '@');
        }
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
        return $this->name;
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
        return in_array($this->name, self::$tags, true);
    }
}
