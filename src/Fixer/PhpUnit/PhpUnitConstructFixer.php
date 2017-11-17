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
use PhpCsFixer\Fixer\ConfigurationDefinitionFixerInterface;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolverRootless;
use PhpCsFixer\FixerConfiguration\FixerOptionBuilder;
use PhpCsFixer\FixerConfiguration\FixerOptionValidatorGenerator;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
final class PhpUnitConstructFixer extends AbstractFixer implements ConfigurationDefinitionFixerInterface
{
    private static $assertionFixers = array(
        'assertSame' => 'fixAssertPositive',
        'assertEquals' => 'fixAssertPositive',
        'assertNotEquals' => 'fixAssertNegative',
        'assertNotSame' => 'fixAssertNegative',
    );

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
    public function getDefinition()
    {
        return new FixerDefinition(
            'PHPUnit assertion method calls like "->assertSame(true, $foo)" should be written with dedicated method like "->assertTrue($foo)".',
            array(
                new CodeSample(
                    '<?php
$this->assertEquals(false, $b);
$this->assertSame(true, $a);
$this->assertNotEquals(null, $c);
$this->assertNotSame(null, $d);
'
                ),
                new CodeSample(
                    '<?php
$this->assertEquals(false, $b);
$this->assertSame(true, $a);
$this->assertNotEquals(null, $c);
$this->assertNotSame(null, $d);
',
                    array('assertions' => array('assertSame', 'assertNotSame'))
                ),
            ),
            null,
            'Fixer could be risky if one is overriding PHPUnit\'s native methods.'
        );
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
    protected function applyFix(\SplFileInfo $file, Tokens $tokens)
    {
        // no assertions to be fixed - fast return
        if (empty($this->configuration['assertions'])) {
            return;
        }

        foreach ($this->configuration['assertions'] as $assertionMethod) {
            $assertionFixer = self::$assertionFixers[$assertionMethod];

            for ($index = 0, $limit = $tokens->count(); $index < $limit; ++$index) {
                $index = $this->{$assertionFixer}($tokens, $index, $assertionMethod);

                if (null === $index) {
                    break;
                }
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function createConfigurationDefinition()
    {
        $generator = new FixerOptionValidatorGenerator();

        $assertions = new FixerOptionBuilder('assertions', 'List of assertion methods to fix.');
        $assertions = $assertions
            ->setAllowedTypes(array('array'))
            ->setAllowedValues(array(
                $generator->allowedValueIsSubsetOf(array_keys(self::$assertionFixers)),
            ))
            ->setDefault(array(
                'assertEquals',
                'assertSame',
                'assertNotEquals',
                'assertNotSame',
            ))
            ->getOption()
        ;

        return new FixerConfigurationResolverRootless('assertions', array($assertions));
    }

    /**
     * @param Tokens $tokens
     * @param int    $index
     * @param string $method
     *
     * @return null|int
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
     * @return null|int
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
     * @return null|int
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
            return null;
        }

        $sequenceIndexes = array_keys($sequence);
        $sequenceIndexes[4] = $tokens->getNextMeaningfulToken($sequenceIndexes[3]);
        $firstParameterToken = $tokens[$sequenceIndexes[4]];

        if (!$firstParameterToken->isNativeConstant()) {
            return $sequenceIndexes[4];
        }

        $sequenceIndexes[5] = $tokens->getNextMeaningfulToken($sequenceIndexes[4]);

        // return if first method argument is an expression, not value
        if (!$tokens[$sequenceIndexes[5]]->equals(',')) {
            return $sequenceIndexes[5];
        }

        $tokens[$sequenceIndexes[2]] = new Token(array(T_STRING, $map[$firstParameterToken->getContent()]));
        $tokens->clearRange($sequenceIndexes[4], $tokens->getNextNonWhitespace($sequenceIndexes[5]) - 1);

        return $sequenceIndexes[5];
    }
}
