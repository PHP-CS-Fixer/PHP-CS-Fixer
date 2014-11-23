<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Fixer\Contrib;

use Symfony\CS\AbstractFixer;
use Symfony\CS\Tokenizer\Tokens;

/**
 * @author Szymon Piasecki <szymon.piasecki@gmail.com>
 */
class OldStyleConstructorFixer extends AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function fix(\SplFileInfo $file, $content)
    {
        $tokens = Tokens::fromCode($content);

        foreach ($tokens->findGivenKind(T_CLASS) as $index => $classToken) {
            $classOpen = $tokens->getNextTokenOfKind($index, array('{'));
            $classEnd = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_CURLY_BRACE, $classOpen);

            if ($this->hasMethodNamed($tokens, "__construct", $classOpen, $classEnd)) {
                continue;
            }

            $className = $tokens[$tokens->getNextNonWhitespace($index)]->getContent();
            if (!$this->hasMethodNamed($tokens, $className, $classOpen, $classEnd)) {
                continue;
            }

            $constructorIndex = $this->getMethodByName($tokens, $className, $classOpen, $classEnd);
            $tokens[$tokens->getNextNonWhitespace($constructorIndex)]->setContent("__construct");
        }

        return $tokens->generateCode();
    }

    /**
     * Check if class has a method with given name.
     *
     * @param Tokens $tokens collection of tokens
     * @param string $name   method name to find
     * @param int    $start  index of class start
     * @param int    $end    index of class end
     *
     * @return bool
     */
    protected function hasMethodNamed(Tokens $tokens, $name, $start, $end)
    {
        return $this->getMethodByName($tokens, $name, $start, $end) !== null;
    }

    /**
     * Get index of class method with given name.
     *
     * @param Tokens $tokens collection of tokens
     * @param string $name   method name to find
     * @param int    $start  index of class start
     * @param int    $end    index of class end
     *
     * @return int|null
     */
    protected function getMethodByName(Tokens $tokens, $name, $start, $end)
    {
        for ($i = $start;$i <= $end; $i++) {
            if (!$tokens[$i]->isGivenKind(T_FUNCTION)) {
                continue;
            }

            $methodName = $tokens->getNextNonWhitespace($i);
            if ($tokens[$methodName]->getContent() === $name) {
                return $i;
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'An old PHP style constructors fixer.';
    }

    /**
     * {@inheritdoc}
     */
    public function getPriority()
    {
        // should be run after PSR2\VisibilityFixer
        return -10;
    }
}
