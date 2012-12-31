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
 * @author Denis Sokolov <denis@sokolov.cc>
 */
class DefaultValuesFixer implements FixerInterface
{
    /**
     * {@inheritdoc}
     */
    public function fix(\SplFileInfo $file, $content)
    {
        // Take one function declaration at a time
        // In the callback, replace all default values spacing
        return preg_replace_callback(
            '/
                (?P<declaration>function\s*[a-zA-Z0-9_]+\s*)
                (?P<parameters>\([^)]+\))
            /x',
            function (
                $match
            ) {
                $declaration = $match['declaration'];
                $parameters = $match['parameters'];
                $parameters = preg_replace('/(?<! )=/', ' =', $parameters);
                $parameters = preg_replace('/=(?! )/', '= ', $parameters);
                return $declaration . $parameters;
            },
            $content
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getLevel()
    {
        // defined in PSR2 ¶4.4
        return FixerInterface::PSR2_LEVEL;
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
        return $file->getExtension() === 'php';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'default_values';
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'Spaces should wrap a default variable name and value';
    }
}
