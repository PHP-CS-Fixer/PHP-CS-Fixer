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

/**
 * @author Fabien Potencier <fabien@symfony.com>
 */
class PhpdocParamsAlignmentFixer implements FixerInterface
{
    const REGEX = '/^ {5}\* @param\s+(?P<hint>[^\$]+?)\s+(?P<var>\$[^\s]+)(\s+(?P<desc>.*)|\s*)$/';

    public function fix(\SplFileInfo $file, $content)
    {
        $lines = explode("\n", $content);
        for ($i = 0, $l = count($lines); $i < $l; $i++) {
            $items = array();
            if (preg_match(self::REGEX, $lines[$i], $matches)) {
                $current = $i;
                $items[] = $matches;
                while (preg_match(self::REGEX, $lines[++$i], $matches)) {
                    $items[] = $matches;
                }

                // find the right number of spaces
                $beforeVar = 1;
                $afterVar = 1;

                // compute the max length of the hint
                $hintMax = 0;
                foreach ($items as $item) {
                    if ($hintMax < $len = strlen($item['hint'])) {
                        $hintMax = $len;
                    }
                }

                // compute the max length of the variables
                $varMax = 0;
                foreach ($items as $item) {
                    if ($varMax < $len = strlen($item['var'])) {
                        $varMax = $len;
                    }
                }

                // update
                foreach ($items as $j => $item) {
                    $lines[$current + $j] =
                        '     * @param '
                        .$item['hint']
                        .str_repeat(' ', $hintMax - strlen($item['hint']) + 1)
                        .$item['var']
                        .(
                            isset($item['desc'])
                            ? str_repeat(' ', $varMax - strlen($item['var']) + 1).$item['desc']
                            : ''
                        )
                    ;
                }
            }
        }

        return implode("\n", $lines);
    }

    public function getLevel()
    {
        return FixerInterface::ALL_LEVEL;
    }

    public function getPriority()
    {
        return 0;
    }

    public function supports(\SplFileInfo $file)
    {
        return 'php' == $file->getExtension();
    }

    public function getName()
    {
        return 'phpdoc_params';
    }

    public function getDescription()
    {
        return 'All items of the @param phpdoc tags must be aligned vertically.';
    }
}
