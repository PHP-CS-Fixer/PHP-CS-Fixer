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

namespace PhpCsFixer\Fixer\Phpdoc;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\DocBlock\Annotation;
use PhpCsFixer\DocBlock\DocBlock;
use PhpCsFixer\DocBlock\Tag;
use PhpCsFixer\DocBlock\TagComparator;
use PhpCsFixer\Fixer\ConfigurableFixerInterface;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolver;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolverInterface;
use PhpCsFixer\FixerConfiguration\FixerOptionBuilder;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @author Graham Campbell <hello@gjcampbell.co.uk>
 * @author Jakub Kwaśniewski <jakub@zero-85.pl>
 */
final class PhpdocSeparationFixer extends AbstractFixer implements ConfigurableFixerInterface
{
    private TagComparator $tagComparator;

    private bool $standardTagsOnly = true;

    /**
     * {@inheritdoc}
     */
    public function getDefinition(): FixerDefinitionInterface
    {
        $code = <<<'EOF'
<?php
/**
 * Hello there!
 *
 * @author John Doe
 * @custom Test!
 *
 * @throws Exception|RuntimeException foo
 * @param string $foo
 *
 * @param bool   $bar Bar
 * @return int  Return the number of changes.
 */

EOF;

        return new FixerDefinition(
            'Annotations in PHPDoc should be grouped together so that annotations of the same type immediately follow each other. Annotations of a different type are separated by a single blank line, except those specified in `additional_groups` option.',
            [
                new CodeSample($code),
                new CodeSample($code, ['additional_groups' => [['param', 'return']]]),
                new CodeSample($code, ['additional_groups' => [['param', 'return']], 'psr_standard_tags_only' => false]),
                new CodeSample($code, ['groups' => [['author', 'throws', 'custom'], ['return', 'param']], 'psr_standard_tags_only' => false]),
            ],
        );
    }

    /**
     * {@inheritdoc}
     */
    public function configure(array $configuration): void
    {
        parent::configure($configuration);

        $this->tagComparator =
            TagComparator::configure($this->configuration['groups'])
                ->withAdditionalGroups($this->configuration['additional_groups'])
        ;

        $this->standardTagsOnly = $this->configuration['psr_standard_tags_only'];
    }

    /**
     * {@inheritdoc}
     *
     * Must run before PhpdocAlignFixer.
     * Must run after AlignMultilineCommentFixer, CommentToPhpdocFixer, GeneralPhpdocAnnotationRemoveFixer, PhpdocIndentFixer, PhpdocNoAccessFixer, PhpdocNoEmptyReturnFixer, PhpdocNoPackageFixer, PhpdocOrderFixer, PhpdocScalarFixer, PhpdocToCommentFixer, PhpdocTypesFixer.
     */
    public function getPriority(): int
    {
        return -3;
    }

    /**
     * {@inheritdoc}
     */
    public function isCandidate(Tokens $tokens): bool
    {
        return $tokens->isTokenKindFound(T_DOC_COMMENT);
    }

    /**
     * {@inheritdoc}
     */
    protected function applyFix(\SplFileInfo $file, Tokens $tokens): void
    {
        foreach ($tokens as $index => $token) {
            if (!$token->isGivenKind(T_DOC_COMMENT)) {
                continue;
            }

            $doc = new DocBlock($token->getContent());
            $this->fixDescription($doc);
            $this->fixAnnotations($doc);

            $tokens[$index] = new Token([T_DOC_COMMENT, $doc->getContent()]);
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function createConfigurationDefinition(): FixerConfigurationResolverInterface
    {
        return new FixerConfigurationResolver([
            (new FixerOptionBuilder('groups', 'Sets of annotation types to be grouped together.'))
                ->setAllowedTypes(['string[][]'])
                ->setDefault(TagComparator::DEFAULT_GROUPS)
                ->getOption(),
            (new FixerOptionBuilder('additional_groups', 'Sets of additional annotation types to be grouped together.'))
                ->setAllowedTypes(['string[][]'])
                ->setDefault([])
                ->getOption(),
            (new FixerOptionBuilder(
                'psr_standard_tags_only',
                'Whether to only process annotations defined by PSR-5 draft, which are: '.
                '`'.implode('`, `', Tag::PSR_STANDARD_TAGS).'`.'
            ))
                ->setAllowedTypes(['bool'])
                ->setDefault(true)
                ->getOption(),
        ]);
    }

    /**
     * Make sure the description is separated from the annotations.
     */
    private function fixDescription(DocBlock $doc): void
    {
        foreach ($doc->getLines() as $index => $line) {
            if ($line->containsATag()) {
                break;
            }

            if ($line->containsUsefulContent()) {
                $next = $doc->getLine($index + 1);

                if (null !== $next && $next->containsATag()) {
                    $line->addBlank();

                    break;
                }
            }
        }
    }

    /**
     * Make sure the annotations are correctly separated.
     */
    private function fixAnnotations(DocBlock $doc): void
    {
        foreach ($doc->getAnnotations() as $index => $annotation) {
            $next = $doc->getAnnotation($index + 1);

            if (null === $next) {
                break;
            }

            if (!$this->standardTagsOnly || $next->getTag()->valid()) {
                if ($this->tagComparator->shouldBeTogether($annotation->getTag(), $next->getTag())) {
                    $this->ensureAreTogether($doc, $annotation, $next);
                } else {
                    $this->ensureAreSeparate($doc, $annotation, $next);
                }
            }
        }
    }

    /**
     * Force the given annotations to immediately follow each other.
     */
    private function ensureAreTogether(DocBlock $doc, Annotation $first, Annotation $second): void
    {
        $pos = $first->getEnd();
        $final = $second->getStart();

        for ($pos = $pos + 1; $pos < $final; ++$pos) {
            $doc->getLine($pos)->remove();
        }
    }

    /**
     * Force the given annotations to have one empty line between each other.
     */
    private function ensureAreSeparate(DocBlock $doc, Annotation $first, Annotation $second): void
    {
        $pos = $first->getEnd();
        $final = $second->getStart() - 1;

        // check if we need to add a line, or need to remove one or more lines
        if ($pos === $final) {
            $doc->getLine($pos)->addBlank();

            return;
        }

        for ($pos = $pos + 1; $pos < $final; ++$pos) {
            $doc->getLine($pos)->remove();
        }
    }
}
