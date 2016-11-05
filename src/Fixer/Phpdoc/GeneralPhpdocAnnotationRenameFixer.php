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
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @author Graham Campbell <graham@alt-three.com>
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
final class GeneralPhpdocAnnotationRenameFixer extends AbstractFixer
{
    /**
     * @var array
     */
    protected $configuration;

    /**
     * {@inheritdoc}
     */
    public function configure(array $configuration = null)
    {
        if (null === $configuration) {
            throw new InvalidFixerConfigurationException($this->getName(), sprintf('Configuration is required.'));
        }

        $this->configuration = $configuration;
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
    public function getDescription()
    {
        return 'Configured annotations inside phpdocs should be renamed.';
    }
}
