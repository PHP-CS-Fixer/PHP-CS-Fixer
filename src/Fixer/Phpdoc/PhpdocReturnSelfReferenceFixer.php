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

namespace PhpCsFixer\Fixer\Phpdoc;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\DocBlock\DocBlock;
use PhpCsFixer\Fixer\ConfigurableFixerInterface;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolver;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolverInterface;
use PhpCsFixer\FixerConfiguration\FixerOptionBuilder;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use PhpCsFixer\Tokenizer\TokensAnalyzer;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\Options;

final class PhpdocReturnSelfReferenceFixer extends AbstractFixer implements ConfigurableFixerInterface
{
    /**
     * @var string[]
     */
    private static array $toTypes = [
        '$this',
        'static',
        'self',
    ];

    /**
     * {@inheritdoc}
     */
    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(
            'The type of `@return` annotations of methods returning a reference to itself must the configured one.',
            [
                new CodeSample(
                    '<?php
class Sample
{
    /**
     * @return this
     */
    public function test1()
    {
        return $this;
    }

    /**
     * @return $self
     */
    public function test2()
    {
        return $this;
    }
}
'
                ),
                new CodeSample(
                    '<?php
class Sample
{
    /**
     * @return this
     */
    public function test1()
    {
        return $this;
    }

    /**
     * @return $self
     */
    public function test2()
    {
        return $this;
    }
}
',
                    ['replacements' => ['this' => 'self']]
                ),
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function isCandidate(Tokens $tokens): bool
    {
        return \count($tokens) > 10 && $tokens->isAllTokenKindsFound([T_DOC_COMMENT, T_FUNCTION]) && $tokens->isAnyTokenKindsFound(Token::getClassyTokenKinds());
    }

    /**
     * {@inheritdoc}
     *
     * Must run before NoSuperfluousPhpdocTagsFixer, PhpdocAlignFixer.
     * Must run after AlignMultilineCommentFixer, CommentToPhpdocFixer, PhpdocIndentFixer, PhpdocScalarFixer, PhpdocToCommentFixer, PhpdocTypesFixer.
     */
    public function getPriority(): int
    {
        return 10;
    }

    /**
     * {@inheritdoc}
     */
    protected function applyFix(\SplFileInfo $file, Tokens $tokens): void
    {
        $tokensAnalyzer = new TokensAnalyzer($tokens);

        foreach ($tokensAnalyzer->getClassyElements() as $index => $element) {
            if ('method' === $element['type']) {
                $this->fixMethod($tokens, $index);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function createConfigurationDefinition(): FixerConfigurationResolverInterface
    {
        $default = [
            'this' => '$this',
            '@this' => '$this',
            '$self' => 'self',
            '@self' => 'self',
            '$static' => 'static',
            '@static' => 'static',
        ];

        return new FixerConfigurationResolver([
            (new FixerOptionBuilder('replacements', 'Mapping between replaced return types with new ones.'))
                ->setAllowedTypes(['array'])
                ->setNormalizer(static function (Options $options, array $value) use ($default): array {
                    $normalizedValue = [];

                    foreach ($value as $from => $to) {
                        if (\is_string($from)) {
                            $from = strtolower($from);
                        }

                        if (!isset($default[$from])) {
                            throw new InvalidOptionsException(sprintf(
                                'Unknown key "%s", expected any of "%s".',
                                \gettype($from).'#'.$from,
                                implode('", "', array_keys($default))
                            ));
                        }

                        if (!\in_array($to, self::$toTypes, true)) {
                            throw new InvalidOptionsException(sprintf(
                                'Unknown value "%s", expected any of "%s".',
                                \is_object($to) ? \get_class($to) : \gettype($to).(\is_resource($to) ? '' : '#'.$to),
                                implode('", "', self::$toTypes)
                            ));
                        }

                        $normalizedValue[$from] = $to;
                    }

                    return $normalizedValue;
                })
                ->setDefault($default)
                ->getOption(),
        ]);
    }

    private function fixMethod(Tokens $tokens, int $index): void
    {
        static $methodModifiers = [T_STATIC, T_FINAL, T_ABSTRACT, T_PRIVATE, T_PROTECTED, T_PUBLIC];

        // find PHPDoc of method (if any)
        while (true) {
            $tokenIndex = $tokens->getPrevMeaningfulToken($index);
            if (!$tokens[$tokenIndex]->isGivenKind($methodModifiers)) {
                break;
            }

            $index = $tokenIndex;
        }

        $docIndex = $tokens->getPrevNonWhitespace($index);
        if (!$tokens[$docIndex]->isGivenKind(T_DOC_COMMENT)) {
            return;
        }

        // find @return
        $docBlock = new DocBlock($tokens[$docIndex]->getContent());
        $returnsBlock = $docBlock->getAnnotationsOfType('return');

        if (0 === \count($returnsBlock)) {
            return; // no return annotation found
        }

        $returnsBlock = $returnsBlock[0];
        $types = $returnsBlock->getTypes();

        if (0 === \count($types)) {
            return; // no return type(s) found
        }

        $newTypes = [];

        foreach ($types as $type) {
            $newTypes[] = $this->configuration['replacements'][strtolower($type)] ?? $type;
        }

        if ($types === $newTypes) {
            return;
        }

        $returnsBlock->setTypes($newTypes);
        $tokens[$docIndex] = new Token([T_DOC_COMMENT, $docBlock->getContent()]);
    }
}
