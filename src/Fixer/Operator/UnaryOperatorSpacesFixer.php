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

namespace PhpCsFixer\Fixer\Operator;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\Fixer\ConfigurationDefinitionFixerInterface;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolver;
use PhpCsFixer\FixerConfiguration\FixerOptionBuilder;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use PhpCsFixer\Tokenizer\TokensAnalyzer;

/**
 * @author Gregor Harlan <gharlan@web.de>
 * @author Javier Spagnoletti <phansys@gmail.com>
 */
final class UnaryOperatorSpacesFixer extends AbstractFixer implements ConfigurationDefinitionFixerInterface
{
    /**
     * @internal
     */
    const NO_TRAILING = 'no_trailing';

    /**
     * @internal
     */
    const ONE_TRAILING = 'one_trailing';

    /**
     * @internal
     */
    const LEADING_AND_TRAILING = 'leading_and_trailing';

    /**
     * {@inheritdoc}
     */
    public function getDefinition()
    {
        return new FixerDefinition(
            'Unary operators should be placed adjacent to their operands.',
            [
                new CodeSample(
                    "<?php\n\$sample ++;\n-- \$sample;\n\$sample = ! ! \$a;\n\$sample = ~  \$c;\nfunction & foo(){}\n\$a = ! \$b;\n\$c /- \$d;\n\$a *- \$b;\n"
                ),
                new CodeSample(
                    "<?php\nif (!\$bar) {\n    echo \"Help!\";\n}\n",
                    ['not_operator_space' => self::ONE_TRAILING]
                ),
                new CodeSample(
                    "<?php\nif (!\$bar) {\n    echo \"Help!\";\n}\n",
                    ['not_operator_space' => self::LEADING_AND_TRAILING]
                ),
            ]
        );
    }

    /**
     * {@inheritdoc}
     *
     * Must run before NotOperatorWithSpaceFixer, NotOperatorWithSuccessorSpaceFixer.
     */
    public function getPriority()
    {
        return 0;
    }

    /**
     * {@inheritdoc}
     */
    public function isCandidate(Tokens $tokens)
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    protected function applyFix(\SplFileInfo $file, Tokens $tokens)
    {
        $tokensAnalyzer = new TokensAnalyzer($tokens);

        for ($index = $tokens->count() - 1; $index >= 0; --$index) {
            if ($tokens[$index]->equals('!')) {
                if (self::ONE_TRAILING === $this->configuration['not_operator_space']) {
                    if (!$tokens[$index + 1]->isWhitespace()) {
                        $tokens->insertAt($index + 1, new Token([T_WHITESPACE, ' ']));
                    } else {
                        $tokens[$index + 1] = new Token([T_WHITESPACE, ' ']);
                    }

                    continue;
                }

                if (self::LEADING_AND_TRAILING === $this->configuration['not_operator_space']) {
                    if (!$tokens[$index + 1]->isWhitespace()) {
                        $tokens->insertAt($index + 1, new Token([T_WHITESPACE, ' ']));
                    }

                    if (!$tokens[$index - 1]->isWhitespace()) {
                        $tokens->insertAt($index, new Token([T_WHITESPACE, ' ']));
                    }

                    continue;
                }
            }

            if ($tokensAnalyzer->isUnarySuccessorOperator($index)) {
                if (!$tokens[$tokens->getPrevNonWhitespace($index)]->isComment()) {
                    $tokens->removeLeadingWhitespace($index);
                }

                continue;
            }

            if ($tokensAnalyzer->isUnaryPredecessorOperator($index)) {
                $tokens->removeTrailingWhitespace($index);

                continue;
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function createConfigurationDefinition()
    {
        return new FixerConfigurationResolver(
            [
                (new FixerOptionBuilder('not_operator_space', 'Space around logical `!` (`not`) operators.'))
                    ->setDefault(self::NO_TRAILING)
                    ->setAllowedValues([self::NO_TRAILING, self::ONE_TRAILING, self::LEADING_AND_TRAILING])
                    ->getOption(),
            ]
        );
    }
}
