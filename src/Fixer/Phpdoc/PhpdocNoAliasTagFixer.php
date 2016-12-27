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
            if (isset($this->configuration[$to])) {
                throw new InvalidFixerConfigurationException(
                    $this->getName(),
                    sprintf('Cannot change tag "%s" to tag "%s", as the tag is set configured to be replaced to "%s".', $from, $to, $this->configuration[$to])
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
    protected function getDescription()
    {
        return 'No alias PHPDoc tags should be used.';
    }
}
