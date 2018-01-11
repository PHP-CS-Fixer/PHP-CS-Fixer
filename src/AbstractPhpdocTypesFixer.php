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

namespace PhpCsFixer;

use PhpCsFixer\DocBlock\Annotation;
use PhpCsFixer\DocBlock\DocBlock;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * This abstract fixer provides a base for fixers to fix types in phpdoc.
 *
 * @author Graham Campbell <graham@alt-three.com>
 *
 * @internal
 */
abstract class AbstractPhpdocTypesFixer extends AbstractFixer
{
    protected const MOD_ARRAY = 1;
    protected const MOD_NULLABLE = 2;
    protected const MOD_COLLECTION = 4;

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
        parent::__construct();

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
    protected function applyFix(\SplFileInfo $file, Tokens $tokens)
    {
        foreach ($tokens as $index => $token) {
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

            $tokens[$index] = new Token([T_DOC_COMMENT, $doc->getContent()]);
        }
    }

    /**
     * Actually normalize the given type.
     *
     * @param string $type
     *
     * @return string|array
     */
    abstract protected function normalize($type);

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
     * @param string[] $types
     *
     * @return string[]
     */
    private function normalizeTypes(array $types)
    {
        foreach ($types as $index => $type) {
            // Extract modifier and modify type
            $modifiers = $this->extractModifier($type);

            $types[$index] = $this->appendModifier($this->normalize($type), $modifiers);
        }

        return $types;
    }

    /**
     * Append given modifiers to a type
     *
     * @param string|array $type
     * @param int          $modifiers
     *
     * @return string
     */
    private function appendModifier($type, int $modifiers)
    {
        $hasArrayMod = ($modifiers & self::MOD_ARRAY) === self::MOD_ARRAY;
        $hasNullableMod = ($modifiers & self::MOD_NULLABLE) === self::MOD_NULLABLE;
        $hasCollectionMod = ($modifiers & self::MOD_COLLECTION) === self::MOD_COLLECTION;

        // Use PSR-5 array type grouping
        $group = false;

        // First of we flatten types if we get an array
        if (is_array($type)) {
            $type = implode('|', $type);
            $group = true;
        }

        // Replace ?type with type|null
        if ($hasNullableMod) {
            $type .= '|null';
        }

        if ($hasCollectionMod) {
            $type = 'Collection<' . $type . '>';
        }

        if ($hasArrayMod) {

            if ($group) {
                $type = '(' . $type . ')';
            }

            $type .= '[]';
        }

        return $type;
    }

    /**
     * @param string &$type
     *
     * @return int
     */
    private function extractModifier(&$type)
    {
        $modifiers = 0;

        if ('[]' === substr($type, -2)) {
            $modifiers |= self::MOD_ARRAY;
            $type = str_replace('[]', '', $type);
        }

        if ('?' === substr($type, 0, 1)) {
            $modifiers |= self::MOD_NULLABLE;
            $type = str_replace('?', '', $type);
        }

        if (preg_match('/Collection<(?<type>.*)>$/', $type, $match)) {
            $modifiers |= self::MOD_COLLECTION;
            $type = $match['type'];
        }

        return $modifiers;
    }
}
