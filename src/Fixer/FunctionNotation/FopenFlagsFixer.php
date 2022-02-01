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

use PhpCsFixer\AbstractFopenFlagFixer;
use PhpCsFixer\Fixer\ConfigurableFixerInterface;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolver;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolverInterface;
use PhpCsFixer\FixerConfiguration\FixerOptionBuilder;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

final class FopenFlagsFixer extends AbstractFopenFlagFixer implements ConfigurableFixerInterface
{
    /**
     * {@inheritdoc}
     */
    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(
            'The flags in `fopen` calls must omit `t`, and `b` must be omitted or included consistently.',
            [
                new CodeSample("<?php\n\$a = fopen(\$foo, 'rwt');\n"),
                new CodeSample("<?php\n\$a = fopen(\$foo, 'rwt');\n", ['b_mode' => false]),
            ],
            null,
            'Risky when the function `fopen` is overridden.'
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function createConfigurationDefinition(): FixerConfigurationResolverInterface
    {
        return new FixerConfigurationResolver([
            (new FixerOptionBuilder('b_mode', 'The `b` flag must be used (`true`) or omitted (`false`).'))
                ->setAllowedTypes(['bool'])
                ->setDefault(true)
                ->getOption(),
        ]);
    }

    protected function fixFopenFlagToken(Tokens $tokens, int $argumentStartIndex, int $argumentEndIndex): void
    {
        $argumentFlagIndex = null;

        for ($i = $argumentStartIndex; $i <= $argumentEndIndex; ++$i) {
            if ($tokens[$i]->isGivenKind([T_WHITESPACE, T_COMMENT, T_DOC_COMMENT])) {
                continue;
            }

            if (null !== $argumentFlagIndex) {
                return; // multiple meaningful tokens found, no candidate for fixing
            }

            $argumentFlagIndex = $i;
        }

        // check if second argument is candidate
        if (null === $argumentFlagIndex || !$tokens[$argumentFlagIndex]->isGivenKind(T_CONSTANT_ENCAPSED_STRING)) {
            return;
        }

        $content = $tokens[$argumentFlagIndex]->getContent();
        $contentQuote = $content[0]; // `'`, `"`, `b` or `B`

        if ('b' === $contentQuote || 'B' === $contentQuote) {
            $binPrefix = $contentQuote;
            $contentQuote = $content[1]; // `'` or `"`
            $mode = substr($content, 2, -1);
        } else {
            $binPrefix = '';
            $mode = substr($content, 1, -1);
        }

        if (false === $this->isValidModeString($mode)) {
            return;
        }

        $mode = str_replace('t', '', $mode);

        if (true === $this->configuration['b_mode']) {
            if (!str_contains($mode, 'b')) {
                $mode .= 'b';
            }
        } else {
            $mode = str_replace('b', '', $mode);
        }

        $newContent = $binPrefix.$contentQuote.$mode.$contentQuote;

        if ($content !== $newContent) {
            $tokens[$argumentFlagIndex] = new Token([T_CONSTANT_ENCAPSED_STRING, $newContent]);
        }
    }
}
