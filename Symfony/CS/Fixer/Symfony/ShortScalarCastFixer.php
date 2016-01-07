<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Fixer\Symfony;

use Symfony\CS\AbstractFixer;
use Symfony\CS\Tokenizer\Tokens;

/**
 * @author SpacePossum
 */
final class ShortScalarCastFixer extends AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function fix(\SplFileInfo $file, $content)
    {
        static $castMap = array(
            'boolean' => 'bool',
            'integer' => 'int',
            'double' => 'float',
            'real' => 'float',
        );

        $tokens = Tokens::fromCode($content);

        for ($index = 0, $count = $tokens->count(); $index  < $count; ++$index) {
            if (!$tokens[$index]->isCast()) {
                continue;
            }

            $castFrom = trim(substr($tokens[$index]->getContent(), 1, -1));
            $castFromLowered = strtolower($castFrom);
            if (!array_key_exists($castFromLowered, $castMap)) {
                continue;
            }

            $tokens[$index]->setContent(str_replace($castFrom, $castMap[$castFromLowered], $tokens[$index]->getContent()));
        }

        return $tokens->generateCode();
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'Cast "(boolean)" and "(integer)" should be written as "(bool)" and "(int)". "(double)" and "(real)" as "(float)".';
    }
}
