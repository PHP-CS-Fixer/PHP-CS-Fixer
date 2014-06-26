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

if (!defined('T_TRAIT')) {
    define('T_TRAIT', 1001);
}

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
class OneClassPerFileFixer implements FixerInterface
{
    public function fix(\SplFileInfo $file, $content)
    {
        $classes = array();
        $classTokens = array('T_CLASS', 'T_INTERFACE', 'T_TRAIT');
        $tokens = token_get_all($content);

        for ($i = 0, $max = count($tokens); $i < $max; ++$i) {
            $token = $tokens[$i];

            if (is_array($token) && in_array(token_name($token[0]), $classTokens)) {
                $classes[] = $tokens[$i + 2][1];
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
        return 'PHP file MUST contain at most one class.';
    }
}
