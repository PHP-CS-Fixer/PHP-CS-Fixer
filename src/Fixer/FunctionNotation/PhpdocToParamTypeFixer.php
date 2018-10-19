<?php

/*
 * This file is part of PHP CS Fixer.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *     Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace PhpCsFixer\Fixer\FunctionNotation;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\DocBlock\Annotation;
use PhpCsFixer\DocBlock\DocBlock;
use PhpCsFixer\Fixer\ConfigurationDefinitionFixerInterface;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolver;
use PhpCsFixer\FixerConfiguration\FixerOptionBuilder;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\VersionSpecification;
use PhpCsFixer\FixerDefinition\VersionSpecificCodeSample;
use PhpCsFixer\Preg;
use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @author Jan Gantzert <jan@familie-gantzert.de>
 */
final class PhpdocToParamTypeFixer extends AbstractFixer implements ConfigurationDefinitionFixerInterface
{
    /**
     * @var array
     */
    private $blacklistFuncNames = [
        [T_STRING, '__construct'],
        [T_STRING, '__destruct'],
        [T_STRING, '__clone'],
    ];

    /**
     * @var array
     */
    private $skippedTypes = [
        'mixed' => true,
        'resource' => true,
        'null' => true,
        'static' => true,
    ];

    /**
     * @var string
     */
    private $classRegex = '/^\\\\?[a-zA-Z_\\x7f-\\xff](?:\\\\?[a-zA-Z0-9_\\x7f-\\xff]+)*(?<array>\[\])*$/';

