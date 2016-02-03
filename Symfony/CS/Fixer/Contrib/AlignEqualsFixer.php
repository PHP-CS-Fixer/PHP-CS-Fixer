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

namespace Symfony\CS\Fixer\Contrib;

use Symfony\CS\AbstractAlignFixer;
use Symfony\CS\Tokenizer\Tokens;

/**
 * @author Carlos Cirello <carlos.cirello.nl@gmail.com>
 * @author Graham Campbell <graham@mineuk.com>
 */
class AlignEqualsFixer extends AbstractAlignFixer
{
    /**
     * {@inheritdoc}
     */
    public function fix(\SplFileInfo $file, $content)
    {
        $data = $this->injectAlignmentPlaceholders($content);

        return $this->replacePlaceholder($data['tokens'], $data['deepestLevel']);
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'Align equals symbols in consecutive lines.';
    }

    /**
     * {@inheritdoc}
     */
    public function getPriority()
    {
        // should be run after the OperatorsSpacesFixer
        return -10;
    }

    /**
     * Inject into the text placeholders of candidates of vertical alignment.
     *
     * Output structure:
     * * Tokens $tokens
     * * int    $deepestLevel
     *
     * @param string $content
     *
     * @return array
     */
    private function injectAlignmentPlaceholders($content)
    {
        $deepestLevel = 0;
        $parenCount = 0;
        $bracketCount = 0;
        $tokens = Tokens::fromCode($content);

        foreach ($tokens as $token) {
            $tokenContent = $token->getContent();

            if (0 === $parenCount && 0 === $bracketCount && $token->equals('=')) {
                $token->setContent(sprintf(self::ALIGNABLE_PLACEHOLDER, $deepestLevel).$tokenContent);
                continue;
            }

            if ($token->isGivenKind(T_FUNCTION)) {
                ++$deepestLevel;
            } elseif ($token->equals('(')) {
                ++$parenCount;
            } elseif ($token->equals(')')) {
                --$parenCount;
            } elseif ($token->equals('[')) {
                ++$bracketCount;
            } elseif ($token->equals(']')) {
                --$bracketCount;
            }
        }

        return array(
            'tokens' => $tokens,
            'deepestLevel' => $deepestLevel,
        );
    }
}
