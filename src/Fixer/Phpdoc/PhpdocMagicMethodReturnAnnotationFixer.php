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
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @author SpacePossum
 */
final class PhpdocMagicMethodReturnAnnotationFixer extends AbstractFixer implements ConfigurableFixerInterface
{
    private static $defaultConfiguration = array(
        'methods' => array(
            '__construct',
            '__destruct',
        ),
    );

    private static $fixableMethods = array(
        '__construct',
        '__clone',
        '__destruct',
        '__set',
        '__unset',
        '__wakeUp',
    );

    /**
     * @var array
     */
    private $config;

    /**
     * {@inheritdoc}
     */
    public function configure(array $configuration = null)
    {
        if (null === $configuration) {
            $this->config = self::$defaultConfiguration;

            return;
        }

        if (!array_key_exists('methods', $configuration) || !is_array($configuration['methods'])) {
            throw new InvalidFixerConfigurationException($this->getName(), 'Configuration "methods" must be provided as array.');
        }

        $this->config = array('methods' => array());
        foreach ($configuration['methods'] as $method) {
            if (!in_array($method, self::$fixableMethods, true)) {
                throw new InvalidFixerConfigurationException(
                    $this->getName(),
                    sprintf(
                        'Only the following magic method names can be configured for fixing "%s".',
                        implode('", "', self::$fixableMethods)
                    )
                );
            }

            $this->config['methods'][] = strtolower($method);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function fix(\SplFileInfo $file, Tokens $tokens)
    {
        // lower count with 12, 1 for 0 index and 11 for minimal number of tokens for a candidate
        for ($index = 1, $count = count($tokens) - 12; $index < $count; ++$index) {
            if ($tokens[$index]->isClassy()) {
                $index = $this->fixClassy($tokens, $index);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getDefinition()
    {
        return new FixerDefinition(
            'PHPDoc of magic methods returning `void` should not have a `@return` annotation.',
            array(new CodeSample(
'<?php
class Sample
{
    /**
     * @return Sample
     */
    public function __construct()
    {
    }
}
'
            )),
            '',
            sprintf(
                'Configure any of the following magic methods of which the PHPDocs should be fixed "%s".',
                implode('", "', self::$fixableMethods)
            ),
            self::$defaultConfiguration
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getPriority()
    {
        // FIXME add to fixer factory test
        // must run after phpdoc_to_comment
        // must run before no_empty_phpdoc
        // should (for speed only) before phpdoc_align, phpdoc_annotation_without_dot, phpdoc_return_self_reference, phpdoc_scalar.

        // FIXME check these ones
        // must before before phpdoc_trim, phpdoc_order (this can be the other way around)
        // no_trailing_whitespace_in_comment, phpdoc_separation, phpdoc_trim, phpdoc_types

        return 1;
    }

    /**
     * {@inheritdoc}
     */
    public function isCandidate(Tokens $tokens)
    {
        return
            $tokens->isAllTokenKindsFound(array(T_DOC_COMMENT, T_FUNCTION, T_STRING))
            && $tokens->isAnyTokenKindsFound(Token::getClassyTokenKinds())
        ;
    }

    /**
     * @param Tokens $tokens
     * @param int    $index  index of T_CLASS token
     *
     * @return int Token index until fixed
     */
    private function fixClassy(Tokens $tokens, $index)
    {
        // figure out where the class begins and end
        $classOpen = $tokens->getNextTokenOfKind($index, array('{'));
        $classClose = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_CURLY_BRACE, $classOpen);

        $fixMethods = $this->config['methods'];
        for ($i = $classOpen, $end = $classClose - 4; $i < $end; ++$i) {
            if (!$tokens[$i]->isGivenKind(T_FUNCTION)) {
                continue;
            }

            $n = $tokens->getNextMeaningfulToken($i);
            if (!$tokens[$n]->isGivenKind(T_STRING)) {
                continue;
            }

            $lowerMethodName = strtolower($tokens[$n]->getContent());
            $key = array_search($lowerMethodName, $fixMethods, true);
            if (false === $key) {
                continue;
            }

            $this->fixMethod($tokens, $i);

            unset($fixMethods[$key]);
            if (0 === count($fixMethods)) {
                break; // no more methods to fix, stop searching
            }
        }

        return $classClose;
    }

    /**
     * @param Tokens $tokens
     * @param int    $index  T_FUNCTION index of the method to fix
     */
    private function fixMethod(Tokens $tokens, $index)
    {
        $modifiers = array(T_PUBLIC, T_PROTECTED, T_PRIVATE, T_ABSTRACT, T_FINAL);
        // loop back until PHPDoc (to fix), or method modifier (to skip), or other non-white space (not a candidate) found.
        $prevBlock = $index;
        do {
            $prevBlock = $tokens->getPrevNonWhitespace($prevBlock);
            if ($tokens[$prevBlock]->isGivenKind(T_DOC_COMMENT)) {
                $this->removeReturnAnnotationFromToken($tokens, $prevBlock);

                break;
            }
        } while ($tokens[$prevBlock]->isGivenKind($modifiers));
    }

    /**
     * @param Tokens $tokens
     * @param int    $index  T_DOC_COMMENT index
     */
    private function removeReturnAnnotationFromToken(Tokens $tokens, $index)
    {
        $doc = new DocBlock($tokens[$index]->getContent());
        $annotation = $doc->getAnnotationsOfType('return');
        if (!count($annotation)) {
            return;
        }

        $annotation[0]->remove();
        $content = $doc->getContent();
        '' === $content
            ? $tokens->clearTokenAndMergeSurroundingWhitespace($index)
            : $tokens[$index]->setContent($doc->getContent())
        ;
    }
}
