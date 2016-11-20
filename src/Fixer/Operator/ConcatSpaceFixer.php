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
use PhpCsFixer\ConfigurationException\InvalidFixerConfigurationException;
use PhpCsFixer\FixerDefinition;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 * @author SpacePossum
 */
final class ConcatSpaceFixer extends AbstractFixer
{
    private $fixCallback;

    /**
     * Configuration must have one element 'spacing' with value 'none' (default) or 'one'.
     *
     * @param null|array $configuration
     */
    public function configure(array $configuration = null)
    {
        if (null === $configuration) {
            $this->fixCallback = 'fixConcatenationToNoSpace';

            return;
        }

        if (!array_key_exists('spacing', $configuration)) {
            throw new InvalidFixerConfigurationException($this->getName(), 'Missing "spacing" configuration.');
        }

        switch ($configuration['spacing']) {
            case 'one':
                $this->fixCallback = 'fixConcatenationToSingleSpace';

                break;
            case 'none':
                $this->fixCallback = 'fixConcatenationToNoSpace';

                break;
            default:
                throw new InvalidFixerConfigurationException($this->getName(), '"spacing" configuration must be "one" or "none".');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function fix(\SplFileInfo $file, Tokens $tokens)
    {
        $callBack = $this->fixCallback;
        for ($index = $tokens->count() - 1; $index >= 0; --$index) {
            if ($tokens[$index]->equals('.')) {
                $this->$callBack($tokens, $index);
            }
        }
    }

    public function getDefinition()
    {
        return new FixerDefinition(
            $this->getDescription(),
            null,
            array(
                array(
                    "<?php\n\$foo = 'bar' . 3 . 'baz'.'qux';",
                    null,
                ),
                array(
                    "<?php\n\$foo = 'bar' . 3 . 'baz'.'qux';",
                    array('spacing' => 'none'),
                ),
                array(
                    "<?php\n\$foo = 'bar' . 3 . 'baz'.'qux';",
                    array('spacing' => 'one'),
                ),
            ),
            "Configuration must have one element 'spacing' with value 'none' (default) or 'one'.",
            array('spacing' => 'none'),
            null
        );
    }

    /**
     * {@inheritdoc}
     */
    public function isCandidate(Tokens $tokens)
    {
        return $tokens->isTokenKindFound('.');
    }

    /**
     * {@inheritdoc}
     */
    protected function getDescription()
    {
        return 'Concatenation should be spaced according configuration.';
    }

    /**
     * @param Tokens $tokens
     * @param int    $index  index of concatenation '.' token
     */
    private function fixConcatenationToNoSpace(Tokens $tokens, $index)
    {
        if (!$tokens[$tokens->getPrevNonWhitespace($index)]->isGivenKind(T_LNUMBER)) {
            $tokens->removeLeadingWhitespace($index, " \t");
        }

        if (!$tokens[$tokens->getNextNonWhitespace($index)]->isGivenKind(array(T_LNUMBER, T_COMMENT, T_DOC_COMMENT))) {
            $tokens->removeTrailingWhitespace($index, " \t");
        }
    }

    /**
     * @param Tokens $tokens
     * @param int    $index  index of concatenation '.' token
     */
    private function fixConcatenationToSingleSpace(Tokens $tokens, $index)
    {
        $this->fixWhiteSpaceAroundConcatToken($tokens, $index, 1);
        $this->fixWhiteSpaceAroundConcatToken($tokens, $index, -1);
    }

    /**
     * @param Tokens $tokens
     * @param int    $index  index of concatenation '.' token
     * @param int    $offset 1 or -1
     */
    private function fixWhiteSpaceAroundConcatToken(Tokens $tokens, $index, $offset)
    {
        $offsetIndex = $index + $offset;

        if (!$tokens[$offsetIndex]->isWhitespace()) {
            $tokens->insertAt($index + (1 === $offset ?: 0), new Token(array(T_WHITESPACE, ' ')));

            return;
        }

        if (false !== strpos($tokens[$offsetIndex]->getContent(), "\n")) {
            return;
        }

        if ($tokens[$index + $offset * 2]->isComment()) {
            return;
        }

        $tokens[$offsetIndex]->setContent(' ');
    }
}
