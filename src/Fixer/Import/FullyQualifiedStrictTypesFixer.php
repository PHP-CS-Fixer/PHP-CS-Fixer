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

namespace PhpCsFixer\Fixer\Import;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\FixerDefinition\VersionSpecification;
use PhpCsFixer\FixerDefinition\VersionSpecificCodeSample;
use PhpCsFixer\Tokenizer\Analyzer\Analysis\TypeAnalysis;
use PhpCsFixer\Tokenizer\Analyzer\FunctionsAnalyzer;
use PhpCsFixer\Tokenizer\Analyzer\NamespacesAnalyzer;
use PhpCsFixer\Tokenizer\Analyzer\NamespaceUsesAnalyzer;
use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Tokenizer\Generator\NamespacedStringTokenGenerator;
use PhpCsFixer\Tokenizer\Resolver\TypeShortNameResolver;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @author VeeWee <toonverwerft@gmail.com>
 */
final class FullyQualifiedStrictTypesFixer extends AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(
            'Transforms imported FQCN parameters and return types in function arguments to short version.',
            [
                new CodeSample(
                    '<?php

use Foo\Bar;

class SomeClass
{
    public function doSomething(\Foo\Bar $foo)
    {
    }
}
'
                ),
                new VersionSpecificCodeSample(
                    '<?php

use Foo\Bar;
use Foo\Bar\Baz;

class SomeClass
{
    public function doSomething(\Foo\Bar $foo): \Foo\Bar\Baz
    {
    }
}
',
                    new VersionSpecification(70000)
                ),
            ]
        );
    }

    /**
     * {@inheritdoc}
     *
     * Must run before NoSuperfluousPhpdocTagsFixer.
     * Must run after PhpdocToReturnTypeFixer.
     */
    public function getPriority(): int
    {
        return 7;
    }

    /**
     * {@inheritdoc}
     */
    public function isCandidate(Tokens $tokens): bool
    {
        return $tokens->isTokenKindFound(T_FUNCTION) && (
            \count((new NamespacesAnalyzer())->getDeclarations($tokens))
            || \count((new NamespaceUsesAnalyzer())->getDeclarationsFromTokens($tokens))
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function applyFix(\SplFileInfo $file, Tokens $tokens): void
    {
        $lastIndex = $tokens->count() - 1;

        for ($index = $lastIndex; $index >= 0; --$index) {
            if (!$tokens[$index]->isGivenKind(T_FUNCTION)) {
                continue;
            }

            // Return types are only available since PHP 7.0
            $this->fixFunctionReturnType($tokens, $index);
            $this->fixFunctionArguments($tokens, $index);
        }
    }

    private function fixFunctionArguments(Tokens $tokens, int $index): void
    {
        $arguments = (new FunctionsAnalyzer())->getFunctionArguments($tokens, $index);

        foreach ($arguments as $argument) {
            if (!$argument->hasTypeAnalysis()) {
                continue;
            }

            $this->detectAndReplaceTypeWithShortType($tokens, $argument->getTypeAnalysis());
        }
    }

    private function fixFunctionReturnType(Tokens $tokens, int $index): void
    {
        if (\PHP_VERSION_ID < 70000) {
            return;
        }

        $returnType = (new FunctionsAnalyzer())->getFunctionReturnType($tokens, $index);
        if (!$returnType) {
            return;
        }

        $this->detectAndReplaceTypeWithShortType($tokens, $returnType);
    }

    private function detectAndReplaceTypeWithShortType(
        Tokens $tokens,
        TypeAnalysis $type
    ): void {
        if ($type->isReservedType()) {
            return;
        }

        $typeStartIndex = $type->getStartIndex();
        if ($tokens[$typeStartIndex]->isGivenKind(CT::T_NULLABLE_TYPE)) {
            $typeStartIndex = $tokens->getNextMeaningfulToken($typeStartIndex);
        }

        foreach ($this->getSimpleTypes($tokens, $typeStartIndex, $type->getEndIndex()) as $simpleType) {
            $typeName = $tokens->generatePartialCode($simpleType['start'], $simpleType['end']);

            if (0 !== strpos($typeName, '\\')) {
                continue;
            }

            $shortType = (new TypeShortNameResolver())->resolve($tokens, $typeName);
            if ($shortType === $typeName) {
                continue;
            }

            $shortType = (new NamespacedStringTokenGenerator())->generate($shortType);

            $tokens->overrideRange(
                $simpleType['start'],
                $simpleType['end'],
                $shortType
            );
        }
    }

    /**
     * @return \Generator<array<int>>
     */
    private function getSimpleTypes(Tokens $tokens, int $startIndex, int $endIndex): iterable
    {
        $index = $startIndex;

        while (true) {
            $prevIndex = $index;
            $index = $tokens->getNextMeaningfulToken($index);

            if (null === $startIndex) {
                $startIndex = $index;
            }

            if ($index >= $endIndex) {
                yield ['start' => $startIndex, 'end' => $index];

                break;
            }

            if ($tokens[$index]->isGivenKind(CT::T_TYPE_ALTERNATION)) {
                yield ['start' => $startIndex, 'end' => $prevIndex];
                $startIndex = null;
            }
        }
    }
}
