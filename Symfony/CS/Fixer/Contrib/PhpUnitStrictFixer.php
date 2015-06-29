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
    static private $phpUnitMethods = array(
        'assertAttributeEquals' => 'assertAttributeSame',
        'assertAttributeNotEquals' => 'assertAttributeNotSame',
        'assertEquals' => 'assertSame',
        'assertNotEquals' => 'assertNotSame',
    );

    /**
     * {@inheritdoc}
     */
    public function fix(\SplFileInfo $file, $content)
    {
        $tokens = Tokens::fromCode($content);

        foreach (self::$phpUnitMethods as $methodBefore => $methodAfter) {
            for ($index = 0, $limit = $tokens->count(); $index < $limit; ++$index) {
                $sequence = $tokens->findSequence(
                    array(
                        array(T_VARIABLE, '$this'),
                        array(T_OBJECT_OPERATOR, '->'),
                        array(T_STRING, $methodBefore),
                        '(',
                    ),
                    0
                );

                if (null === $sequence) {
                    break;
                }

                $sequenceIndexes = array_keys($sequence);
                $tokens[$sequenceIndexes[2]]->setContent($methodAfter);

                $index = $sequenceIndexes[3];
            }
        }

        return $tokens->generateCode();
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'PHPUnit methods like "assertSame" should be used instead of "assertEquals". Warning! This could change code behavior.';
    }
}
