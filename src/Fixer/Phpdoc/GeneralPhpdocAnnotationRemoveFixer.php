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
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
final class GeneralPhpdocAnnotationRemoveFixer extends AbstractFixer implements ConfigurableFixerInterface
{
    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(
            'Configured annotations should be omitted from PHPDoc.',
            [
                new CodeSample(
                    '<?php
/**
 * @internal
 * @author John Doe
 * @AuThOr Jane Doe
 */
function foo() {}
',
                    ['annotations' => ['author']]
                ),
                new CodeSample(
                    '<?php
/**
 * @internal
 * @author John Doe
 * @AuThOr Jane Doe
 */
function foo() {}
',
                    ['annotations' => ['author'], 'case_sensitive' => false]
                ),
                new CodeSample(
                    '<?php
/**
 * @author John Doe
 * @package ACME API
 * @subpackage Authorization
 * @version 1.0
 */
function foo() {}
',
                    ['annotations' => ['package', 'subpackage']]
                ),
            ]
        );
    }

    /**
     * {@inheritdoc}
     *
     * Must run before NoEmptyPhpdocFixer, PhpdocAlignFixer, PhpdocLineSpanFixer, PhpdocSeparationFixer, PhpdocTrimFixer.
     * Must run after AlignMultilineCommentFixer, CommentToPhpdocFixer, PhpdocIndentFixer, PhpdocScalarFixer, PhpdocToCommentFixer, PhpdocTypesFixer.
     */
    public function getPriority(): int
    {
        return 10;
    }

    public function isCandidate(Tokens $tokens): bool
    {
        return $tokens->isTokenKindFound(T_DOC_COMMENT);
    }

    protected function applyFix(\SplFileInfo $file, Tokens $tokens): void
    {
        if (0 === \count($this->configuration['annotations'])) {
            return;
        }

        foreach ($tokens as $index => $token) {
            if (!$token->isGivenKind(T_DOC_COMMENT)) {
                continue;
            }

            $doc = new DocBlock($token->getContent());
            $annotations = $this->getAnnotationsToRemove($doc);

            // nothing to do if there are no annotations
            if (0 === \count($annotations)) {
                continue;
            }

            foreach ($annotations as $annotation) {
                $annotation->remove();
            }

            if ('' === $doc->getContent()) {
                $tokens->clearTokenAndMergeSurroundingWhitespace($index);
            } else {
                $tokens[$index] = new Token([T_DOC_COMMENT, $doc->getContent()]);
            }
        }
    }

    protected function createConfigurationDefinition(): FixerConfigurationResolverInterface
    {
        return new FixerConfigurationResolver([
            (new FixerOptionBuilder('annotations', 'List of annotations to remove, e.g. `["author"]`.'))
                ->setAllowedTypes(['array'])
                ->setDefault([])
                ->getOption(),
            (new FixerOptionBuilder('case_sensitive', 'Should annotations be case sensitive.'))
                ->setAllowedTypes(['bool'])
                ->setDefault(true)
                ->getOption(),
        ]);
    }

    /**
     * @return list<Annotation>
     */
    private function getAnnotationsToRemove(DocBlock $doc): array
    {
        if (true === $this->configuration['case_sensitive']) {
            return $doc->getAnnotationsOfType($this->configuration['annotations']);
        }

        $typesToSearchFor = array_map(function (string $type): string {
            return strtolower($type);
        }, $this->configuration['annotations']);

        $annotations = [];

        foreach ($doc->getAnnotations() as $annotation) {
            $tagName = strtolower($annotation->getTag()->getName());
            if (\in_array($tagName, $typesToSearchFor, true)) {
                $annotations[] = $annotation;
            }
        }

        return $annotations;
    }
}
