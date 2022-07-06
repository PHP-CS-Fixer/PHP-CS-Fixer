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
use PhpCsFixer\DocBlock\TypeExpression;
use PhpCsFixer\Fixer\WhitespacesAwareFixerInterface;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Preg;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @author Fabien Potencier <fabien@symfony.com>
 * @author Jordi Boggiano <j.boggiano@seld.be>
 * @author Sebastiaan Stok <s.stok@rollerscapes.net>
 * @author Graham Campbell <graham@alt-three.com>
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 * @author Jakub Kwaśniewski <jakub@zero-85.pl>
 */
final class LaravelPhpdocAlignmentFixer extends AbstractFixer implements WhitespacesAwareFixerInterface
{
    private const ALIGNABLE_TAGS = [
        'param',
        'property',
        'property-read',
        'property-write',
        'return',
        'throws',
        'type',
        'var',
        'method',
    ];

    private const TAGS_WITH_NAME = [
        'param',
        'property',
    ];

    private const TAGS_WITH_METHOD_SIGNATURE = [
        'method',
    ];

    /**
     * @var string
     */
    private $regex;

    /**
     * @var string
     */
    private $regexCommentLine;

    public function __construct()
    {
        parent::__construct();
        $this->prepareConfig();
    }

    /**
     * {@inheritdoc}
     */
    public function getDefinition(): FixerDefinitionInterface
    {
        $code = <<<'EOF'
<?php
/**
 * @param  EngineInterface $templating
 * @param string      $format
 * @param  int  $code       an HTTP response status code
 * @param    bool         $debug
 * @param  mixed    &$reference     a parameter passed by reference
 */

EOF;

        return new FixerDefinition(
            'All items of the given phpdoc tags must be either left-aligned separated with one space except `@param` tag which has to be separated with two.',
            [
                new CodeSample($code),
            ]
        );
    }

