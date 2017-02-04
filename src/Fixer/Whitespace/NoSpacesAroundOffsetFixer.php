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

namespace PhpCsFixer\Fixer\Whitespace;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\Fixer\ConfigurationDefinitionFixerInterface;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolver;
use PhpCsFixer\FixerConfiguration\FixerOption;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @author Javier Spagnoletti <phansys@gmail.com>
 */
final class NoSpacesAroundOffsetFixer extends AbstractFixer implements ConfigurationDefinitionFixerInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigurationDefinition()
    {
        $configurationDefinition = new FixerConfigurationResolver();
        $values = array('inside', 'outside');

        $positions = new FixerOption('positions', 'Whether spacing should be fixed inside and/or outside the offset braces.');
        $positions
            ->setAllowedValueIsSubsetOf($values)
            ->setDefault($values)
        ;

        return $configurationDefinition
            ->addOption($positions)
            ->mapRootConfigurationTo('positions')
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function fix(\SplFileInfo $file, Tokens $tokens)
    {
        foreach ($tokens as $index => $token) {
            if (!$token->equalsAny(array('[', array(CT::T_ARRAY_INDEX_CURLY_BRACE_OPEN)))) {
                continue;
            }

            if (in_array('inside', $this->configuration['positions'], true)) {
                if ($token->equals('[')) {
                    $endIndex = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_INDEX_SQUARE_BRACE, $index);
                } else {
                    $endIndex = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_ARRAY_INDEX_CURLY_BRACE, $index);
                }

                // remove space after opening `[` or `{`
                $this->removeWhitespaceToken($tokens[$index + 1]);

                // remove space before closing `]` or `}`
                $this->removeWhitespaceToken($tokens[$endIndex - 1]);
            }

            if (in_array('outside', $this->configuration['positions'], true)) {
                $prevNonWhitespaceIndex = $tokens->getPrevNonWhitespace($index);
                if ($tokens[$prevNonWhitespaceIndex]->isComment()) {
                    continue;
                }

                $tokens->removeLeadingWhitespace($index);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getDefinition()
    {
        return new FixerDefinition(
            'There MUST NOT be spaces around offset braces.',
            array(
                new CodeSample("<?php\n\$sample = \$b [ 'a' ] [ 'b' ];"),
                new CodeSample("<?php\n\$sample = \$b [ 'a' ] [ 'b' ];", array('positions' => array('inside'))),
                new CodeSample("<?php\n\$sample = \$b [ 'a' ] [ 'b' ];", array('positions' => array('outside'))),
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function isCandidate(Tokens $tokens)
    {
        return $tokens->isAnyTokenKindsFound(array('[', CT::T_ARRAY_INDEX_CURLY_BRACE_OPEN));
    }

    /**
     * Removes the token if it is single line whitespace.
     *
     * @param Token $token
     */
    private function removeWhitespaceToken(Token $token)
    {
        if ($token->isWhitespace(" \t")) {
            $token->clear();
        }
    }
}
