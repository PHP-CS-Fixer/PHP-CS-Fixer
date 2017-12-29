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
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolver;
use PhpCsFixer\FixerConfiguration\FixerOptionBuilder;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @author SpacePossum
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
final class PhpUnitDedicateAssertFixer extends AbstractFixer implements ConfigurationDefinitionFixerInterface
{
    private static $fixMap = [
        'array_key_exists' => ['assertArrayNotHasKey', 'assertArrayHasKey'],
        'empty' => ['assertNotEmpty', 'assertEmpty'],
        'file_exists' => ['assertFileNotExists', 'assertFileExists'],
        'is_array' => true,
        'is_bool' => true,
        'is_callable' => true,
        'is_dir' => ['assertDirectoryNotExists', 'assertDirectoryExists'],
        'is_double' => true,
        'is_float' => true,
        'is_infinite' => ['assertFinite', 'assertInfinite'],
        'is_int' => true,
        'is_integer' => true,
        'is_long' => true,
        'is_nan' => [false, 'assertNan'],
        'is_null' => ['assertNotNull', 'assertNull'],
        'is_numeric' => true,
        'is_object' => true,
        'is_readable' => ['assertNotIsReadable', 'assertIsReadable'],
        'is_real' => true,
        'is_resource' => true,
        'is_scalar' => true,
        'is_string' => true,
        'is_writable' => ['assertNotIsWritable', 'assertIsWritable'],
    ];

    /**
     * @var string[]
     */
    private $functions = [];

