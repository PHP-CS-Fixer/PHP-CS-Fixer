<?php

declare(strict_types=1);

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

use PhpCsFixer\Fixer\AbstractPhpUnitFixer;
use PhpCsFixer\Fixer\ConfigurableFixerInterface;
use PhpCsFixer\Fixer\WhitespacesAwareFixerInterface;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolver;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolverInterface;
use PhpCsFixer\FixerConfiguration\FixerOptionBuilder;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Analyzer\ArgumentsAnalyzer;
use PhpCsFixer\Tokenizer\Analyzer\WhitespacesAnalyzer;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
final class PhpUnitExpectationFixer extends AbstractPhpUnitFixer implements ConfigurableFixerInterface, WhitespacesAwareFixerInterface
{
    /**
     * @var array<string, string>
     */
    private array $methodMap = [];

    /**
     * {@inheritdoc}
     */
    public function configure(array $configuration): void
    {
        parent::configure($configuration);

        $this->methodMap = [
            'setExpectedException' => 'expectExceptionMessage',
        ];

        if (PhpUnitTargetVersion::fulfills($this->configuration['target'], PhpUnitTargetVersion::VERSION_5_6)) {
            $this->methodMap['setExpectedExceptionRegExp'] = 'expectExceptionMessageRegExp';
        }

        if (PhpUnitTargetVersion::fulfills($this->configuration['target'], PhpUnitTargetVersion::VERSION_8_4)) {
            $this->methodMap['setExpectedExceptionRegExp'] = 'expectExceptionMessageMatches';
            $this->methodMap['expectExceptionMessageRegExp'] = 'expectExceptionMessageMatches';
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(
            'Usages of `->setExpectedException*` methods MUST be replaced by `->expectException*` methods.',
            [
                new CodeSample(
                    '<?php
final class MyTest extends \PHPUnit_Framework_TestCase
{
    public function testFoo()
    {
        $this->setExpectedException("RuntimeException", "Msg", 123);
        foo();
    }

    public function testBar()
    {
        $this->setExpectedExceptionRegExp("RuntimeException", "/Msg.*/", 123);
        bar();
    }
}
'
                ),
                new CodeSample(
                    '<?php
final class MyTest extends \PHPUnit_Framework_TestCase
{
    public function testFoo()
    {
        $this->setExpectedException("RuntimeException", null, 123);
        foo();
    }

    public function testBar()
    {
        $this->setExpectedExceptionRegExp("RuntimeException", "/Msg.*/", 123);
        bar();
    }
}
',
                    ['target' => PhpUnitTargetVersion::VERSION_8_4]
                ),
                new CodeSample(
                    '<?php
final class MyTest extends \PHPUnit_Framework_TestCase
{
    public function testFoo()
    {
        $this->setExpectedException("RuntimeException", null, 123);
        foo();
    }

    public function testBar()
    {
        $this->setExpectedExceptionRegExp("RuntimeException", "/Msg.*/", 123);
        bar();
    }
}
',
                    ['target' => PhpUnitTargetVersion::VERSION_5_6]
                ),
                new CodeSample(
                    '<?php
final class MyTest extends \PHPUnit_Framework_TestCase
{
    public function testFoo()
    {
        $this->setExpectedException("RuntimeException", "Msg", 123);
        foo();
    }

    public function testBar()
    {
        $this->setExpectedExceptionRegExp("RuntimeException", "/Msg.*/", 123);
        bar();
    }
}
',
                    ['target' => PhpUnitTargetVersion::VERSION_5_2]
                ),
            ],
            null,
            'Risky when PHPUnit classes are overridden or not accessible, or when project has PHPUnit incompatibilities.'
        );
    }

    /**
     * {@inheritdoc}
     *
     * Must run after PhpUnitNoExpectationAnnotationFixer.
     */
    public function getPriority(): int
    {
        return 0;
    }

    /**
     * {@inheritdoc}
     */
    public function isRisky(): bool
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    protected function createConfigurationDefinition(): FixerConfigurationResolverInterface
    {
        return new FixerConfigurationResolver([
            (new FixerOptionBuilder('target', 'Target version of PHPUnit.'))
                ->setAllowedTypes(['string'])
                ->setAllowedValues([PhpUnitTargetVersion::VERSION_5_2, PhpUnitTargetVersion::VERSION_5_6, PhpUnitTargetVersion::VERSION_8_4, PhpUnitTargetVersion::VERSION_NEWEST])
                ->setDefault(PhpUnitTargetVersion::VERSION_NEWEST)
                ->getOption(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    protected function applyPhpUnitClassFix(Tokens $tokens, int $startIndex, int $endIndex): void
    {
        foreach (Token::getObjectOperatorKinds() as $objectOperator) {
            $this->applyPhpUnitClassFixWithObjectOperator($tokens, $startIndex, $endIndex, $objectOperator);
        }
    }

    private function applyPhpUnitClassFixWithObjectOperator(Tokens $tokens, int $startIndex, int $endIndex, int $objectOperator): void
    {
        $argumentsAnalyzer = new ArgumentsAnalyzer();

        $oldMethodSequence = [
            [T_VARIABLE, '$this'],
            [$objectOperator],
            [T_STRING],
        ];

        for ($index = $startIndex; $startIndex < $endIndex; ++$index) {
            $match = $tokens->findSequence($oldMethodSequence, $index);

            if (null === $match) {
                return;
            }

            [$thisIndex, , $index] = array_keys($match);

            if (!isset($this->methodMap[$tokens[$index]->getContent()])) {
                continue;
            }

            $openIndex = $tokens->getNextTokenOfKind($index, ['(']);
            $closeIndex = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $openIndex);
            $commaIndex = $tokens->getPrevMeaningfulToken($closeIndex);
            if ($tokens[$commaIndex]->equals(',')) {
                $tokens->removeTrailingWhitespace($commaIndex);
                $tokens->clearAt($commaIndex);
            }

            $arguments = $argumentsAnalyzer->getArguments($tokens, $openIndex, $closeIndex);
            $argumentsCnt = \count($arguments);

            $argumentsReplacements = ['expectException', $this->methodMap[$tokens[$index]->getContent()], 'expectExceptionCode'];

            $indent = $this->whitespacesConfig->getLineEnding().WhitespacesAnalyzer::detectIndent($tokens, $thisIndex);

            $isMultilineWhitespace = false;

            for ($cnt = $argumentsCnt - 1; $cnt >= 1; --$cnt) {
                $argStart = array_keys($arguments)[$cnt];
                $argBefore = $tokens->getPrevMeaningfulToken($argStart);

                if ('expectExceptionMessage' === $argumentsReplacements[$cnt]) {
                    $paramIndicatorIndex = $tokens->getNextMeaningfulToken($argBefore);
                    $afterParamIndicatorIndex = $tokens->getNextMeaningfulToken($paramIndicatorIndex);

                    if (
                        $tokens[$paramIndicatorIndex]->equals([T_STRING, 'null'], false)
                        && $tokens[$afterParamIndicatorIndex]->equals(')')
                    ) {
                        if ($tokens[$argBefore + 1]->isWhitespace()) {
                            $tokens->clearTokenAndMergeSurroundingWhitespace($argBefore + 1);
                        }
                        $tokens->clearTokenAndMergeSurroundingWhitespace($argBefore);
                        $tokens->clearTokenAndMergeSurroundingWhitespace($paramIndicatorIndex);

                        continue;
                    }
                }

                $isMultilineWhitespace = $isMultilineWhitespace || ($tokens[$argStart]->isWhitespace() && !$tokens[$argStart]->isWhitespace(" \t"));
                $tokensOverrideArgStart = [
                    new Token([T_WHITESPACE, $indent]),
                    new Token([T_VARIABLE, '$this']),
                    new Token([T_OBJECT_OPERATOR, '->']),
                    new Token([T_STRING, $argumentsReplacements[$cnt]]),
                    new Token('('),
                ];
                $tokensOverrideArgBefore = [
                    new Token(')'),
                    new Token(';'),
                ];

                if ($isMultilineWhitespace) {
                    $tokensOverrideArgStart[] = new Token([T_WHITESPACE, $indent.$this->whitespacesConfig->getIndent()]);
                    array_unshift($tokensOverrideArgBefore, new Token([T_WHITESPACE, $indent]));
                }

                if ($tokens[$argStart]->isWhitespace()) {
                    $tokens->overrideRange($argStart, $argStart, $tokensOverrideArgStart);
                } else {
                    $tokens->insertAt($argStart, $tokensOverrideArgStart);
                }

                $tokens->overrideRange($argBefore, $argBefore, $tokensOverrideArgBefore);
            }

            $methodName = 'expectException';
            if ('expectExceptionMessageRegExp' === $tokens[$index]->getContent()) {
                $methodName = $this->methodMap[$tokens[$index]->getContent()];
            }
            $tokens[$index] = new Token([T_STRING, $methodName]);
        }
    }
}