    /**
     * {@inheritdoc}
     */
    public function getDefinition()
    {
        return new FixerDefinition(
            'EXPERIMENTAL: Takes `@param` annotations of non-mixed types and adjusts accordingly the function signature. Requires PHP >= 7.0.',
            [
                new VersionSpecificCodeSample(
                    '<?php

/** @param string $bar */
function my_foo($bar)
{}
',
                    new VersionSpecification(70000)
                ),
                new VersionSpecificCodeSample(
                    '<?php

/** @param string|null $bar */
function my_foo($bar)
{}
',
                    new VersionSpecification(70100)
                ),
            ],
            null,
            '[1] This rule is EXPERIMENTAL and is not covered with backward compatibility promise. [2] `@param` annotation is mandatory for the fixer to make changes, signatures of methods without it (no docblock, inheritdocs) will not be fixed. [3] Manual actions are required if inherited signatures are not properly documented.'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function isCandidate(Tokens $tokens)
    {
        return \PHP_VERSION_ID >= 70000 && $tokens->isTokenKindFound(T_FUNCTION);
    }

    /**
     * {@inheritdoc}
     */
    public function getPriority()
    {
        // NoSuperfluousPhpdocTagsFixer.
        return 8;
    }

    /**
     * {@inheritdoc}
     */
    public function isRisky()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    protected function createConfigurationDefinition()
    {
        return new FixerConfigurationResolver([
            (new FixerOptionBuilder('scalar_types', 'Fix also scalar types; may have unexpected behaviour due to PHP bad type coercion system.'))
                ->setAllowedTypes(['bool'])
                ->setDefault(true)
                ->getOption(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    protected function applyFix(\SplFileInfo $file, Tokens $tokens)
    {
        for ($index = $tokens->count() - 1; 0 < $index; --$index) {
            if (!$tokens[$index]->isGivenKind(T_FUNCTION)) {
                continue;
            }

            $funcName = $tokens->getNextMeaningfulToken($index);
            if ($tokens[$funcName]->equalsAny($this->blacklistFuncNames, false)) {
                continue;
            }

            $paramTypeAnnotations = $this->findParamAnnotations($tokens, $index);

            foreach ($paramTypeAnnotations as $paramTypeAnnotation) {
                $types = array_values($paramTypeAnnotation->getTypes());
                $typesCount = \count($types);
                if (1 > $typesCount || 2 < $typesCount) {
                    continue;
                }

                $isNullable = false;
                $paramType = current($types);
                if (2 === $typesCount) {
                    $null = $types[0];
                    $paramType = $types[1];
                    if ('null' !== $null) {
                        $null = $types[1];
                        $paramType = $types[0];
                    }

                    if ('null' !== $null) {
                        continue;
                    }

                    $isNullable = true;

                    if (\PHP_VERSION_ID < 70100) {
                        continue;
                    }

                    if ('void' === $paramType) {
                        continue;
                    }
                }

                if (isset($this->skippedTypes[$paramType])) {
                    continue;
                }

                if (1 !== Preg::match($this->classRegex, $paramType, $matches)) {
                    continue;
                }

                if (isset($matches['array'])) {
                    $paramType = 'array';
                }

                $startIndex = $tokens->getNextTokenOfKind($index, ['(']) + 1;
                $variableIndex = $this->findCorrectVariable($tokens, $startIndex - 1, $paramTypeAnnotation);

                if ('(' === $tokens[$variableIndex - 1]->getContent()) {
                    if (!$tokens[$variableIndex]->isGivenKind([T_VARIABLE])) {
                        continue;
                    }
                } else {
                    if ($this->hasParamTypeHint($tokens, $variableIndex - 2)) {
                        continue;
                    }
                }

                $this->fixFunctionDefinition($tokens, $variableIndex, $isNullable, $paramType);
            }
        }
    }

    /**
     * Determine whether the function already has a param type hint.
     *
     * @param Tokens $tokens
     * @param int    $index  The index of the end of the function definition line, EG at { or ;
     *
     * @return bool
     */
    private function hasParamTypeHint(Tokens $tokens, $index)
    {
        return $tokens[$index]->isGivenKind([T_STRING, T_NS_SEPARATOR, CT::T_ARRAY_TYPEHINT, T_CALLABLE, CT::T_NULLABLE_TYPE]);
    }

    /**
     * @param Tokens $tokens
     * @param int    $index      The index of the end of the function definition line, EG at { or ;
     * @param bool   $isNullable
     * @param string $paramType
     */
    private function fixFunctionDefinition(Tokens $tokens, $index, $isNullable, $paramType)
    {
        static $specialTypes = [
            'array' => [CT::T_ARRAY_TYPEHINT, 'array'],
            'callable' => [T_CALLABLE, 'callable'],
        ];
        if (true === $isNullable) {
            $newTokens[] = new Token([CT::T_NULLABLE_TYPE, '?']);
        }

        if (isset($specialTypes[$paramType])) {
            $newTokens[] = new Token($specialTypes[$paramType]);
            $newTokens[] = new Token([T_WHITESPACE, ' ']);
        } else {
            foreach (explode('\\', $paramType) as $nsIndex => $value) {
                if (0 === $nsIndex && '' === $value) {
                    continue;
                }

                if (0 < $nsIndex) {
                    $newTokens[] = new Token([T_NS_SEPARATOR, '\\']);
                }
                $newTokens[] = new Token([T_STRING, $value]);
            }
            $newTokens[] = new Token([T_WHITESPACE, ' ']);
        }
        $tokens->insertAt($index, $newTokens);
    }

    /**
     * Find all the param annotations in the function's PHPDoc comment.
     *
     * @param Tokens $tokens
     * @param int    $index  The index of the function token
     *
     * @return Annotation[]
     */
    private function findParamAnnotations(Tokens $tokens, $index)
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

        if (!$tokens[$index]->isGivenKind(T_DOC_COMMENT)) {
            return [];
        }

        $doc = new DocBlock($tokens[$index]->getContent());

        return $doc->getAnnotationsOfType('param');
    }

    /**
     * @param Tokens $tokens
     * @param $index
     * @param $paramTypeAnnotation
     *
     * @return null|int
     */
    private function findCorrectVariable(Tokens $tokens, $index, $paramTypeAnnotation)
    {
        $variableIndex = $tokens->getNextTokenOfKind($index, [[T_VARIABLE]]);
        $variableToken = $tokens[$variableIndex]->getContent();
        preg_match('/@param\s*[^\s]+\s*([^\s]+)/', $paramTypeAnnotation->getContent(), $paramVariable);
        if (isset($paramVariable[1]) && $paramVariable[1] === $variableToken) {
            return $variableIndex;
        }

        return $this->findCorrectVariable($tokens, $index + 1, $paramTypeAnnotation);
    }
}
