<?php

/*
 * This file is part of PHP CS Fixer.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *     Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace PhpCsFixer\Fixer\PhpUnit;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\ConfigurationException\InvalidFixerConfigurationException;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
final class PhpUnitConstructFixer extends AbstractFixer
{
    /**
     * @var string[]
     */
    private static $defaultConfiguration = array(
        'assertSame',
        'assertEquals',
        'assertNotEquals',
        'assertNotSame',
    );

    /**
     * @var string[]
     */
    private $configuration;

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
            $this->configuration = self::$defaultConfiguration;

            return;
        }

        foreach ($usingMethods as $method) {
            if (!array_key_exists($method, $this->assertionFixers)) {
                throw new InvalidFixerConfigurationException($this->getName(), sprintf('Configured method "%s" cannot be fixed by this fixer.', $method));
            }
        }

        $this->configuration = $usingMethods;
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
    public function isRisky()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function fix(\SplFileInfo $file, Tokens $tokens)
    {
        // no assertions to be fixed - fast return
        if (empty($this->configuration)) {
            return;
        }

        foreach ($this->configuration as $assertionMethod) {
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
    public function getPriority()
    {
        // should be run after the PhpUnitStrictFixer and before PhpUnitDedicateAssertFixer.
        return -10;
    }

    /**
     * {@inheritdoc}
     */
    protected function getDescription()
    {
        return 'PHPUnit assertion method calls like "->assertSame(true, $foo)" should be written with dedicated method like "->assertTrue($foo)".';
    }

    /**
     * @param Tokens $tokens
     * @param int    $index
     * @param string $method
     *
     * @return int|null
     */
    private function fixAssertNegative(Tokens $tokens, $index, $method)
    {
        static $map = array(
            'false' => 'assertNotFalse',
            'null' => 'assertNotNull',
            'true' => 'assertNotTrue',
        );

        return $this->fixAssert($map, $tokens, $index, $method);
    }

    /**
     * @param Tokens $tokens
     * @param int    $index
     * @param string $method
     *
     * @return int|null
     */
    private function fixAssertPositive(Tokens $tokens, $index, $method)
    {
        static $map = array(
            'false' => 'assertFalse',
            'null' => 'assertNull',
            'true' => 'assertTrue',
        );

        return $this->fixAssert($map, $tokens, $index, $method);
    }

    /**
     * @param array<string, string> $map
     * @param Tokens                $tokens
     * @param int                   $index
     * @param string                $method
     *
     * @return int|null
     */
    private function fixAssert(array $map, Tokens $tokens, $index, $method)
    {
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

        $sequenceIndexes[5] = $tokens->getNextMeaningfulToken($sequenceIndexes[4]);

        // return if first method argument is an expression, not value
        if (!$tokens[$sequenceIndexes[5]]->equals(',')) {
            return;
        }

        $tokens[$sequenceIndexes[2]]->setContent($map[$firstParameterToken->getContent()]);
        $tokens->clearRange($sequenceIndexes[4], $tokens->getNextNonWhitespace($sequenceIndexes[5]) - 1);

        return $sequenceIndexes[5];
    }
}
