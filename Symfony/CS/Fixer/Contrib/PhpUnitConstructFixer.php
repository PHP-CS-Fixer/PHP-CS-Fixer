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
final class PhpUnitConstructFixer extends AbstractFixer
{
    private $configuration = array(
        'assertSame' => true,
        'assertEquals' => true,
        'assertNotEquals' => true,
        'assertNotSame' => true,
    );

    private $assertionFixers = array(
        'assertSame' => 'fixAssertPositive',
        'assertEquals' => 'fixAssertPositive',
        'assertNotEquals' => 'fixAssertNegative',
        'assertNotSame' => 'fixAssertNegative',
    );

    /**
     * {@inheritdoc}
     */
    public function configure(array $usingMethods = null)
    {
        if (null === $usingMethods) {
            return;
        }

        foreach ($usingMethods as $method => $fix) {
            if (!isset($this->configuration[$method])) {
                throw new \InvalidArgumentException();
            }

            $this->configuration[$method] = $fix;
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
        // no assertions to be fixed - fast return
        if (!in_array(true, $this->configuration, true)) {
            return;
        }

        foreach ($this->configuration as $assertionMethod => $assertionShouldBeFixed) {
            if (true !== $assertionShouldBeFixed) {
                continue;
            }

            $assertionFixer = $this->assertionFixers[$assertionMethod];

            for ($index = 0, $limit = $tokens->count(); $index < $limit; ++$index) {
                $index = $this->$assertionFixer($tokens, $index, $assertionMethod);

                if (null === $index) {
                    break;
                }
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'PHPUnit assertion method calls like "->assertSame(true, $foo)" should be written with dedicated method like "->assertTrue($foo)". Warning! This could change code behavior.';
    }

    /**
     * {@inheritdoc}
     */
    public function getPriority()
    {
        // should be run after the PhpUnitStrictFixer
        return -10;
    }

    private function fixAssertNegative(Tokens $tokens, $index, $method)
    {
        $sequence = $tokens->findSequence(
            array(
                array(T_VARIABLE, '$this'),
                array(T_OBJECT_OPERATOR, '->'),
                array(T_STRING, $method),
                '(',
                array(T_STRING, 'null'),
                ',',
            ),
            $index
        );

        if (null === $sequence) {
            return;
        }

        $sequenceIndexes = array_keys($sequence);
        $tokens[$sequenceIndexes[2]]->setContent('assertNotNull');
        $tokens->clearRange($sequenceIndexes[4], $tokens->getNextNonWhitespace($sequenceIndexes[5]) - 1);

        return $sequenceIndexes[5];
    }

    private function fixAssertPositive(Tokens $tokens, $index, $method)
    {
        static $map = array(
            'false' => 'assertFalse',
            'null' => 'assertNull',
            'true' => 'assertTrue',
        );

        $sequence = $tokens->findSequence(
            array(
                array(T_VARIABLE, '$this'),
                array(T_OBJECT_OPERATOR, '->'),
                array(T_STRING, $method),
                '(',
            ),
            $index
        );

        if (null === $sequence) {
            return;
        }

        $sequenceIndexes = array_keys($sequence);
        $sequenceIndexes[4] = $tokens->getNextMeaningfulToken($sequenceIndexes[3]);
        $firstParameterToken = $tokens[$sequenceIndexes[4]];

        if (!$firstParameterToken->isNativeConstant()) {
            return;
        }

        $sequenceIndexes[5] = $tokens->getNextNonWhitespace($sequenceIndexes[4]);

        $tokens[$sequenceIndexes[2]]->setContent($map[$firstParameterToken->getContent()]);
        $tokens->clearRange($sequenceIndexes[4], $tokens->getNextNonWhitespace($sequenceIndexes[5]) - 1);

        return $sequenceIndexes[5];
    }
}
