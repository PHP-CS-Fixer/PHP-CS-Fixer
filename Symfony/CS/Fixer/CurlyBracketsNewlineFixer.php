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
 * @author Marek Kalnik <marekk@theodo.fr>
 */
class CurlyBracketsNewlineFixer implements FixerInterface
{
    const REMOVE_NEWLINE = '\\1 {\\4';

    // Capture the indentation first
    const ADD_NEWLINE = "\\1\\2\n\\1{";

    public function fix(\SplFileInfo $file, $content)
    {
        $content = $this->classDeclarationFix($content);
        $content = $this->functionDeclarationFix($content);
        $content = $this->anonymousFunctionsFix($content);
        $content = $this->controlStatementsFix($content);
        $content = $this->controlStatementContinuationFix($content);
        $content = $this->doWhileFix($content);

        return $content;
    }

    public function getLevel()
    {
        // defined in PSR2 ¶4.3, ¶4.3, ¶4.4, ¶5
        return FixerInterface::PSR2_LEVEL;
    }

    public function getPriority()
    {
        return 0;
    }

    public function supports(\SplFileInfo $file)
    {
        return 'php' === pathinfo($file->getFilename(), PATHINFO_EXTENSION);
    }

    public function getName()
    {
        return 'braces';
    }

    public function getDescription()
    {
        return 'Opening braces for classes, interfaces, traits and methods must go on the next line, and closing braces must go on the next line after the body. Opening braces for control structures must go on the same line, and closing braces must go on the next line after the body.';
    }

    private function classDeclarationFix($content)
    {
        // [Structure] Add new line after class declaration
        return preg_replace('/^([ \t]*)((?:[\w \t]+ )?(class|interface|trait) [\w, \t\\\\]+?)[ \t]*{\s*$/m', self::ADD_NEWLINE, $content);
    }

    private function functionDeclarationFix($content)
    {
        // [Structure] Add new line after function declaration
        return preg_replace('/^([ \t]*)((?:[\w \t]+ )?function [\w \t]+\(.*?\))[ \t]*{\s*$/m', self::ADD_NEWLINE, $content);
    }

    private function anonymousFunctionsFix($content)
    {
        // [Structure] No new line after anonymous function call
        return preg_replace('/((^|[\s\W])function\s*\(.*\))([^\n]*?) *\n[^\S\n]*{/', self::REMOVE_NEWLINE, $content);
    }

    private function controlStatementsFix($content)
    {
        $statements = array(
            '\bif\s*\(.*\)',
            '\belse\s*if\s*\(.*\)',
            '\b(?<!\$)else\b',
            '\bfor\s*\(.*\)',
            '\b(?<!\$)do\b',
            '\bwhile\s*\(.*\)',
            '\bforeach\s*\(.*\)',
            '\bswitch\s*\(.*\)',
            '\b(?<!\$)try\b',
            '\bcatch\s*\(.*\)',
        );

        // [Structure] No new line after control statements
        return preg_replace('/((^|[\s\W])('.implode('|', $statements).'))([^\n]*?) *\n[^\S\n]*{/', self::REMOVE_NEWLINE, $content);
    }

    private function controlStatementContinuationFix($content)
    {
        $statements = array(
            'catch',
            'else',
        );

        // [Structure] No new line after control statements
        return preg_replace('/}\s*\n\s*('.implode('|', $statements).')/', '} \\1', $content);
    }

    private function doWhileFix($content)
    {
        // [Structure] do...while loops are formatted like if {\n... \n} else {\n
        return preg_replace('/(do {[\s\S]*)}\s*\n\s*while/', '\\1} while', $content);
    }
}
