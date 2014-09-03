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
 * @author John Kelly <wablam@gmail.com>
 * @author Graham Campbell <graham@mineuk.com>
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

            $previous = $tokens[$index - 1];

            if ($previous->isWhitespace()) {
                $previous->clear();
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
        return true;
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
