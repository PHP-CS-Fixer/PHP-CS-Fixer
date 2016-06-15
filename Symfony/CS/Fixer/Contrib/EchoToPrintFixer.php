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

use Symfony\CS\AbstractFixer;
use Symfony\CS\Tokenizer\Token;
use Symfony\CS\Tokenizer\Tokens;

/**
 * @author Sullivan Senechal <soullivaneuh@gmail.com>
 */
final class EchoToPrintFixer extends AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function fix(\SplFileInfo $file, $content)
    {
        $tokens = Tokens::fromCode($content);

        $echoTokens = $tokens->findGivenKind(T_ECHO);

        if (defined('HHVM_VERSION')) {
            /*
             * HHVM parses '<?=' as T_ECHO instead of T_OPEN_TAG_WITH_ECHO
             *
             * @see https://github.com/facebook/hhvm/issues/4809
             * @see https://github.com/facebook/hhvm/issues/7161
             */
            $echoTokens = array_filter(
                $echoTokens,
                function (Token $token) {
                    return 0 !== strpos($token->getContent(), '<?=');
                }
            );
        }

        foreach ($echoTokens as $echoIndex => $echoToken) {
            $nextTokenIndex = $tokens->getNextMeaningfulToken($echoIndex);
            $endTokenIndex = $tokens->getNextTokenOfKind($echoIndex, array(';', array(T_CLOSE_TAG)));
            $canBeConverted = true;
            for ($i = $nextTokenIndex; $i < $endTokenIndex; ++$i) {
                if ($tokens[$i]->equalsAny(array('(', '['))) {
                    $blockType = $tokens->detectBlockType($tokens[$i]);
                    $i = $tokens->findBlockEnd($blockType['type'], $i);
                }

                if ($tokens[$i]->equals(',')) {
                    $canBeConverted = false;
                    break;
                }
            }

            if (false === $canBeConverted) {
                continue;
            }

            $tokens->overrideAt($echoIndex, array(T_PRINT, 'print'));
        }

        return $tokens->generateCode();
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'Converts echo language construct to print if possible.';
    }

    /**
     * EchoToPrintFixer should run after ShortEchoTagFixer.
     *
     * {@inheritdoc}
     */
    public function getPriority()
    {
        return -10;
    }
}
