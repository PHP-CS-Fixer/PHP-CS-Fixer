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

namespace PhpCsFixer\Fixer\Phpdoc;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\ConfigurationException\InvalidFixerConfigurationException;
use PhpCsFixer\DocBlock\DocBlock;
use PhpCsFixer\Fixer\ConfigurableFixerInterface;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\Tokenizer\Tokens;
use PhpCsFixer\Tokenizer\TokensAnalyzer;

/**
 * @author SpacePossum
 */
final class PhpdocReturnSelfReferenceFixer extends AbstractFixer implements ConfigurableFixerInterface
{
    private static $defaultConfiguration = array(
        'this' => '$this',
        '@this' => '$this',
        '$self' => 'self',
        '@self' => 'self',
        '$static' => 'static',
        '@static' => 'static',
    );

    private static $toTypes = array(
        '$this',
        'static',
        'self',
    );

    /**
     * @var array<string, string>
     */
    private $configuration;

    /**
     * {@inheritdoc}
     */
    public function configure(array $configuration = null)
    {
        if (null === $configuration) {
            $this->configuration = self::$defaultConfiguration;

            return;
        }

        $newConfig = array();
        foreach ($configuration as $key => $value) {
            if (is_string($key)) {
                $key = strtolower($key);
            }

            if (!isset(self::$defaultConfiguration[$key])) {
                throw new InvalidFixerConfigurationException(
                    $this->getName(),
                    sprintf(
                        'Unknown key "%s", expected any of "%s".',
                        is_object($key) ? get_class($key) : gettype($key).(is_resource($key) ? '' : '#'.$key),
                        implode('", "', array_keys(self::$defaultConfiguration))
                    )
                );
            }

            if (!in_array($value, self::$toTypes, true)) {
                throw new InvalidFixerConfigurationException(
                    $this->getName(),
                    sprintf(
                        'Unknown value "%s", expected any of "%s".',
                        is_object($value) ? get_class($value) : gettype($value).(is_resource($value) ? '' : '#'.$value),
                        implode('", "', self::$toTypes)
                    )
                );
            }

            $newConfig[strtolower($key)] = $value;
        }

        $this->configuration = $newConfig;
    }

    /**
     * {@inheritdoc}
     */
    public function fix(\SplFileInfo $file, Tokens $tokens)
    {
        $tokensAnalyzer = new TokensAnalyzer($tokens);
        foreach ($tokensAnalyzer->getClassyElements() as $index => $element) {
            if ('method' === $element['type']) {
                $this->fixMethod($tokens, $index);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getDefinition()
    {
        return new FixerDefinition(
            'The type of `@return` annotations of methods returning a reference to itself must the configured one.',
            array(new CodeSample('
<?php
class Sample
{
    /**
     * @return this
     */
    public function test()
    {
        return $this;
    }
}'
            )),
            '',
            sprintf(
                'Fixer can be configured to fix any of (case insensitive) `%s` to any of `%s`.',
                implode('`,`', array_keys(self::$defaultConfiguration)),
                implode('`,`', self::$toTypes)
            ),
            self::$defaultConfiguration
        );
    }

    /**
     * {@inheritdoc}
     */
    public function isCandidate(Tokens $tokens)
    {
        return count($tokens) > 10 && $tokens->isTokenKindFound(T_DOC_COMMENT) && $tokens->isAnyTokenKindsFound(array(T_CLASS, T_INTERFACE));
    }

    /**
     * @param Tokens $tokens
     * @param int    $index
     */
    private function fixMethod(Tokens $tokens, $index)
    {
        static $methodModifiers = array(T_STATIC, T_FINAL, T_ABSTRACT, T_PRIVATE, T_PROTECTED, T_PUBLIC);

        // find PHPDoc of method (if any)
        do {
            $tokenIndex = $tokens->getPrevMeaningfulToken($index);
            if (!$tokens[$tokenIndex]->isGivenKind($methodModifiers)) {
                break;
            }

            $index = $tokenIndex;
        } while (true);

        $docIndex = $tokens->getPrevNonWhitespace($index);
        if (!$tokens[$docIndex]->isGivenKind(T_DOC_COMMENT)) {
            return;
        }

        // find @return
        $docBlock = new DocBlock($tokens[$docIndex]->getContent());
        $returnsBlock = $docBlock->getAnnotationsOfType('return');

        if (!count($returnsBlock)) {
            return; // no return annotation found
        }

        $returnsBlock = $returnsBlock[0];
        $types = $returnsBlock->getTypes();

        if (!count($types)) {
            return; // no return type(s) found
        }

        $newTypes = array();
        foreach ($types as $type) {
            $lower = strtolower($type);
            $newTypes[] = isset($this->configuration[$lower]) ? $this->configuration[$lower] : $type;
        }

        if ($types === $newTypes) {
            return;
        }

        $returnsBlock->setTypes($newTypes);
        $tokens[$docIndex]->setContent($docBlock->getContent());
    }
}
