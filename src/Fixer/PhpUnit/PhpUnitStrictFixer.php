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
use PhpCsFixer\Fixer\ConfigurableFixerInterface;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
final class PhpUnitStrictFixer extends AbstractFixer implements ConfigurableFixerInterface
{
    /**
     * @var string[]
     */
    private static $defaultConfiguration = array(
        'assertAttributeEquals',
        'assertAttributeNotEquals',
        'assertEquals',
        'assertNotEquals',
    );

    /**
     * @var string[]
     */
    private $configuration;

    private static $assertionMap = array(
        'assertAttributeEquals' => 'assertAttributeSame',
        'assertAttributeNotEquals' => 'assertAttributeNotSame',
        'assertEquals' => 'assertSame',
        'assertNotEquals' => 'assertNotSame',
    );

    /**
     * {@inheritdoc}
     */
    public function configure(array $configuration = null)
    {
        if (null === $configuration) {
            $this->configuration = self::$defaultConfiguration;

            return;
        }

        foreach ($configuration as $method) {
            if (!array_key_exists($method, self::$assertionMap)) {
                throw new InvalidFixerConfigurationException($this->getName(), sprintf('Configured method "%s" cannot be fixed by this fixer.', $method));
            }
        }

        $this->configuration = $configuration;
    }

    /**
     * {@inheritdoc}
     */
    public function fix(\SplFileInfo $file, Tokens $tokens)
    {
        foreach ($this->configuration as $methodBefore) {
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
            'Configure which of the following functions should be replaced `assertAttributeEquals`, `assertAttributeNotEquals`, `assertEquals`, `assertNotEquals`',
            array(
                'assertAttributeEquals',
                'assertAttributeNotEquals',
                'assertEquals',
                'assertNotEquals',
            ),
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
