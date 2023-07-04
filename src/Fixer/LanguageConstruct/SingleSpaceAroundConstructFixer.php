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

namespace PhpCsFixer\Fixer\LanguageConstruct;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\Fixer\ConfigurableFixerInterface;
use PhpCsFixer\FixerConfiguration\AllowedValueSubset;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolver;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolverInterface;
use PhpCsFixer\FixerConfiguration\FixerOptionBuilder;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Preg;
use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @author Andreas Möller <am@localheinz.com>
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
final class SingleSpaceAroundConstructFixer extends AbstractFixer implements ConfigurableFixerInterface
{
    /**
     * @var array<string, null|int>
     */
    private static array $tokenMapContainASingleSpace = [
        // for now, only one case - but we are ready to extend it, when we learn about new cases to cover
        'yield_from' => T_YIELD_FROM,
    ];

    /**
     * @var array<string, null|int>
     */
    private static array $tokenMapPrecededByASingleSpace = [
        'as' => T_AS,
        'use_lambda' => CT::T_USE_LAMBDA,
    ];

    /**
     * @var array<string, null|int>
     */
    private static array $tokenMapFollowedByASingleSpace = [
        'abstract' => T_ABSTRACT,
        'as' => T_AS,
        'attribute' => CT::T_ATTRIBUTE_CLOSE,
        'break' => T_BREAK,
        'case' => T_CASE,
        'catch' => T_CATCH,
        'class' => T_CLASS,
        'clone' => T_CLONE,
        'comment' => T_COMMENT,
        'const' => T_CONST,
        'const_import' => CT::T_CONST_IMPORT,
        'continue' => T_CONTINUE,
        'do' => T_DO,
        'echo' => T_ECHO,
        'else' => T_ELSE,
        'elseif' => T_ELSEIF,
        'enum' => null,
        'extends' => T_EXTENDS,
        'final' => T_FINAL,
        'finally' => T_FINALLY,
        'for' => T_FOR,
        'foreach' => T_FOREACH,
        'function' => T_FUNCTION,
        'function_import' => CT::T_FUNCTION_IMPORT,
        'global' => T_GLOBAL,
        'goto' => T_GOTO,
        'if' => T_IF,
        'implements' => T_IMPLEMENTS,
        'include' => T_INCLUDE,
        'include_once' => T_INCLUDE_ONCE,
        'instanceof' => T_INSTANCEOF,
        'insteadof' => T_INSTEADOF,
        'interface' => T_INTERFACE,
        'match' => null,
        'named_argument' => CT::T_NAMED_ARGUMENT_COLON,
        'namespace' => T_NAMESPACE,
        'new' => T_NEW,
        'open_tag_with_echo' => T_OPEN_TAG_WITH_ECHO,
        'php_doc' => T_DOC_COMMENT,
        'php_open' => T_OPEN_TAG,
        'print' => T_PRINT,
        'private' => T_PRIVATE,
        'protected' => T_PROTECTED,
        'public' => T_PUBLIC,
        'readonly' => null,
        'require' => T_REQUIRE,
        'require_once' => T_REQUIRE_ONCE,
        'return' => T_RETURN,
        'static' => T_STATIC,
        'switch' => T_SWITCH,
        'throw' => T_THROW,
        'trait' => T_TRAIT,
        'try' => T_TRY,
        'type_colon' => CT::T_TYPE_COLON,
        'use' => T_USE,
        'use_lambda' => CT::T_USE_LAMBDA,
        'use_trait' => CT::T_USE_TRAIT,
        'var' => T_VAR,
        'while' => T_WHILE,
        'yield' => T_YIELD,
        'yield_from' => T_YIELD_FROM,
    ];

    /**
     * @var array<string, int>
     */
    private array $fixTokenMapFollowedByASingleSpace = [];

    /**
     * @var array<string, int>
     */
    private array $fixTokenMapContainASingleSpace = [];

