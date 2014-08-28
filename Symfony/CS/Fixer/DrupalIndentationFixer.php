<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Fixer;

use Symfony\CS\FixerInterface;
use Symfony\CS\ConfigInterface;

/**
 * @author Fabien Potencier <fabien@symfony.com>, Peter Drake <pdrake@gmail.com>
 */
class DrupalIndentationFixer implements FixerInterface
{
    public function fix(\SplFileInfo $file, $content)
    {
        // [Structure] Indentation is done by steps of two spaces (tabs are never allowed)
        return preg_replace_callback('/^([ \t]+)/m', function ($matches) use ($content) {
            return str_replace("\t", '  ', $matches[0]);
        }, $content);
    }

    public function getLevel()
    {
        return false;
    }

    public function getPriority()
    {
        return 50;
    }

    public function supports(\SplFileInfo $file, ConfigInterface $config)
    {
        return 'php' === $config->getFileType($file);
    }

    public function getName()
    {
        return 'drupal_indentation';
    }

    public function getDescription()
    {
        return 'Code must use 2 spaces for indenting, not tabs.';
    }
}
