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
use PhpCsFixer\FixerConfiguration\AllowedValueSubset;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolver;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolverInterface;
use PhpCsFixer\FixerConfiguration\FixerOptionBuilder;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Analyzer\FunctionsAnalyzer;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
final class PhpUnitConstructFixer extends AbstractPhpUnitFixer implements ConfigurableFixerInterface
{
    /**
     * @var array<string,string>
     */
    private static array $assertionFixers = [
        'assertSame' => 'fixAssertPositive',
        'assertEquals' => 'fixAssertPositive',
        'assertNotEquals' => 'fixAssertNegative',
        'assertNotSame' => 'fixAssertNegative',
    ];

    public function isRisky(): bool
    {
        return true;
    }

    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(
            'PHPUnit assertion method calls like `->assertSame(true, $foo)` should be written with dedicated method like `->assertTrue($foo)`.',
            [
                new CodeSample(
                    '<?php
final class FooTest extends \PHPUnit_Framework_TestCase {
    public function testSomething() {
        $this->assertEquals(false, $b);
        $this->assertSame(true, $a);
        $this->assertNotEquals(null, $c);
        $this->assertNotSame(null, $d);
    }
}
'
                ),
                new CodeSample(
                    '<?php
final class FooTest extends \PHPUnit_Framework_TestCase {
    public function testSomething() {
        $this->assertEquals(false, $b);
        $this->assertSame(true, $a);
        $this->assertNotEquals(null, $c);
        $this->assertNotSame(null, $d);
    }
}
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
     *
     * Must run before PhpUnitDedicateAssertFixer.
     */
    public function getPriority(): int
    {
        return -8;
    }

    protected function applyPhpUnitClassFix(Tokens $tokens, int $startIndex, int $endIndex): void
    {
        // no assertions to be fixed - fast return
        if (empty($this->configuration['assertions'])) {
            return;
        }

        foreach ($this->configuration['assertions'] as $assertionMethod) {
            $assertionFixer = self::$assertionFixers[$assertionMethod];

            for ($index = $startIndex; $index < $endIndex; ++$index) {
                $index = $this->{$assertionFixer}($tokens, $index, $assertionMethod);

                if (null === $index) {
                    break;
                }
            }
        }
    }

    protected function createConfigurationDefinition(): FixerConfigurationResolverInterface
    {
        return new FixerConfigurationResolver([
            (new FixerOptionBuilder('assertions', 'List of assertion methods to fix.'))
                ->setAllowedTypes(['array'])
                ->setAllowedValues([new AllowedValueSubset(array_keys(self::$assertionFixers))])
                ->setDefault([
                    'assertEquals',
                    'assertSame',
                    'assertNotEquals',
                    'assertNotSame',
                ])
                ->getOption(),
        ]);
    }

    private function fixAssertNegative(Tokens $tokens, int $index, string $method): ?int
    {
        static $map = [
            'false' => 'assertNotFalse',
            'null' => 'assertNotNull',
            'true' => 'assertNotTrue',
        ];

        return $this->fixAssert($map, $tokens, $index, $method);
    }

    private function fixAssertPositive(Tokens $tokens, int $index, string $method): ?int
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
     */
    private function fixAssert(array $map, Tokens $tokens, int $index, string $method): ?int
    {
        $functionsAnalyzer = new FunctionsAnalyzer();

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

        $sequenceIndices = array_keys($sequence);

        if (!$functionsAnalyzer->isTheSameClassCall($tokens, $sequenceIndices[0])) {
            return null;
        }

        $sequenceIndices[2] = $tokens->getNextMeaningfulToken($sequenceIndices[1]);
        $firstParameterToken = $tokens[$sequenceIndices[2]];

        if (!$firstParameterToken->isNativeConstant()) {
            return $sequenceIndices[2];
        }

        $sequenceIndices[3] = $tokens->getNextMeaningfulToken($sequenceIndices[2]);

        // return if first method argument is an expression, not value
        if (!$tokens[$sequenceIndices[3]]->equals(',')) {
            return $sequenceIndices[3];
        }

        $tokens[$sequenceIndices[0]] = new Token([T_STRING, $map[strtolower($firstParameterToken->getContent())]]);
        $tokens->clearRange($sequenceIndices[2], $tokens->getNextNonWhitespace($sequenceIndices[3]) - 1);

        return $sequenceIndices[3];
    }
}
