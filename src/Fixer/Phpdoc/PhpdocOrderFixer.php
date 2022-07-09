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
 * @author Jakub Kwaśniewski <jakub@zero-85.pl>
 */
final class PhpdocOrderFixer extends AbstractFixer implements ConfigurableFixerInterface
{
    /**
     * @internal
     */
    public const ORDER_STYLE_PHPCS = 'phpcs';

    /**
     * @internal
     */
    public const ORDER_STYLE_SYMFONY = 'symfony';

    private const ORDERS = [
        'phpcs' => ['param', 'throws', 'return'],
        'symfony' => ['param', 'return', 'throws'],
    ];

    /**
     * @var string[]
     */
    private array $order;

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
 * @throws Exception|RuntimeException foo
 * @custom Test!
 * @return int  Return the number of changes.
 * @param string $foo
 * @param bool   $bar Bar
 */

EOF;

        $description = 'Annotations in PHPDoc should be ordered in one of the styles below:'."\n";
        $opts = [];
        foreach (self::ORDERS as $style => $order) {
            $opts[] = "\n- `'{$style}'` style annotations order is `@".implode('`, `@', $order).'`';
        }
        $description .= implode(',', $opts).'.';

        return new FixerDefinition(
            'Annotations in PHPDoc should be ordered in specific style.',
            [
                new CodeSample($code),
                new CodeSample($code, ['style' => self::ORDER_STYLE_SYMFONY]),
                new CodeSample($code, ['style' => self::ORDER_STYLE_PHPCS]),
            ],
            $description
        );
    }

    /**
     * {@inheritdoc}
     */
    public function configure(array $configuration): void
    {
        parent::configure($configuration);

        $this->order = self::ORDERS[$this->configuration['style']];
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
     *
     * Must run before PhpdocAlignFixer, PhpdocSeparationFixer, PhpdocTrimFixer.
     * Must run after AlignMultilineCommentFixer, CommentToPhpdocFixer, PhpdocAddMissingParamAnnotationFixer, PhpdocIndentFixer, PhpdocNoEmptyReturnFixer, PhpdocScalarFixer, PhpdocToCommentFixer, PhpdocTypesFixer.
     */
    public function getPriority(): int
    {
        return -2;
    }

    /**
     * {@inheritdoc}
     */
    protected function createConfigurationDefinition(): FixerConfigurationResolverInterface
    {
        $style = new FixerOptionBuilder('style', 'Style in which annotations in PHPDoc should be ordered.');
        $style
            ->setAllowedTypes(['string'])
            ->setAllowedValues([self::ORDER_STYLE_PHPCS, self::ORDER_STYLE_SYMFONY])
            ->setDefault(self::ORDER_STYLE_PHPCS)
        ;

        return new FixerConfigurationResolver([$style->getOption()]);
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

            // assuming we sort three annotation tags
            [$first, $middle, $last] = $this->order;
            $content = $token->getContent();
            // move $first to start, $last to end, leave $middle in the middle
            $content = $this->moveAnnotationsBefore($first, [$middle, $last], $content);
            // we're parsing the content again to make sure the internal
            // state of the docblock is correct after the modifications
            $content = $this->moveAnnotationsAfter($last, [$first, $middle], $content);
            // persist the content at the end
            $tokens[$index] = new Token([T_DOC_COMMENT, $content]);
        }
    }

    /**
     * Move all given annotations in before given set of annotations.
     *
     * @param string   $move   Tag of annotations that should be moved
     * @param string[] $before Tags of annotations that should moved annotations be placed before
     */
    private function moveAnnotationsBefore(string $move, array $before, string $content): string
    {
        $doc = new DocBlock($content);
        $toBeMoved = $doc->getAnnotationsOfType($move);

        // nothing to do if there are no annotations to be moved
        if (0 === \count($toBeMoved)) {
            return $content;
        }

        $others = $doc->getAnnotationsOfType($before);

        if (0 === \count($others)) {
            return $content;
        }

        // get the index of the final line of the final toBoMoved annotation
        $end = end($toBeMoved)->getEnd();

        $line = $doc->getLine($end);

        // move stuff about if required
        foreach ($others as $other) {
            if ($other->getStart() < $end) {
                // we're doing this to maintain the original line indices
                $line->setContent($line->getContent().$other->getContent());
                $other->remove();
            }
        }

        return $doc->getContent();
    }

    /**
     * Move all given annotations after given set of annotations.
     *
     * @param string   $move  Tag of annotations that should be moved
     * @param string[] $after Tags of annotations that should moved annotations be placed after
     */
    private function moveAnnotationsAfter(string $move, array $after, string $content): string
    {
        $doc = new DocBlock($content);
        $toBeMoved = $doc->getAnnotationsOfType($move);

        // nothing to do if there are no annotations to be moved
        if (0 === \count($toBeMoved)) {
            return $content;
        }

        $others = $doc->getAnnotationsOfType($after);

        // nothing to do if there are no other annotations
        if (0 === \count($others)) {
            return $content;
        }

        // get the index of the first line of the first toBeMoved annotation
        $start = $toBeMoved[0]->getStart();
        $line = $doc->getLine($start);

        // move stuff about if required
        foreach (array_reverse($others) as $other) {
            if ($other->getEnd() > $start) {
                // we're doing this to maintain the original line indices
                $line->setContent($other->getContent().$line->getContent());
                $other->remove();
            }
        }

        return $doc->getContent();
    }
}
