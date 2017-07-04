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

namespace PhpCsFixer\Fixer\DoctrineAnnotation;

use Doctrine\Common\Annotations\DocLexer;
use PhpCsFixer\AbstractDoctrineAnnotationFixer;
use PhpCsFixer\ConfigurationException\InvalidFixerConfigurationException;
use PhpCsFixer\Doctrine\Annotation\Token;
use PhpCsFixer\Doctrine\Annotation\Tokens;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolver;
use PhpCsFixer\FixerConfiguration\FixerOptionBuilder;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;

/**
 * Fixes spaces around commas and assignment operators in Doctrine annotations.
 */
final class DoctrineAnnotationSpacesFixer extends AbstractDoctrineAnnotationFixer
{
    /**
     * {@inheritdoc}
     */
    public function getDefinition()
    {
        return new FixerDefinition(
            'Fixes spaces in Doctrine annotations.',
            [
                new CodeSample(
                    "<?php\n/**\n * @Foo ( )\n */\nclass Bar {}\n\n/**\n * @Foo(\"bar\" ,\"baz\")\n */\nclass Bar2 {}\n\n/**\n * @Foo(foo = \"foo\", bar = {\"foo\":\"foo\", \"bar\"=\"bar\"})\n */\nclass Bar3 {}"
                ),
            ],
            'There must not be any space around parentheses; commas must be preceded by no space and followed by one space; there must be no space around named arguments assignment operator; there must be one space around array assignment operator.'
        );
    }

    public function configure(array $configuration = null)
    {
        parent::configure($configuration);

        if (
            !$this->configuration['around_parentheses']
            && !$this->configuration['around_commas']
            && !$this->configuration['around_argument_assignments']
            && !$this->configuration['around_array_assignments']
        ) {
            throw new InvalidFixerConfigurationException(
                $this->getName(),
                'At least one of options "around_parentheses", "around_commas", "around_argument_assignments" and "around_array_assignments" must be enabled.'
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function createConfigurationDefinition()
    {
        return new FixerConfigurationResolver(array_merge(
            parent::createConfigurationDefinition()->getOptions(),
            [
                (new FixerOptionBuilder('around_parentheses', 'Whether to fix spaces around parentheses.'))
                    ->setAllowedTypes(['bool'])
                    ->setDefault(true)
                    ->getOption(),
                (new FixerOptionBuilder('around_commas', 'Whether to fix spaces around commas.'))
                    ->setAllowedTypes(['bool'])
                    ->setDefault(true)
                    ->getOption(),
                (new FixerOptionBuilder('around_argument_assignments', 'Whether to fix spaces around argument assignment operator.'))
                    ->setAllowedTypes(['bool'])
                    ->setDefault(true)
                    ->getOption(),
                (new FixerOptionBuilder('around_array_assignments', 'Whether to fix spaces around array assignment operators.'))
                    ->setAllowedTypes(['bool'])
                    ->setDefault(true)
                    ->getOption(),
            ]
        ));
    }

    /**
     * {@inheritdoc}
     */
    protected function fixAnnotations(Tokens $tokens)
    {
        if ($this->configuration['around_parentheses']) {
            $this->fixSpacesAroundParentheses($tokens);
        }

        if ($this->configuration['around_commas']) {
            $this->fixSpacesAroundCommas($tokens);
        }

        if ($this->configuration['around_argument_assignments'] || $this->configuration['around_array_assignments']) {
            $this->fixAroundAssignments($tokens);
        }
    }

    /**
     * @param Tokens $tokens
     */
    private function fixSpacesAroundParentheses(Tokens $tokens)
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
                if (false !== strpos($token->getContent(), "\n")) {
                    continue;
                }

                $token->clear();
            }
        }
    }

    /**
     * @param Tokens $tokens
     */
    private function fixSpacesAroundCommas(Tokens $tokens)
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

            if ($index < count($tokens) - 1 && !preg_match('/^\s/', $tokens[$index + 1]->getContent())) {
                $tokens->insertAt($index + 1, new Token(DocLexer::T_NONE, ' '));
            }
        }
    }

    /**
     * @param Tokens $tokens
     */
    private function fixAroundAssignments(Tokens $tokens)
    {
        $arguments = $this->configuration['around_argument_assignments'];
        $arrays = $this->configuration['around_array_assignments'];

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

            if ($arguments && DocLexer::T_CLOSE_PARENTHESIS === $endScopeType && $token->isType(DocLexer::T_EQUALS)) {
                $token = $tokens[$index - 1];
                if ($token->isType(DocLexer::T_NONE)) {
                    $token->clear();
                }

                $token = $tokens[$index + 1];
                if ($token->isType(DocLexer::T_NONE)) {
                    $token->clear();
                }

                continue;
            }

            if ($arrays && DocLexer::T_CLOSE_CURLY_BRACES === $endScopeType && $token->isType([DocLexer::T_EQUALS, DocLexer::T_COLON])) {
                $token = $tokens[$index + 1];
                if (!$token->isType(DocLexer::T_NONE)) {
                    $tokens->insertAt($index + 1, $token = new Token());
                }

                $token->setContent(' ');

                $token = $tokens[$index - 1];
                if (!$token->isType(DocLexer::T_NONE)) {
                    $tokens->insertAt($index, $token = new Token());
                }

                $token->setContent(' ');

                continue;
            }
        }
    }
}
