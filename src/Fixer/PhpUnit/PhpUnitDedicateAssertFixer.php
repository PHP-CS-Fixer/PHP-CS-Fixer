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
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolver;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolverInterface;
use PhpCsFixer\FixerConfiguration\FixerOptionBuilder;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Analyzer\ArgumentsAnalyzer;
use PhpCsFixer\Tokenizer\Analyzer\FunctionsAnalyzer;
use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
final class PhpUnitDedicateAssertFixer extends AbstractPhpUnitFixer implements ConfigurableFixerInterface
{
    /**
     * @var array<string, array<string, bool|int|string>|true>
     */
    private static array $fixMap = [
        'array_key_exists' => [
            'positive' => 'assertArrayHasKey',
            'negative' => 'assertArrayNotHasKey',
            'argument_count' => 2,
        ],
        'empty' => [
            'positive' => 'assertEmpty',
            'negative' => 'assertNotEmpty',
        ],
        'file_exists' => [
            'positive' => 'assertFileExists',
            'negative' => 'assertFileNotExists',
        ],
        'is_array' => true,
        'is_bool' => true,
        'is_callable' => true,
        'is_dir' => [
            'positive' => 'assertDirectoryExists',
            'negative' => 'assertDirectoryNotExists',
        ],
        'is_double' => true,
        'is_float' => true,
        'is_infinite' => [
            'positive' => 'assertInfinite',
            'negative' => 'assertFinite',
        ],
        'is_int' => true,
        'is_integer' => true,
        'is_long' => true,
        'is_nan' => [
            'positive' => 'assertNan',
            'negative' => false,
        ],
        'is_null' => [
            'positive' => 'assertNull',
            'negative' => 'assertNotNull',
        ],
        'is_numeric' => true,
        'is_object' => true,
        'is_readable' => [
            'positive' => 'assertIsReadable',
            'negative' => 'assertNotIsReadable',
        ],
        'is_real' => true,
        'is_resource' => true,
        'is_scalar' => true,
        'is_string' => true,
        'is_writable' => [
            'positive' => 'assertIsWritable',
            'negative' => 'assertNotIsWritable',
        ],
        'str_contains' => [ // since 7.5
            'positive' => 'assertStringContainsString',
            'negative' => 'assertStringNotContainsString',
            'argument_count' => 2,
            'swap_arguments' => true,
        ],
        'str_ends_with' => [ // since 3.4
            'positive' => 'assertStringEndsWith',
            'negative' => 'assertStringEndsNotWith',
            'argument_count' => 2,
            'swap_arguments' => true,
        ],
        'str_starts_with' => [ // since 3.4
            'positive' => 'assertStringStartsWith',
            'negative' => 'assertStringStartsNotWith',
            'argument_count' => 2,
            'swap_arguments' => true,
        ],
    ];

    /**
     * @var string[]
     */
    private array $functions = [];

