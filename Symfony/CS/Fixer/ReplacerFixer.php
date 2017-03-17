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

/**
 * @author Luis Cordova <cordoval@gmail.com>
 * @author Raul Rodriguez <raulrodriguez782@gmail.com>
 */
class ReplacerFixer implements FixerInterface
{
    public function fix(\SplFileInfo $file, $content)
    {
        // let's wait for the PR to be merged :D
    }

    public function fixWithOptions(\SplFileInfo $file, $content, $options)
    {
        // @todo create own closure using $options to replace
        //   $options['target']
        //   $options['source']
        $content = str_replace($options['target'], $options['source'], $content);

        return $content;
    }

    public function getLevel()
    {
        return FixerInterface::ALL_LEVEL;
    }

    public function getPriority()
    {
        return 0;
    }

    public function supports(\SplFileInfo $file)
    {
        $extensions = array('php', 'js', 'css', 'html');
        $currentExtension = pathinfo($file->getFilename(), PATHINFO_EXTENSION);

        return in_array($currentExtension, $extensions);
    }

    public function getName()
    {
        return 'replacer';
    }

    public function getDescription()
    {
        return 'Replacer is a fixer that replaces a target string with a passed option input string.';
    }
}
