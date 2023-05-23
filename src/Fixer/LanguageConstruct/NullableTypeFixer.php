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
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Analyzer\FunctionsAnalyzer;
use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

final class NullableTypeFixer extends AbstractFixer
{
    private const FUNCTION_TOKENS = [
        T_FN,
        T_FUNCTION,
    ];
    private const PROPERTY_TOKENS = [
        T_PRIVATE,
        T_PROTECTED,
        T_PUBLIC,
    ];

    private FunctionsAnalyzer $functionAnalyzer;

    public function __construct()
    {
        parent::__construct();
        $this->functionAnalyzer = new FunctionsAnalyzer();
    }

    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(
            'Replaces ? with the corresponding union type.',
            [
                new CodeSample(
                    "<?php\nfunction sample(?string \$str = null)\n{}\n"
                ),
                new CodeSample(
                    "<?php\nclass Foo {\n  private ?string \$str = null;\n}\n",
                ),
                new CodeSample(
                    "<?php\nfunction sample(): ?string\n{}\n"
                ),
                new CodeSample(
                    "<?php\n\$fn = fn (): ?int => 5;\n"
                ),
            ],
            'Rule is applied only in a PHP 8.0+ environment.'
        );
    }

    /**
     * {@inheritdoc}
     *
     * Must run before OrderedTypesFixer.
     */
    public function getPriority(): int
    {
        return 1;
    }

    public function isCandidate(Tokens $tokens): bool
    {
        // we want a function with variables or
        return \PHP_VERSION_ID >= 8_00_00 && ($tokens->isAnyTokenKindsFound(self::FUNCTION_TOKENS)
            || ($tokens->isTokenKindFound(T_CLASS) && $tokens->isAnyTokenKindsFound(self::PROPERTY_TOKENS)));
    }

    protected function applyFix(\SplFileInfo $file, Tokens $tokens): void
    {
        for ($index = $tokens->count() - 1; $index >= 0; --$index) {
            $token = $tokens[$index];

            if ($token->isGivenKind(self::FUNCTION_TOKENS)) {
                $this->processFunction($tokens, $index);
            }

            if ($token->isGivenKind(self::PROPERTY_TOKENS)) {
                $this->processProperty($tokens, $index);
            }
        }
    }

    private function processFunction(Tokens $tokens, int $index): void
    {
        // starting from return type since we're moving backwards
        $this->processReturnType($tokens, $index);
        $arguments = $this->functionAnalyzer->getFunctionArguments($tokens, $index);

        // fix arguments
        foreach (array_reverse($arguments) as $argumentInfo) {
            // we don't touch this unless it's available anyway so it's fine to get it earlier
            $argumentTypeInfo = $argumentInfo->getTypeAnalysis();

            if (
                // Skip, if parameter
                // - doesn't have a type declaration
                !$argumentInfo->hasTypeAnalysis()
                // - doesn't have nullability
                || !$argumentTypeInfo->isNullable()
                // - we're changing to a union and this parameter already is one
                // there can never be a ? sign when using unions as PHP disallows it
                // so there's no need to check anything here
                || str_contains($argumentTypeInfo->getName(), '|')) {
                continue;
            }

            $tokens->clearTokenAndMergeSurroundingWhitespace($argumentTypeInfo->getStartIndex());
            $tokens->insertAt(
                $argumentTypeInfo->getStartIndex(),
                [
                    new Token([T_STRING, 'null']),
                    new Token([CT::T_TYPE_ALTERNATION, '|']),
                ]
            );
        }
    }

    private function processReturnType(Tokens $tokens, int $index): void
    {
        $type = $this->functionAnalyzer->getFunctionReturnType($tokens, $index);

        if (null === $type) {
            return;
        }

        // Skip, if return type
        // - doesn't have nullability
        // - is already a union
        if (!$type->isNullable() || str_contains($type->getName(), '|')) {
            return;
        }

        $tokens->clearTokenAndMergeSurroundingWhitespace($type->getStartIndex());
        $tokens->insertAt(
            $type->getStartIndex(),
            [
                new Token([T_STRING, 'null']),
                new Token([CT::T_TYPE_ALTERNATION, '|']),
            ]
        );
    }

    private function processProperty(Tokens $tokens, int $index): void
    {
        // this gets the token at index where the property is declared, so we need to find the type declaration
        $next = $tokens[$index + 1];

        // since public, protected and private are function keywords too we don't really wanna deal with them here
        if ($this->isMethod($tokens, $index)) {
            return;
        }

        $typeIndex = $this->getNullableTypeStart($tokens, $index);
        if (null === $typeIndex) {
            return;
        }

        $tokens->clearTokenAndMergeSurroundingWhitespace($typeIndex);
        $tokens->insertAt(
            $typeIndex,
            [
                new Token([T_STRING, 'null']),
                new Token([CT::T_TYPE_ALTERNATION, '|']),
            ]
        );
    }

    private function getNullableTypeStart(Tokens $tokens, int $index): ?int
    {
        $next = $tokens->getNonWhitespaceSibling($index, 1);
        if (null === $next) {
            return null;
        }

        $skip = [
            T_STATIC,
        ];

        if (\defined('T_READONLY')) {
            $skip[] = T_READONLY;
        }

        // we only need to skip one token because it's not possible to have a static readonly
        if (\in_array($tokens[$next]->getId(), $skip, true)) {
            $next = $tokens->getNonWhitespaceSibling($next, 1);
        }

        if (null === $next || CT::T_NULLABLE_TYPE !== $tokens[$next]->getId()) {
            return null;
        }

        return $next;
    }

    private function isMethod(Tokens $tokens, int $index): bool
    {
        $next = $tokens->getNonWhitespaceSibling($index, 1);
        if (null === $next) {
            return false;
        }

        // since static can appear as a token for both methods and properties we need to ignore it
        if (T_STATIC === $tokens[$next]->getId()) {
            $next = $tokens->getNonWhitespaceSibling($next, 1);
        }

        if (null === $next) {
            return false;
        }

        return T_FUNCTION === $tokens[$next]->getId();
    }
}
