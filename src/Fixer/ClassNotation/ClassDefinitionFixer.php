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

namespace PhpCsFixer\Fixer\ClassNotation;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\ConfigurationException\InvalidFixerConfigurationException;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use PhpCsFixer\Tokenizer\TokensAnalyzer;
use PhpCsFixer\WhitespacesFixerConfigAwareInterface;

/**
 * Fixer for part of the rules defined in PSR2 ¶4.1 Extends and Implements.
 *
 * @author SpacePossum
 */
final class ClassDefinitionFixer extends AbstractFixer implements WhitespacesFixerConfigAwareInterface
{
    /**
     * @var array<string, bool>
     */
    private static $defaultConfig = array(
        // put class declaration on one line
        'singleLine' => false,
        // if a classy extends or implements only one element than put it on the same line
        'singleItemSingleLine' => false,
        // if an interface extends multiple interfaces declared over multiple lines put each interface on its own line
        'multiLineExtendsEachSingleLine' => false,
    );

    /**
     * @var array
     */
    private $config;

    /**
     * @param null|array $configuration
     *
     * @throws InvalidFixerConfigurationException
     */
    public function configure(array $configuration = null)
    {
        if (null === $configuration || count($configuration) < 1) {
            $this->config = self::$defaultConfig;

            return;
        }

        $configuration = array_merge(self::$defaultConfig, $configuration);

        foreach ($configuration as $item => $value) {
            if (!array_key_exists($item, self::$defaultConfig)) {
                throw new InvalidFixerConfigurationException('class_definition', sprintf('Unknown configuration item "%s", expected any of "%s".', $item, implode(', ', array_keys(self::$defaultConfig))));
            }

            if (!is_bool($value)) {
                throw new InvalidFixerConfigurationException('class_definition', sprintf('Configuration value for item "%s" must be a bool, got "%s".', $item, is_object($value) ? get_class($value) : gettype($value)));
            }
        }

        $this->config = $configuration;
    }

    /**
     * {@inheritdoc}
     */
    public function isCandidate(Tokens $tokens)
    {
        return $tokens->isAnyTokenKindsFound(Token::getClassyTokenKinds());
    }

