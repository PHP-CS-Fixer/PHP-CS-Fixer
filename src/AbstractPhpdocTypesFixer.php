<?php

/*
 * This file is part of the PHP CS Fixer.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *     Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace PhpCsFixer;

use PhpCsFixer\DocBlock\Annotation;
use PhpCsFixer\DocBlock\DocBlock;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * This abstract fixer provides a base for fixers to fix types in phpdoc.
 *
 * @author Graham Campbell <graham@mineuk.com>
 *
 * @internal
 */
abstract class AbstractPhpdocTypesFixer extends AbstractFixer
{
    /**
     * The annotation tags search inside.
     *
     * @var string[]
     */
    protected $tags;

    /**
     * {@inheritdoc}
     */
    public function __construct()
    {
        $this->tags = Annotation::getTagsWithTypes();
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
        foreach ($tokens as $token) {
            if (!$token->isGivenKind(T_DOC_COMMENT)) {
                continue;
            }

            $doc = new DocBlock($token->getContent());
            $annotations = $doc->getAnnotationsOfType($this->tags);

            if (empty($annotations)) {
                continue;
            }

            foreach ($annotations as $annotation) {
                $this->fixTypes($annotation);
            }

            $token->setContent($doc->getContent());
        }
    }

    /**
     * Fix the types at the given line.
     *
     * We must be super careful not to modify parts of words.
     *
     * This will be nicely handled behind the scenes for us by the annotation class.
     *
     * @param Annotation $annotation
     */
    private function fixTypes(Annotation $annotation)
    {
        $types = $annotation->getTypes();

        $new = $this->normalizeTypes($types);

        if ($types !== $new) {
            $annotation->setTypes($new);
        }
    }

    /**
     * Normalize the given types.
     *
     * @param string[] $types
     *
     * @return string[]
     */
    private function normalizeTypes(array $types)
    {
        foreach ($types as $index => $type) {
            $types[$index] = $this->normalizeType($type);
        }

        return $types;
    }

    /**
     * Prepair the type and normalize it.
     *
     * @param string $type
     *
     * @return string
     */
    private function normalizeType($type)
    {
        if (substr($type, -2) === '[]') {
            return $this->normalize(substr($type, 0, -2)).'[]';
        }

        return $this->normalize($type);
    }

    /**
     * Actually normalize the given type.
     *
     * @param string $type
     *
     * @return string
     */
    abstract protected function normalize($type);
}
