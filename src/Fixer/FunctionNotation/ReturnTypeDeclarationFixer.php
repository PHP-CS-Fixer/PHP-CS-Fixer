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
use PhpCsFixer\Fixer\ConfigurationDefinitionFixerInterface;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolver;
use PhpCsFixer\FixerConfiguration\FixerOptionBuilder;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\VersionSpecification;
use PhpCsFixer\FixerDefinition\VersionSpecificCodeSample;
use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
final class ReturnTypeDeclarationFixer extends AbstractFixer implements ConfigurationDefinitionFixerInterface
{
    /**
     * {@inheritdoc}
     */
    public function getDefinition()
    {
        $versionSpecification = new VersionSpecification(70000);

        return new FixerDefinition(
            'There should be one or no space before colon, and one space after it in return type declarations, according to configuration.',
            array(
                new VersionSpecificCodeSample(
                    "<?php\nfunction foo(int \$a):string {};",
                    $versionSpecification
                ),
                new VersionSpecificCodeSample(
                    "<?php\nfunction foo(int \$a):string {};",
                    $versionSpecification,
                    array('space_before' => 'none')
                ),
                new VersionSpecificCodeSample(
                    "<?php\nfunction foo(int \$a):string {};",
                    $versionSpecification,
                    array('space_before' => 'one')
                ),
            ),
            'Rule is applied only in a PHP 7+ environment.'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function isCandidate(Tokens $tokens)
    {
        return PHP_VERSION_ID >= 70000 && $tokens->isTokenKindFound(CT::T_TYPE_COLON);
    }

    /**
     * {@inheritdoc}
     */
    protected function applyFix(\SplFileInfo $file, Tokens $tokens)
    {
        $oneSpaceBefore = 'one' === $this->configuration['space_before'];

        for ($index = 0, $limit = $tokens->count(); $index < $limit; ++$index) {
            if (!$tokens[$index]->isGivenKind(CT::T_TYPE_COLON)) {
                continue;
            }

            $previousIndex = $index - 1;
            $previousToken = $tokens[$previousIndex];

            if ($previousToken->isWhitespace()) {
                if (!$tokens[$tokens->getPrevNonWhitespace($index - 1)]->isComment()) {
                    if ($oneSpaceBefore) {
                        $tokens[$previousIndex] = new Token(array(T_WHITESPACE, ' '));
                    } else {
                        $tokens->clearAt($previousIndex);
                    }
                }
            } elseif ($oneSpaceBefore) {
                $tokenWasAdded = $tokens->ensureWhitespaceAtIndex($index, 0, ' ');

                if ($tokenWasAdded) {
                    ++$limit;
                }

                ++$index;
            }

            ++$index;

            $tokenWasAdded = $tokens->ensureWhitespaceAtIndex($index, 0, ' ');

            if ($tokenWasAdded) {
                ++$limit;
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function createConfigurationDefinition()
    {
        $spaceBefore = new FixerOptionBuilder('space_before', 'Spacing to apply before colon.');
        $spaceBefore = $spaceBefore
            ->setAllowedValues(array('one', 'none'))
            ->setDefault('none')
            ->getOption()
        ;

        return new FixerConfigurationResolver(array($spaceBefore));
    }
}
