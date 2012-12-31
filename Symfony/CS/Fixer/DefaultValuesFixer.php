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
        $content = preg_replace('/(function\s*[a-z]+\s*\([^)]*[^ ])=/', '\1 =', $content);
        $content = preg_replace('/(function\s*[a-z]+\s*\([^)]*[^ ]) =([^ ])/', '\1 = \2', $content);
        return $content;
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
        return 'php' == pathinfo($file->getFilename(), PATHINFO_EXTENSION);
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