    /**
     * {@inheritdoc}
     */
    public function configure(array $configuration)
    {
        parent::configure($configuration);

        // assertions added in 3.0: assertArrayNotHasKey assertArrayHasKey assertFileNotExists assertFileExists assertNotNull, assertNull
        $this->functions = [
            'array_key_exists',
            'file_exists',
            'is_null',
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
                'is_resource',
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
    public function getDefinition()
    {
        return new FixerDefinition(
            'PHPUnit assertions like "assertInternalType", "assertFileExists", should be used over "assertTrue".',
            [
                new CodeSample(
                    '<?php
$this->assertTrue(is_float( $a), "my message");
$this->assertTrue(is_nan($a));
'
                ),
                new CodeSample(
                    '<?php
$this->assertTrue(is_dir($a));
$this->assertTrue(is_writable($a));
$this->assertTrue(is_readable($a));
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
     */
    public function getPriority()
    {
        // should be run after the PhpUnitConstructFixer.
        return -15;
    }

    /**
     * {@inheritdoc}
     */
    protected function applyFix(\SplFileInfo $file, Tokens $tokens)
    {
        static $searchSequence = [
            [T_VARIABLE, '$this'],
            [T_OBJECT_OPERATOR, '->'],
            [T_STRING],
        ];

        $index = 1;
        $candidate = $tokens->findSequence($searchSequence, $index);
        while (null !== $candidate) {
            end($candidate);
            $index = $this->getAssertCandidate($tokens, key($candidate));
            if (is_array($index)) {
                $index = $this->fixAssert($tokens, $index);
            }

            ++$index;
            $candidate = $tokens->findSequence($searchSequence, $index);
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function createConfigurationDefinition()
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
                ->setDefault(PhpUnitTargetVersion::VERSION_5_0) // @TODO 3.x: change to `VERSION_NEWEST`
                ->getOption(),
        ]);
    }

    /**
     * @param Tokens $tokens
     * @param int    $assertCallIndex Token index of assert method call
     *
     * @return int|int[] indexes of assert call, test call and positive flag, or last index checked
     */
    private function getAssertCandidate(Tokens $tokens, $assertCallIndex)
    {
        $content = strtolower($tokens[$assertCallIndex]->getContent());
        if ('asserttrue' === $content) {
            $isPositive = 1;
        } elseif ('assertfalse' === $content) {
            $isPositive = 0;
        } else {
            return $assertCallIndex;
        }

        // test candidate for simple calls like: ([\]+'some fixable call'(...))
        $assertCallOpenIndex = $tokens->getNextMeaningfulToken($assertCallIndex);
        if (!$tokens[$assertCallOpenIndex]->equals('(')) {
            return $assertCallIndex;
        }

        $testDefaultNamespaceTokenIndex = false;
        $testIndex = $tokens->getNextMeaningfulToken($assertCallOpenIndex);

        if (!$tokens[$testIndex]->isGivenKind([T_EMPTY, T_STRING])) {
            if (!$tokens[$testIndex]->isGivenKind(T_NS_SEPARATOR)) {
                return $testIndex;
            }

            $testDefaultNamespaceTokenIndex = $testIndex;
            $testIndex = $tokens->getNextMeaningfulToken($testIndex);
        }

        $testOpenIndex = $tokens->getNextMeaningfulToken($testIndex);
        if (!$tokens[$testOpenIndex]->equals('(')) {
            return $testOpenIndex;
        }

        $testCloseIndex = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $testOpenIndex);

        $assertCallCloseIndex = $tokens->getNextMeaningfulToken($testCloseIndex);
        if (!$tokens[$assertCallCloseIndex]->equalsAny([')', ','])) {
            return $assertCallCloseIndex;
        }

        return [
            $isPositive,
            $assertCallIndex,
            $assertCallOpenIndex,
            $testDefaultNamespaceTokenIndex,
            $testIndex,
            $testOpenIndex,
            $testCloseIndex,
            $assertCallCloseIndex,
        ];
    }

    /**
     * @param Tokens $tokens
     * @param array  $assertIndexes
     *
     * @return int index up till processed, number of tokens added
     */
    private function fixAssert(Tokens $tokens, array $assertIndexes)
    {
        list(
            $isPositive,
            $assertCallIndex,
            ,
            $testDefaultNamespaceTokenIndex,
            $testIndex,
            $testOpenIndex,
            $testCloseIndex,
            $assertCallCloseIndex
        ) = $assertIndexes;

        $content = strtolower($tokens[$testIndex]->getContent());
        if (!in_array($content, $this->functions, true)) {
            return $assertCallCloseIndex;
        }

        if (is_array(self::$fixMap[$content])) {
            if (false !== self::$fixMap[$content][$isPositive]) {
                $tokens[$assertCallIndex] = new Token([T_STRING, self::$fixMap[$content][$isPositive]]);
                $this->removeFunctionCall($tokens, $testDefaultNamespaceTokenIndex, $testIndex, $testOpenIndex, $testCloseIndex);
            }

            return $assertCallCloseIndex;
        }

        $type = substr($content, 3);

        $tokens[$assertCallIndex] = new Token([T_STRING, $isPositive ? 'assertInternalType' : 'assertNotInternalType']);
        $tokens[$testIndex] = new Token([T_CONSTANT_ENCAPSED_STRING, "'".$type."'"]);
        $tokens[$testOpenIndex] = new Token(',');

        $tokens->clearTokenAndMergeSurroundingWhitespace($testCloseIndex);

        if (!$tokens[$testOpenIndex + 1]->isWhitespace()) {
            $tokens->insertAt($testOpenIndex + 1, new Token([T_WHITESPACE, ' ']));
        }

        if (false !== $testDefaultNamespaceTokenIndex) {
            $tokens->clearTokenAndMergeSurroundingWhitespace($testDefaultNamespaceTokenIndex);
        }

        return $assertCallCloseIndex;
    }

    /**
     * @param Tokens    $tokens
     * @param false|int $callNSIndex
     * @param int       $callIndex
     * @param int       $openIndex
     * @param int       $closeIndex
     */
    private function removeFunctionCall(Tokens $tokens, $callNSIndex, $callIndex, $openIndex, $closeIndex)
    {
        $tokens->clearTokenAndMergeSurroundingWhitespace($callIndex);
        if (false !== $callNSIndex) {
            $tokens->clearTokenAndMergeSurroundingWhitespace($callNSIndex);
        }

        $tokens->clearTokenAndMergeSurroundingWhitespace($openIndex);
        $tokens->clearTokenAndMergeSurroundingWhitespace($closeIndex);
    }
}
