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
use PhpCsFixer\DocBlock\DocBlock;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use SplFileInfo;

/**
 * @author Julius Kiekbusch <jubeki99@gmail.com>
 */
class PhpdocLaravelSeparationFixer extends AbstractFixer
{
    /**
     * Groups of tags that should be allowed to immediately follow each other.
     *
     * @var array<int, array<int, string>>
     */
    protected $groups = [
        ['deprecated', 'link', 'see', 'since'],
        ['author', 'copyright', 'license'],
        ['category', 'package', 'subpackage'],
        ['property', 'property-read', 'property-write'],
        ['param', 'return'],
    ];

    /**
     * @inheritdoc
     */
    public function getName(): string
    {
        return 'Laravel/laravel_phpdoc_separation';
    }

    /**
     * @inheritdoc
     */
    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition('Annotations should be grouped together.', []);
    }

    /**
     * @inheritdoc
     */
    public function getPriority(): int
    {
        return -3;
    }

    /**
     * @inheritdoc
     */
    public function isCandidate(Tokens $tokens): bool
    {
        return $tokens->isTokenKindFound(T_DOC_COMMENT);
    }

    /**
     * Applies the rule fix to the given file.
     */
    protected function applyFix(SplFileInfo $file, Tokens $tokens): void
    {
        foreach ($tokens as $index => $token) {
            if (! $token->isGivenKind(T_DOC_COMMENT)) {
                continue;
            }

            $doc = new DocBlock($token->getContent());
            $this->fixDescription($doc);
            $this->fixAnnotations($doc);

            $tokens[$index] = new Token([T_DOC_COMMENT, $doc->getContent()]);
        }
    }

    /**
     * Make sure the description is separated from the annotations.
     *
     * @param  \PhpCsFixer\DocBlock\DocBlock  $doc
     * @return void
     */
    protected function fixDescription($doc)
    {
        foreach ($doc->getLines() as $index => $line) {
            if ($line->containsATag()) {
                break;
            }

            if ($line->containsUsefulContent()) {
                $next = $doc->getLine($index + 1);

                if ($next != null && $next->containsATag()) {
                    $line->addBlank();

                    break;
                }
            }
        }
    }

    /**
     * Make sure the annotations are correctly separated.
     *
     * @param  \PhpCsFixer\DocBlock\DocBlock  $doc
     * @return void
     */
    protected function fixAnnotations($doc)
    {
        foreach ($doc->getAnnotations() as $index => $annotation) {
            $next = $doc->getAnnotation($index + 1);

            if ($next == null) {
                break;
            }

            if ($next->getTag()->valid() == true) {
                if ($this->shouldBeTogether($annotation->getTag(), $next->getTag())) {
                    $this->ensureAreTogether($doc, $annotation, $next);
                } else {
                    $this->ensureAreSeparate($doc, $annotation, $next);
                }
            }
        }
    }

    /**
     * Ensure the given annotations to immediately follow each other.
     *
     * @param  \PhpCsFixer\DocBlock\DocBlock  $doc
     * @param  \PhpCsFixer\DocBlock\Annotation  $annotation
     * @param  \PhpCsFixer\DocBlock\Annotation  $next
     * @return void
     */
    protected function ensureAreTogether($doc, $annotation, $next)
    {
        $pos = $annotation->getEnd();
        $final = $next->getStart();

        for ($pos = $pos + 1; $pos < $final; $pos++) {
            $doc->getLine($pos)->remove();
        }
    }

    /**
     * Ensure the given annotations to have one empty line between each other.
     *
     * @param  \PhpCsFixer\DocBlock\DocBlock  $doc
     * @param  \PhpCsFixer\DocBlock\Annotation  $annotation
     * @param  \PhpCsFixer\DocBlock\Annotation  $next
     * @return void
     */
    protected function ensureAreSeparate($doc, $annotation, $next)
    {
        $pos = $annotation->getEnd();
        $final = $next->getStart() - 1;

        if ($pos === $final) {
            $doc->getLine($pos)->addBlank();

            return;
        }

        for ($pos = $pos + 1; $pos < $final; $pos++) {
            $doc->getLine($pos)->remove();
        }
    }

    /**
     * If the given tags should be together or apart.
     *
     * @param  \PhpCsFixer\DocBlock\Tag  $first
     * @param  \PhpCsFixer\DocBlock\Tag  $second
     * @return bool
     */
    protected function shouldBeTogether($first, $second)
    {
        $firstName = $first->getName();
        $secondName = $second->getName();

        if ($firstName == $secondName) {
            return true;
        }

        return array_filter(
            $this->groups,
            static function (array $group) use ($firstName, $secondName) {
                return in_array($firstName, $group, true) && in_array($secondName, $group, true);
            }
        ) !== [];
    }
}
