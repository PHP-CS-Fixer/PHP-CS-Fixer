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

namespace Symfony\CS\Fixer\PSR2;

use Symfony\CS\AbstractFixer;
use Symfony\CS\ConfigurationException\InvalidFixerConfigurationException;
use Symfony\CS\Tokenizer\Token;
use Symfony\CS\Tokenizer\Tokens;

/**
 * Fixer for part of the rules defined in PSR2 ¶4.1 Extends and Implements.
 *
 * @author SpacePossum
 */
final class ClassDefinitionFixer extends AbstractFixer
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
    private static $config;

    /**
     * @param array $configuration
     *
     * @throws \Symfony\CS\ConfigurationException\InvalidFixerConfigurationException
     */
    public static function configure(array $configuration = null)
    {
        if (null === $configuration || count($configuration) < 1) {
            self::$config = self::$defaultConfig;

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

        self::$config = $configuration;
    }

    /**
     * {@inheritdoc}
     */
    public function fix(\SplFileInfo $file, $content)
    {
        $tokens = Tokens::fromCode($content);
        // -4, one for count to index, 3 because min. of tokens for a classy location.
        for ($index = $tokens->getSize() - 4; $index > 0; --$index) {
            if ($tokens[$index]->isClassy()) {
                $this->fixClassyDefinition($tokens, $index);
            }
        }

        return $tokens->generateCode();
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
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
            $tokens->isAnonymousClass($classyIndex) ? $tokens->getPrevMeaningfulToken($classyIndex) : $classDefInfo['start'],
            $end
        );
    }

    private function fixClassyDefinitionExtends(Tokens $tokens, $classOpenIndex, $classExtendsInfo)
    {
        $endIndex = $tokens->getPrevNonWhitespace($classOpenIndex);

        if (self::$config['singleLine'] || false === $classExtendsInfo['multiLine']) {
            $this->makeClassyDefinitionSingleLine($tokens, $classExtendsInfo['start'], $endIndex);
        } elseif (self::$config['singleItemSingleLine'] && 1 === $classExtendsInfo['numberOfExtends']) {
            $this->makeClassyDefinitionSingleLine($tokens, $classExtendsInfo['start'], $endIndex);
        } elseif (self::$config['multiLineExtendsEachSingleLine'] && $classExtendsInfo['multiLine']) {
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

        if (self::$config['singleLine'] || false === $classImplementsInfo['multiLine']) {
            $this->makeClassyDefinitionSingleLine($tokens, $classImplementsInfo['start'], $endIndex);
        } elseif (self::$config['singleItemSingleLine'] && 1 === $classImplementsInfo['numberOfImplements']) {
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
            $tokens[$openIndex - 1]->setContent("\n");

            return $openIndex;
        }

        $tokens->insertAt($openIndex, new Token(array(T_WHITESPACE, "\n")));

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
        if (!(defined('T_TRAIT') && $tokens[$classyIndex]->isGivenKind(T_TRAIT))) {
            $extends = $tokens->findGivenKind(T_EXTENDS, $classyIndex, $openIndex);
            $extends = count($extends) ? $this->getClassyInheritanceInfo($tokens, key($extends), $openIndex, 'numberOfExtends') : false;

            if (!$tokens[$classyIndex]->isGivenKind(T_INTERFACE)) {
                $implements = $tokens->findGivenKind(T_IMPLEMENTS, $classyIndex, $openIndex);
                $implements = count($implements) ? $this->getClassyInheritanceInfo($tokens, key($implements), $openIndex, 'numberOfImplements') : false;
            }
        }

        return array(
            'start' => $startIndex,
            'classy' => $classyIndex,
            'open' => $openIndex,
            'extends' => $extends,
            'implements' => $implements,
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
        for ($i = $endIndex - 1; $i >= $startIndex; --$i) {
            if ($tokens[$i]->isWhitespace()) {
                if ($tokens[$i + 1]->equalsAny(array(',', '(', ')')) || $tokens[$i - 1]->equals('(')) {
                    $tokens[$i]->clear();
                } elseif (!$tokens[$i + 1]->isComment()) {
                    $tokens[$i]->setContent(' ');
                }

                --$i;
                continue;
            }

            if (
                !$tokens[$i + 1]->equalsAny(array(',', '(', ')', array(T_NS_SEPARATOR)))
                && !$tokens[$i]->equalsAny(array('(', array(T_NS_SEPARATOR)))
                && false === strpos($tokens[$i]->getContent(), "\n")
            ) {
                $tokens->insertAt($i + 1, new Token(array(T_WHITESPACE, ' ')));
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
                    $tokens[$breakAtIndex - 1]->setContent("\n");
                } else {
                    $tokens->insertAt($breakAtIndex, new Token(array(T_WHITESPACE, "\n")));
                }
            }

            $i = $previousInterfaceImplementingIndex + 1;
        }
    }
}
