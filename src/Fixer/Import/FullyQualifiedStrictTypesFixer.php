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
use PhpCsFixer\Tokenizer\Analyzer\Analysis\TypeAnalysis;
use PhpCsFixer\Tokenizer\Analyzer\FunctionsAnalyzer;
use PhpCsFixer\Tokenizer\Analyzer\NamespaceUsesAnalyzer;
use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @author VeeWee <toonverwerft@gmail.com>
 */
final class FullyQualifiedStrictTypesFixer extends AbstractFixer
{
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
                new CodeSample(
                    '<?php

use Foo\Bar;
use Foo\Bar\Baz;

class SomeClass
{
    public function doSomething(\Foo\Bar $foo): \Foo\Bar\Baz
    {
    }
}
'
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

    public function isCandidate(Tokens $tokens): bool
    {
        return $tokens->isTokenKindFound(T_FUNCTION);
    }

    protected function applyFix(\SplFileInfo $file, Tokens $tokens): void
    {
        $namespaceUsesAnalyzer = new NamespaceUsesAnalyzer();
        $functionsAnalyzer = new FunctionsAnalyzer();

        foreach ($tokens->getNamespaceDeclarations() as $namespace) {
            $namespaceName = strtolower($namespace->getFullName());
            $uses = [];

            foreach ($namespaceUsesAnalyzer->getDeclarationsInNamespace($tokens, $namespace) as $use) {
                $uses[strtolower(ltrim($use->getFullName(), '\\'))] = $use->getShortName();
            }

            for ($index = $namespace->getScopeStartIndex(); $index < $namespace->getScopeEndIndex(); ++$index) {
                if ($tokens[$index]->isGivenKind(T_FUNCTION)) {
                    $this->fixFunction($functionsAnalyzer, $tokens, $index, $uses, $namespaceName);
                }
            }
        }
    }

    /**
     * @param array<string, string> $uses
     */
    private function fixFunction(FunctionsAnalyzer $functionsAnalyzer, Tokens $tokens, int $index, array $uses, string $namespaceName): void
    {
        $arguments = $functionsAnalyzer->getFunctionArguments($tokens, $index);

        foreach ($arguments as $argument) {
            if ($argument->hasTypeAnalysis()) {
                $this->replaceByShortType($tokens, $argument->getTypeAnalysis(), $uses, $namespaceName);
            }
        }

        $returnTypeAnalysis = $functionsAnalyzer->getFunctionReturnType($tokens, $index);

        if (null !== $returnTypeAnalysis) {
            $this->replaceByShortType($tokens, $returnTypeAnalysis, $uses, $namespaceName);
        }
    }

    /**
     * @param array<string, string> $uses
     */
    private function replaceByShortType(Tokens $tokens, TypeAnalysis $type, array $uses, string $namespaceName): void
    {
        if ($type->isReservedType()) {
            return;
        }

        $typeStartIndex = $type->getStartIndex();

        if ($tokens[$typeStartIndex]->isGivenKind(CT::T_NULLABLE_TYPE)) {
            $typeStartIndex = $tokens->getNextMeaningfulToken($typeStartIndex);
        }

        $namespaceNameLength = \strlen($namespaceName);
        $types = $this->getTypes($tokens, $typeStartIndex, $type->getEndIndex());

        $prefix = $namespaceName ? '\\'.$namespaceName.'\\' : '\\';
        foreach ($types as $typeName => [$startIndex, $endIndex]) {
            $typeNameLower = strtolower($typeName);
            if (!str_starts_with($typeNameLower, $prefix)) {
                continue; // no shorter type possible
            }

            $typeName = substr($typeName, 1);
            $typeNameLower = substr($typeNameLower, 1);

            if (isset($uses[$typeNameLower])) {
                // if the type without leading "\" equals any of the full "uses" long names, it can be replaced with the short one
                $tokens->overrideRange($startIndex, $endIndex, $this->namespacedStringToTokens($uses[$typeNameLower]));
            } elseif ('' === $namespaceName) {
                // if we are in the global namespace and the type is not imported the leading '\' can be removed (TODO nice config candidate)
                foreach ($uses as $useShortName) {
                    if (strtolower($useShortName) === $typeNameLower) {
                        continue 2;
                    }
                }

                $tokens->overrideRange($startIndex, $endIndex, $this->namespacedStringToTokens($typeName));
            } elseif ($typeNameLower !== $namespaceName && str_starts_with($typeNameLower, $namespaceName)) {
                // if the type starts with namespace and the type is not the same as the namespace it can be shortened
                $typeNameShort = substr($typeName, $namespaceNameLength + 1);

                // if short names are the same, but long one are different then it cannot be shortened
                foreach ($uses as $useLongName => $useShortName) {
                    if (
                        strtolower($typeNameShort) === strtolower($useShortName)
                        && strtolower($typeName) !== strtolower($useLongName)
                    ) {
                        continue 2;
                    }
                }

                $tokens->overrideRange($startIndex, $endIndex, $this->namespacedStringToTokens($typeNameShort));
            }
        }
    }

    /**
     * @return iterable<string, array{int, int}>
     */
    private function getTypes(Tokens $tokens, int $index, int $endIndex): iterable
    {
        $index = $typeStartIndex = $typeEndIndex = $tokens->getNextMeaningfulToken($index - 1);
        $type = $tokens[$index]->getContent();

        while (true) {
            $index = $tokens->getNextMeaningfulToken($index);

            if ($tokens[$index]->isGivenKind([CT::T_TYPE_ALTERNATION, CT::T_TYPE_INTERSECTION])) {
                yield $type => [$typeStartIndex, $typeEndIndex];

                $index = $typeStartIndex = $typeEndIndex = $tokens->getNextMeaningfulToken($index);
                $type = $tokens[$index]->getContent();

                continue;
            }

            if ($index > $endIndex || !$tokens[$index]->isGivenKind([T_STRING, T_NS_SEPARATOR])) {
                yield $type => [$typeStartIndex, $typeEndIndex];

                break;
            }

            $typeEndIndex = $index;
            $type .= $tokens[$index]->getContent();
        }
    }

    /**
     * @return Token[]
     */
    private function namespacedStringToTokens(string $input): array
    {
        $tokens = [];
        $parts = explode('\\', $input);

        foreach ($parts as $index => $part) {
            $tokens[] = new Token([T_STRING, $part]);

            if ($index !== \count($parts) - 1) {
                $tokens[] = new Token([T_NS_SEPARATOR, '\\']);
            }
        }

        return $tokens;
    }
}
