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

namespace PhpCsFixer\Fixer\Operator;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\Fixer\ConfigurableFixerInterface;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolver;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolverInterface;
use PhpCsFixer\FixerConfiguration\FixerOptionBuilder;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Tokens;
use PhpCsFixer\Tokenizer\TokensAnalyzer;

/**
 * @author Gregor Harlan <gharlan@web.de>
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
final class UnaryOperatorSpacesFixer extends AbstractFixer implements ConfigurableFixerInterface
{
    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(
            'Unary operators should be placed adjacent to their operands.',
            [
                new CodeSample("<?php\n\$sample ++;\n-- \$sample;\n\$sample = ! ! \$a;\n\$sample = ~  \$c;\nfunction & foo(){}\n"),
                new CodeSample(
                    '<?php
function foo($a, ...   $b) { return (--   $a) * ($b   ++);}
',
                    ['only_dec_inc' => false]
                ),
                new CodeSample(
                    '<?php
function foo($a, ...   $b) { return (--   $a) * ($b   ++);}
',
                    ['only_dec_inc' => true]
                ),
            ]
        );
    }

    /**
     * {@inheritdoc}
     *
     * Must run before NotOperatorWithSpaceFixer, NotOperatorWithSuccessorSpaceFixer.
     */
    public function getPriority(): int
    {
        return 0;
    }

    public function isCandidate(Tokens $tokens): bool
    {
        return true;
    }

    protected function createConfigurationDefinition(): FixerConfigurationResolverInterface
    {
        return new FixerConfigurationResolver([
            (new FixerOptionBuilder('only_dec_inc', 'Limit to increment and decrement operators.'))
                ->setAllowedTypes(['bool'])
                ->setDefault(false)
                ->getOption(),
        ]);
    }

    protected function applyFix(\SplFileInfo $file, Tokens $tokens): void
    {
        $tokensAnalyzer = new TokensAnalyzer($tokens);

        for ($index = $tokens->count() - 1; $index >= 0; --$index) {
            if (true === $this->configuration['only_dec_inc'] && !$tokens[$index]->isGivenKind([T_DEC, T_INC])) {
                continue;
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
}
