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
use Symfony\CS\ConfigInterface;

/**
 * @author Marek Kalnik <marekk@theodo.fr>, Peter Drake <pdrake@gmail.com>
 */
class DrupalCurlyBracketsNewlineFixer implements FixerInterface
{
    const REMOVE_NEWLINE = '\\1 {\\4';

    // Capture the indentation first
    const REMOVE_NEWLINE_TWO = "\\1\\2 {";

    public function fix(\SplFileInfo $file, $content)
    {
        $content = $this->classDeclarationFix($content);
        $content = $this->functionDeclarationFix($content);
        $content = $this->anonymousFunctionsFix($content);
        $content = $this->controlStatementContinuationFix($content);

        return $content;
    }

    public function getLevel()
    {
        return false;
    }

    public function getPriority()
    {
        return -1;
    }

    public function supports(\SplFileInfo $file, ConfigInterface $config)
    {
        return 'php' === $config->getFileType($file);
    }

    public function getName()
    {
        return 'drupal_braces';
    }

    public function getDescription()
    {
        return 'Opening braces for classes, interfaces, traits, methods and control structures must go on the same line, and closing braces must go on the next line after the body.';
    }

    private function classDeclarationFix($content)
    {
        // [Structure] No new line after class declaration
        return preg_replace('/^([ \t]*)((?:[\w \t]+ )?(class|interface|trait) [\w, \t\\\\]+?)[ \t]*\n{\s*$/m', self::REMOVE_NEWLINE_TWO, $content);
    }

    private function functionDeclarationFix($content)
    {
        // [Structure] No new line after function declaration
        return preg_replace('/^([ \t]*)((?:[\w \t]+ )?function [\w \t]+\(.*?\))[ \t]*\n{\s*$/m', self::REMOVE_NEWLINE_TWO, $content);
    }

    private function anonymousFunctionsFix($content)
    {
        // [Structure] No new line after anonymous function call
        return preg_replace('/((^|[\s\W])function\s*\(.*\))([^\n]*?) *\n[^\S\n]*\n{/', self::REMOVE_NEWLINE, $content);
    }

    private function controlStatementContinuationFix($content)
    {
        $statements = array(
            'catch',
            'else',
        );

        // [Structure] Add new line after control statements
        return preg_replace('/(^|[\s\W])}\s*(' . implode('|', $statements) . ')/', "\\1}\n\\1\\1\\2", $content);
    }
}