    /**
     * {@inheritdoc}
     */
    public function configure(array $configuration): void
    {
        parent::configure($configuration);

        // assertions added in 3.0: assertArrayNotHasKey assertArrayHasKey assertFileNotExists assertFileExists assertNotNull, assertNull
        $this->functions = [
            'array_key_exists',
            'file_exists',
            'is_null',
            'str_ends_with',
            'str_starts_with',
        ];

        if (PhpUnitTargetVersion::fulfills($this->configuration['target'], PhpUnitTargetVersion::VERSION_3_5)) {
            // assertions added in 3.5: assertInternalType assertNotEmpty assertEmpty
            $this->functions = array_merge($this->functions, [
                'empty',
                'is_array',
                'is_bool',
                'is_boolean',
                'is_callable',
                'is_double',
                'is_float',
                'is_int',
                'is_integer',
                'is_long',
                'is_numeric',
                'is_object',
                'is_real',
                'is_scalar',
                'is_string',
            ]);
        }

        if (PhpUnitTargetVersion::fulfills($this->configuration['target'], PhpUnitTargetVersion::VERSION_5_0)) {
            // assertions added in 5.0: assertFinite assertInfinite assertNan
            $this->functions = array_merge($this->functions, [
                'is_infinite',
                'is_nan',
            ]);
        }

        if (PhpUnitTargetVersion::fulfills($this->configuration['target'], PhpUnitTargetVersion::VERSION_5_6)) {
            // assertions added in 5.6: assertDirectoryExists assertDirectoryNotExists assertIsReadable assertNotIsReadable assertIsWritable assertNotIsWritable
            $this->functions = array_merge($this->functions, [
                'is_dir',
                'is_readable',
                'is_writable',
            ]);
        }

        if (PhpUnitTargetVersion::fulfills($this->configuration['target'], PhpUnitTargetVersion::VERSION_7_5)) {
            $this->functions = array_merge($this->functions, [
                'str_contains',
            ]);
        }
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
    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(
            'PHPUnit assertions like `assertInternalType`, `assertFileExists`, should be used over `assertTrue`.',
            [
                new CodeSample(
                    '<?php
final class MyTest extends \PHPUnit_Framework_TestCase
{
    public function testSomeTest()
    {
        $this->assertTrue(is_float( $a), "my message");
        $this->assertTrue(is_nan($a));
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
        $this->assertTrue(is_dir($a));
        $this->assertTrue(is_writable($a));
        $this->assertTrue(is_readable($a));
    }
}
',
                    ['target' => PhpUnitTargetVersion::VERSION_5_6]
                ),
            ],
            null,
            'Fixer could be risky if one is overriding PHPUnit\'s native methods.'
        );
    }

    /**
     * {@inheritdoc}
     *
     * Must run before NoUnusedImportsFixer, PhpUnitDedicateAssertInternalTypeFixer.
     * Must run after ModernizeStrposFixer, NoAliasFunctionsFixer, PhpUnitConstructFixer.
     */
    public function getPriority(): int
    {
        return -9;
    }

