<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Fixer;

use Symfony\CS\FixerInterface;
use Symfony\CS\Tokens;

/**
 * @author Fabien Potencier <fabien@symfony.com>
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
class ShortTagFixer implements FixerInterface
{
    public function fix(\SplFileInfo $file, $content)
    {
        $tokens = Tokens::fromCode($content);

        foreach ($tokens as $token) {
            if ($token->isGivenKind(T_OPEN_TAG)) {
                $token->content = preg_replace('/<\?(\s|$)/', '<?php$1', $token->content, 1);
            }
        }

        return $tokens->generateCode();
    }

    public function getLevel()
    {
        // defined in PSR1 ¶2.1
        return FixerInterface::PSR1_LEVEL;
    }

    public function getPriority()
    {
        return 0;
    }

    public function supports(\SplFileInfo $file)
    {
        return 'php' === pathinfo($file->getFilename(), PATHINFO_EXTENSION);
    }

    public function getName()
    {
        return 'short_tag';
    }

    public function getDescription()
    {
        return 'PHP code must use the long <?php ?> tags or the short-echo <?= ?> tags; it must not use the other tag variations.';
    }
}
