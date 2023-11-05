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

namespace PhpCsFixer;

use PhpCsFixer\DocBlock\Annotation;
use PhpCsFixer\DocBlock\DocBlock;
use PhpCsFixer\DocBlock\TypeExpression;
use PhpCsFixer\Fixer\ConfigurableFixerInterface;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolver;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolverInterface;
use PhpCsFixer\FixerConfiguration\FixerOptionBuilder;
use PhpCsFixer\Tokenizer\Analyzer\NamespacesAnalyzer;
use PhpCsFixer\Tokenizer\Analyzer\NamespaceUsesAnalyzer;
use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @internal
 */
abstract class AbstractPhpdocToTypeDeclarationFixer extends AbstractFixer implements ConfigurableFixerInterface
{
    private const REGEX_CLASS = '(?:\\\\?+'.TypeExpression::REGEX_IDENTIFIER
        .'(\\\\'.TypeExpression::REGEX_IDENTIFIER.')*+)';

    /**
     * @var array<string, int>
     */
    private array $versionSpecificTypes = [
        'void' => 7_01_00,
        'iterable' => 7_01_00,
        'object' => 7_02_00,
        'mixed' => 8_00_00,
        'never' => 8_01_00,
    ];

    /**
     * @var array<string, bool>
     */
    private array $scalarTypes = [
        'bool' => true,
        'float' => true,
        'int' => true,
        'string' => true,
    ];

    /**
     * @var array<string, bool>
     */
    private static array $syntaxValidationCache = [];

    public function isRisky(): bool
    {
        return true;
    }

    abstract protected function isSkippedType(string $type): bool;

    protected function createConfigurationDefinition(): FixerConfigurationResolverInterface
    {
        return new FixerConfigurationResolver([
            (new FixerOptionBuilder('scalar_types', 'Fix also scalar types; may have unexpected behaviour due to PHP bad type coercion system.'))
                ->setAllowedTypes(['bool'])
                ->setDefault(true)
                ->getOption(),
        ]);
    }

    /**
     * @param int $index The index of the function token
     */
    protected function findFunctionDocComment(Tokens $tokens, int $index): ?int
    {
        do {
            $index = $tokens->getPrevNonWhitespace($index);
        } while ($tokens[$index]->isGivenKind([
            T_COMMENT,
            T_ABSTRACT,
            T_FINAL,
            T_PRIVATE,
            T_PROTECTED,
            T_PUBLIC,
            T_STATIC,
        ]));

        if ($tokens[$index]->isGivenKind(T_DOC_COMMENT)) {
            return $index;
        }

        return null;
    }

    /**
     * @return Annotation[]
     */
    protected function getAnnotationsFromDocComment(string $name, Tokens $tokens, int $docCommentIndex): array
    {
        $namespacesAnalyzer = new NamespacesAnalyzer();
        $namespace = $namespacesAnalyzer->getNamespaceAt($tokens, $docCommentIndex);

        $namespaceUsesAnalyzer = new NamespaceUsesAnalyzer();
        $namespaceUses = $namespaceUsesAnalyzer->getDeclarationsInNamespace($tokens, $namespace);

        $doc = new DocBlock(
            $tokens[$docCommentIndex]->getContent(),
            $namespace,
            $namespaceUses
        );

        return $doc->getAnnotationsOfType($name);
    }

    /**
     * @return Token[]
     */
    protected function createTypeDeclarationTokens(string $type, bool $isNullable): array
    {
        $newTokens = [];

        if (true === $isNullable && 'mixed' !== $type) {
            $newTokens[] = new Token([CT::T_NULLABLE_TYPE, '?']);
        }

        $newTokens = array_merge(
            $newTokens,
            $this->createTokensFromRawType($type)->toArray()
        );

        // 'scalar's, 'void', 'iterable' and 'object' must be unqualified
        foreach ($newTokens as $i => $token) {
            if ($token->isGivenKind(T_STRING)) {
                $typeUnqualified = $token->getContent();

                if (
                    (isset($this->scalarTypes[$typeUnqualified]) || isset($this->versionSpecificTypes[$typeUnqualified]))
                    && isset($newTokens[$i - 1])
                    && '\\' === $newTokens[$i - 1]->getContent()
                ) {
                    unset($newTokens[$i - 1]);
                }
            }
        }

        return array_values($newTokens);
    }