    /**
     * {@inheritdoc}
     */
    protected function applyPhpUnitClassFix(Tokens $tokens, int $startIndex, int $endIndex): void
    {
        $argumentsAnalyzer = new ArgumentsAnalyzer();

        foreach ($this->getPreviousAssertCall($tokens, $startIndex, $endIndex) as $assertCall) {
            // test and fix for assertTrue/False to dedicated asserts
            if ('asserttrue' === $assertCall['loweredName'] || 'assertfalse' === $assertCall['loweredName']) {
                $this->fixAssertTrueFalse($tokens, $argumentsAnalyzer, $assertCall);

                continue;
            }

            if (
                'assertsame' === $assertCall['loweredName']
                || 'assertnotsame' === $assertCall['loweredName']
                || 'assertequals' === $assertCall['loweredName']
                || 'assertnotequals' === $assertCall['loweredName']
            ) {
                $this->fixAssertSameEquals($tokens, $assertCall);

                continue;
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function createConfigurationDefinition(): FixerConfigurationResolverInterface
    {
        return new FixerConfigurationResolver([
            (new FixerOptionBuilder('target', 'Target version of PHPUnit.'))
                ->setAllowedTypes(['string'])
                ->setAllowedValues([
                    PhpUnitTargetVersion::VERSION_3_0,
                    PhpUnitTargetVersion::VERSION_3_5,
                    PhpUnitTargetVersion::VERSION_5_0,
                    PhpUnitTargetVersion::VERSION_5_6,
                    PhpUnitTargetVersion::VERSION_NEWEST,
                ])
                ->setDefault(PhpUnitTargetVersion::VERSION_NEWEST)
                ->getOption(),
        ]);
    }

    /**
     * @param array{
     *     index: int,
     *     loweredName: string,
     *     openBraceIndex: int,
     *     closeBraceIndex: int,
     * } $assertCall
     */
    private function fixAssertTrueFalse(Tokens $tokens, ArgumentsAnalyzer $argumentsAnalyzer, array $assertCall): void
    {
        $testDefaultNamespaceTokenIndex = null;
        $testIndex = $tokens->getNextMeaningfulToken($assertCall['openBraceIndex']);

        if (!$tokens[$testIndex]->isGivenKind([T_EMPTY, T_STRING])) {
            if ($this->fixAssertTrueFalseInstanceof($tokens, $assertCall, $testIndex)) {
                return;
            }

            if (!$tokens[$testIndex]->isGivenKind(T_NS_SEPARATOR)) {
                return;
            }

            $testDefaultNamespaceTokenIndex = $testIndex;
            $testIndex = $tokens->getNextMeaningfulToken($testIndex);
        }

        $testOpenIndex = $tokens->getNextMeaningfulToken($testIndex);

        if (!$tokens[$testOpenIndex]->equals('(')) {
            return;
        }

        $testCloseIndex = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $testOpenIndex);
        $assertCallCloseIndex = $tokens->getNextMeaningfulToken($testCloseIndex);

        if (!$tokens[$assertCallCloseIndex]->equalsAny([')', ','])) {
            return;
        }

        $content = strtolower($tokens[$testIndex]->getContent());

        if (!\in_array($content, $this->functions, true)) {
            return;
        }

        $arguments = $argumentsAnalyzer->getArguments($tokens, $testOpenIndex, $testCloseIndex);
        $isPositive = 'asserttrue' === $assertCall['loweredName'];

        if (\is_array(self::$fixMap[$content])) {
            $expectedCount = self::$fixMap[$content]['argument_count'] ?? 1;

            if ($expectedCount !== \count($arguments)) {
                return;
            }

            $isPositive = $isPositive ? 'positive' : 'negative';

            if (false === self::$fixMap[$content][$isPositive]) {
                return;
            }

            $tokens[$assertCall['index']] = new Token([T_STRING, self::$fixMap[$content][$isPositive]]);
            $this->removeFunctionCall($tokens, $testDefaultNamespaceTokenIndex, $testIndex, $testOpenIndex, $testCloseIndex);

            if (self::$fixMap[$content]['swap_arguments'] ?? false) {
                if (2 !== $expectedCount) {
                    throw new \RuntimeException('Can only swap two arguments, please update map or logic.');
                }

                $this->swapArguments($tokens, $arguments);
            }

            return;
        }

        if (1 !== \count($arguments)) {
            return;
        }

        $type = substr($content, 3);

        $tokens[$assertCall['index']] = new Token([T_STRING, $isPositive ? 'assertInternalType' : 'assertNotInternalType']);
        $tokens[$testIndex] = new Token([T_CONSTANT_ENCAPSED_STRING, "'".$type."'"]);
        $tokens[$testOpenIndex] = new Token(',');

        $tokens->clearTokenAndMergeSurroundingWhitespace($testCloseIndex);
        $commaIndex = $tokens->getPrevMeaningfulToken($testCloseIndex);

        if ($tokens[$commaIndex]->equals(',')) {
            $tokens->removeTrailingWhitespace($commaIndex);
            $tokens->clearAt($commaIndex);
        }

        if (!$tokens[$testOpenIndex + 1]->isWhitespace()) {
            $tokens->insertAt($testOpenIndex + 1, new Token([T_WHITESPACE, ' ']));
        }

        if (null !== $testDefaultNamespaceTokenIndex) {
            $tokens->clearTokenAndMergeSurroundingWhitespace($testDefaultNamespaceTokenIndex);
        }
    }

    /**
     * @param array{
     *     index: int,
     *     loweredName: string,
     *     openBraceIndex: int,
     *     closeBraceIndex: int,
     * } $assertCall
     */
    private function fixAssertTrueFalseInstanceof(Tokens $tokens, array $assertCall, int $testIndex): bool
    {
        if ($tokens[$testIndex]->equals('!')) {
            $variableIndex = $tokens->getNextMeaningfulToken($testIndex);
            $positive = false;
        } else {
            $variableIndex = $testIndex;
            $positive = true;
        }

        if (!$tokens[$variableIndex]->isGivenKind(T_VARIABLE)) {
            return false;
        }

        $instanceOfIndex = $tokens->getNextMeaningfulToken($variableIndex);

        if (!$tokens[$instanceOfIndex]->isGivenKind(T_INSTANCEOF)) {
            return false;
        }

        $classEndIndex = $instanceOfIndex;
        $classPartTokens = [];

        do {
            $classEndIndex = $tokens->getNextMeaningfulToken($classEndIndex);
            $classPartTokens[] = $tokens[$classEndIndex];
        } while ($tokens[$classEndIndex]->isGivenKind([T_STRING, T_NS_SEPARATOR, T_VARIABLE]));

        if ($tokens[$classEndIndex]->equalsAny([',', ')'])) { // do the fixing
            array_pop($classPartTokens);
            $isInstanceOfVar = reset($classPartTokens)->isGivenKind(T_VARIABLE);
            $insertIndex = $testIndex - 1;
            $newTokens = [];

            foreach ($classPartTokens as $token) {
                $newTokens[++$insertIndex] = clone $token;
            }

            if (!$isInstanceOfVar) {
                $newTokens[++$insertIndex] = new Token([T_DOUBLE_COLON, '::']);
                $newTokens[++$insertIndex] = new Token([CT::T_CLASS_CONSTANT, 'class']);
            }

            $newTokens[++$insertIndex] = new Token(',');
            $newTokens[++$insertIndex] = new Token([T_WHITESPACE, ' ']);
            $newTokens[++$insertIndex] = clone $tokens[$variableIndex];

            for ($i = $classEndIndex - 1; $i >= $testIndex; --$i) {
                if (!$tokens[$i]->isComment()) {
                    $tokens->clearTokenAndMergeSurroundingWhitespace($i);
                }
            }

            $tokens->insertSlices($newTokens);
            $tokens[$assertCall['index']] = new Token([T_STRING, $positive ? 'assertInstanceOf' : 'assertNotInstanceOf']);
        }

        return true;
    }

    /**
     * @param array{
     *     index: int,
     *     loweredName: string,
     *     openBraceIndex: int,
     *     closeBraceIndex: int,
     * } $assertCall
     */
    private function fixAssertSameEquals(Tokens $tokens, array $assertCall): void
    {
        // @ $this->/self::assertEquals/Same([$nextIndex])
        $expectedIndex = $tokens->getNextMeaningfulToken($assertCall['openBraceIndex']);

        // do not fix
        // let $a = [1,2]; $b = "2";
        // "$this->assertEquals("2", count($a)); $this->assertEquals($b, count($a)); $this->assertEquals(2.1, count($a));"

        if ($tokens[$expectedIndex]->isGivenKind([T_VARIABLE])) {
            if (!$tokens[$tokens->getNextMeaningfulToken($expectedIndex)]->equals(',')) {
                return;
            }
        } elseif (!$tokens[$expectedIndex]->isGivenKind([T_LNUMBER, T_VARIABLE])) {
            return;
        }

        // @ $this->/self::assertEquals/Same([$nextIndex,$commaIndex])
        $commaIndex = $tokens->getNextMeaningfulToken($expectedIndex);

        if (!$tokens[$commaIndex]->equals(',')) {
            return;
        }

        // @ $this->/self::assertEquals/Same([$nextIndex,$commaIndex,$countCallIndex])
        $countCallIndex = $tokens->getNextMeaningfulToken($commaIndex);

        if ($tokens[$countCallIndex]->isGivenKind(T_NS_SEPARATOR)) {
            $defaultNamespaceTokenIndex = $countCallIndex;
            $countCallIndex = $tokens->getNextMeaningfulToken($countCallIndex);
        } else {
            $defaultNamespaceTokenIndex = null;
        }

        if (!$tokens[$countCallIndex]->isGivenKind(T_STRING)) {
            return;
        }

        $lowerContent = strtolower($tokens[$countCallIndex]->getContent());

        if ('count' !== $lowerContent && 'sizeof' !== $lowerContent) {
            return; // not a call to "count" or "sizeOf"
        }

        // @ $this->/self::assertEquals/Same([$nextIndex,$commaIndex,[$defaultNamespaceTokenIndex,]$countCallIndex,$countCallOpenBraceIndex])
        $countCallOpenBraceIndex = $tokens->getNextMeaningfulToken($countCallIndex);

        if (!$tokens[$countCallOpenBraceIndex]->equals('(')) {
            return;
        }

        $countCallCloseBraceIndex = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $countCallOpenBraceIndex);
        $afterCountCallCloseBraceIndex = $tokens->getNextMeaningfulToken($countCallCloseBraceIndex);

        if (!$tokens[$afterCountCallCloseBraceIndex]->equalsAny([')', ','])) {
            return;
        }

        $this->removeFunctionCall(
            $tokens,
            $defaultNamespaceTokenIndex,
            $countCallIndex,
            $countCallOpenBraceIndex,
            $countCallCloseBraceIndex
        );

        $tokens[$assertCall['index']] = new Token([
            T_STRING,
            false === strpos($assertCall['loweredName'], 'not', 6) ? 'assertCount' : 'assertNotCount',
        ]);
    }

    /**
     * @return iterable<array{
     *     index: int,
     *     loweredName: string,
     *     openBraceIndex: int,
     *     closeBraceIndex: int,
     * }>
     */
    private function getPreviousAssertCall(Tokens $tokens, int $startIndex, int $endIndex): iterable
    {
        $functionsAnalyzer = new FunctionsAnalyzer();

        for ($index = $endIndex; $index > $startIndex; --$index) {
            $index = $tokens->getPrevTokenOfKind($index, [[T_STRING]]);

            if (null === $index) {
                return;
            }

            // test if "assert" something call
            $loweredContent = strtolower($tokens[$index]->getContent());

            if (!str_starts_with($loweredContent, 'assert')) {
                continue;
            }

            // test candidate for simple calls like: ([\]+'some fixable call'(...))
            $openBraceIndex = $tokens->getNextMeaningfulToken($index);

            if (!$tokens[$openBraceIndex]->equals('(')) {
                continue;
            }

            if (!$functionsAnalyzer->isTheSameClassCall($tokens, $index)) {
                continue;
            }

            yield [
                'index' => $index,
                'loweredName' => $loweredContent,
                'openBraceIndex' => $openBraceIndex,
                'closeBraceIndex' => $tokens->findBlockEnd(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $openBraceIndex),
            ];
        }
    }

    private function removeFunctionCall(Tokens $tokens, ?int $callNSIndex, int $callIndex, int $openIndex, int $closeIndex): void
    {
        $tokens->clearTokenAndMergeSurroundingWhitespace($callIndex);

        if (null !== $callNSIndex) {
            $tokens->clearTokenAndMergeSurroundingWhitespace($callNSIndex);
        }

        $tokens->clearTokenAndMergeSurroundingWhitespace($openIndex);
        $commaIndex = $tokens->getPrevMeaningfulToken($closeIndex);

        if ($tokens[$commaIndex]->equals(',')) {
            $tokens->removeTrailingWhitespace($commaIndex);
            $tokens->clearAt($commaIndex);
        }

        $tokens->clearTokenAndMergeSurroundingWhitespace($closeIndex);
    }

    /**
     * @param array<int, int> $argumentsIndices
     */
    private function swapArguments(Tokens $tokens, array $argumentsIndices): void
    {
        [$firstArgumentIndex, $secondArgumentIndex] = array_keys($argumentsIndices);

        $firstArgumentEndIndex = $argumentsIndices[$firstArgumentIndex];
        $secondArgumentEndIndex = $argumentsIndices[$secondArgumentIndex];

        $firstClone = $this->cloneAndClearTokens($tokens, $firstArgumentIndex, $firstArgumentEndIndex);
        $secondClone = $this->cloneAndClearTokens($tokens, $secondArgumentIndex, $secondArgumentEndIndex);

        if (!$firstClone[0]->isWhitespace()) {
            array_unshift($firstClone, new Token([T_WHITESPACE, ' ']));
        }

        $tokens->insertAt($secondArgumentIndex, $firstClone);

        if ($secondClone[0]->isWhitespace()) {
            array_shift($secondClone);
        }

        $tokens->insertAt($firstArgumentIndex, $secondClone);
    }

    /**
     * @return list<Token>
     */
    private function cloneAndClearTokens(Tokens $tokens, int $start, int $end): array
    {
        $clone = [];

        for ($i = $start; $i <= $end; ++$i) {
            if ('' === $tokens[$i]->getContent()) {
                continue;
            }

            $clone[] = clone $tokens[$i];
            $tokens->clearAt($i);
        }

        return $clone;
    }
}
