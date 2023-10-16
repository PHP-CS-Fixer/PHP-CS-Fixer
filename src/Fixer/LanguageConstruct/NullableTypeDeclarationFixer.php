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
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolver;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolverInterface;
use PhpCsFixer\FixerConfiguration\FixerOptionBuilder;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\FixerDefinition\VersionSpecification;
use PhpCsFixer\FixerDefinition\VersionSpecificCodeSample;
use PhpCsFixer\Preg;
use PhpCsFixer\Tokenizer\Analyzer\Analysis\TypeAnalysis;
use PhpCsFixer\Tokenizer\Analyzer\FunctionsAnalyzer;
use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use PhpCsFixer\Tokenizer\TokensAnalyzer;

/**
 * @author John Paul E. Balandan, CPA <paulbalandan@gmail.com>
 */
final class NullableTypeDeclarationFixer extends AbstractFixer implements ConfigurableFixerInterface
{
    private const OPTION_SYNTAX_UNION = 'union';
    private const OPTION_SYNTAX_QUESTION_MARK = 'question_mark';

    private int $candidateTokenKind;

    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(
            'Nullable single type declaration should be standardised using configured syntax.',
            [
                new VersionSpecificCodeSample(
                    "<?php\nfunction bar(null|int \$value, null|\\Closure \$callable): void {}\n",
                    new VersionSpecification(8_00_00)
                ),
                new VersionSpecificCodeSample(
                    "<?php\nfunction baz(?int \$value, ?\\stdClass \$obj, ?array \$config): ?int {}\n",
                    new VersionSpecification(8_00_00),
                    ['syntax' => self::OPTION_SYNTAX_UNION]
                ),
                new VersionSpecificCodeSample(
                    '<?php
class ValueObject
{
    public null|string $name;
    public ?int $count;
    public null|bool $internal;
    public null|\Closure $callback;
}
',
                    new VersionSpecification(8_00_00),
                    ['syntax' => self::OPTION_SYNTAX_QUESTION_MARK]
                ),
            ]
        );
    }

    public function isCandidate(Tokens $tokens): bool
    {
        return \PHP_VERSION_ID >= 8_00_00 && $tokens->isTokenKindFound($this->candidateTokenKind);
    }

    /**
     * {@inheritdoc}
     *
     * Must run before OrderedTypesFixer, TypesSpacesFixer.
     * Must run after NullableTypeDeclarationForDefaultNullValueFixer, SingleSpaceAroundConstructFixer.
     */
    public function getPriority(): int
    {
        return 2;
    }

    public function configure(array $configuration): void
    {
        parent::configure($configuration);

        $this->candidateTokenKind = self::OPTION_SYNTAX_QUESTION_MARK === $this->configuration['syntax']
            ? CT::T_TYPE_ALTERNATION // `|` -> `?`
            : CT::T_NULLABLE_TYPE; // `?` -> `|`
    }

    protected function createConfigurationDefinition(): FixerConfigurationResolverInterface
    {
        return new FixerConfigurationResolver([
            (new FixerOptionBuilder('syntax', 'Whether to use question mark (`?`) or explicit `null` union for nullable type.'))
                ->setAllowedValues([self::OPTION_SYNTAX_UNION, self::OPTION_SYNTAX_QUESTION_MARK])
                ->setDefault(self::OPTION_SYNTAX_QUESTION_MARK)
                ->getOption(),
        ]);
    }

    protected function applyFix(\SplFileInfo $file, Tokens $tokens): void
    {
        $functionsAnalyzer = new FunctionsAnalyzer();

        foreach (array_reverse($this->getElements($tokens), true) as $index => $type) {
            if ('property' === $type) {
                $this->normalizePropertyType($tokens, $index);

                continue;
            }

            $this->normalizeMethodReturnType($functionsAnalyzer, $tokens, $index);
            $this->normalizeMethodArgumentType($functionsAnalyzer, $tokens, $index);
        }
    }

    /**
     * @return array<int, string>
     *
     * @phpstan-return array<int, 'function'|'property'>
     */
    private function getElements(Tokens $tokens): array
    {
        $tokensAnalyzer = new TokensAnalyzer($tokens);

        $elements = array_map(
            static fn (array $element): string => 'method' === $element['type'] ? 'function' : $element['type'],
            array_filter(
                $tokensAnalyzer->getClassyElements(),
                static fn (array $element): bool => \in_array($element['type'], ['method', 'property'], true)
            )
        );

        foreach ($tokens as $index => $token) {
            if (
                $token->isGivenKind(T_FN)
                || ($token->isGivenKind(T_FUNCTION) && !isset($elements[$index]))
            ) {
                $elements[$index] = 'function';
            }
        }

        return $elements;
    }

    private function collectTypeAnalysis(Tokens $tokens, int $startIndex, int $endIndex): ?TypeAnalysis
    {
        $type = '';
        $typeStartIndex = $tokens->getNextMeaningfulToken($startIndex);
        $typeEndIndex = $typeStartIndex;

        for ($i = $typeStartIndex; $i < $endIndex; ++$i) {
            if ($tokens[$i]->isWhitespace() || $tokens[$i]->isComment()) {
                continue;
            }

            $type .= $tokens[$i]->getContent();
            $typeEndIndex = $i;
        }

        return '' !== $type ? new TypeAnalysis($type, $typeStartIndex, $typeEndIndex) : null;
    }

    private function isTypeNormalizable(TypeAnalysis $typeAnalysis): bool
    {
        if (!$typeAnalysis->isNullable()) {
            return false;
        }

        $type = $typeAnalysis->getName();

        if (str_contains($type, '&')) {
            return false; // skip DNF types
        }

        if (!str_contains($type, '|')) {
            return true;
        }

        return 1 === substr_count($type, '|') && Preg::match('/(?:\|null$|^null\|)/i', $type);
    }

    private function normalizePropertyType(Tokens $tokens, int $index): void
    {
        $propertyEndIndex = $index;
        $propertyModifiers = [T_PRIVATE, T_PROTECTED, T_PUBLIC, T_STATIC, T_VAR];

        if (\defined('T_READONLY')) {
            $propertyModifiers[] = T_READONLY; // @TODO: Drop condition when PHP 8.1+ is required
        }

        do {
            $index = $tokens->getPrevMeaningfulToken($index);
        } while (!$tokens[$index]->isGivenKind($propertyModifiers));

        $propertyType = $this->collectTypeAnalysis($tokens, $index, $propertyEndIndex);

        if (null === $propertyType || !$this->isTypeNormalizable($propertyType)) {
            return;
        }

        $this->normalizeNullableType($tokens, $propertyType);
    }

    private function normalizeMethodArgumentType(FunctionsAnalyzer $functionsAnalyzer, Tokens $tokens, int $index): void
    {
        foreach (array_reverse($functionsAnalyzer->getFunctionArguments($tokens, $index), true) as $argumentInfo) {
            $argumentType = $argumentInfo->getTypeAnalysis();

            if (null === $argumentType || !$this->isTypeNormalizable($argumentType)) {
                continue;
            }

            $this->normalizeNullableType($tokens, $argumentType);
        }
    }

    private function normalizeMethodReturnType(FunctionsAnalyzer $functionsAnalyzer, Tokens $tokens, int $index): void
    {
        $returnType = $functionsAnalyzer->getFunctionReturnType($tokens, $index);

        if (null === $returnType || !$this->isTypeNormalizable($returnType)) {
            return;
        }

        $this->normalizeNullableType($tokens, $returnType);
    }

    private function normalizeNullableType(Tokens $tokens, TypeAnalysis $typeAnalysis): void
    {
        $type = $typeAnalysis->getName();

        if (!str_contains($type, '|') && !str_contains($type, '&')) {
            $type = ($typeAnalysis->isNullable() ? '?' : '').$type;
        }

        $isQuestionMarkSyntax = self::OPTION_SYNTAX_QUESTION_MARK === $this->configuration['syntax'];

        if ($isQuestionMarkSyntax) {
            $normalizedType = $this->convertToNullableType($type);
            $normalizedTypeAsString = implode('', $normalizedType);
        } else {
            $normalizedType = $this->convertToExplicitUnionType($type);
            $normalizedTypeAsString = implode('|', $normalizedType);
        }

        if ($normalizedTypeAsString === $type) {
            return; // nothing to fix
        }

        $tokens->overrideRange(
            $typeAnalysis->getStartIndex(),
            $typeAnalysis->getEndIndex(),
            $this->createTypeDeclarationTokens($normalizedType, $isQuestionMarkSyntax)
        );
    }

    /**
     * @return list<string>
     */
    private function convertToNullableType(string $type): array
    {
        if (str_starts_with($type, '?')) {
            return [$type]; // no need to convert; already fixed
        }

        return ['?', Preg::replace('/(?:\|null$|^null\|)/i', '', $type)];
    }

    /**
     * @return list<string>
     */
    private function convertToExplicitUnionType(string $type): array
    {
        if (str_contains($type, '|')) {
            return [$type]; // no need to convert; already fixed
        }

        return ['null', substr($type, 1)];
    }

    /**
     * @param list<string> $types
     *
     * @return list<Token>
     */
    private function createTypeDeclarationTokens(array $types, bool $isQuestionMarkSyntax): array
    {
        static $specialTypes = [
            '?' => [CT::T_NULLABLE_TYPE, '?'],
            'array' => [CT::T_ARRAY_TYPEHINT, 'array'],
            'callable' => [T_CALLABLE, 'callable'],
            'static' => [T_STATIC, 'static'],
        ];

        $count = \count($types);
        $newTokens = [];

        foreach ($types as $index => $type) {
            if (isset($specialTypes[$type])) {
                $newTokens[] = new Token($specialTypes[$type]);
            } else {
                foreach (explode('\\', $type) as $nsIndex => $value) {
                    if (0 === $nsIndex && '' === $value) {
                        continue;
                    }

                    if ($nsIndex > 0) {
                        $newTokens[] = new Token([T_NS_SEPARATOR, '\\']);
                    }

                    $newTokens[] = new Token([T_STRING, $value]);
                }
            }

            if ($index <= $count - 2 && !$isQuestionMarkSyntax) {
                $newTokens[] = new Token([CT::T_TYPE_ALTERNATION, '|']);
            }
        }

        return $newTokens;
    }
}