    /**
     * @var array<string, int>
     */
    private array $fixTokenMapPrecededByASingleSpace = [];

    public function configure(array $configuration): void
    {
        parent::configure($configuration);

        if (\defined('T_MATCH')) { // @TODO: drop condition when PHP 8.0+ is required
            self::$tokenMapFollowedByASingleSpace['match'] = T_MATCH;
        }

        if (\defined('T_READONLY')) { // @TODO: drop condition when PHP 8.1+ is required
            self::$tokenMapFollowedByASingleSpace['readonly'] = T_READONLY;
        }

        if (\defined('T_ENUM')) { // @TODO: drop condition when PHP 8.1+ is required
            self::$tokenMapFollowedByASingleSpace['enum'] = T_ENUM;
        }

        $this->fixTokenMapContainASingleSpace = [];

        foreach ($this->configuration['constructs_contain_a_single_space'] as $key) {
            if (null !== self::$tokenMapContainASingleSpace[$key]) {
                $this->fixTokenMapContainASingleSpace[$key] = self::$tokenMapContainASingleSpace[$key];
            }
        }

        $this->fixTokenMapPrecededByASingleSpace = [];

        foreach ($this->configuration['constructs_preceded_by_a_single_space'] as $key) {
            if (null !== self::$tokenMapPrecededByASingleSpace[$key]) {
                $this->fixTokenMapPrecededByASingleSpace[$key] = self::$tokenMapPrecededByASingleSpace[$key];
            }
        }

        $this->fixTokenMapFollowedByASingleSpace = [];

        foreach ($this->configuration['constructs_followed_by_a_single_space'] as $key) {
            if (null !== self::$tokenMapFollowedByASingleSpace[$key]) {
                $this->fixTokenMapFollowedByASingleSpace[$key] = self::$tokenMapFollowedByASingleSpace[$key];
            }
        }

        if (isset($this->fixTokenMapFollowedByASingleSpace['public'])) {
            $this->fixTokenMapFollowedByASingleSpace['constructor_public'] = CT::T_CONSTRUCTOR_PROPERTY_PROMOTION_PUBLIC;
        }

        if (isset($this->fixTokenMapFollowedByASingleSpace['protected'])) {
            $this->fixTokenMapFollowedByASingleSpace['constructor_protected'] = CT::T_CONSTRUCTOR_PROPERTY_PROMOTION_PROTECTED;
        }

        if (isset($this->fixTokenMapFollowedByASingleSpace['private'])) {
            $this->fixTokenMapFollowedByASingleSpace['constructor_private'] = CT::T_CONSTRUCTOR_PROPERTY_PROMOTION_PRIVATE;
        }
    }

    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(
            'Ensures a single space after language constructs.',
            [
                new CodeSample(
                    '<?php

throw  new  \Exception();
'
                ),
                new CodeSample(
                    '<?php

function foo() { yield  from  baz(); }
',
                    [
                        'constructs_contain_a_single_space' => [
                            'yield_from',
                        ],
                        'constructs_followed_by_a_single_space' => [
                            'yield_from',
                        ],
                    ]
                ),

                new CodeSample(
                    '<?php

$foo = function& ()use($bar) {
};
',
                    [
                        'constructs_preceded_by_a_single_space' => [
                            'use_lambda',
                        ],
                        'constructs_followed_by_a_single_space' => [
                            'use_lambda',
                        ],
                    ]
                ),
                new CodeSample(
                    '<?php

echo  "Hello!";
',
                    [
                        'constructs_followed_by_a_single_space' => [
                            'echo',
                        ],
                    ]
                ),
                new CodeSample(
                    '<?php

yield  from  baz();
',
                    [
                        'constructs_followed_by_a_single_space' => [
                            'yield_from',
                        ],
                    ]
                ),
            ]
        );
    }

    /**
     * {@inheritdoc}
     *
     * Must run before BracesFixer, FunctionDeclarationFixer, NullableTypeDeclarationFixer.
     * Must run after ModernizeStrposFixer.
     */
    public function getPriority(): int
    {
        return 36;
    }

    public function isCandidate(Tokens $tokens): bool
    {
        $tokenKinds = array_merge(
            array_values($this->fixTokenMapContainASingleSpace),
            array_values($this->fixTokenMapPrecededByASingleSpace),
            array_values($this->fixTokenMapFollowedByASingleSpace),
        );

        return $tokens->isAnyTokenKindsFound($tokenKinds) && !$tokens->hasAlternativeSyntax();
    }

    protected function applyFix(\SplFileInfo $file, Tokens $tokens): void
    {
        $tokenKindsContainASingleSpace = array_values($this->fixTokenMapContainASingleSpace);

        for ($index = $tokens->count() - 1; $index > 0; --$index) {
            if ($tokens[$index]->isGivenKind($tokenKindsContainASingleSpace)) {
                $token = $tokens[$index];

                if (
                    $token->isGivenKind(T_YIELD_FROM)
                    && 'yield from' !== strtolower($token->getContent())
                ) {
                    $tokens[$index] = new Token([T_YIELD_FROM, Preg::replace(
                        '/\s+/',
                        ' ',
                        $token->getContent()
                    )]);
                }
            }
        }

        $tokenKindsPrecededByASingleSpace = array_values($this->fixTokenMapPrecededByASingleSpace);

        for ($index = $tokens->count() - 1; $index > 0; --$index) {
            if ($tokens[$index]->isGivenKind($tokenKindsPrecededByASingleSpace)) {
                $tokens->ensureWhitespaceAtIndex($index - 1, 1, ' ');
            }
        }

        $tokenKindsFollowedByASingleSpace = array_values($this->fixTokenMapFollowedByASingleSpace);

        for ($index = $tokens->count() - 2; $index >= 0; --$index) {
            $token = $tokens[$index];

            if (!$token->isGivenKind($tokenKindsFollowedByASingleSpace)) {
                continue;
            }

            $whitespaceTokenIndex = $index + 1;

            if ($tokens[$whitespaceTokenIndex]->equalsAny([',', ';', ')', [CT::T_ARRAY_SQUARE_BRACE_CLOSE], [CT::T_DESTRUCTURING_SQUARE_BRACE_CLOSE]])) {
                continue;
            }

            if (
                $token->isGivenKind(T_STATIC)
                && !$tokens[$tokens->getNextMeaningfulToken($index)]->isGivenKind([T_FN, T_FUNCTION, T_NS_SEPARATOR, T_STRING, T_VARIABLE, CT::T_ARRAY_TYPEHINT, CT::T_NULLABLE_TYPE])
            ) {
                continue;
            }

            if ($token->isGivenKind(T_OPEN_TAG)) {
                if ($tokens[$whitespaceTokenIndex]->equals([T_WHITESPACE]) && !str_contains($tokens[$whitespaceTokenIndex]->getContent(), "\n") && !str_contains($token->getContent(), "\n")) {
                    $tokens->clearAt($whitespaceTokenIndex);
                }

                continue;
            }

            if ($token->isGivenKind(T_CLASS) && $tokens[$tokens->getNextMeaningfulToken($index)]->equals('(')) {
                continue;
            }

            if ($token->isGivenKind([T_EXTENDS, T_IMPLEMENTS]) && $this->isMultilineExtendsOrImplementsWithMoreThanOneAncestor($tokens, $index)) {
                continue;
            }

            if ($token->isGivenKind(T_RETURN) && $this->isMultiLineReturn($tokens, $index)) {
                continue;
            }

            if ($token->isGivenKind(T_CONST) && $this->isMultilineConstant($tokens, $index)) {
                continue;
            }

            if ($token->isComment() || $token->isGivenKind(CT::T_ATTRIBUTE_CLOSE)) {
                if ($tokens[$whitespaceTokenIndex]->equals([T_WHITESPACE]) && str_contains($tokens[$whitespaceTokenIndex]->getContent(), "\n")) {
                    continue;
                }
            }

            $tokens->ensureWhitespaceAtIndex($whitespaceTokenIndex, 0, ' ');
        }
    }

    protected function createConfigurationDefinition(): FixerConfigurationResolverInterface
    {
        $tokenMapContainASingleSpaceKeys = array_keys(self::$tokenMapContainASingleSpace);
        $tokenMapPrecededByASingleSpaceKeys = array_keys(self::$tokenMapPrecededByASingleSpace);
        $tokenMapFollowedByASingleSpaceKeys = array_keys(self::$tokenMapFollowedByASingleSpace);

        return new FixerConfigurationResolver([
            (new FixerOptionBuilder('constructs_contain_a_single_space', 'List of constructs which must contain a single space.'))
                ->setAllowedTypes(['array'])
                ->setAllowedValues([new AllowedValueSubset($tokenMapContainASingleSpaceKeys)])
                ->setDefault($tokenMapContainASingleSpaceKeys)
                ->getOption(),
            (new FixerOptionBuilder('constructs_preceded_by_a_single_space', 'List of constructs which must be preceded by a single space.'))
                ->setAllowedTypes(['array'])
                ->setAllowedValues([new AllowedValueSubset($tokenMapPrecededByASingleSpaceKeys)])
                ->setDefault($tokenMapPrecededByASingleSpaceKeys)
                ->getOption(),
            (new FixerOptionBuilder('constructs_followed_by_a_single_space', 'List of constructs which must be followed by a single space.'))
                ->setAllowedTypes(['array'])
                ->setAllowedValues([new AllowedValueSubset($tokenMapFollowedByASingleSpaceKeys)])
                ->setDefault($tokenMapFollowedByASingleSpaceKeys)
                ->getOption(),
        ]);
    }

    private function isMultiLineReturn(Tokens $tokens, int $index): bool
    {
        ++$index;
        $tokenFollowingReturn = $tokens[$index];

        if (
            !$tokenFollowingReturn->isGivenKind(T_WHITESPACE)
            || !str_contains($tokenFollowingReturn->getContent(), "\n")
        ) {
            return false;
        }

        $nestedCount = 0;

        for ($indexEnd = \count($tokens) - 1, ++$index; $index < $indexEnd; ++$index) {
            if (str_contains($tokens[$index]->getContent(), "\n")) {
                return true;
            }

            if ($tokens[$index]->equals('{')) {
                ++$nestedCount;
            } elseif ($tokens[$index]->equals('}')) {
                --$nestedCount;
            } elseif (0 === $nestedCount && $tokens[$index]->equalsAny([';', [T_CLOSE_TAG]])) {
                break;
            }
        }

        return false;
    }

    private function isMultilineExtendsOrImplementsWithMoreThanOneAncestor(Tokens $tokens, int $index): bool
    {
        $hasMoreThanOneAncestor = false;

        while (++$index) {
            $token = $tokens[$index];

            if ($token->equals(',')) {
                $hasMoreThanOneAncestor = true;

                continue;
            }

            if ($token->equals('{')) {
                return false;
            }

            if ($hasMoreThanOneAncestor && str_contains($token->getContent(), "\n")) {
                return true;
            }
        }

        return false;
    }

    private function isMultilineConstant(Tokens $tokens, int $index): bool
    {
        $scopeEnd = $tokens->getNextTokenOfKind($index, [';', [T_CLOSE_TAG]]) - 1;
        $hasMoreThanOneConstant = null !== $tokens->findSequence([new Token(',')], $index + 1, $scopeEnd);

        return $hasMoreThanOneConstant && $tokens->isPartialCodeMultiline($index, $scopeEnd);
    }
}
