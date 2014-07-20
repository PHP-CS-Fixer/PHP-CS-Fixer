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
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
class OneClassPerFileFixer implements FixerInterface
{
    public function fix(\SplFileInfo $file, $content)
    {
        $classes = array();
        $tokens = Tokens::fromCode($content);

        foreach ($tokens as $index => $token) {
            if ($token->isClassy() && $tokens[$index + 2]->isArray()) {
                $classes[] = $tokens[$index + 2]->content;
            }
        }

        if (count($classes) > 1) {
            echo '! Found multiple classes/interfaces/traits in '.strtr($file->getRealPath(), '\\', '/').': '.implode(', ', $classes).PHP_EOL;
        }

        return $content;
    }

    public function getLevel()
    {
        return FixerInterface::PSR0_LEVEL;
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
        return 'one_class_per_file';
    }

    public function getDescription()
    {
        return 'PHP file MUST contain at most one class (detect only).';
    }
}
