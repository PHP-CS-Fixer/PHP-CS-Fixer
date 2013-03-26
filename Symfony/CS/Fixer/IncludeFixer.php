<?php

/*
 * This file is part of the Symfony CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Fixer;

use Symfony\CS\FixerInterface;

/**
 * @author Саша Стаменковић <umpirsky@gmail.com>
 */
class IncludeFixer implements FixerInterface
{
    /**
     * {@inheritdoc}
     */
    public function fix(\SplFileInfo $file, $content)
    {
        $statements = implode('|', array(
            'include',
            'include_once',
            'require',
            'require_once',
        ));

        return preg_replace(
            array(
                sprintf('#^(\s*(?:return +)?(?:\$[a-z0-9_()>-]+ *= *)?(?:%s))\s*\(?\s*[\'"]{1}(?!\")([a-zA-Z0-9\-_.\/]*)[\'"]{1}\s*\)?#m', $statements), // Remove enclosing brackets, trailing spaces and convert double with single quotes
                sprintf('#^(\s*(?:return +)?(?:\$[a-z0-9_()>-]+ *= *)?(?:%s))[^\S\n]+(.*)#m', $statements),                                              // Replace multiple spaces with single between include and file path
            ),
            array(
                "\\1 '\\2'",
                '\\1 \\2',
            ),
            $content
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getLevel()
    {
        return FixerInterface::ALL_LEVEL;
    }

    /**
     * {@inheritdoc}
     */
    public function getPriority()
    {
        return 0;
    }

    /**
     * {@inheritdoc}
     */
    public function supports(\SplFileInfo $file)
    {
        return 'php' == pathinfo($file->getFilename(), PATHINFO_EXTENSION);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'include';
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'Include and file path should be divided with a single space. File path should not be placed under brackets.';
    }
}
