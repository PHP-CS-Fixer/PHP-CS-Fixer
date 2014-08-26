<?php
/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Fixer\All;

use Symfony\CS\FixerInterface;
use Symfony\CS\Token;
use Symfony\CS\Tokens;

/**
 * @author John Kelly <johnmkelly86@gmail.com>
 */
class SpaceBeforeClosingSemicolonFixer implements FixerInterface
{
    /**
     * {@inheritdoc}
     */
    public function fix(\SplFileInfo $file, $content)
    {
        $tokens = Tokens::fromCode($content);

        foreach ($tokens as $index => $token) {
            /** @var Token $token */
            if ($token->isArray() || ';' !== $token->content) {
                continue;
            }

            $prevNonWhitespaceIndex = null;
            $prevNonWhitespaceToken = $tokens->getPrevNonWhitespace($index, array(), $prevNonWhitespaceIndex);

            if (!$prevNonWhitespaceToken->isArray()) {
                for ($i = $index - 1; $i > $prevNonWhitespaceIndex; --$i) {
                    if (false === strpos($tokens[$i]->content, "\n")) {
                        $tokens[$i]->clear();
                    }
                }
            }
        }

        return $tokens->generateCode();
    }

    /**
     * {@inheritdoc}
     */
    public function getLevel()
    {
        return FixerInterface::ALL_LEVEL;
    }

    /**
     * {@inheritdoc}
     */
    public function getPriority()
    {
        return 0;
    }

    /**
     * {@inheritdoc}
     */
    public function supports(\SplFileInfo $file)
    {
        return 'php' === pathinfo($file->getFilename(), PATHINFO_EXTENSION);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'semicolon_spaces';
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'Single-line whitespaces before closing semicolon are prohibited.';
    }
}
