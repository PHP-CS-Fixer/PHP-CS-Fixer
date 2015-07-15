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
use Symfony\CS\DocBlock\Line;
use Symfony\CS\Tokenizer\Tokens;

/**
 * @author Graham Campbell <graham@mineuk.com>
 */
final class PhpdocScalarFixer extends AbstractFixer
{
    /**
     * The annotation tags search inside.
     *
     * @var array
     */
    private static $tags = array('param', 'return', 'type', 'var');

    /**
     * The types to fix.
     *
     * @var array
     */
    private static $types = array(
        'integer' => 'int',
        'boolean' => 'bool',
        'real' => 'float',
        'double' => 'float',
    );

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'Scalar types should always be written in the same form. "int", not "integer"; "bool", not "boolean"; "float", not "real" or "double".';
    }

    public function getPriority()
    {
        /*
         * Should be run before all other docblock fixers apart from the
         * phpdoc_to_comment and phpdoc_indent fixer to make sure all fixers apply
         * correct indentation to new code they add. This should run before
         * alignment of params is done since this fixer might change the
         * type and thereby un-aligning the params.
         */
        return 15;
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
            $annotations = $doc->getAnnotationsOfType(self::$tags);

            if (empty($annotations)) {
                continue;
            }

            foreach ($annotations as $annotation) {
                $this->fixScalars($doc->getLine($annotation->getStart()), $annotation->getTag()->getName());
            }

            $token->setContent($doc->getContent());
        }
    }

    /**
     * Fix scalar types.
     *
     * We must be super careful not to modify parts of words.
     *
     * @param Line   $line
     * @param string $tag
     */
    private function fixScalars(Line $line, $tag)
    {
        $content = $line->getContent();
        $tagSplit = preg_split('/\s*\@'.$tag.'\s*/', $content);
        $spaceSplit = preg_split('/\s/', $tagSplit[1]);
        $usefulContent = $spaceSplit[0];

        if (strpos($usefulContent, '|') !== false) {
            $newContent = implode('|', self::normalizeTypes(explode('|', $usefulContent)));
        } else {
            $newContent = self::normalizeType($usefulContent);
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
    private static function normalizeTypes(array $types)
    {
        foreach ($types as $index => $type) {
            $types[$index] = self::normalizeType($type);
        }

        return $types;
    }

    /**
     * Normalize the given type.
     *
     * @param string $type
     *
     * @return string
     */
    private static function normalizeType($type)
    {
        if (array_key_exists($type, self::$types)) {
            return self::$types[$type];
        }

        return $type;
    }
}