    /**
     * {@inheritdoc}
     *
     * Must run before PhpdocAlignFixer.
     * Must run after AlignMultilineCommentFixer, CommentToPhpdocFixer, PhpdocIndentFixer, PhpdocScalarFixer, PhpdocToCommentFixer, PhpdocTypesFixer.
     */
    public function getPriority(): int
    {
        /*
         * Should be run after all other docblock fixers. This because they
         * modify other annotations to change their type and or separation
         * which totally change the behavior of this fixer. It's important that
         * annotations are of the correct type, and are grouped correctly
         * before running this fixer.
         */
        return -41;
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

            $content = $token->getContent();
            $docBlock = new DocBlock($content);
            $this->fixDocBlock($docBlock);
            $newContent = $docBlock->getContent();
            if ($newContent !== $content) {
                $tokens[$index] = new Token([T_DOC_COMMENT, $newContent]);
            }
        }
    }

    private function prepareConfig(): void
    {
        $tagsWithNameToAlign = self::TAGS_WITH_NAME;
        $tagsWithMethodSignatureToAlign = self::TAGS_WITH_METHOD_SIGNATURE;
        $tagsWithoutNameToAlign = array_diff(self::ALIGNABLE_TAGS, $tagsWithNameToAlign, $tagsWithMethodSignatureToAlign);
        $types = [];

        $indent = '(?P<indent>(?:\ {2}|\t)*)';

        // e.g. @param <hint> <$var>
        if ([] !== $tagsWithNameToAlign) {
            $types[] = '(?P<tag>'.implode('|', $tagsWithNameToAlign).')\s+(?P<hint>(?:'.TypeExpression::REGEX_TYPES.')?)\s+(?P<var>(?:&|\.{3})?\$\S+)';
        }

        // e.g. @return <hint>
        if ([] !== $tagsWithoutNameToAlign) {
            $types[] = '(?P<tag2>'.implode('|', $tagsWithoutNameToAlign).')\s+(?P<hint2>(?:'.TypeExpression::REGEX_TYPES.')?)';
        }

        // e.g. @method <hint> <signature>
        if ([] !== $tagsWithMethodSignatureToAlign) {
            $types[] = '(?P<tag3>'.implode('|', $tagsWithMethodSignatureToAlign).')(\s+(?P<hint3>[^\s(]+)|)\s+(?P<signature>.+\))';
        }

        // optional <desc>
        $desc = '(?:\s+(?P<desc>\V*))';

        $this->regex = '/^'.$indent.'\ \*\ @(?J)(?:'.implode('|', $types).')'.$desc.'\s*$/ux';
        $this->regexCommentLine = '/^'.$indent.' \*(?! @)(?:\s+(?P<desc>\V+))(?<!\*\/)\r?$/u';
    }

    private function fixDocBlock(DocBlock $docBlock): void
    {
        $lineEnding = $this->whitespacesConfig->getLineEnding();

        for ($i = 0, $l = \count($docBlock->getLines()); $i < $l; ++$i) {
            $items = [];
            $matches = $this->getMatches($docBlock->getLine($i)->getContent());

            if (null === $matches) {
                continue;
            }

            $current = $i;
            $items[] = $matches;

            while (true) {
                if (null === $docBlock->getLine(++$i)) {
                    break 2;
                }

                $matches = $this->getMatches($docBlock->getLine($i)->getContent(), true);

                if (null === $matches) {
                    break;
                }

                $items[] = $matches;
            }

            $currTag = null;

            // update
            foreach ($items as $j => $item) {
                if (null === $item['tag']) {
                    if ('@' === $item['desc'][0]) {
                        $docBlock->getLine($current + $j)->setContent($item['indent'].' * '.$item['desc'].$lineEnding);

                        continue;
                    }

                    $extraIndent = 2;

                    if (\in_array($currTag, self::TAGS_WITH_NAME, true) || \in_array($currTag, self::TAGS_WITH_METHOD_SIGNATURE, true)) {
                        $extraIndent = 3;
                    }

                    $line =
                        $item['indent']
                        .' *  '
                        .$this->getIndent(
                            $this->getLeftAlignedDescriptionIndent($items, $j)
                        )
                        .$item['desc']
                        .$lineEnding;

                    $docBlock->getLine($current + $j)->setContent($line);

                    continue;
                }

                $currTag = $item['tag'];

                $indent = $this->getIndent($this->spaceForTag($currTag));

                $line =
                    $item['indent']
                    .' * @'
                    .$item['tag']
                    .(!empty($item['hint']) ? $indent.$item['hint'] : '')
                    .(!empty($item['var']) ? $indent.$item['var'] : '')
                    .(!empty($item['desc']) ? $indent.$item['desc'] : '')
                    .$lineEnding;

                $docBlock->getLine($current + $j)->setContent($line);
            }
        }
    }

    private function spaceForTag(?string $tag): int
    {
        return 'param' === $tag ? 2 : 1;
    }

    private function dumpMatches($i, $l, $matches): void
    {
        print_r("LINE: {$i} of {$l}\n");
        echo $matches[0];
        print_r(array_filter(
            $matches,
            function ($val, $key) {
                return !\is_int($key) && !empty($val);
            },
            ARRAY_FILTER_USE_BOTH
        ));
    }

    /**
     * @return null|array<string, null|string>
     */
    private function getMatches(string $line, bool $matchCommentOnly = false): ?array
    {
        if (Preg::match($this->regex, $line, $matches)) {
            if (!empty($matches['tag2'])) {
                $matches['tag'] = $matches['tag2'];
                $matches['hint'] = $matches['hint2'];
                $matches['var'] = '';
            }

            if (!empty($matches['tag3'])) {
                $matches['tag'] = $matches['tag3'];
                $matches['hint'] = $matches['hint3'];
                $matches['var'] = $matches['signature'];
            }

            if (isset($matches['hint'])) {
                $matches['hint'] = trim($matches['hint']);
            }

            return $matches;
        }

        if ($matchCommentOnly && Preg::match($this->regexCommentLine, $line, $matches)) {
            $matches['tag'] = null;
            $matches['var'] = '';
            $matches['hint'] = '';

            return $matches;
        }

        return null;
    }

    private function getIndent(int $leftAlignIndent = 1): string
    {
        return str_repeat(' ', $leftAlignIndent);
    }

    /**
     * @param array[] $items
     */
    private function getLeftAlignedDescriptionIndent(array $items, int $index): int
    {
        // Find last tagged line:
        $item = null;
        for (; $index >= 0; --$index) {
            $item = $items[$index];
            if (null !== $item['tag']) {
                break;
            }
        }

        // No last tag found — no indent:
        if (null === $item) {
            return 0;
        }

        $space = $this->spaceForTag($item['tag']);

        // Indent according to existing values:
        return
            $this->getSentenceIndent($item['tag'], $space) +
            $this->getSentenceIndent($item['hint'], $space) +
            $this->getSentenceIndent($item['var'], $space);
    }

    /**
     * Get indent for sentence.
     */
    private function getSentenceIndent(?string $sentence, int $space = 1): int
    {
        if (null === $sentence) {
            return 0;
        }

        $length = \strlen($sentence);

        return 0 === $length ? 0 : $length + $space;
    }
}
