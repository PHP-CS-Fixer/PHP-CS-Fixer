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
use PhpCsFixer\Fixer\ConfigurableFixerInterface;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolver;
use PhpCsFixer\FixerConfiguration\FixerOptionBuilder;
use PhpCsFixer\FixerConfiguration\FixerOptionValidatorGenerator;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
final class PhpUnitConstructFixer extends AbstractFixer implements ConfigurableFixerInterface
{
    private static $assertionFixers = [
        'assertSame' => 'fixAssertPositive',
        'assertEquals' => 'fixAssertPositive',
        'assertNotEquals' => 'fixAssertNegative',
        'assertNotSame' => 'fixAssertNegative',
    ];

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
            [
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
                    ['assertions' => ['assertSame', 'assertNotSame']]
                ),
            ],
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
        return new FixerConfigurationResolver([
            (new FixerOptionBuilder('assertions', 'List of assertion methods to fix.'))
                ->setAllowedTypes(['array'])
                ->setAllowedValues([
                    (new FixerOptionValidatorGenerator())->allowedValueIsSubsetOf(array_keys(self::$assertionFixers)),
                ])
                ->setDefault([
                    'assertEquals',
                    'assertSame',
                    'assertNotEquals',
                    'assertNotSame',
                ])
                ->getOption(),
        ]);
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
        static $map = [
            'false' => 'assertNotFalse',
            'null' => 'assertNotNull',
            'true' => 'assertNotTrue',
        ];

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
        static $map = [
            'false' => 'assertFalse',
            'null' => 'assertNull',
            'true' => 'assertTrue',
        ];

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
            [
                [T_STRING, $method],
                '(',
            ],
            $index
        );

        if (null === $sequence) {
            return null;
        }

        $sequenceIndexes = array_keys($sequence);
        $operatorIndex = $tokens->getPrevMeaningfulToken($sequenceIndexes[0]);
        $referenceIndex = $tokens->getPrevMeaningfulToken($operatorIndex);
        if (
            !($tokens[$operatorIndex]->equals([T_OBJECT_OPERATOR, '->']) && $tokens[$referenceIndex]->equals([T_VARIABLE, '$this']))
            && !($tokens[$operatorIndex]->equals([T_DOUBLE_COLON, '::']) && $tokens[$referenceIndex]->equals([T_STRING, 'self']))
            && !($tokens[$operatorIndex]->equals([T_DOUBLE_COLON, '::']) && $tokens[$referenceIndex]->equals([T_STATIC, 'static']))
        ) {
            return null;
        }

        $sequenceIndexes[2] = $tokens->getNextMeaningfulToken($sequenceIndexes[1]);
        $firstParameterToken = $tokens[$sequenceIndexes[2]];

        if (!$firstParameterToken->isNativeConstant()) {
            return $sequenceIndexes[2];
        }

        $sequenceIndexes[3] = $tokens->getNextMeaningfulToken($sequenceIndexes[2]);

        // return if first method argument is an expression, not value
        if (!$tokens[$sequenceIndexes[3]]->equals(',')) {
            return $sequenceIndexes[3];
        }

        $tokens[$sequenceIndexes[0]] = new Token([T_STRING, $map[$firstParameterToken->getContent()]]);
        $tokens->clearRange($sequenceIndexes[2], $tokens->getNextNonWhitespace($sequenceIndexes[3]) - 1);

        return $sequenceIndexes[3];
    }
}
