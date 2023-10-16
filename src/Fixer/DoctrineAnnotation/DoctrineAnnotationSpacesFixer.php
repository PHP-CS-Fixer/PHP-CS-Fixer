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

namespace PhpCsFixer\Fixer\DoctrineAnnotation;

use PhpCsFixer\AbstractDoctrineAnnotationFixer;
use PhpCsFixer\Doctrine\Annotation\DocLexer;
use PhpCsFixer\Doctrine\Annotation\Token;
use PhpCsFixer\Doctrine\Annotation\Tokens;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolver;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolverInterface;
use PhpCsFixer\FixerConfiguration\FixerOptionBuilder;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Preg;

/**
 * Fixes spaces around commas and assignment operators in Doctrine annotations.
 */
final class DoctrineAnnotationSpacesFixer extends AbstractDoctrineAnnotationFixer
{
    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(
            'Fixes spaces in Doctrine annotations.',
            [
                new CodeSample(
                    "<?php\n/**\n * @Foo ( )\n */\nclass Bar {}\n\n/**\n * @Foo(\"bar\" ,\"baz\")\n */\nclass Bar2 {}\n\n/**\n * @Foo(foo = \"foo\", bar = {\"foo\":\"foo\", \"bar\"=\"bar\"})\n */\nclass Bar3 {}\n"
                ),
                new CodeSample(
                    "<?php\n/**\n * @Foo(foo = \"foo\", bar = {\"foo\":\"foo\", \"bar\"=\"bar\"})\n */\nclass Bar {}\n",
                    ['after_array_assignments_equals' => false, 'before_array_assignments_equals' => false]
                ),
            ],
            'There must not be any space around parentheses; commas must be preceded by no space and followed by one space; there must be no space around named arguments assignment operator; there must be one space around array assignment operator.'
        );
    }

    /**
     * {@inheritdoc}
     *
     * Must run after DoctrineAnnotationArrayAssignmentFixer.
     */
    public function getPriority(): int
    {
        return 0;
    }

    protected function createConfigurationDefinition(): FixerConfigurationResolverInterface
    {
        return new FixerConfigurationResolver([
            ...parent::createConfigurationDefinition()->getOptions(),
            (new FixerOptionBuilder('around_parentheses', 'Whether to fix spaces around parentheses.'))
                ->setAllowedTypes(['bool'])
                ->setDefault(true)
                ->getOption(),
            (new FixerOptionBuilder('around_commas', 'Whether to fix spaces around commas.'))
                ->setAllowedTypes(['bool'])
                ->setDefault(true)
                ->getOption(),
            (new FixerOptionBuilder('before_argument_assignments', 'Whether to add, remove or ignore spaces before argument assignment operator.'))
                ->setAllowedTypes(['null', 'bool'])
                ->setDefault(false)
                ->getOption(),
            (new FixerOptionBuilder('after_argument_assignments', 'Whether to add, remove or ignore spaces after argument assignment operator.'))
                ->setAllowedTypes(['null', 'bool'])
                ->setDefault(false)
                ->getOption(),
            (new FixerOptionBuilder('before_array_assignments_equals', 'Whether to add, remove or ignore spaces before array `=` assignment operator.'))
                ->setAllowedTypes(['null', 'bool'])
                ->setDefault(true)
                ->getOption(),
            (new FixerOptionBuilder('after_array_assignments_equals', 'Whether to add, remove or ignore spaces after array assignment `=` operator.'))
                ->setAllowedTypes(['null', 'bool'])
                ->setDefault(true)
                ->getOption(),
            (new FixerOptionBuilder('before_array_assignments_colon', 'Whether to add, remove or ignore spaces before array `:` assignment operator.'))
                ->setAllowedTypes(['null', 'bool'])
                ->setDefault(true)
                ->getOption(),
            (new FixerOptionBuilder('after_array_assignments_colon', 'Whether to add, remove or ignore spaces after array assignment `:` operator.'))
                ->setAllowedTypes(['null', 'bool'])
                ->setDefault(true)
                ->getOption(),
        ]);
    }

    protected function fixAnnotations(Tokens $doctrineAnnotationTokens): void
    {
        if (true === $this->configuration['around_parentheses']) {
            $this->fixSpacesAroundParentheses($doctrineAnnotationTokens);
        }

        if (true === $this->configuration['around_commas']) {
            $this->fixSpacesAroundCommas($doctrineAnnotationTokens);
        }

        if (
            null !== $this->configuration['before_argument_assignments']
            || null !== $this->configuration['after_argument_assignments']
            || null !== $this->configuration['before_array_assignments_equals']
            || null !== $this->configuration['after_array_assignments_equals']
            || null !== $this->configuration['before_array_assignments_colon']
            || null !== $this->configuration['after_array_assignments_colon']
        ) {
            $this->fixAroundAssignments($doctrineAnnotationTokens);
        }
    }

    private function fixSpacesAroundParentheses(Tokens $tokens): void
    {
        $inAnnotationUntilIndex = null;

        foreach ($tokens as $index => $token) {
            if (null !== $inAnnotationUntilIndex) {
                if ($index === $inAnnotationUntilIndex) {
                    $inAnnotationUntilIndex = null;

                    continue;
                }
            } elseif ($tokens[$index]->isType(DocLexer::T_AT)) {
                $endIndex = $tokens->getAnnotationEnd($index);
                if (null !== $endIndex) {
                    $inAnnotationUntilIndex = $endIndex + 1;
                }

                continue;
            }

            if (null === $inAnnotationUntilIndex) {
                continue;
            }

            if (!$token->isType([DocLexer::T_OPEN_PARENTHESIS, DocLexer::T_CLOSE_PARENTHESIS])) {
                continue;
            }

            if ($token->isType(DocLexer::T_OPEN_PARENTHESIS)) {
                $token = $tokens[$index - 1];
                if ($token->isType(DocLexer::T_NONE)) {
                    $token->clear();
                }

                $token = $tokens[$index + 1];
            } else {
                $token = $tokens[$index - 1];
            }

            if ($token->isType(DocLexer::T_NONE)) {
                if (str_contains($token->getContent(), "\n")) {
                    continue;
                }

                $token->clear();
            }
        }
    }

    private function fixSpacesAroundCommas(Tokens $tokens): void
    {
        $inAnnotationUntilIndex = null;

        foreach ($tokens as $index => $token) {
            if (null !== $inAnnotationUntilIndex) {
                if ($index === $inAnnotationUntilIndex) {
                    $inAnnotationUntilIndex = null;

                    continue;
                }
            } elseif ($tokens[$index]->isType(DocLexer::T_AT)) {
                $endIndex = $tokens->getAnnotationEnd($index);
                if (null !== $endIndex) {
                    $inAnnotationUntilIndex = $endIndex;
                }

                continue;
            }

            if (null === $inAnnotationUntilIndex) {
                continue;
            }

            if (!$token->isType(DocLexer::T_COMMA)) {
                continue;
            }

            $token = $tokens[$index - 1];
            if ($token->isType(DocLexer::T_NONE)) {
                $token->clear();
            }

            if ($index < \count($tokens) - 1 && !Preg::match('/^\s/', $tokens[$index + 1]->getContent())) {
                $tokens->insertAt($index + 1, new Token(DocLexer::T_NONE, ' '));
            }
        }
    }

    private function fixAroundAssignments(Tokens $tokens): void
    {
        $beforeArguments = $this->configuration['before_argument_assignments'];
        $afterArguments = $this->configuration['after_argument_assignments'];
        $beforeArraysEquals = $this->configuration['before_array_assignments_equals'];
        $afterArraysEquals = $this->configuration['after_array_assignments_equals'];
        $beforeArraysColon = $this->configuration['before_array_assignments_colon'];
        $afterArraysColon = $this->configuration['after_array_assignments_colon'];

        $scopes = [];
        foreach ($tokens as $index => $token) {
            $endScopeType = end($scopes);
            if (false !== $endScopeType && $token->isType($endScopeType)) {
                array_pop($scopes);

                continue;
            }

            if ($tokens[$index]->isType(DocLexer::T_AT)) {
                $scopes[] = DocLexer::T_CLOSE_PARENTHESIS;

                continue;
            }

            if ($tokens[$index]->isType(DocLexer::T_OPEN_CURLY_BRACES)) {
                $scopes[] = DocLexer::T_CLOSE_CURLY_BRACES;

                continue;
            }

            if (DocLexer::T_CLOSE_PARENTHESIS === $endScopeType && $token->isType(DocLexer::T_EQUALS)) {
                $this->updateSpacesAfter($tokens, $index, $afterArguments);
                $this->updateSpacesBefore($tokens, $index, $beforeArguments);

                continue;
            }

            if (DocLexer::T_CLOSE_CURLY_BRACES === $endScopeType) {
                if ($token->isType(DocLexer::T_EQUALS)) {
                    $this->updateSpacesAfter($tokens, $index, $afterArraysEquals);
                    $this->updateSpacesBefore($tokens, $index, $beforeArraysEquals);

                    continue;
                }

                if ($token->isType(DocLexer::T_COLON)) {
                    $this->updateSpacesAfter($tokens, $index, $afterArraysColon);
                    $this->updateSpacesBefore($tokens, $index, $beforeArraysColon);
                }
            }
        }
    }

    private function updateSpacesAfter(Tokens $tokens, int $index, ?bool $insert): void
    {
        $this->updateSpacesAt($tokens, $index + 1, $index + 1, $insert);
    }

    private function updateSpacesBefore(Tokens $tokens, int $index, ?bool $insert): void
    {
        $this->updateSpacesAt($tokens, $index - 1, $index, $insert);
    }

    private function updateSpacesAt(Tokens $tokens, int $index, int $insertIndex, ?bool $insert): void
    {
        if (null === $insert) {
            return;
        }

        $token = $tokens[$index];
        if ($insert) {
            if (!$token->isType(DocLexer::T_NONE)) {
                $tokens->insertAt($insertIndex, $token = new Token());
            }

            $token->setContent(' ');
        } elseif ($token->isType(DocLexer::T_NONE)) {
            $token->clear();
        }
    }
}
