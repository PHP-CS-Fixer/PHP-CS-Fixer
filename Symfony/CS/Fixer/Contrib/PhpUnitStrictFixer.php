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
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
final class PhpUnitStrictFixer extends AbstractFixer
{
    private $phpUnitMethods = array(
        'assertAttributeEquals' => 'assertAttributeSame',
        'assertAttributeNotEquals' => 'assertAttributeNotSame',
        'assertEquals' => 'assertSame',
        'assertNotEquals' => 'assertNotSame',
    );

    public function configure(array $usingMethods = null)
    {
        if (null === $usingMethods) {
            return;
        }

        foreach (array_keys($this->phpUnitMethods) as $method) {
            if (!in_array($method, $usingMethods, true)) {
                unset($this->phpUnitMethods[$method]);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function isCandidate(Tokens $tokens)
    {
        return $tokens->isTokenKindFound(T_STRING);
    }

    /**
     * {@inheritdoc}
     */
    public function fix(\SplFileInfo $file, Tokens $tokens)
    {
        foreach ($this->phpUnitMethods as $methodBefore => $methodAfter) {
            for ($index = 0, $limit = $tokens->count(); $index < $limit; ++$index) {
                $sequence = $tokens->findSequence(
                    array(
                        array(T_VARIABLE, '$this'),
                        array(T_OBJECT_OPERATOR, '->'),
                        array(T_STRING, $methodBefore),
                        '(',
                    ),
                    $index
                );

                if (null === $sequence) {
                    break;
                }

                $sequenceIndexes = array_keys($sequence);
                $tokens[$sequenceIndexes[2]]->setContent($methodAfter);

                $index = $sequenceIndexes[3];
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'PHPUnit methods like "assertSame" should be used instead of "assertEquals". Warning! This could change code behavior.';
    }
}
