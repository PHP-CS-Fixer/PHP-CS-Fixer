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
use Symfony\CS\Tokens;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
class CamelCaseFixer implements FixerInterface
{
    /**
     * {@inheritdoc}
     */
    public function fix(\SplFileInfo $file, $content)
    {
        $tokens = Tokens::fromCode($content);

        $elements = $tokens->getClassyElements();

        foreach ($elements['methods'] as $index => $token) {
            $methodNameToken = $tokens->getNextTokenOfKind($index, array(array(T_STRING), ));

            if (!Tokens::isMethodNameIsMagic($methodNameToken[1]) && !Tokens::isNameIsInCamelCase($methodNameToken[1])) {
                echo '! File '.strtr($file->getRealPath(), '\\', '/').' contains method not in camelCase: '.$methodNameToken[1].PHP_EOL;
            }
        }

        return $tokens->generateCode();
    }

    /**
     * {@inheritdoc}
     */
    public function getLevel()
    {
        // defined in PSR-1 ¶4.3
        return FixerInterface::PSR1_LEVEL;
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
        return 'camel_case';
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'Method names MUST be declared in camelCase (detect only).';
    }
}