    /**
     * Each fixer inheriting from this class must define a way of creating token collection representing type
     * gathered from phpDoc, e.g. `Foo|Bar` should be transformed into 3 tokens (`Foo`, `|` and `Bar`).
     * This can't be standardised, because some types may be allowed in one place, and invalid in others.
     *
     * @param string $type Type determined (and simplified) from phpDoc
     */
    abstract protected function createTokensFromRawType(string $type): Tokens;

    /**
     * @return null|array{string, bool}
     */
    protected function getCommonTypeInfo(TypeExpression $typesExpression, bool $isReturnType): ?array
    {
        $commonType = $typesExpression->getCommonType();
        $isNullable = $typesExpression->allowsNull();

        if (null === $commonType) {
            return null;
        }

        if ($isNullable && 'void' === $commonType) {
            return null;
        }

        if ('static' === $commonType && (!$isReturnType || \PHP_VERSION_ID < 8_00_00)) {
            $commonType = 'self';
        }

        if ($this->isSkippedType($commonType)) {
            return null;
        }

        if (isset($this->versionSpecificTypes[$commonType]) && \PHP_VERSION_ID < $this->versionSpecificTypes[$commonType]) {
            return null;
        }

        if (isset($this->scalarTypes[$commonType])) {
            if (false === $this->configuration['scalar_types']) {
                return null;
            }
        } elseif (!Preg::match('/^'.self::REGEX_CLASS.'$/', $commonType)) {
            return null;
        }

        return [$commonType, $isNullable];
    }

    protected function getUnionTypes(TypeExpression $typesExpression, bool $isReturnType): ?string
    {
        if (\PHP_VERSION_ID < 8_00_00) {
            return null;
        }

        if (!$typesExpression->isUnionType() || '|' !== $typesExpression->getTypesGlue()) {
            return null;
        }

        $types = $typesExpression->getTypes();
        $isNullable = $typesExpression->allowsNull();
        $unionTypes = [];
        $containsOtherThanIterableType = false;
        $containsOtherThanEmptyType = false;

        foreach ($types as $type) {
            if ('null' === $type) {
                continue;
            }

            if ($this->isSkippedType($type)) {
                return null;
            }

            if (isset($this->versionSpecificTypes[$type]) && \PHP_VERSION_ID < $this->versionSpecificTypes[$type]) {
                return null;
            }

            $typeExpression = new TypeExpression($type, null, []);
            $commonType = $typeExpression->getCommonType();

            if (!$containsOtherThanIterableType && !\in_array($commonType, ['array', 'Traversable', 'iterable'], true)) {
                $containsOtherThanIterableType = true;
            }
            if ($isReturnType && !$containsOtherThanEmptyType && !\in_array($commonType, ['null', 'void', 'never'], true)) {
                $containsOtherThanEmptyType = true;
            }

            if (!$isNullable && $typesExpression->allowsNull()) {
                $isNullable = true;
            }

            $unionTypes[] = $commonType;
        }

        if (!$containsOtherThanIterableType) {
            return null;
        }
        if ($isReturnType && !$containsOtherThanEmptyType) {
            return null;
        }

        if ($isNullable) {
            $unionTypes[] = 'null';
        }

        return implode($typesExpression->getTypesGlue(), array_unique($unionTypes));
    }

    final protected function isValidSyntax(string $code): bool
    {
        if (!isset(self::$syntaxValidationCache[$code])) {
            try {
                Tokens::fromCode($code);
                self::$syntaxValidationCache[$code] = true;
            } catch (\ParseError $e) {
                self::$syntaxValidationCache[$code] = false;
            }
        }

        return self::$syntaxValidationCache[$code];
    }
}
