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

namespace PhpCsFixer\Fixer\ClassNotation;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\Fixer\ConfigurableFixerInterface;
use PhpCsFixer\Fixer\WhitespacesAwareFixerInterface;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolver;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolverInterface;
use PhpCsFixer\FixerConfiguration\FixerOptionBuilder;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\FixerDefinition\VersionSpecification;
use PhpCsFixer\FixerDefinition\VersionSpecificCodeSample;
use PhpCsFixer\Preg;
use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use PhpCsFixer\Tokenizer\TokensAnalyzer;
use PhpCsFixer\Utils;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;

/**
 * Make sure there is one blank line above and below class elements.
 *
 * The exception is when an element is the first or last item in a 'classy'.
 */
final class ClassAttributesSeparationFixer extends AbstractFixer implements ConfigurableFixerInterface, WhitespacesAwareFixerInterface
{
    /**
     * @internal
     */
    public const SPACING_NONE = 'none';

    /**
     * @internal
     */
    public const SPACING_ONE = 'one';

    private const SPACING_ONLY_IF_META = 'only_if_meta';

    /**
     * @var array<string, string>
     */
    private array $classElementTypes = [];

    public function configure(array $configuration): void
    {
        parent::configure($configuration);

        $this->classElementTypes = []; // reset previous configuration

        foreach ($this->configuration['elements'] as $elementType => $spacing) {
            $this->classElementTypes[$elementType] = $spacing;
        }
    }

    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(
            'Class, trait and interface elements must be separated with one or none blank line.',
            [
                new CodeSample(
                    '<?php
final class Sample
{
    protected function foo()
    {
    }
    protected function bar()
    {
    }


}
'
                ),
                new CodeSample(
                    '<?php
class Sample
{private $a; // foo
    /** second in a hour */
    private $b;
}
',
                    ['elements' => ['property' => self::SPACING_ONE]]
                ),
                new CodeSample(
                    '<?php
class Sample
{
    const A = 1;
    /** seconds in some hours */
    const B = 3600;
}
',
                    ['elements' => ['const' => self::SPACING_ONE]]
                ),
                new CodeSample(
                    '<?php
class Sample
{
    /** @var int */
    const SECOND = 1;
    /** @var int */
    const MINUTE = 60;

    const HOUR = 3600;

    const DAY = 86400;
}
',
                    ['elements' => ['const' => self::SPACING_ONLY_IF_META]]
                ),
                new VersionSpecificCodeSample(
                    '<?php
class Sample
{
    public $a;
    #[SetUp]
    public $b;
    /** @var string */
    public $c;
    /** @internal */
    #[Assert\String()]
    public $d;

    public $e;
}
',
                    new VersionSpecification(8_00_00),
                    ['elements' => ['property' => self::SPACING_ONLY_IF_META]]
                ),
            ]
        );
    }

    /**
     * {@inheritdoc}
     *
     * Must run before BracesFixer, IndentationTypeFixer, NoExtraBlankLinesFixer, StatementIndentationFixer.
     * Must run after OrderedClassElementsFixer, SingleClassElementPerStatementFixer, VisibilityRequiredFixer.
     */
    public function getPriority(): int
    {
        return 55;
    }

    public function isCandidate(Tokens $tokens): bool
    {
        return $tokens->isAnyTokenKindsFound(Token::getClassyTokenKinds());
    }

    protected function applyFix(\SplFileInfo $file, Tokens $tokens): void
    {
        foreach ($this->getElementsByClass($tokens) as $class) {
            $elements = $class['elements'];
            $elementCount = \count($elements);

            if (0 === $elementCount) {
                continue;
            }

            if (isset($this->classElementTypes[$elements[0]['type']])) {
                $this->fixSpaceBelowClassElement($tokens, $class);
                $this->fixSpaceAboveClassElement($tokens, $class, 0);
            }

            for ($index = 1; $index < $elementCount; ++$index) {
                if (isset($this->classElementTypes[$elements[$index]['type']])) {
                    $this->fixSpaceAboveClassElement($tokens, $class, $index);
                }
            }
        }
    }

    protected function createConfigurationDefinition(): FixerConfigurationResolverInterface
    {
        return new FixerConfigurationResolver([
            (new FixerOptionBuilder('elements', 'Dictionary of `const|method|property|trait_import|case` => `none|one|only_if_meta` values.'))
                ->setAllowedTypes(['array'])
                ->setAllowedValues([static function (array $option): bool {
                    foreach ($option as $type => $spacing) {
                        $supportedTypes = ['const', 'method', 'property', 'trait_import', 'case'];

                        if (!\in_array($type, $supportedTypes, true)) {
                            throw new InvalidOptionsException(
                                sprintf(
                                    'Unexpected element type, expected any of %s, got "%s".',
                                    Utils::naturalLanguageJoin($supportedTypes),
                                    \gettype($type).'#'.$type
                                )
                            );
                        }

                        $supportedSpacings = [self::SPACING_NONE, self::SPACING_ONE, self::SPACING_ONLY_IF_META];

                        if (!\in_array($spacing, $supportedSpacings, true)) {
                            throw new InvalidOptionsException(
                                sprintf(
                                    'Unexpected spacing for element type "%s", expected any of %s, got "%s".',
                                    $spacing,
                                    Utils::naturalLanguageJoin($supportedSpacings),
                                    \is_object($spacing) ? \get_class($spacing) : (null === $spacing ? 'null' : \gettype($spacing).'#'.$spacing)
                                )
                            );
                        }
                    }

                    return true;
                }])
                ->setDefault([
                    'const' => self::SPACING_ONE,
                    'method' => self::SPACING_ONE,
                    'property' => self::SPACING_ONE,
                    'trait_import' => self::SPACING_NONE,
                    'case' => self::SPACING_NONE,
                ])
                ->getOption(),
        ]);
    }

    /**
     * Fix spacing above an element of a class, interface or trait.
     *
     * Deals with comments, PHPDocs and spaces above the element with respect to the position of the
     * element within the class, interface or trait.
     *
     * @param array{
     *     index: int,
     *     open: int,
     *     close: int,
     *     elements: non-empty-list<array{token: Token, type: string, index: int, start: int, end: int}>
     * } $class
     */
    private function fixSpaceAboveClassElement(Tokens $tokens, array $class, int $elementIndex): void
    {
        $element = $class['elements'][$elementIndex];
        $elementAboveEnd = isset($class['elements'][$elementIndex + 1]) ? $class['elements'][$elementIndex + 1]['end'] : 0;
        $nonWhiteAbove = $tokens->getPrevNonWhitespace($element['start']);

        // element is directly after class open brace
        if ($nonWhiteAbove === $class['open']) {
            $this->correctLineBreaks($tokens, $nonWhiteAbove, $element['start'], 1);

            return;
        }

        // deal with comments above an element
        if ($tokens[$nonWhiteAbove]->isGivenKind(T_COMMENT)) {
            // check if the comment belongs to the previous element
            if ($elementAboveEnd === $nonWhiteAbove) {
                $this->correctLineBreaks($tokens, $nonWhiteAbove, $element['start'], $this->determineRequiredLineCount($tokens, $class, $elementIndex));

                return;
            }

            // more than one line break, always bring it back to 2 line breaks between the element start and what is above it
            if ($tokens[$nonWhiteAbove + 1]->isWhitespace() && substr_count($tokens[$nonWhiteAbove + 1]->getContent(), "\n") > 1) {
                $this->correctLineBreaks($tokens, $nonWhiteAbove, $element['start'], 2);

                return;
            }

            // there are 2 cases:
            if (
                1 === $element['start'] - $nonWhiteAbove
                || $tokens[$nonWhiteAbove - 1]->isWhitespace() && substr_count($tokens[$nonWhiteAbove - 1]->getContent(), "\n") > 0
                || $tokens[$nonWhiteAbove + 1]->isWhitespace() && substr_count($tokens[$nonWhiteAbove + 1]->getContent(), "\n") > 0
            ) {
                // 1. The comment is meant for the element (although not a PHPDoc),
                //    make sure there is one line break between the element and the comment...
                $this->correctLineBreaks($tokens, $nonWhiteAbove, $element['start'], 1);
                //    ... and make sure there is blank line above the comment (with the exception when it is directly after a class opening)
                $nonWhiteAbove = $this->findCommentBlockStart($tokens, $nonWhiteAbove, $elementAboveEnd);
                $nonWhiteAboveComment = $tokens->getPrevNonWhitespace($nonWhiteAbove);

                $this->correctLineBreaks($tokens, $nonWhiteAboveComment, $nonWhiteAbove, $nonWhiteAboveComment === $class['open'] ? 1 : 2);
            } else {
                // 2. The comment belongs to the code above the element,
                //    make sure there is a blank line above the element (i.e. 2 line breaks)
                $this->correctLineBreaks($tokens, $nonWhiteAbove, $element['start'], 2);
            }

            return;
        }

        // deal with element with a PHPDoc/attribute above it
        if ($tokens[$nonWhiteAbove]->isGivenKind([T_DOC_COMMENT, CT::T_ATTRIBUTE_CLOSE])) {
            // there should be one linebreak between the element and the attribute above it
            $this->correctLineBreaks($tokens, $nonWhiteAbove, $element['start'], 1);

            // make sure there is blank line above the comment (with the exception when it is directly after a class opening)
            $nonWhiteAbove = $this->findCommentBlockStart($tokens, $nonWhiteAbove, $elementAboveEnd);
            $nonWhiteAboveComment = $tokens->getPrevNonWhitespace($nonWhiteAbove);

            $this->correctLineBreaks($tokens, $nonWhiteAboveComment, $nonWhiteAbove, $nonWhiteAboveComment === $class['open'] ? 1 : 2);

            return;
        }

        $this->correctLineBreaks($tokens, $nonWhiteAbove, $element['start'], $this->determineRequiredLineCount($tokens, $class, $elementIndex));
    }

    private function determineRequiredLineCount(Tokens $tokens, array $class, int $elementIndex): int
    {
        $type = $class['elements'][$elementIndex]['type'];
        $spacing = $this->classElementTypes[$type];

        if (self::SPACING_ONE === $spacing) {
            return 2;
        }

        if (self::SPACING_NONE === $spacing) {
            if (!isset($class['elements'][$elementIndex + 1])) {
                return 1;
            }

            $aboveElement = $class['elements'][$elementIndex + 1];

            if ($aboveElement['type'] !== $type) {
                return 2;
            }

            $aboveElementDocCandidateIndex = $tokens->getPrevNonWhitespace($aboveElement['start']);

            return $tokens[$aboveElementDocCandidateIndex]->isGivenKind([T_DOC_COMMENT, CT::T_ATTRIBUTE_CLOSE]) ? 2 : 1;
        }

        if (self::SPACING_ONLY_IF_META === $spacing) {
            $aboveElementDocCandidateIndex = $tokens->getPrevNonWhitespace($class['elements'][$elementIndex]['start']);

            return $tokens[$aboveElementDocCandidateIndex]->isGivenKind([T_DOC_COMMENT, CT::T_ATTRIBUTE_CLOSE]) ? 2 : 1;
        }

        throw new \RuntimeException(sprintf('Unknown spacing "%s".', $spacing));
    }

    /**
     * @param array{
     *     index: int,
     *     open: int,
     *     close: int,
     *     elements: non-empty-list<array{token: Token, type: string, index: int, start: int, end: int}>
     * } $class
     */
    private function fixSpaceBelowClassElement(Tokens $tokens, array $class): void
    {
        $element = $class['elements'][0];

        // if this is last element fix; fix to the class end `}` here if appropriate
        if ($class['close'] === $tokens->getNextNonWhitespace($element['end'])) {
            $this->correctLineBreaks($tokens, $element['end'], $class['close'], 1);
        }
    }

    private function correctLineBreaks(Tokens $tokens, int $startIndex, int $endIndex, int $reqLineCount): void
    {
        $lineEnding = $this->whitespacesConfig->getLineEnding();

        ++$startIndex;
        $numbOfWhiteTokens = $endIndex - $startIndex;

        if (0 === $numbOfWhiteTokens) {
            $tokens->insertAt($startIndex, new Token([T_WHITESPACE, str_repeat($lineEnding, $reqLineCount)]));

            return;
        }

        $lineBreakCount = $this->getLineBreakCount($tokens, $startIndex, $endIndex);

        if ($reqLineCount === $lineBreakCount) {
            return;
        }

        if ($lineBreakCount < $reqLineCount) {
            $tokens[$startIndex] = new Token([
                T_WHITESPACE,
                str_repeat($lineEnding, $reqLineCount - $lineBreakCount).$tokens[$startIndex]->getContent(),
            ]);

            return;
        }

        // $lineCount = > $reqLineCount : check the one Token case first since this one will be true most of the time
        if (1 === $numbOfWhiteTokens) {
            $tokens[$startIndex] = new Token([
                T_WHITESPACE,
                Preg::replace('/\r\n|\n/', '', $tokens[$startIndex]->getContent(), $lineBreakCount - $reqLineCount),
            ]);

            return;
        }

        // $numbOfWhiteTokens = > 1
        $toReplaceCount = $lineBreakCount - $reqLineCount;

        for ($i = $startIndex; $i < $endIndex && $toReplaceCount > 0; ++$i) {
            $tokenLineCount = substr_count($tokens[$i]->getContent(), "\n");

            if ($tokenLineCount > 0) {
                $tokens[$i] = new Token([
                    T_WHITESPACE,
                    Preg::replace('/\r\n|\n/', '', $tokens[$i]->getContent(), min($toReplaceCount, $tokenLineCount)),
                ]);
                $toReplaceCount -= $tokenLineCount;
            }
        }
    }

    private function getLineBreakCount(Tokens $tokens, int $startIndex, int $endIndex): int
    {
        $lineCount = 0;

        for ($i = $startIndex; $i < $endIndex; ++$i) {
            $lineCount += substr_count($tokens[$i]->getContent(), "\n");
        }

        return $lineCount;
    }

    private function findCommentBlockStart(Tokens $tokens, int $start, int $elementAboveEnd): int
    {
        for ($i = $start; $i > $elementAboveEnd; --$i) {
            if ($tokens[$i]->isGivenKind(CT::T_ATTRIBUTE_CLOSE)) {
                $start = $i = $tokens->findBlockStart(Tokens::BLOCK_TYPE_ATTRIBUTE, $i);

                continue;
            }

            if ($tokens[$i]->isComment()) {
                $start = $i;

                continue;
            }

            if (!$tokens[$i]->isWhitespace() || $this->getLineBreakCount($tokens, $i, $i + 1) > 1) {
                break;
            }
        }

        return $start;
    }

    /**
     * @TODO Introduce proper DTO instead of an array
     *
     * @return \Generator<array{
     *     index: int,
     *     open: int,
     *     close: int,
     *     elements: non-empty-list<array{token: Token, type: string, index: int, start: int, end: int}>
     * }>
     */
    private function getElementsByClass(Tokens $tokens): \Generator
    {
        $tokensAnalyzer = new TokensAnalyzer($tokens);
        $class = $classIndex = false;
        $elements = $tokensAnalyzer->getClassyElements();

        for (end($elements);; prev($elements)) {
            $index = key($elements);

            if (null === $index) {
                break;
            }

            $element = current($elements);
            $element['index'] = $index;

            if ($element['classIndex'] !== $classIndex) {
                if (false !== $class) {
                    yield $class;
                }

                $classIndex = $element['classIndex'];
                $classOpen = $tokens->getNextTokenOfKind($classIndex, ['{']);
                $classEnd = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_CURLY_BRACE, $classOpen);
                $class = [
                    'index' => $classIndex,
                    'open' => $classOpen,
                    'close' => $classEnd,
                    'elements' => [],
                ];
            }

            unset($element['classIndex']);
            $element['start'] = $this->getFirstTokenIndexOfClassElement($tokens, $class, $element);
            $element['end'] = $this->getLastTokenIndexOfClassElement($tokens, $class, $element, $tokensAnalyzer);

            $class['elements'][] = $element; // reset the key by design
        }

        if (false !== $class) {
            yield $class;
        }
    }

    private function getFirstTokenIndexOfClassElement(Tokens $tokens, array $class, array $element): int
    {
        $modifierTypes = [T_PRIVATE, T_PROTECTED, T_PUBLIC, T_ABSTRACT, T_FINAL, T_STATIC, T_STRING, T_NS_SEPARATOR, T_VAR, CT::T_NULLABLE_TYPE, CT::T_ARRAY_TYPEHINT, CT::T_TYPE_ALTERNATION, CT::T_TYPE_INTERSECTION];

        if (\defined('T_READONLY')) { // @TODO: drop condition when PHP 8.1+ is required
            $modifierTypes[] = T_READONLY;
        }

        $firstElementAttributeIndex = $element['index'];

        do {
            $nonWhiteAbove = $tokens->getPrevMeaningfulToken($firstElementAttributeIndex);

            if (null !== $nonWhiteAbove && $tokens[$nonWhiteAbove]->isGivenKind($modifierTypes)) {
                $firstElementAttributeIndex = $nonWhiteAbove;
            } else {
                break;
            }
        } while ($firstElementAttributeIndex > $class['open']);

        return $firstElementAttributeIndex;
    }

    // including trailing single line comments if belonging to the class element
    private function getLastTokenIndexOfClassElement(Tokens $tokens, array $class, array $element, TokensAnalyzer $tokensAnalyzer): int
    {
        // find last token of the element
        if ('method' === $element['type'] && !$tokens[$class['index']]->isGivenKind(T_INTERFACE)) {
            $attributes = $tokensAnalyzer->getMethodAttributes($element['index']);

            if (true === $attributes['abstract']) {
                $elementEndIndex = $tokens->getNextTokenOfKind($element['index'], [';']);
            } else {
                $elementEndIndex = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_CURLY_BRACE, $tokens->getNextTokenOfKind($element['index'], ['{']));
            }
        } elseif ('trait_import' === $element['type']) {
            $elementEndIndex = $element['index'];

            do {
                $elementEndIndex = $tokens->getNextMeaningfulToken($elementEndIndex);
            } while ($tokens[$elementEndIndex]->isGivenKind([T_STRING, T_NS_SEPARATOR]) || $tokens[$elementEndIndex]->equals(','));

            if (!$tokens[$elementEndIndex]->equals(';')) {
                $elementEndIndex = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_CURLY_BRACE, $tokens->getNextTokenOfKind($element['index'], ['{']));
            }
        } else { // 'const', 'property', enum-'case', or 'method' of an interface
            $elementEndIndex = $tokens->getNextTokenOfKind($element['index'], [';']);
        }

        $singleLineElement = true;

        for ($i = $element['index'] + 1; $i < $elementEndIndex; ++$i) {
            if (str_contains($tokens[$i]->getContent(), "\n")) {
                $singleLineElement = false;

                break;
            }
        }

        if ($singleLineElement) {
            while (true) {
                $nextToken = $tokens[$elementEndIndex + 1];

                if (($nextToken->isComment() || $nextToken->isWhitespace()) && !str_contains($nextToken->getContent(), "\n")) {
                    ++$elementEndIndex;
                } else {
                    break;
                }
            }

            if ($tokens[$elementEndIndex]->isWhitespace()) {
                $elementEndIndex = $tokens->getPrevNonWhitespace($elementEndIndex);
            }
        }

        return $elementEndIndex;
    }
}
