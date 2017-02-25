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

/**
 * Case sensitive tag replace fixer (does not process inline tags like {@inheritdoc}).
 *
 * @author Graham Campbell <graham@alt-three.com>
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 * @author SpacePossum
 */
final class PhpdocNoAliasTagFixer extends AbstractFixer implements ConfigurableFixerInterface
{
    /**
     * @var array<string, string>
     */
    private $configuration;

    /**
     * @var array
     */
    private static $defaultConfiguration = array(
        'property-read' => 'property',
        'property-write' => 'property',
        'type' => 'var',
        'link' => 'see',
    );

    /**
     * Key value pairs of string, replace from -> to tags (without '@').
     *
     * @param string[]|null $configuration
     */
    public function configure(array $configuration = null)
    {
        if (null === $configuration) {
            $this->configuration = self::$defaultConfiguration;

            return;
        }

        $this->configuration = array();
        foreach ($configuration as $from => $to) {
            if (!is_string($from)) {
                throw new InvalidFixerConfigurationException($this->getName(), 'Tag to replace must be a string.');
            }

            if (!is_string($to)) {
                throw new InvalidFixerConfigurationException($this->getName(), sprintf('Tag to replace to from "%s" must be a string.', $from));
            }

            if (1 !== preg_match('#^\S+$#', $to) || false !== strpos($to, '*/')) {
                throw new InvalidFixerConfigurationException($this->getName(), sprintf('Tag "%s" cannot be replaced by invalid tag "%s".', $from, $to));
            }

            $this->configuration[trim($from)] = trim($to);
        }

        foreach ($this->configuration as $from => $to) {
            if (isset($this->configuration[$to])) {
                throw new InvalidFixerConfigurationException(
                    $this->getName(),
                    sprintf('Cannot change tag "%1$s" to tag "%2$s", as the tag "%2$s" is configured to be replaced to "%3$s".', $from, $to, $this->configuration[$to])
                );
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function isCandidate(Tokens $tokens)
    {
        return $tokens->isTokenKindFound(T_DOC_COMMENT);
    }

    /**
     * {@inheritdoc}
     */
    public function fix(\SplFileInfo $file, Tokens $tokens)
    {
        $searchFor = array_keys($this->configuration);

        foreach ($tokens as $token) {
            if (!$token->isGivenKind(T_DOC_COMMENT)) {
                continue;
            }

            $doc = new DocBlock($token->getContent());
            $annotations = $doc->getAnnotationsOfType($searchFor);

            if (empty($annotations)) {
                continue;
            }

            foreach ($annotations as $annotation) {
                $annotation->getTag()->setName($this->configuration[$annotation->getTag()->getName()]);
            }

            $token->setContent($doc->getContent());
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getDefinition()
    {
        return new FixerDefinition(
            'No alias PHPDoc tags should be used.',
            array(
                new CodeSample(
                    '<?php
/**
 * @property string $foo
 * @property-read string $bar
 *
 * @link baz
 */
final class Example
{
}
'
                ),
                new CodeSample(
                    '<?php
/**
 * @property string $foo
 * @property-read string $bar
 *
 * @link baz
 */
final class Example
{
}
',
                    array('link' => 'website')
                ),
            ),
            null,
            'Array that maps current annotations into new ones.',
            self::$defaultConfiguration
        );
    }
}
