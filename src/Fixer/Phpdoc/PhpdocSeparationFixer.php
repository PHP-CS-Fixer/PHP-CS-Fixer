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
use PhpCsFixer\Preg;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;

/**
 * @author Graham Campbell <hello@gjcampbell.co.uk>
 * @author Jakub Kwaśniewski <jakub@zero-85.pl>
 */
final class PhpdocSeparationFixer extends AbstractFixer implements ConfigurableFixerInterface
{
    /**
     * @var string[][]
     */
    private array $groups;

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
            'Annotations in PHPDoc should be grouped together so that annotations of the same type immediately follow each other. Annotations of a different type are separated by a single blank line.',
            [
                new CodeSample($code),
                new CodeSample($code, ['groups' => [
                    ['deprecated', 'link', 'see', 'since'],
                    ['author', 'copyright', 'license'],
                    ['category', 'package', 'subpackage'],
                    ['property', 'property-read', 'property-write'],
                    ['param', 'return'],
                ]]),
                new CodeSample($code, ['groups' => [
                    ['author', 'throws', 'custom'],
                    ['return', 'param'],
                ]]),
                new CodeSample(
                    <<<'EOF'
                    <?php
                    /**
                     * @ORM\Id
                     *
                     * @ORM\GeneratedValue
                     * @Assert\NotNull
                     *
                     * @Assert\Type("string")
                     */

                    EOF,
                    ['groups' => [['ORM\*'], ['Assert\*']]],
                ),
                new CodeSample($code, ['skip_unlisted_annotations' => true]),
            ],
        );
    }

    /**
     * {@inheritdoc}
     */
    public function configure(array $configuration): void
    {
        parent::configure($configuration);

        $this->groups = $this->configuration['groups'];
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
        $allowTagToBelongToOnlyOneGroup = function ($groups) {
            $tags = [];
            foreach ($groups as $groupIndex => $group) {
                foreach ($group as $member) {
                    if (isset($tags[$member])) {
                        if ($groupIndex === $tags[$member]) {
                            throw new InvalidOptionsException(
                                'The option "groups" value is invalid. '.
                                'The "'.$member.'" tag is specified more than once.'
                            );
                        }

                        throw new InvalidOptionsException(
                            'The option "groups" value is invalid. '.
                            'The "'.$member.'" tag belongs to more than one group.'
                        );
                    }
                    $tags[$member] = $groupIndex;
                }
            }

            return true;
        };

        return new FixerConfigurationResolver([
            (new FixerOptionBuilder('groups', 'Sets of annotation types to be grouped together. Use `*` to match any tag character.'))
                ->setAllowedTypes(['string[][]'])
                ->setDefault([
                    ['deprecated', 'link', 'see', 'since'],
                    ['author', 'copyright', 'license'],
                    ['category', 'package', 'subpackage'],
                    ['property', 'property-read', 'property-write'],
                ])
                ->setAllowedValues([$allowTagToBelongToOnlyOneGroup])
                ->getOption(),
            (new FixerOptionBuilder('skip_unlisted_annotations', 'Whether to skip annotations that are not listed in any group.'))
                ->setAllowedTypes(['bool'])
                ->setDefault(false) // @TODO 4.0: set to `true`.
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

            $shouldBeTogether = $this->shouldBeTogether($annotation, $next, $this->groups);

            if (true === $shouldBeTogether) {
                $this->ensureAreTogether($doc, $annotation, $next);
            } elseif (false === $shouldBeTogether || !$this->configuration['skip_unlisted_annotations']) {
                $this->ensureAreSeparate($doc, $annotation, $next);
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

    /**
     * @param list<list<string>> $groups
     */
    private function shouldBeTogether(Annotation $first, Annotation $second, array $groups): ?bool
    {
        $firstName = $this->tagName($first);
        $secondName = $this->tagName($second);

        // A tag could not be read.
        if (null === $firstName || null === $secondName) {
            return null;
        }

        if ($firstName === $secondName) {
            return true;
        }

        foreach ($groups as $group) {
            $firstTagIsInGroup = $this->isInGroup($firstName, $group);
            $secondTagIsInGroup = $this->isInGroup($secondName, $group);

            if ($firstTagIsInGroup) {
                return $secondTagIsInGroup;
            }

            if ($secondTagIsInGroup) {
                return false;
            }
        }

        return null;
    }

    private function tagName(Annotation $annotation): ?string
    {
        Preg::match('/@([a-zA-Z0-9_\\\\-]+(?=\s|$|\())/', $annotation->getContent(), $matches);

        return $matches[1] ?? null;
    }

    /**
     * @param list<string> $group
     */
    private function isInGroup(string $tag, array $group): bool
    {
        foreach ($group as $tagInGroup) {
            $tagInGroup = str_replace('*', '\*', $tagInGroup);
            $tagInGroup = preg_quote($tagInGroup, '/');
            $tagInGroup = str_replace('\\\\\*', '.*?', $tagInGroup);

            if (1 === Preg::match("/^{$tagInGroup}$/", $tag)) {
                return true;
            }
        }

        return false;
    }
}
