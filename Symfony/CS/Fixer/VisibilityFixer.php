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
        // skip files with no OOP code
        if (!preg_match('{\b(?<!\$)(?:class|interface|trait)\b}i', $content)) {
            return $content;
        }

        // Visibility MUST be declared on all properties and methods;
        // abstract and final MUST be declared before the visibility;
        // static MUST be declared after the visibility
        $content = preg_replace_callback('/^( {2,4}|\t)((?:(?:public|protected|private|static|var) +)+) *(\$[a-z0-9_]+)/im', function ($matches) {
            $flags = explode(' ', strtolower(trim($matches[2])));
            if (in_array('protected', $flags)) {
                $visibility = 'protected';
            } elseif (in_array('private', $flags)) {
                $visibility = 'private';
            } else {
                $visibility = 'public';
            }

            return $matches[1] . $visibility
                . (in_array('static', $flags) ? ' static' : '')
                . ' ' . $matches[3];
        }, $content);

        $content = preg_replace_callback('/^( {2,4}|\t)((?:(?:public|protected|private|static|abstract|final) +)*)(function +[a-z0-9_]+)/im', function ($matches) {
            $flags = explode(' ', strtolower(trim($matches[2])));
            if (in_array('protected', $flags)) {
                $visibility = 'protected';
            } elseif (in_array('private', $flags)) {
                $visibility = 'private';
            } else {
                $visibility = 'public';
            }

            return $matches[1]
                . (in_array('abstract', $flags) ? 'abstract ' : '')
                . (in_array('final', $flags) ? 'final ' : '')
                . $visibility
                . (in_array('static', $flags) ? ' static' : '')
                . ' '. $matches[3];
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
        return 'php' == pathinfo($file->getFilename(), PATHINFO_EXTENSION);
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
