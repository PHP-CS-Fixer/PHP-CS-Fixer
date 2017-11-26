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
 * @author Michał Adamski <michal.adamski@gmail.com>
 */
final class PhpUnitShortWillReturnFixer extends AbstractFixer implements ConfigurationDefinitionFixerInterface
{
    const METHOD_RETURN_SELF = 'returnSelf';
    const METHOD_RETURN_VALUE = 'returnValue';
    const METHOD_RETURN_ARGUMENT = 'returnArgument';
    const METHOD_RETURN_CALLBACK = 'returnCallback';
    const METHOD_RETURN_VALUE_MAP = 'returnValueMap';

    const RETURN_METHODS_MAP = [
        self::METHOD_RETURN_VALUE => 'willReturn',
        self::METHOD_RETURN_SELF => 'willReturnSelf',
        self::METHOD_RETURN_VALUE_MAP => 'willReturnMap',
        self::METHOD_RETURN_ARGUMENT => 'willReturnArgument',
        self::METHOD_RETURN_CALLBACK => 'willReturnCallback',
    ];

    /**
     * {@inheritdoc}
     */
    public function getDefinition()
    {
        return new FixerDefinition(
            'Usage of `->will($this->returnValue(..))` statements must be replaced by it\'s shorter equivalent `->willReturn(...)`.',
            [
                new CodeSample('<?php
final class MyTest extends \PHPUnit_Framework_TestCase
{
    public function testSomeTest()
    {
        $someMock = $this->createMock(Some::class);
        $someMock->method(\'some\')->will($this->returnSelf());
        $someMock->method(\'some\')->will($this->returnValue(\'example\'));
        $someMock->method(\'some\')->will($this->returnArgument(2));
        $someMock->method(\'some\')->will($this->returnCallback(\'str_rot13\'));
        $someMock->method(\'some\')->will($this->returnValueMap([\'a\',\'b\',\'c\']));
    }
}
'),
            ],
            null,
            'Risky when PHPUnit classes are overridden or not accessible, or when project has PHPUnit incompatibilities.'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function isCandidate(Tokens $tokens)
    {
        return !empty($tokens->findGivenKind(array_keys(static::RETURN_METHODS_MAP)));
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
    protected function createConfigurationDefinition()
    {
        return new FixerConfigurationResolver([
            (new FixerOptionBuilder(static::METHOD_RETURN_VALUE, 'Fix `returnValue` occurrence'))
                ->setAllowedTypes(['bool'])
                ->setAllowedValues([false, true])
                ->setDefault(true)
                ->getOption(),
            (new FixerOptionBuilder(static::METHOD_RETURN_SELF, 'Fix `returnSelf` occurrence'))
                ->setAllowedTypes(['bool'])
                ->setAllowedValues([false, true])
                ->setDefault(true)
                ->getOption(),
            (new FixerOptionBuilder(static::METHOD_RETURN_ARGUMENT, 'Fix `returnArgument` occurrence'))
                ->setAllowedTypes(['bool'])
                ->setAllowedValues([false, true])
                ->setDefault(true)
                ->getOption(),
            (new FixerOptionBuilder(static::METHOD_RETURN_CALLBACK, 'Fix `returnCallback` occurrence'))
                ->setAllowedTypes(['bool'])
                ->setAllowedValues([false, true])
                ->setDefault(true)
                ->getOption(),
            (new FixerOptionBuilder(static::METHOD_RETURN_VALUE_MAP, 'Fix `returnValueMap` occurrence'))
                ->setAllowedTypes(['bool'])
                ->setAllowedValues([false, true])
                ->setDefault(true)
                ->getOption(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    protected function applyFix(\SplFileInfo $file, Tokens $tokens)
    {
        foreach ($this->getSequences() as $method => $sequence) {
            $this->fixSequence($tokens, $sequence, $method);
        }

        $tokens->clearEmptyTokens();

        return $tokens->generateCode();
    }

    /**
     * @return array
     */
    private function getSequences()
    {
        $sequences = [];

        foreach (static::RETURN_METHODS_MAP as $longerOccurrence => $shorterOccurrence) {
            if (!$this->configuration[$longerOccurrence]) {
                continue;
            }

            $sequences[$shorterOccurrence] = [
                [T_OBJECT_OPERATOR, '->'],
                [T_STRING, 'will'],
                '(',
                [T_VARIABLE, '$this'],
                [T_OBJECT_OPERATOR, '->'],
                [T_STRING, $longerOccurrence],
                '(',
            ];
        }

        return $sequences;
    }

    /**
     * @param Tokens $tokens
     * @param array  $sequence
     * @param string $method
     */
    private function fixSequence(Tokens $tokens, $sequence, $method)
    {
        $occurrence = $tokens->findSequence($sequence);
        while (null !== $occurrence) {
            $index = $this->fixOccurrence($tokens, $occurrence, $method);
            $occurrence = $tokens->findSequence($sequence, ++$index);
        }
    }

    /**
     * @param Tokens $tokens
     * @param array  $occurrence
     * @param string $method
     *
     * @return int last closing brace index
     */
    private function fixOccurrence(Tokens $tokens, array $occurrence, $method)
    {
        $willReturnToken = new Token([T_STRING, $method]);
        $sequenceIndexes = array_keys($occurrence);
        $openBraceIndex = end($sequenceIndexes);
        $closingBraceIndex = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $openBraceIndex);

        $tokens->clearRange($sequenceIndexes[2], $sequenceIndexes[5]);
        $tokens->offsetSet($sequenceIndexes[1], $willReturnToken);
        $tokens->clearAt($closingBraceIndex);

        return $closingBraceIndex;
    }
}
