<?php

declare(strict_types=1);

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
use PhpCsFixer\DocBlock\TypeExpression;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * This abstract fixer provides a base for fixers to fix types in PHPDoc.
 *
 * @author Graham Campbell <hello@gjcampbell.co.uk>
 *
 * @internal
 */
abstract class AbstractPhpdocTypesFixer extends AbstractFixer
{
    /**
     * The annotation tags search inside.
     *
     * @var list<string>
     */
    protected array $tags;

    public function __construct()
    {
        parent::__construct();

        $this->tags = Annotation::getTagsWithTypes();
    }

    public function isCandidate(Tokens $tokens): bool
    {
        return $tokens->isTokenKindFound(T_DOC_COMMENT);
    }

    protected function applyFix(\SplFileInfo $file, Tokens $tokens): void
    {
        foreach ($tokens as $index => $token) {
            if (!$token->isGivenKind(T_DOC_COMMENT)) {
                continue;
            }

            $doc = new DocBlock($token->getContent());
            $annotations = $doc->getAnnotationsOfType($this->tags);

            if (0 === \count($annotations)) {
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
     */
    abstract protected function normalize(string $type): string;

    /**
     * Fix the types at the given line.
     *
     * We must be super careful not to modify parts of words.
     *
     * This will be nicely handled behind the scenes for us by the annotation class.
     */
    private function fixTypes(Annotation $annotation): void
    {
        $types = $annotation->getTypes();

        $new = $this->normalizeTypes($types);

        if ($types !== $new) {
            $annotation->setTypes($new);
        }
    }

    /**
     * @param list<string> $types
     *
     * @return list<string>
     */
    private function normalizeTypes(array $types): array
    {
        return array_map(
            function (string $type): string {
                $typeExpression = new TypeExpression($type, null, []);

                $typeExpression->walkTypes(function (TypeExpression $type): void {
                    if (!$type->isUnionType()) {
                        $value = $this->normalize($type->toString());

                        // TODO TypeExpression should be immutable and walkTypes method should be changed to mapTypes method
                        \Closure::bind(static function () use ($type, $value): void {
                            $type->value = $value;
                        }, null, TypeExpression::class)();
                    }
                });

                return $typeExpression->toString();
            },
            $types
        );
    }
}
