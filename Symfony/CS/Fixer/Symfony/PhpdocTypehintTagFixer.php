<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Fixer\Symfony;

use Symfony\CS\AbstractFixer;
use Symfony\CS\DocBlock\DocBlock;
use Symfony\CS\Tokenizer\Tokens;

/**
 * @author Graham Campbell <graham@mineuk.com>
 * @author Dariusz Rumiñski <dariusz.ruminski@gmail.com>
 */
final class PhpdocTypehintTagFixer extends AbstractFixer
{
    public static $configMap = array(
        'type' => array(
            'dst' => '@type',
            'search' => 'var',
            'src' => '@var',
        ),
        'var' => array(
            'dst' => '@var',
            'search' => 'type',
            'src' => '@type',
        ),
    );

    private $configuration = array(
        'annotation' => 'var',
    );

    /**
     * {@inheritdoc}
     */
    public function configure(array $configuration = null)
    {
        if (null === $configuration) {
            return;
        }

        $this->configuration['annotation'] = $configuration['annotation'];
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
        $config = self::$configMap[$this->configuration['annotation']];

        foreach ($tokens as $token) {
            if (!$token->isGivenKind(T_DOC_COMMENT)) {
                continue;
            }

            $doc = new DocBlock($token->getContent());
            $annotations = $doc->getAnnotationsOfType($config['search']);

            if (empty($annotations)) {
                continue;
            }

            foreach ($annotations as $annotation) {
                $line = $doc->getLine($annotation->getStart());
                $line->setContent(str_replace($config['src'], $config['dst'], $line->getContent()));
            }

            $token->setContent($doc->getContent());
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'Typehint PHPDoc annotation should always be written same way (@var or @type).';
    }
}
