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

namespace PhpCsFixer\Fixer\FunctionNotation;

use PhpCsFixer\AbstractPhpdocToTypeDeclarationFixer;
use PhpCsFixer\DocBlock\Annotation;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\FixerDefinition\VersionSpecification;
use PhpCsFixer\FixerDefinition\VersionSpecificCodeSample;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @author Jan Gantzert <jan@familie-gantzert.de>
 */
final class PhpdocToParamTypeFixer extends AbstractPhpdocToTypeDeclarationFixer
{
    private const MINIMUM_PHP_VERSION = 70000;

    /**
     * @var array{int, string}[]
     */
    private const EXCLUDE_FUNC_NAMES = [
        [T_STRING, '__clone'],
        [T_STRING, '__destruct'],
    ];

    /**
     * @var array<string, true>
     */
    private const SKIPPED_TYPES = [
        'mixed' => true,
        'resource' => true,
        'static' => true,
        'void' => true,
    ];

    /**
     * {@inheritdoc}
     */
    public function getDefinition(): FixerDefinitionInterface
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
                new VersionSpecificCodeSample(
                    '<?php

/** @param Foo $foo */
function foo($foo) {}
/** @param string $foo */
function bar($foo) {}
',
                    new VersionSpecification(70100),
                    ['scalar_types' => false]
                ),
            ],
            null,
            'This rule is EXPERIMENTAL and [1] is not covered with backward compatibility promise. [2] `@param` annotation is mandatory for the fixer to make changes, signatures of methods without it (no docblock, inheritdocs) will not be fixed. [3] Manual actions are required if inherited signatures are not properly documented.'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function isCandidate(Tokens $tokens): bool
    {
        return \PHP_VERSION_ID >= self::MINIMUM_PHP_VERSION && $tokens->isTokenKindFound(T_FUNCTION);
    }

    /**
     * {@inheritdoc}
     *
     * Must run before NoSuperfluousPhpdocTagsFixer, PhpdocAlignFixer.
     * Must run after AlignMultilineCommentFixer, CommentToPhpdocFixer, PhpdocIndentFixer, PhpdocScalarFixer, PhpdocToCommentFixer, PhpdocTypesFixer.
     */
    public function getPriority(): int
    {
        return 8;
    }

    protected function isSkippedType(string $type): bool
    {
        return isset(self::SKIPPED_TYPES[$type]);
    }

    /**
     * {@inheritdoc}
     */
    protected function applyFix(\SplFileInfo $file, Tokens $tokens): void
    {
        for ($index = $tokens->count() - 1; 0 < $index; --$index) {
            if (!$tokens[$index]->isGivenKind(T_FUNCTION)) {
                continue;
            }

            $funcName = $tokens->getNextMeaningfulToken($index);
            if ($tokens[$funcName]->equalsAny(self::EXCLUDE_FUNC_NAMES, false)) {
                continue;
            }

            $paramTypeAnnotations = $this->findAnnotations('param', $tokens, $index);

            foreach ($paramTypeAnnotations as $paramTypeAnnotation) {
                $typeInfo = $this->getCommonTypeFromAnnotation($paramTypeAnnotation, false);

                if (null === $typeInfo) {
                    continue;
                }

                list($paramType, $isNullable) = $typeInfo;

                $startIndex = $tokens->getNextTokenOfKind($index, ['(']) + 1;
                $variableIndex = $this->findCorrectVariable($tokens, $startIndex - 1, $paramTypeAnnotation);

                if (null === $variableIndex) {
                    continue;
                }

                $byRefIndex = $tokens->getPrevMeaningfulToken($variableIndex);
                if ($tokens[$byRefIndex]->equals('&')) {
                    $variableIndex = $byRefIndex;
                }

                if ($this->hasParamTypeHint($tokens, $variableIndex)) {
                    continue;
                }

                if (!$this->isValidSyntax(sprintf('<?php function f(%s $x) {}', $paramType))) {
                    continue;
                }

                $tokens->insertAt($variableIndex, array_merge(
                    $this->createTypeDeclarationTokens($paramType, $isNullable),
                    [new Token([T_WHITESPACE, ' '])]
                ));
            }
        }
    }

    private function findCorrectVariable(Tokens $tokens, int $index, Annotation $paramTypeAnnotation): ?int
    {
        $nextFunction = $tokens->getNextTokenOfKind($index, [[T_FUNCTION]]);
        $variableIndex = $tokens->getNextTokenOfKind($index, [[T_VARIABLE]]);

        if (\is_int($nextFunction) && $variableIndex > $nextFunction) {
            return null;
        }

        if (!isset($tokens[$variableIndex])) {
            return null;
        }

        $variableToken = $tokens[$variableIndex]->getContent();
        if ($paramTypeAnnotation->getVariableName() === $variableToken) {
            return $variableIndex;
        }

        return $this->findCorrectVariable($tokens, $index + 1, $paramTypeAnnotation);
    }

    /**
     * Determine whether the function already has a param type hint.
     *
     * @param int $index The index of the end of the function definition line, EG at { or ;
     */
    private function hasParamTypeHint(Tokens $tokens, int $index): bool
    {
        $prevIndex = $tokens->getPrevMeaningfulToken($index);

        return !$tokens[$prevIndex]->equalsAny([',', '(']);
    }
}
