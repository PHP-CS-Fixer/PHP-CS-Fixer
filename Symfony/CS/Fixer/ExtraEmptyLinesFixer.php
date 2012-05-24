<?php

/*
 * This file is part of the Symfony CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Fixer;

use Symfony\CS\FixerInterface;

/**
 * @author Christophe Coevoet <stof@notk.org>
 * @author Adrien Brault <adrien.brault@gmail.com>
 */
class ExtraEmptyLinesFixer implements FixerInterface
{
    /**
     * {@inheritdoc}
     */
    public function fix(\SplFileInfo $file, $content)
    {
        $regex = <<<'REGEX'
(?: # heredoc/nowdoc
    <<<(?P<nowdoc_quote>'?) (?P<nowdoc_delimiter>[_[:alpha:]][_[:alnum:]]*) (?P=nowdoc_quote)
        [^\g{+1}]*
    (\n(?P=nowdoc_delimiter))
)
|(?: # single quoted string
    '
        [^\\']*+
        (?:\\.[^\\']*+)*+
    '
)
|(?: # double quoted string
    "
        [^\\"]*+
        (?:\\.[^\\"]*+)*+
    "
)
|(?P<to_fix>
    \n{3}\n*
)
REGEX;

        // [Structure] Duplicated empty lines outside strings should not be used.
        return preg_replace_callback(sprintf('@%s@x', $regex), function ($matches) {
            if (isset($matches['to_fix'])) {
                return "\n\n";
            }

            return $matches[0];
        }, $content);
    }

    /**
     * {@inheritdoc}
     */
    public function supports(\SplFileInfo $file)
    {
        return true;
    }
}
