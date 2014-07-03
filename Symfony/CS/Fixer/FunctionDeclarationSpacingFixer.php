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
class FunctionDeclarationSpacingFixer implements FixerInterface
{
    /**
     * {@inheritdoc}
     */
    public function fix(\SplFileInfo $file, $content)
    {
        $content = $this->fixNamedFunctions($content);
        $content = $this->fixAnonymousFunctions($content);
        $content = $this->fixSpaceBeforeBrace($content);

        return $content;
    }

    /**
     * {@inheritdoc}
     */
    public function getLevel()
    {
        // defined in PSR2 generally (¶1 and ¶6)
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
        return 'php' === pathinfo($file->getFilename(), PATHINFO_EXTENSION);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'function_declaration';
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'Spaces should be properly placed in a function declaration';
    }

    private function fixAnonymousFunctions($content)
    {
        $content = preg_replace(
            $this->regex(array('params', 'end')),
            '\1function (\2)\3',
            $content
        );
        $content = preg_replace(
            $this->regex(array('params', 'use', 'end')),
            '\1function (\2) use (\3)\4',
            $content
        );

        return $content;
    }

    private function fixNamedFunctions($content)
    {
        return preg_replace(
            $this->regex(array('name', 'params', 'end')),
            '\1function \2(\3)\4',
            $content
        );
    }

    /**
     * In previous steps we have cut all horizontal whitespace,
     * so where are left with function(){
     * Add a missing space at the end: function () {
     * This does not touch function declarations with brace on another line.
     */
    private function fixSpaceBeforeBrace($content)
    {
        return preg_replace(
            '/(function[^{]+\)){/',
            '\1 {',
            $content);
    }

    private function regex(array $keys)
    {
        $map = array(
            'name' => '\s+([a-zA-Z0-9_]+)',
            'params' => '
                \s*
                \(
                    \h*
                    (
                        [^)]*
                        [^)\s]
                    )?
                    \h*
                \)
            ',
            'end' => '\h*((?:\v\s*)?{)',
        );
        $map['use'] = '\s*use'.$map['params'];

        return '/(^|[^a-zA-Z0-9_\x7f-\xff\$])function' . implode('', array_map(function ($key) use ($map) {
            return $map[$key];
        }, $keys)).'/x';
    }
}
