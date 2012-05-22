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
 * @author Jordi Boggiano <j.boggiano@seld.be>
 */
class VisibilityFixer implements FixerInterface
{
    public function fix(\SplFileInfo $file, $content)
    {
        // Visibility MUST be declared on all properties and methods;
        // abstract and final MUST be declared before the visibility;
        // static MUST be declared after the visibility
        $content = preg_replace_callback('/^    ((?:(?:public|protected|private|static|var) +)+) *(\$[a-z0-9_]+)/im', function ($matches) {
            $flags = explode(' ', strtolower(trim($matches[1])));
            if (in_array('protected', $flags)) {
                $visibility = 'protected';
            } elseif (in_array('private', $flags)) {
                $visibility = 'private';
            } else {
                $visibility = 'public';
            }

            return '    ' . $visibility
                . (in_array('static', $flags) ? ' static' : '')
                . ' ' . $matches[2];
        }, $content);

        $content = preg_replace_callback('/^    ((?:(?:public|protected|private|static|abstract|final) +)*)(function +[a-z0-9_]+)/im', function ($matches) {
            $flags = explode(' ', strtolower(trim($matches[1])));
            if (in_array('protected', $flags)) {
                $visibility = 'protected';
            } elseif (in_array('private', $flags)) {
                $visibility = 'private';
            } else {
                $visibility = 'public';
            }

            return '    '
                . (in_array('abstract', $flags) ? 'abstract ' : '')
                . (in_array('final', $flags) ? 'final ' : '')
                . $visibility
                . (in_array('static', $flags) ? ' static' : '')
                . ' '. $matches[2];
        }, $content);

        return $content;
    }

    public function getLevel()
    {
        // defined in PSR2 ¶4.4
        return FixerInterface::PSR2_LEVEL;
    }

    public function getPriority()
    {
        return 0;
    }

    public function supports(\SplFileInfo $file)
    {
        return 'php' == $file->getExtension();
    }

    public function getName()
    {
        return 'visibility';
    }

    public function getDescription()
    {
        return 'Visibility must be declared on all properties and methods; abstract and final must be declared before the visibility; static must be declared after the visibility.';
    }
}
