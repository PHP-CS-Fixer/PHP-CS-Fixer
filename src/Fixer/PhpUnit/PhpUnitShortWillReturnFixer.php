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
    /**
     * @internal
     */
    const METHOD_RETURN_SELF = 'returnSelf';

    /**
     * @internal
     */
    const METHOD_RETURN_VALUE = 'returnValue';

    /**
     * @internal
     */
    const METHOD_RETURN_ARGUMENT = 'returnArgument';

    /**
     * @internal
     */
    const METHOD_RETURN_CALLBACK = 'returnCallback';

    /**
     * @internal
     */
    const METHOD_RETURN_VALUE_MAP = 'returnValueMap';

    /**
     * @internal
     */
    const RETURN_METHODS_MAP = [
        self::METHOD_RETURN_VALUE => 'willReturn',
        self::METHOD_RETURN_SELF => 'willReturnSelf',
        self::METHOD_RETURN_VALUE_MAP => 'willReturnMap',
        self::METHOD_RETURN_ARGUMENT => 'willReturnArgument',
        self::METHOD_RETURN_CALLBACK => 'willReturnCallback',
    ];

    /**
     * @internal
     */
    const SEQUENCE = [
        [T_OBJECT_OPERATOR, '->'],
        [T_STRING, 'will'],
        '(',
        [T_VARIABLE, '$this'],
        [T_OBJECT_OPERATOR, '->'],
        [T_STRING],
        '(',
    ];

    /**
     * {@inheritdoc}
     */
    public function getDefinition()
    {
        return new FixerDefinition(
            'Usage of `->will($this->returnValue(..))` statements must be replaced by its shorter equivalent `->willReturn(...)`.',
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
                ->setDefault(true)
                ->getOption(),
            (new FixerOptionBuilder(static::METHOD_RETURN_SELF, 'Fix `returnSelf` occurrence'))
                ->setAllowedTypes(['bool'])
                ->setDefault(true)
                ->getOption(),
            (new FixerOptionBuilder(static::METHOD_RETURN_ARGUMENT, 'Fix `returnArgument` occurrence'))
                ->setAllowedTypes(['bool'])
                ->setDefault(true)
                ->getOption(),
            (new FixerOptionBuilder(static::METHOD_RETURN_CALLBACK, 'Fix `returnCallback` occurrence'))
                ->setAllowedTypes(['bool'])
                ->setDefault(true)
                ->getOption(),
            (new FixerOptionBuilder(static::METHOD_RETURN_VALUE_MAP, 'Fix `returnValueMap` occurrence'))
                ->setAllowedTypes(['bool'])
                ->setDefault(true)
                ->getOption(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    protected function applyFix(\SplFileInfo $file, Tokens $tokens)
    {
        $occurrence = $tokens->findSequence(static::SEQUENCE);
        while (null !== $occurrence) {
            $index = $this->fixOccurrence($tokens, $occurrence);
            $occurrence = $tokens->findSequence(static::SEQUENCE, ++$index);
        }
    }

    /**
     * @param Tokens $tokens
     * @param array  $occurrence
     *
     * @return int last closing brace index
     */
    private function fixOccurrence(Tokens $tokens, array $occurrence)
    {
        $sequenceIndexes = array_keys($occurrence);
        $openBraceIndex = $sequenceIndexes[2];
        $longOccurrenceIndex = $sequenceIndexes[5];
        $closingBraceIndex = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $openBraceIndex);
        $longOccurrenceMethod = $occurrence[$longOccurrenceIndex]->getContent();

        if (!$this->isSupportedMethod($longOccurrenceMethod)) {
            return $closingBraceIndex;
        }

        $willReturnToken = new Token([T_STRING, static::RETURN_METHODS_MAP[$longOccurrenceMethod]]);
        $tokens->clearRange($openBraceIndex, $longOccurrenceIndex);
        $tokens->clearTokenAndMergeSurroundingWhitespace($openBraceIndex);
        $tokens->offsetSet($sequenceIndexes[1], $willReturnToken);
        $tokens->clearAt($closingBraceIndex);
        if ($tokens->isEmptyAt($closingBraceIndex) && !$this->isPreviousTokenAComment($tokens, $closingBraceIndex)) {
            $tokens->removeLeadingWhitespace($closingBraceIndex);
            $tokens->removeTrailingWhitespace($closingBraceIndex);
        }

        return $closingBraceIndex;
    }

    /**
     * @param string $method
     *
     * @return bool
     */
    private function isSupportedMethod($method)
    {
        return in_array($method, array_keys(static::RETURN_METHODS_MAP), true) && $this->configuration[$method];
    }

    /**
     * @param Tokens $tokens
     * @param int    $closingBraceIndex
     *
     * @return bool
     */
    private function isPreviousTokenAComment(Tokens $tokens, $closingBraceIndex)
    {
        $prevMeaningfulToken = $tokens->getPrevMeaningfulToken($closingBraceIndex);
        $prevNonWhitespace = $tokens->getPrevNonWhitespace($closingBraceIndex);

        return $prevMeaningfulToken !== $prevNonWhitespace;
    }
}
