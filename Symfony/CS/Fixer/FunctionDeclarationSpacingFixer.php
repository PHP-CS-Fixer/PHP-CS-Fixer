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
        return $file->getExtension() === 'php';
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
            $this->regex('params'),
            'function (\1) {',
            $content
        );
        $content = preg_replace(
            $this->regex('params', 'use'),
            'function (\1) use (\2) {',
            $content
        );
        return $content;
    }

    private function fixNamedFunctions($content)
    {
        return preg_replace(
            $this->regex('name', 'params'),
            'function \1(\2) {',
            $content
        );
    }

    private function regex()
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
        );
        $map['use'] = '\s*use'.$map['params'];

        return '/function'.implode('', array_map(function($arg) use ($map) {
            return $map[$arg];
        }, func_get_args())).'\h*{/x';
    }
}
