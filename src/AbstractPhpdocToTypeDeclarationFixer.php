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
    /**
     * @var string
     */
    private $classRegex = '/^\\\\?[a-zA-Z_\\x7f-\\xff](?:\\\\?[a-zA-Z0-9_\\x7f-\\xff]+)*$/';

    /**
     * @var array<string, int>
     */
    private $versionSpecificTypes = [
        'void' => 70100,
        'iterable' => 70100,
        'object' => 70200,
        'mixed' => 80000,
    ];

    /**
     * @var array<string, bool>
     */
    private $scalarTypes = [
        'bool' => true,
        'float' => true,
        'int' => true,
        'string' => true,
    ];

    /**
     * @var array<string, bool>
     */
    private static $syntaxValidationCache = [];

    /**
     * {@inheritdoc}
     */
    public function isRisky(): bool
    {
        return true;
    }

    abstract protected function isSkippedType(string $type): bool;

    /**
     * {@inheritdoc}
     */
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
        static $specialTypes = [
            'array' => [CT::T_ARRAY_TYPEHINT, 'array'],
            'callable' => [T_CALLABLE, 'callable'],
            'static' => [T_STATIC, 'static'],
        ];

        $newTokens = [];

        if (true === $isNullable && 'mixed' !== $type) {
            $newTokens[] = new Token([CT::T_NULLABLE_TYPE, '?']);
        }

        if (isset($specialTypes[$type])) {
            $newTokens[] = new Token($specialTypes[$type]);
        } else {
            $typeUnqualified = ltrim($type, '\\');

            if (isset($this->scalarTypes[$typeUnqualified]) || isset($this->versionSpecificTypes[$typeUnqualified])) {
                // 'scalar's, 'void', 'iterable' and 'object' must be unqualified
                $newTokens[] = new Token([T_STRING, $typeUnqualified]);
            } else {
                foreach (explode('\\', $type) as $nsIndex => $value) {
                    if (0 === $nsIndex && '' === $value) {
                        continue;
                    }

                    if (0 < $nsIndex) {
                        $newTokens[] = new Token([T_NS_SEPARATOR, '\\']);
                    }

                    $newTokens[] = new Token([T_STRING, $value]);
                }
            }
        }

        return $newTokens;
    }

    protected function getCommonTypeFromAnnotation(Annotation $annotation, bool $isReturnType): ?array
    {
        $typesExpression = $annotation->getTypeExpression();

        $commonType = $typesExpression->getCommonType();
        $isNullable = $typesExpression->allowsNull();

        if (null === $commonType) {
            return null;
        }

        if ($isNullable && (\PHP_VERSION_ID < 70100 || 'void' === $commonType)) {
            return null;
        }

        if ('static' === $commonType && (!$isReturnType || \PHP_VERSION_ID < 80000)) {
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
        } elseif (1 !== Preg::match($this->classRegex, $commonType)) {
            return null;
        }

        return [$commonType, $isNullable];
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
