<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS;

use Symfony\CS\DocBlock\DocBlock;
use Symfony\CS\DocBlock\Line;
use Symfony\CS\Tokenizer\Tokens;

/**
 * This abstract fixer provides a base for fixers to fix types in phpdoc.
 *
 * @author Graham Campbell <graham@mineuk.com>
 */
abstract class AbstractPhpdocTypesFixer extends AbstractFixer
{
    /**
     * The annotation tags search inside.
     *
     * @var string[]
     */
    protected static $tags = array('param', 'return', 'type', 'var', 'property');

    /**
     * {@inheritdoc}
     */
    public function fix(\SplFileInfo $file, $content)
    {
        $tokens = Tokens::fromCode($content);

        foreach ($tokens as $token) {
            if (!$token->isGivenKind(T_DOC_COMMENT)) {
                continue;
            }

            $doc = new DocBlock($token->getContent());
            $annotations = $doc->getAnnotationsOfType(static::$tags);

            if (empty($annotations)) {
                continue;
            }

            foreach ($annotations as $annotation) {
                $this->fixTypes($doc->getLine($annotation->getStart()), $annotation->getTag()->getName());
            }

            $token->setContent($doc->getContent());
        }

        return $tokens->generateCode();
    }

    /**
     * Fix the types at the given line.
     *
     * We must be super careful not to modify parts of words.
     *
     * @param Line   $line
     * @param string $tag
     */
    private function fixTypes(Line $line, $tag)
    {
        $content = $line->getContent();
        $tagSplit = preg_split('/\s*\@'.$tag.'\s*/', $content, 2);
        $spaceSplit = preg_split('/\s/', $tagSplit[1], 2);
        $usefulContent = $spaceSplit[0];

        if (strpos($usefulContent, '|') !== false) {
            $newContent = implode('|', $this->normalizeTypes(explode('|', $usefulContent)));
        } else {
            $newContent = $this->normalizeType($usefulContent);
        }

        if ($newContent !== $usefulContent) {
            // limiting to 1 replacement to prevent errors like
            // "integer $integer" being converted to "int $int"
            // when they should be converted to "int $integer"
            $line->setContent(preg_replace('/'.preg_quote($usefulContent).'/', $newContent, $content, 1));
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
