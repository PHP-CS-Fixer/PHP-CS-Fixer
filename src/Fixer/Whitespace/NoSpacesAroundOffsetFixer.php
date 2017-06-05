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
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolverRootless;
use PhpCsFixer\FixerConfiguration\FixerOptionBuilder;
use PhpCsFixer\FixerConfiguration\FixerOptionValidatorGenerator;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @author Javier Spagnoletti <phansys@gmail.com>
 */
final class NoSpacesAroundOffsetFixer extends AbstractFixer implements ConfigurationDefinitionFixerInterface
{
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
     * {@inheritdoc}
     */
    protected function applyFix(\SplFileInfo $file, Tokens $tokens)
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
                if ($tokens[$index + 1]->isWhitespace(" \t")) {
                    $tokens->clearAt($index + 1);
                }

                // remove space before closing `]` or `}`
                if ($tokens[$endIndex - 1]->isWhitespace(" \t")) {
                    $tokens->clearAt($endIndex - 1);
                }
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
    protected function createConfigurationDefinition()
    {
        $generator = new FixerOptionValidatorGenerator();
        $values = array('inside', 'outside');

        $positions = new FixerOptionBuilder('positions', 'Whether spacing should be fixed inside and/or outside the offset braces.');
        $positions = $positions
            ->setAllowedTypes(array('array'))
            ->setAllowedValues(array(
                $generator->allowedValueIsSubsetOf($values),
            ))
            ->setDefault($values)
            ->getOption()
        ;

        return new FixerConfigurationResolverRootless('positions', array($positions));
    }
}
