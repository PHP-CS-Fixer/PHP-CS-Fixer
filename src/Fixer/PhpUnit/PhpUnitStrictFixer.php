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
use PhpCsFixer\Tokenizer\Analyzer\ArgumentsAnalyzer;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
final class PhpUnitStrictFixer extends AbstractFixer implements ConfigurableFixerInterface
{
    private static $assertionMap = [
        'assertAttributeEquals' => 'assertAttributeSame',
        'assertAttributeNotEquals' => 'assertAttributeNotSame',
        'assertEquals' => 'assertSame',
        'assertNotEquals' => 'assertNotSame',
    ];

    /**
     * {@inheritdoc}
     */
    public function getDefinition()
    {
        return new FixerDefinition(
            'PHPUnit methods like `assertSame` should be used instead of `assertEquals`.',
            [
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
',
                    ['assertions' => ['assertEquals']]
                ),
            ],
            null,
            'Risky when any of the functions are overridden.'
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

    /**
     * {@inheritdoc}
     */
    protected function applyFix(\SplFileInfo $file, Tokens $tokens)
    {
        $argumentsAnalyzer = new ArgumentsAnalyzer();

        foreach ($this->configuration['assertions'] as $methodBefore) {
            $methodAfter = self::$assertionMap[$methodBefore];

            for ($index = 0, $limit = $tokens->count(); $index < $limit; ++$index) {
                $methodIndex = $tokens->getNextTokenOfKind($index, [[T_STRING, $methodBefore]]);

                if (null === $methodIndex) {
                    break;
                }

                $operatorIndex = $tokens->getPrevMeaningfulToken($methodIndex);
                $referenceIndex = $tokens->getPrevMeaningfulToken($operatorIndex);
                if (
                    !($tokens[$operatorIndex]->equals([T_OBJECT_OPERATOR, '->']) && $tokens[$referenceIndex]->equals([T_VARIABLE, '$this']))
                    && !($tokens[$operatorIndex]->equals([T_DOUBLE_COLON, '::']) && $tokens[$referenceIndex]->equals([T_STRING, 'self']))
                    && !($tokens[$operatorIndex]->equals([T_DOUBLE_COLON, '::']) && $tokens[$referenceIndex]->equals([T_STATIC, 'static']))
                ) {
                    continue;
                }

                $openingParenthesisIndex = $tokens->getNextmeaningfulToken($methodIndex);
                $argumentsCount = $argumentsAnalyzer->countArguments(
                    $tokens,
                    $openingParenthesisIndex,
                    $tokens->findBlockEnd(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $openingParenthesisIndex)
                );

                if (2 === $argumentsCount || 3 === $argumentsCount) {
                    $tokens[$methodIndex] = new Token([T_STRING, $methodAfter]);
                }

                $index = $methodIndex;
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
                    (new FixerOptionValidatorGenerator())->allowedValueIsSubsetOf(array_keys(self::$assertionMap)),
                ])
                ->setDefault([
                    'assertAttributeEquals',
                    'assertAttributeNotEquals',
                    'assertEquals',
                    'assertNotEquals',
                ])
                ->getOption(),
        ]);
    }
}