    /**
     * {@inheritdoc}
     */
    public function fix(\SplFileInfo $file, Tokens $tokens)
    {
        // -4, one for count to index, 3 because min. of tokens for a classy location.
        for ($index = $tokens->getSize() - 4; $index > 0; --$index) {
            if ($tokens[$index]->isClassy()) {
                $this->fixClassyDefinition($tokens, $index);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function getDescription()
    {
        return 'Whitespace around the key words of a class, trait or interfaces definition should be one space.';
    }

    /**
     * @param Tokens $tokens
     * @param int    $classyIndex Class definition token start index
     */
    private function fixClassyDefinition(Tokens $tokens, $classyIndex)
    {
        $classDefInfo = $this->getClassyDefinitionInfo($tokens, $classyIndex);

        // PSR: class definition open curly brace must go on a new line
        $classDefInfo['open'] = $this->fixClassyDefinitionOpenSpacing($tokens, $classDefInfo['open']);

        // PSR2 4.1 Lists of implements MAY be split across multiple lines, where each subsequent line is indented once.
        // When doing so, the first item in the list MUST be on the next line, and there MUST be only one interface per line.
        if (false !== $classDefInfo['implements']) {
            $this->fixClassyDefinitionImplements(
                $tokens,
                $classDefInfo['open'],
                $classDefInfo['implements']
            );
        }

        if (false !== $classDefInfo['extends']) {
            $this->fixClassyDefinitionExtends(
                $tokens,
                false === $classDefInfo['implements'] ? $classDefInfo['open'] : 1 + $classDefInfo['implements']['start'],
                $classDefInfo['extends']
            );
        }

        if ($classDefInfo['implements']) {
            $end = $classDefInfo['implements']['start'];
        } elseif ($classDefInfo['extends']) {
            $end = $classDefInfo['extends']['start'];
        } else {
            $end = $tokens->getPrevNonWhitespace($classDefInfo['open']);
        }

        // 4.1 The extends and implements keywords MUST be declared on the same line as the class name.
        $this->makeClassyDefinitionSingleLine(
            $tokens,
            $classDefInfo['anonymousClass'] ? $tokens->getPrevMeaningfulToken($classyIndex) : $classDefInfo['start'],
            $end
        );
    }

    private function fixClassyDefinitionExtends(Tokens $tokens, $classOpenIndex, $classExtendsInfo)
    {
        $endIndex = $tokens->getPrevNonWhitespace($classOpenIndex);

        if ($this->config['singleLine'] || false === $classExtendsInfo['multiLine']) {
            $this->makeClassyDefinitionSingleLine($tokens, $classExtendsInfo['start'], $endIndex);
        } elseif ($this->config['singleItemSingleLine'] && 1 === $classExtendsInfo['numberOfExtends']) {
            $this->makeClassyDefinitionSingleLine($tokens, $classExtendsInfo['start'], $endIndex);
        } elseif ($this->config['multiLineExtendsEachSingleLine'] && $classExtendsInfo['multiLine']) {
            $this->makeClassyInheritancePartMultiLine($tokens, $classExtendsInfo['start'], $endIndex);
        }
    }

    /**
     * @param Tokens $tokens
     * @param int    $classOpenIndex
     * @param array  $classImplementsInfo
     */
    private function fixClassyDefinitionImplements(Tokens $tokens, $classOpenIndex, array $classImplementsInfo)
    {
        $endIndex = $tokens->getPrevNonWhitespace($classOpenIndex);

        if ($this->config['singleLine'] || false === $classImplementsInfo['multiLine']) {
            $this->makeClassyDefinitionSingleLine($tokens, $classImplementsInfo['start'], $endIndex);
        } elseif ($this->config['singleItemSingleLine'] && 1 === $classImplementsInfo['numberOfImplements']) {
            $this->makeClassyDefinitionSingleLine($tokens, $classImplementsInfo['start'], $endIndex);
        } else {
            $this->makeClassyInheritancePartMultiLine($tokens, $classImplementsInfo['start'], $endIndex);
        }
    }

    /**
     * @param Tokens $tokens
     * @param int    $openIndex
     *
     * @return int
     */
    private function fixClassyDefinitionOpenSpacing(Tokens $tokens, $openIndex)
    {
        if (false !== strpos($tokens[$openIndex - 1]->getContent(), "\n")) {
            return $openIndex;
        }

        if ($tokens[$openIndex - 1]->isWhitespace()) {
            $tokens[$openIndex - 1]->setContent($this->whitespacesConfig->getLineEnding());

            return $openIndex;
        }

        $tokens->insertAt($openIndex, new Token(array(T_WHITESPACE, $this->whitespacesConfig->getLineEnding())));

        return $openIndex + 1;
    }

    /**
     * @param Tokens $tokens
     * @param int    $classyIndex
     *
     * @return array
     */
    private function getClassyDefinitionInfo(Tokens $tokens, $classyIndex)
    {
        $openIndex = $tokens->getNextTokenOfKind($classyIndex, array('{'));
        $prev = $tokens->getPrevMeaningfulToken($classyIndex);
        $startIndex = $tokens[$prev]->isGivenKind(array(T_FINAL, T_ABSTRACT)) ? $prev : $classyIndex;

        $extends = false;
        $implements = false;
        $anonymousClass = false;

        if (!(defined('T_TRAIT') && $tokens[$classyIndex]->isGivenKind(T_TRAIT))) {
            $extends = $tokens->findGivenKind(T_EXTENDS, $classyIndex, $openIndex);
            $extends = count($extends) ? $this->getClassyInheritanceInfo($tokens, key($extends), $openIndex, 'numberOfExtends') : false;

            if (!$tokens[$classyIndex]->isGivenKind(T_INTERFACE)) {
                $implements = $tokens->findGivenKind(T_IMPLEMENTS, $classyIndex, $openIndex);
                $implements = count($implements) ? $this->getClassyInheritanceInfo($tokens, key($implements), $openIndex, 'numberOfImplements') : false;
                $tokensAnalyzer = new TokensAnalyzer($tokens);
                $anonymousClass = $tokensAnalyzer->isAnonymousClass($classyIndex);
            }
        }

        return array(
            'start' => $startIndex,
            'classy' => $classyIndex,
            'open' => $openIndex,
            'extends' => $extends,
            'implements' => $implements,
            'anonymousClass' => $anonymousClass,
        );
    }

    /**
     * @param Tokens $tokens
     * @param int    $implementsIndex
     * @param int    $openIndex
     * @param string $label
     *
     * @return array
     */
    private function getClassyInheritanceInfo(Tokens $tokens, $implementsIndex, $openIndex, $label)
    {
        $implementsInfo = array('start' => $implementsIndex, $label => 1, 'multiLine' => false);
        $lastMeaningFul = $tokens->getPrevMeaningfulToken($openIndex);
        for ($i = $implementsIndex; $i < $lastMeaningFul; ++$i) {
            if ($tokens[$i]->equals(',')) {
                ++$implementsInfo[$label];

                continue;
            }

            if (!$implementsInfo['multiLine'] && false !== strpos($tokens[$i]->getContent(), "\n")) {
                $implementsInfo['multiLine'] = true;
            }
        }

        return $implementsInfo;
    }

    /**
     * @param Tokens $tokens
     * @param int    $startIndex
     * @param int    $endIndex
     */
    private function makeClassyDefinitionSingleLine(Tokens $tokens, $startIndex, $endIndex)
    {
        for ($i = $endIndex; $i >= $startIndex; --$i) {
            if ($tokens[$i]->isWhitespace()) {
                if (
                    $tokens[$i + 1]->equalsAny(array(',', '(', ')'))
                    || $tokens[$i - 1]->equals('(')
                ) {
                    $tokens[$i]->clear();
                } elseif (
                    !$tokens[$i + 1]->isComment()
                    && !($tokens[$i - 1]->isGivenKind(T_COMMENT) && '//' === substr($tokens[$i - 1]->getContent(), 0, 2))
                ) {
                    $tokens[$i]->setContent(' ');
                }

                --$i;
                continue;
            }

            if ($tokens[$i]->equals(',') && !$tokens[$i + 1]->isWhitespace()) {
                $tokens->insertAt($i + 1, new Token(array(T_WHITESPACE, ' ')));

                continue;
            }

            if (!$tokens[$i]->isComment()) {
                continue;
            }

            if (!$tokens[$i + 1]->isWhitespace() && !$tokens[$i + 1]->isComment() && false === strpos($tokens[$i]->getContent(), "\n")) {
                $tokens->insertAt($i + 1, new Token(array(T_WHITESPACE, ' ')));
            }

            if (!$tokens[$i - 1]->isWhitespace() && !$tokens[$i - 1]->isComment()) {
                $tokens->insertAt($i, new Token(array(T_WHITESPACE, ' ')));
            }
        }
    }

    /**
     * @param Tokens $tokens
     * @param int    $startIndex
     * @param int    $endIndex
     */
    private function makeClassyInheritancePartMultiLine(Tokens $tokens, $startIndex, $endIndex)
    {
        for ($i = $endIndex; $i > $startIndex; --$i) {
            $previousInterfaceImplementingIndex = $tokens->getPrevTokenOfKind($i, array(',', array(T_IMPLEMENTS), array(T_EXTENDS)));
            $breakAtIndex = $tokens->getNextMeaningfulToken($previousInterfaceImplementingIndex);
            // make the part of a ',' or 'implements' single line
            $this->makeClassyDefinitionSingleLine(
                $tokens,
                $breakAtIndex,
                $i
            );

            // make sure the part is on its own line
            $isOnOwnLine = false;
            for ($j = $breakAtIndex; $j > $previousInterfaceImplementingIndex; --$j) {
                if (false !== strpos($tokens[$j]->getContent(), "\n")) {
                    $isOnOwnLine = true;

                    break;
                }
            }

            if (!$isOnOwnLine) {
                if ($tokens[$breakAtIndex - 1]->isWhitespace()) {
                    $tokens[$breakAtIndex - 1]->setContent($this->whitespacesConfig->getLineEnding().$this->whitespacesConfig->getIndent());
                } else {
                    $tokens->insertAt($breakAtIndex, new Token(array(T_WHITESPACE, $this->whitespacesConfig->getLineEnding().$this->whitespacesConfig->getIndent())));
                }
            }

            $i = $previousInterfaceImplementingIndex + 1;
        }
    }
}
