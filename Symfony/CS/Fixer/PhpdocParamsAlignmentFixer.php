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
    private $regex;

    public function __construct()
    {
        // e.g. @param <hint> <$var>
        $paramTag = '(?P<tag>param)\s+(?P<hint>[^$]+?)\s+(?P<var>&?\$[^\s]+)';
        // e.g. @return <hint>
        $returnThrowsTag = '(?P<tag2>return|throws)\s+(?P<hint2>[^$]+?)';
        // optional <desc>
        $desc = '(?:\s+(?P<desc>.*)|\s*)';
        $this->regex = '/^ {5}\* @(?:'.$paramTag.'|'.$returnThrowsTag.')'.$desc.'$/';
    }

    public function fix(\SplFileInfo $file, $content)
    {
        $lines = explode("\n", $content);
        for ($i = 0, $l = count($lines); $i < $l; $i++) {
            $items = array();
            if ($matches = $this->getMatches($lines[$i])) {
                $current = $i;
                $items[] = $matches;
                while ($matches = $this->getMatches($lines[++$i])) {
                    $items[] = $matches;
                }

                // find the right number of spaces
                $beforeVar = 1;
                $afterVar = 1;

                // compute the max length of the tag, hint and variables
                $tagMax = 0;
                $hintMax = 0;
                $varMax = 0;
                foreach ($items as $item) {
                    $tagMax  = max($tagMax, strlen($item['tag']));
                    $hintMax = max($hintMax, strlen($item['hint']));
                    $varMax  = max($varMax, strlen($item['var']));
                }

                // update
                foreach ($items as $j => $item) {
                    $line =
                        '     * @'
                        .$item['tag']
                        .str_repeat(' ', $tagMax - strlen($item['tag']) + 1)
                        .$item['hint']
                    ;

                    if (!empty($item['var'])) {
                        $line .=
                            str_repeat(' ', $hintMax - strlen($item['hint']) + 1)
                            .$item['var']
                            .(
                                !empty($item['desc'])
                                ? str_repeat(' ', $varMax - strlen($item['var']) + 1).$item['desc']
                                : ''
                            )
                        ;
                    } elseif (!empty($item['desc'])) {
                        $line .= str_repeat(' ', $hintMax - strlen($item['hint']) + 1).$item['desc'];
                    }

                    $lines[$current + $j] = $line;
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
        return 'php' == pathinfo($file->getFilename(), PATHINFO_EXTENSION);
    }

    public function getName()
    {
        return 'phpdoc_params';
    }

    public function getDescription()
    {
        return 'All items of the @param phpdoc tags must be aligned vertically.';
    }

    private function getMatches($line)
    {
        if (preg_match($this->regex, $line, $matches)) {
            if (!empty($matches['tag2'])) {
                $matches['tag'] = $matches['tag2'];
                $matches['hint'] = $matches['hint2'];
            }

            return $matches;
        }
    }
}
