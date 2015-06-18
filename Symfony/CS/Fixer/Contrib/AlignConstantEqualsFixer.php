<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Fixer\Contrib;

use Symfony\CS\AbstractAlignFixer;
use Symfony\CS\Tokenizer\Tokens;

/**
 * @author Sullivan Senechal <soullivaneuh@gmail.com>
 */
class AlignConstantEqualsFixer extends AbstractAlignFixer
{
    /**
     * {@inheritdoc}
     */
    public function fix(\SplFileInfo $file, $content)
    {
        list($tokens, $deepestLevel) = $this->injectAlignmentPlaceholders($content);

        return $this->replacePlaceholder($tokens, $deepestLevel);
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'Align constant equals symbols in consecutive lines.';
    }

    /**
     * Inject into the text placeholders of candidates of vertical alignment.
     *
     * @param string $content
     *
     * @return array($code, $deepestLevel)
     */
    private function injectAlignmentPlaceholders($content)
    {
        $deepestLevel = 0;
        $constantDetected = false;
        $tokens = Tokens::fromCode($content);

        foreach ($tokens as $token) {
            $tokenContent = $token->getContent();

            if ($tokenContent === 'const') {
                $constantDetected = true;
            }

            if (true === $constantDetected && $token->equals('=')) {
                $token->setContent(sprintf(self::ALIGNABLE_PLACEHOLDER, $deepestLevel).$tokenContent);
                $constantDetected = false;
                continue;
            }

            if ($token->isGivenKind(T_CLASS)) {
                ++$deepestLevel;
            }
        }

        return array($tokens, $deepestLevel);
    }
}
