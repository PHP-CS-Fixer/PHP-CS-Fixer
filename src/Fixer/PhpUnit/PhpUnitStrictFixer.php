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
use PhpCsFixer\FixerConfiguration\FixerOption;
use PhpCsFixer\FixerConfiguration\FixerOptionValidatorGenerator;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
final class PhpUnitStrictFixer extends AbstractFixer implements ConfigurationDefinitionFixerInterface
{
    private static $assertionMap = array(
        'assertAttributeEquals' => 'assertAttributeSame',
        'assertAttributeNotEquals' => 'assertAttributeNotSame',
        'assertEquals' => 'assertSame',
        'assertNotEquals' => 'assertNotSame',
    );

    /**
     * {@inheritdoc}
     */
    public function getConfigurationDefinition()
    {
        $generator = new FixerOptionValidatorGenerator();

        $assertions = new FixerOption('assertions', 'List of assertion methods to fix.');
        $assertions
            ->setAllowedTypes(array('array'))
            ->setAllowedValues(array(
                $generator->allowedValueIsSubsetOf(array_keys(self::$assertionMap)),
            ))
            ->setDefault(array(
                'assertAttributeEquals',
                'assertAttributeNotEquals',
                'assertEquals',
                'assertNotEquals',
            ))
        ;

        return new FixerConfigurationResolverRootless('assertions', array(
            $assertions,
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function fix(\SplFileInfo $file, Tokens $tokens)
    {
        foreach ($this->configuration['assertions'] as $methodBefore) {
            $methodAfter = self::$assertionMap[$methodBefore];

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
    public function getDefinition()
    {
        return new FixerDefinition(
            'PHPUnit methods like `assertSame` should be used instead of `assertEquals`.',
            array(
                new CodeSample(
'<?php
final class MyTest extends \PHPUnit_Framework_TestCase
{
    public function testSomeTest()
    {
        $this->assertAttributeEquals(a(), b());
        $this->assertAttributeNotEquals(a(), b());
        $this->assertEquals(a(), b());
        $this->assertNotEquals(a(), b());
    }
}
'
                ),
            ),
            null,
            'Risky when the any of functions are overridden.'
        );
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
}
