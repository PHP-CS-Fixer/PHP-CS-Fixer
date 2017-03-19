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
use PhpCsFixer\DocBlock\DocBlock;
use PhpCsFixer\Fixer\ConfigurationDefinitionFixerInterface;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolverRootless;
use PhpCsFixer\FixerConfiguration\FixerOptionBuilder;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\Tokenizer\Tokens;
use PhpCsFixer\Tokenizer\TokensAnalyzer;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\Options;

/**
 * @author SpacePossum
 */
final class PhpdocReturnSelfReferenceFixer extends AbstractFixer implements ConfigurationDefinitionFixerInterface
{
    private static $toTypes = array(
        '$this',
        'static',
        'self',
    );

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
            ))
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
     * {@inheritdoc}
     */
    protected function createConfigurationDefinition()
    {
        $toTypes = self::$toTypes;
        $default = array(
            'this' => '$this',
            '@this' => '$this',
            '$self' => 'self',
            '@self' => 'self',
            '$static' => 'static',
            '@static' => 'static',
        );

        $replacements = new FixerOptionBuilder('replacements', 'Mapping between replaced return types with new ones.');
        $replacements = $replacements
            ->setAllowedTypes(array('array'))
            ->setNormalizer(function (Options $options, $value) use ($toTypes, $default) {
                $normalizedValue = array();
                foreach ($value as $from => $to) {
                    if (is_string($from)) {
                        $from = strtolower($from);
                    }

                    if (!isset($default[$from])) {
                        throw new InvalidOptionsException(sprintf(
                            'Unknown key "%s", expected any of "%s".',
                            is_object($from) ? get_class($from) : gettype($from).(is_resource($from) ? '' : '#'.$from),
                            implode('", "', array_keys($default))
                        ));
                    }

                    if (!in_array($to, $toTypes, true)) {
                        throw new InvalidOptionsException(sprintf(
                            'Unknown value "%s", expected any of "%s".',
                            is_object($to) ? get_class($to) : gettype($to).(is_resource($to) ? '' : '#'.$to),
                            implode('", "', $toTypes)
                        ));
                    }

                    $normalizedValue[$from] = $to;
                }

                return $normalizedValue;
            })
            ->setDefault($default)
            ->getOption()
        ;

        return new FixerConfigurationResolverRootless('replacements', array($replacements));
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
            $newTypes[] = isset($this->configuration['replacements'][$lower]) ? $this->configuration['replacements'][$lower] : $type;
        }

        if ($types === $newTypes) {
            return;
        }

        $returnsBlock->setTypes($newTypes);
        $tokens[$docIndex]->setContent($docBlock->getContent());
    }
}
