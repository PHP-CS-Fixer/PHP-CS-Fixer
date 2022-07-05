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

namespace PhpCsFixer\Fixer\Basic;

use PhpCsFixer\AbstractProxyFixer;
use PhpCsFixer\Fixer\ConfigurableFixerInterface;
use PhpCsFixer\Fixer\ControlStructure\ControlStructureBracesFixer;
use PhpCsFixer\Fixer\ControlStructure\ControlStructureContinuationPositionFixer;
use PhpCsFixer\Fixer\LanguageConstruct\DeclareParenthesesFixer;
use PhpCsFixer\Fixer\Whitespace\StatementIndentationFixer;
use PhpCsFixer\Fixer\WhitespacesAwareFixerInterface;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolver;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolverInterface;
use PhpCsFixer\FixerConfiguration\FixerOptionBuilder;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Preg;
use PhpCsFixer\Tokenizer\Analyzer\WhitespacesAnalyzer;
use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use PhpCsFixer\Tokenizer\TokensAnalyzer;

/**
 * Fixer for rules defined in PSR2 ¶4.1, ¶4.4, ¶5.
 *
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
final class BracesFixer extends AbstractProxyFixer implements ConfigurableFixerInterface, WhitespacesAwareFixerInterface
{
    /**
     * @internal
     */
    public const LINE_NEXT = 'next';

    /**
     * @internal
     */
    public const LINE_SAME = 'same';

    /**
     * @var null|CurlyBracesPositionFixer
     */
    private $curlyBracesPositionFixer;

    /**
     * @var null|ControlStructureContinuationPositionFixer
     */
    private $controlStructureContinuationPositionFixer;

    /**
     * {@inheritdoc}
     */
    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(
            'The body of each structure MUST be enclosed by braces. Braces should be properly placed. Body of braces should be properly indented.',
            [
                new CodeSample(
                    '<?php

class Foo {
    public function bar($baz) {
        if ($baz = 900) echo "Hello!";

        if ($baz = 9000)
            echo "Wait!";

        if ($baz == true)
        {
            echo "Why?";
        }
        else
        {
            echo "Ha?";
        }

        if (is_array($baz))
            foreach ($baz as $b)
            {
                echo $b;
            }
    }
}
'
                ),
                new CodeSample(
                    '<?php
$positive = function ($item) { return $item >= 0; };
$negative = function ($item) {
                return $item < 0; };
',
                    ['allow_single_line_closure' => true]
                ),
                new CodeSample(
                    '<?php

class Foo
{
    public function bar($baz)
    {
        if ($baz = 900) echo "Hello!";

        if ($baz = 9000)
            echo "Wait!";

        if ($baz == true)
        {
            echo "Why?";
        }
        else
        {
            echo "Ha?";
        }

        if (is_array($baz))
            foreach ($baz as $b)
            {
                echo $b;
            }
    }
}
',
                    ['position_after_functions_and_oop_constructs' => self::LINE_SAME]
                ),
            ]
        );
    }

    /**
     * {@inheritdoc}
     *
     * Must run before HeredocIndentationFixer, MethodChainingIndentationFixer.
     * Must run after ClassAttributesSeparationFixer, ClassDefinitionFixer, EmptyLoopBodyFixer, LineEndingFixer, NoAlternativeSyntaxFixer, NoEmptyStatementFixer, NoUselessElseFixer, SingleLineThrowFixer, SingleSpaceAfterConstructFixer, SingleTraitInsertPerStatementFixer.
     */
    public function getPriority(): int
    {
        return 35;
    }

    public function configure(array $configuration = null): void
    {
        parent::configure($configuration);

        $this->getCurlyBracesPositionFixer()->configure([
            'control_structures_opening_brace' => $this->translatePositionOption($this->configuration['position_after_control_structures']),
            'functions_opening_brace' => $this->translatePositionOption($this->configuration['position_after_functions_and_oop_constructs']),
            'anonymous_functions_opening_brace' => $this->translatePositionOption($this->configuration['position_after_anonymous_constructs']),
            'classes_opening_brace' => $this->translatePositionOption($this->configuration['position_after_functions_and_oop_constructs']),
            'anonymous_classes_opening_brace' => $this->translatePositionOption($this->configuration['position_after_anonymous_constructs']),
            'allow_single_line_empty_anonymous_classes' => $this->configuration['allow_single_line_anonymous_class_with_empty_body'],
            'allow_single_line_anonymous_functions' => $this->configuration['allow_single_line_closure'],
        ]);

        $this->getControlStructureContinuationPositionFixer()->configure([
            'position' => self::LINE_NEXT === $this->configuration['position_after_control_structures']
                ? ControlStructureContinuationPositionFixer::NEXT_LINE
                : ControlStructureContinuationPositionFixer::SAME_LINE,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function isCandidate(Tokens $tokens): bool
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    protected function applyFix(\SplFileInfo $file, Tokens $tokens): void
    {
        $this->fixCommentBeforeBrace($tokens);
        $this->proxyFixers['control_structure_braces']->fix($file, $tokens);
        $this->proxyFixers['no_multiple_statements_per_line']->fix($file, $tokens);
        $this->fixIndents($tokens);
        $this->fixSpaceAroundToken($tokens);
        $this->proxyFixers['control_structure_continuation_position']->fix($file, $tokens);
        $this->proxyFixers['curly_braces_position']->fix($file, $tokens);
        $this->proxyFixers['declare_parentheses']->fix($file, $tokens);
        $this->proxyFixers['statement_indentation']->fix($file, $tokens);
    }

    /**
     * {@inheritdoc}
     */
    protected function createConfigurationDefinition(): FixerConfigurationResolverInterface
    {
        return new FixerConfigurationResolver([
            (new FixerOptionBuilder('allow_single_line_anonymous_class_with_empty_body', 'Whether single line anonymous class with empty body notation should be allowed.'))
                ->setAllowedTypes(['bool'])
                ->setDefault(false)
                ->getOption(),
            (new FixerOptionBuilder('allow_single_line_closure', 'Whether single line lambda notation should be allowed.'))
                ->setAllowedTypes(['bool'])
                ->setDefault(false)
                ->getOption(),
            (new FixerOptionBuilder('position_after_functions_and_oop_constructs', 'whether the opening brace should be placed on "next" or "same" line after classy constructs (non-anonymous classes, interfaces, traits, methods and non-lambda functions).'))
                ->setAllowedValues([self::LINE_NEXT, self::LINE_SAME])
                ->setDefault(self::LINE_NEXT)
                ->getOption(),
            (new FixerOptionBuilder('position_after_control_structures', 'whether the opening brace should be placed on "next" or "same" line after control structures.'))
                ->setAllowedValues([self::LINE_NEXT, self::LINE_SAME])
                ->setDefault(self::LINE_SAME)
                ->getOption(),
            (new FixerOptionBuilder('position_after_anonymous_constructs', 'whether the opening brace should be placed on "next" or "same" line after anonymous constructs (anonymous classes and lambda functions).'))
                ->setAllowedValues([self::LINE_NEXT, self::LINE_SAME])
                ->setDefault(self::LINE_SAME)
                ->getOption(),
        ]);
    }

    protected function createProxyFixers(): array
    {
        return [
            new ControlStructureBracesFixer(),
            $this->getCurlyBracesPositionFixer(),
            $this->getControlStructureContinuationPositionFixer(),
            new DeclareParenthesesFixer(),
            new NoMultipleStatementsPerLineFixer(),
            new StatementIndentationFixer(true),
        ];
    }

    private function fixCommentBeforeBrace(Tokens $tokens): void
    {
        $tokensAnalyzer = new TokensAnalyzer($tokens);
        $controlTokens = $this->getControlTokens();

        for ($index = $tokens->count() - 1; 0 <= $index; --$index) {
            $token = $tokens[$index];

            if ($token->isGivenKind($controlTokens)) {
                $prevIndex = $this->findParenthesisEnd($tokens, $index);
            } elseif (
                ($token->isGivenKind(T_FUNCTION) && $tokensAnalyzer->isLambda($index))
                || ($token->isGivenKind(T_CLASS) && $tokensAnalyzer->isAnonymousClass($index))
            ) {
                $prevIndex = $tokens->getNextTokenOfKind($index, ['{']);
                $prevIndex = $tokens->getPrevMeaningfulToken($prevIndex);
            } else {
                continue;
            }

            $commentIndex = $tokens->getNextNonWhitespace($prevIndex);
            $commentToken = $tokens[$commentIndex];

            if (!$commentToken->isGivenKind(T_COMMENT) || str_starts_with($commentToken->getContent(), '/*')) {
                continue;
            }

            $braceIndex = $tokens->getNextMeaningfulToken($commentIndex);
            $braceToken = $tokens[$braceIndex];

            if (!$braceToken->equals('{')) {
                continue;
            }

            /** @var Token $tokenTmp */
            $tokenTmp = $tokens[$braceIndex];

            $newBraceIndex = $prevIndex + 1;
            for ($i = $braceIndex; $i > $newBraceIndex; --$i) {
                // we might be moving one white space next to another, these have to be merged
                /** @var Token $previousToken */
                $previousToken = $tokens[$i - 1];
                $tokens[$i] = $previousToken;
                if ($tokens[$i]->isWhitespace() && $tokens[$i + 1]->isWhitespace()) {
                    $tokens[$i] = new Token([T_WHITESPACE, $tokens[$i]->getContent().$tokens[$i + 1]->getContent()]);
                    $tokens->clearAt($i + 1);
                }
            }

            $tokens[$newBraceIndex] = $tokenTmp;
            $c = $tokens[$braceIndex]->getContent();
            if (substr_count($c, "\n") > 1) {
                // left trim till last line break
                $tokens[$braceIndex] = new Token([T_WHITESPACE, substr($c, strrpos($c, "\n"))]);
            }
        }
    }

    private function fixIndents(Tokens $tokens): void
    {
        $classyTokens = Token::getClassyTokenKinds();
        $classyAndFunctionTokens = array_merge([T_FUNCTION], $classyTokens);
        $controlTokens = $this->getControlTokens();
        $indentTokens = array_filter(
            array_merge($classyAndFunctionTokens, $controlTokens),
            static function (int $item): bool {
                return T_SWITCH !== $item;
            }
        );
        $tokensAnalyzer = new TokensAnalyzer($tokens);

        for ($index = 0, $limit = \count($tokens); $index < $limit; ++$index) {
            $token = $tokens[$index];

            // if token is not a structure element - continue
            if (!$token->isGivenKind($indentTokens)) {
                continue;
            }

            // do not change indent for `while` in `do ... while ...`
            if (
                $token->isGivenKind(T_WHILE)
                && $tokensAnalyzer->isWhilePartOfDoWhile($index)
            ) {
                continue;
            }

            if (
                $this->configuration['allow_single_line_anonymous_class_with_empty_body']
                && $token->isGivenKind(T_CLASS)
            ) {
                $prevIndex = $tokens->getPrevMeaningfulToken($index);
                if ($tokens[$prevIndex]->isGivenKind(T_NEW)) {
                    $braceStartIndex = $tokens->getNextTokenOfKind($index, ['{']);
                    $braceEndIndex = $tokens->getNextMeaningfulToken($braceStartIndex);

                    if ('}' === $tokens[$braceEndIndex]->getContent() && !$this->isMultilined($tokens, $index, $braceEndIndex)) {
                        $index = $braceEndIndex;

                        continue;
                    }
                }
            }

            if (
                $this->configuration['allow_single_line_closure']
                && $token->isGivenKind(T_FUNCTION)
                && $tokensAnalyzer->isLambda($index)
            ) {
                $braceEndIndex = $tokens->findBlockEnd(
                    Tokens::BLOCK_TYPE_CURLY_BRACE,
                    $tokens->getNextTokenOfKind($index, ['{'])
                );

                if (!$this->isMultilined($tokens, $index, $braceEndIndex)) {
                    $index = $braceEndIndex;

                    continue;
                }
            }

            if ($token->isGivenKind($classyAndFunctionTokens)) {
                $startBraceIndex = $tokens->getNextTokenOfKind($index, [';', '{']);
                $startBraceToken = $tokens[$startBraceIndex];
            } else {
                $parenthesisEndIndex = $this->findParenthesisEnd($tokens, $index);
                $startBraceIndex = $tokens->getNextNonWhitespace($parenthesisEndIndex);
                $startBraceToken = $tokens[$startBraceIndex];
            }

            // structure without braces block - nothing to do, e.g. do { } while (true);
            if (!$startBraceToken->equals('{')) {
                continue;
            }

            $nextNonWhitespaceIndex = $tokens->getNextNonWhitespace($startBraceIndex, " \t");
            $nextNonWhitespace = $tokens[$nextNonWhitespaceIndex];

            /* if CLOSE_TAG is after { on the same line, do not indent. e.g. <?php if ($condition) { ?> */
            if ($nextNonWhitespace->isGivenKind(T_CLOSE_TAG)) {
                continue;
            }

            /* if CLOSE_TAG is after { on the next line and a comment on this line, do not indent. e.g. <?php if ($condition) { // \n?> */
            if ($nextNonWhitespace->isComment() && $tokens[$tokens->getNextMeaningfulToken($nextNonWhitespaceIndex)]->isGivenKind(T_CLOSE_TAG)) {
                continue;
            }

            $endBraceIndex = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_CURLY_BRACE, $startBraceIndex);

            $indent = WhitespacesAnalyzer::detectIndent($tokens, $index);

            // fix indent near closing brace
            $tokens->ensureWhitespaceAtIndex($endBraceIndex - 1, 1, $this->whitespacesConfig->getLineEnding().$indent);

            // fix indent between braces
            $lastCommaIndex = $tokens->getPrevTokenOfKind($endBraceIndex - 1, [';', '}']);

            $nestLevel = 1;
            for ($nestIndex = $lastCommaIndex; $nestIndex >= $startBraceIndex; --$nestIndex) {
                $nestToken = $tokens[$nestIndex];

                if ($nestToken->equalsAny([')', [CT::T_BRACE_CLASS_INSTANTIATION_CLOSE]])) {
                    $nestIndex = $tokens->findBlockStart(
                        $nestToken->equals(')') ? Tokens::BLOCK_TYPE_PARENTHESIS_BRACE : Tokens::BLOCK_TYPE_BRACE_CLASS_INSTANTIATION,
                        $nestIndex
                    );

                    continue;
                }

                if (1 === $nestLevel) {
                    // Next token is the beginning of a line that can be indented when
                    // the current token is a `;`, a `}` or the opening `{` of current
                    // scope. Current token may also be a comment that follows `;` or
                    // `}`, in which case indentation will only be fixed if this
                    // comment is followed by a newline.
                    $nextLineCanBeIndented = false;
                    if ($nestToken->equalsAny([';', '}'])) {
                        $nextLineCanBeIndented = true;
                    } elseif ($this->isCommentWithFixableIndentation($tokens, $nestIndex)) {
                        for ($i = $nestIndex; $i > $startBraceIndex; --$i) {
                            if ($tokens[$i]->equalsAny([';', '}'])) {
                                $nextLineCanBeIndented = true;

                                break;
                            }

                            if (!$tokens[$i]->isWhitespace() && !$tokens[$i]->isComment()) {
                                break;
                            }
                        }

                        if ($nextLineCanBeIndented || $i === $startBraceIndex) {
                            $nextToken = $tokens[$nestIndex + 1];
                            $nextLineCanBeIndented = $nextToken->isWhitespace() && 1 === Preg::match('/\R/', $nextToken->getContent());
                        }
                    }

                    if (!$nextLineCanBeIndented) {
                        continue;
                    }

                    $nextNonWhitespaceNestIndex = $tokens->getNextNonWhitespace($nestIndex);
                    $nextNonWhitespaceNestToken = $tokens[$nextNonWhitespaceNestIndex];

                    if (
                        // next Token is not a comment on its own line
                        !($nextNonWhitespaceNestToken->isComment() && (
                            !$tokens[$nextNonWhitespaceNestIndex - 1]->isWhitespace()
                            || !Preg::match('/\R/', $tokens[$nextNonWhitespaceNestIndex - 1]->getContent())
                        ))
                        // and it is not a `$foo = function () {};` situation
                        && !($nestToken->equals('}') && $nextNonWhitespaceNestToken->equalsAny([';', ',', ']', [CT::T_ARRAY_SQUARE_BRACE_CLOSE]]))
                        // and it is not a `Foo::{bar}()` situation
                        && !($nestToken->equals('}') && $nextNonWhitespaceNestToken->equals('('))
                        // and it is not a `${"a"}->...` and `${"b{$foo}"}->...` situation
                        && !($nestToken->equals('}') && $tokens[$nestIndex - 1]->equalsAny(['"', "'", [T_CONSTANT_ENCAPSED_STRING], [T_VARIABLE]]))
                        // and next token is not a closing tag that would break heredoc/nowdoc syntax
                        && !($tokens[$nestIndex - 1]->isGivenKind(T_END_HEREDOC) && $nextNonWhitespaceNestToken->isGivenKind(T_CLOSE_TAG))
                    ) {
                        if (
                            (
                                self::LINE_NEXT !== $this->configuration['position_after_control_structures']
                                && $nextNonWhitespaceNestToken->isGivenKind($this->getControlContinuationTokens())
                                && !$tokens[$tokens->getPrevNonWhitespace($nextNonWhitespaceNestIndex)]->isComment()
                            )
                            || $nextNonWhitespaceNestToken->isGivenKind(T_CLOSE_TAG)
                            || (
                                self::LINE_NEXT !== $this->configuration['position_after_control_structures']
                                && $nextNonWhitespaceNestToken->isGivenKind(T_WHILE)
                                && $tokensAnalyzer->isWhilePartOfDoWhile($nextNonWhitespaceNestIndex)
                            )
                        ) {
                            $whitespace = ' ';
                        } else {
                            $nextToken = $tokens[$nestIndex + 1];
                            $nextWhitespace = '';

                            if ($nextToken->isWhitespace()) {
                                $nextWhitespace = rtrim($nextToken->getContent(), " \t");

                                if ('' !== $nextWhitespace) {
                                    $nextWhitespace = Preg::replace(
                                        sprintf('/%s$/', $this->whitespacesConfig->getLineEnding()),
                                        '',
                                        $nextWhitespace,
                                        1
                                    );
                                }
                            }

                            $whitespace = $nextWhitespace.$this->whitespacesConfig->getLineEnding().$indent;

                            if (!$nextNonWhitespaceNestToken->equals('}')) {
                                $determineIsIndentableBlockContent = static function (int $contentIndex) use ($tokens): bool {
                                    if (!$tokens[$contentIndex]->isComment()) {
                                        return true;
                                    }

                                    if (!$tokens[$tokens->getPrevMeaningfulToken($contentIndex)]->equals(';')) {
                                        return true;
                                    }

                                    $nextIndex = $tokens->getNextMeaningfulToken($contentIndex);

                                    if (!$tokens[$nextIndex]->equals('}')) {
                                        return true;
                                    }

                                    $nextNextIndex = $tokens->getNextMeaningfulToken($nextIndex);

                                    if (null === $nextNextIndex) {
                                        return true;
                                    }

                                    if ($tokens[$nextNextIndex]->equalsAny([
                                        [T_ELSE],
                                        [T_ELSEIF],
                                        ',',
                                    ])) {
                                        return false;
                                    }

                                    return true;
                                };

                                // add extra indent only if current content is not a comment for content outside of current block
                                if ($determineIsIndentableBlockContent($nestIndex + 2)) {
                                    $whitespace .= $this->whitespacesConfig->getIndent();
                                }
                            }
                        }

                        $this->ensureWhitespaceAtIndexAndIndentMultilineComment($tokens, $nestIndex + 1, $whitespace);
                    }
                }

                if ($nestToken->equals('}')) {
                    ++$nestLevel;

                    continue;
                }

                if ($nestToken->equals('{')) {
                    --$nestLevel;

                    continue;
                }
            }

            // fix indent near opening brace
            if (isset($tokens[$startBraceIndex + 2]) && $tokens[$startBraceIndex + 2]->equals('}')) {
                $tokens->ensureWhitespaceAtIndex($startBraceIndex + 1, 0, $this->whitespacesConfig->getLineEnding().$indent);
            } else {
                $nextToken = $tokens[$startBraceIndex + 1];
                $nextNonWhitespaceToken = $tokens[$tokens->getNextNonWhitespace($startBraceIndex)];

                // set indent only if it is not a case, when comment is following { on same line
                if (
                    !$nextNonWhitespaceToken->isComment()
                    || ($nextToken->isWhitespace() && 1 === substr_count($nextToken->getContent(), "\n")) // preserve blank lines
                ) {
                    $this->ensureWhitespaceAtIndexAndIndentMultilineComment(
                        $tokens,
                        $startBraceIndex + 1,
                        $this->whitespacesConfig->getLineEnding().$indent.$this->whitespacesConfig->getIndent()
                    );
                }
            }

            if ($token->isGivenKind($classyTokens) && !$tokensAnalyzer->isAnonymousClass($index)) {
                if (self::LINE_SAME === $this->configuration['position_after_functions_and_oop_constructs'] && !$tokens[$tokens->getPrevNonWhitespace($startBraceIndex)]->isComment()) {
                    $ensuredWhitespace = ' ';
                } else {
                    $ensuredWhitespace = $this->whitespacesConfig->getLineEnding().$indent;
                }

                $tokens->ensureWhitespaceAtIndex($startBraceIndex - 1, 1, $ensuredWhitespace);
            } elseif (
                $token->isGivenKind(T_FUNCTION) && !$tokensAnalyzer->isLambda($index)
                || (
                    self::LINE_NEXT === $this->configuration['position_after_control_structures'] && $token->isGivenKind($controlTokens)
                    || (
                        self::LINE_NEXT === $this->configuration['position_after_anonymous_constructs']
                        && (
                            $token->isGivenKind(T_FUNCTION) && $tokensAnalyzer->isLambda($index)
                            || $token->isGivenKind(T_CLASS) && $tokensAnalyzer->isAnonymousClass($index)
                        )
                    )
                )
            ) {
                $isAnonymousClass = $token->isGivenKind($classyTokens) && $tokensAnalyzer->isAnonymousClass($index);

                $closingParenthesisIndex = $tokens->getPrevTokenOfKind($startBraceIndex, [')']);
                if (null === $closingParenthesisIndex && !$isAnonymousClass) {
                    continue;
                }

                if (
                    !$isAnonymousClass
                    && $tokens[$closingParenthesisIndex - 1]->isWhitespace()
                    && str_contains($tokens[$closingParenthesisIndex - 1]->getContent(), "\n")
                ) {
                    if (!$tokens[$startBraceIndex - 2]->isComment()) {
                        $tokens->ensureWhitespaceAtIndex($startBraceIndex - 1, 1, ' ');
                    }
                } else {
                    if (
                        self::LINE_SAME === $this->configuration['position_after_functions_and_oop_constructs']
                        && (
                            $token->isGivenKind(T_FUNCTION) && !$tokensAnalyzer->isLambda($index)
                            || $token->isGivenKind($classyTokens) && !$tokensAnalyzer->isAnonymousClass($index)
                        )
                        && !$tokens[$tokens->getPrevNonWhitespace($startBraceIndex)]->isComment()
                    ) {
                        $ensuredWhitespace = ' ';
                    } else {
                        $ensuredWhitespace = $this->whitespacesConfig->getLineEnding().$indent;
                    }

                    $tokens->ensureWhitespaceAtIndex($startBraceIndex - 1, 1, $ensuredWhitespace);
                }
            }

            // reset loop limit due to collection change
            $limit = \count($tokens);
        }
    }

    private function fixSpaceAroundToken(Tokens $tokens): void
    {
        $controlTokens = $this->getControlTokens();

        for ($index = $tokens->count() - 1; 0 <= $index; --$index) {
            $token = $tokens[$index];

            // Declare tokens don't follow the same rules are other control statements
            if ($token->isGivenKind(T_DECLARE)) {
                continue; // delegated to DeclareParenthesesFixer
            }

            if ($token->isGivenKind($controlTokens) || $token->isGivenKind(CT::T_USE_LAMBDA)) {
                $nextNonWhitespaceIndex = $tokens->getNextNonWhitespace($index);

                if (!$tokens[$nextNonWhitespaceIndex]->equals(':')) {
                    $tokens->ensureWhitespaceAtIndex(
                        $index + 1,
                        0,
                        self::LINE_NEXT === $this->configuration['position_after_control_structures'] && !$tokens[$nextNonWhitespaceIndex]->equals('(') ?
                            $this->whitespacesConfig->getLineEnding().WhitespacesAnalyzer::detectIndent($tokens, $index)
                            : ' '
                    );
                }

                $prevToken = $tokens[$index - 1];

                if (!$prevToken->isWhitespace() && !$prevToken->isComment() && !$prevToken->isGivenKind(T_OPEN_TAG)) {
                    $tokens->ensureWhitespaceAtIndex($index - 1, 1, ' ');
                }
            }
        }
    }

    private function findParenthesisEnd(Tokens $tokens, int $structureTokenIndex): int
    {
        $nextIndex = $tokens->getNextMeaningfulToken($structureTokenIndex);
        $nextToken = $tokens[$nextIndex];

        // return if next token is not opening parenthesis
        if (!$nextToken->equals('(')) {
            return $structureTokenIndex;
        }

        return $tokens->findBlockEnd(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $nextIndex);
    }

    private function getControlTokens(): array
    {
        static $tokens = [
            T_DECLARE,
            T_DO,
            T_ELSE,
            T_ELSEIF,
            T_FINALLY,
            T_FOR,
            T_FOREACH,
            T_IF,
            T_WHILE,
            T_TRY,
            T_CATCH,
            T_SWITCH,
        ];

        // @TODO: drop condition when PHP 8.0+ is required
        if (\defined('T_MATCH')) {
            $tokens['match'] = T_MATCH;
        }

        return $tokens;
    }

    private function getControlContinuationTokens(): array
    {
        static $tokens = [
            T_CATCH,
            T_ELSE,
            T_ELSEIF,
            T_FINALLY,
        ];

        return $tokens;
    }

    private function ensureWhitespaceAtIndexAndIndentMultilineComment(Tokens $tokens, int $index, string $whitespace): void
    {
        if ($tokens[$index]->isWhitespace()) {
            $nextTokenIndex = $tokens->getNextNonWhitespace($index);
        } else {
            $nextTokenIndex = $index;
        }

        $nextToken = $tokens[$nextTokenIndex];
        if ($nextToken->isComment()) {
            $previousToken = $tokens[$nextTokenIndex - 1];
            $nextTokenContent = $nextToken->getContent();

            // do not indent inline comments used to comment out unused code
            if (
                $previousToken->isWhitespace()
                && 1 === Preg::match('/\R$/', $previousToken->getContent())
                && (
                    (str_starts_with($nextTokenContent, '//'.$this->whitespacesConfig->getIndent()) || '//' === $nextTokenContent)
                    || (str_starts_with($nextTokenContent, '#'.$this->whitespacesConfig->getIndent()) || '#' === $nextTokenContent)
                )
            ) {
                return;
            }

            $tokens[$nextTokenIndex] = new Token([
                $nextToken->getId(),
                Preg::replace(
                    '/(\R)'.WhitespacesAnalyzer::detectIndent($tokens, $nextTokenIndex).'(\h*\S+.*)/',
                    '$1'.Preg::replace('/^.*\R(\h*)$/s', '$1', $whitespace).'$2',
                    $nextToken->getContent()
                ),
            ]);
        }

        $tokens->ensureWhitespaceAtIndex($index, 0, $whitespace);
    }

    private function isMultilined(Tokens $tokens, int $startParenthesisIndex, int $endParenthesisIndex): bool
    {
        for ($i = $startParenthesisIndex; $i < $endParenthesisIndex; ++$i) {
            if (str_contains($tokens[$i]->getContent(), "\n")) {
                return true;
            }
        }

        return false;
    }

    /**
     * Returns whether the token at given index is a comment whose indentation
     * can be fixed.
     *
     * Indentation of a comment is not changed when the comment is part of a
     * multi-line message whose lines are all single-line comments and at least
     * one line has meaningful content.
     */
    private function isCommentWithFixableIndentation(Tokens $tokens, int $index): bool
    {
        if (!$tokens[$index]->isComment()) {
            return false;
        }

        if (str_starts_with($tokens[$index]->getContent(), '/*')) {
            return true;
        }

        $firstCommentIndex = $index;
        while (true) {
            $i = $this->getSiblingContinuousSingleLineComment($tokens, $firstCommentIndex, false);
            if (null === $i) {
                break;
            }

            $firstCommentIndex = $i;
        }

        $lastCommentIndex = $index;
        while (true) {
            $i = $this->getSiblingContinuousSingleLineComment($tokens, $lastCommentIndex, true);
            if (null === $i) {
                break;
            }

            $lastCommentIndex = $i;
        }

        if ($firstCommentIndex === $lastCommentIndex) {
            return true;
        }

        for ($i = $firstCommentIndex + 1; $i < $lastCommentIndex; ++$i) {
            if (!$tokens[$i]->isWhitespace() && !$tokens[$i]->isComment()) {
                return false;
            }
        }

        return true;
    }

    private function getSiblingContinuousSingleLineComment(Tokens $tokens, int $index, bool $after): ?int
    {
        $siblingIndex = $index;
        do {
            $siblingIndex = $tokens->getTokenOfKindSibling($siblingIndex, $after ? 1 : -1, [[T_COMMENT]]);

            if (null === $siblingIndex) {
                return null;
            }
        } while (str_starts_with($tokens[$siblingIndex]->getContent(), '/*'));

        $newLines = 0;
        for ($i = min($siblingIndex, $index) + 1, $max = max($siblingIndex, $index); $i < $max; ++$i) {
            if ($tokens[$i]->isWhitespace() && Preg::match('/\R/', $tokens[$i]->getContent())) {
                if (1 === $newLines || Preg::match('/\R.*\R/', $tokens[$i]->getContent())) {
                    return null;
                }

                ++$newLines;
            }
        }

        return $siblingIndex;
    }

    private function getCurlyBracesPositionFixer(): CurlyBracesPositionFixer
    {
        if (null === $this->curlyBracesPositionFixer) {
            $this->curlyBracesPositionFixer = new CurlyBracesPositionFixer();
        }

        return $this->curlyBracesPositionFixer;
    }

    private function getControlStructureContinuationPositionFixer(): ControlStructureContinuationPositionFixer
    {
        if (null === $this->controlStructureContinuationPositionFixer) {
            $this->controlStructureContinuationPositionFixer = new ControlStructureContinuationPositionFixer();
        }

        return $this->controlStructureContinuationPositionFixer;
    }

    private function translatePositionOption(string $option): string
    {
        return self::LINE_NEXT === $option
            ? CurlyBracesPositionFixer::NEXT_LINE_UNLESS_NEWLINE_AT_SIGNATURE_END
            : CurlyBracesPositionFixer::SAME_LINE
        ;
    }
}
