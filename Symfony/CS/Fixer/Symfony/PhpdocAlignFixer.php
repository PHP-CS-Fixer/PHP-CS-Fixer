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
use Symfony\CS\Utils;

/**
 * @author Fabien Potencier <fabien@symfony.com>
 * @author Jordi Boggiano <j.boggiano@seld.be>
 * @author Sebastiaan Stok <s.stok@rollerscapes.net>
 * @author Graham Campbell <graham@mineuk.com>
 */
class PhpdocAlignFixer extends AbstractFixer
{
    private $regex;
    private $regexCommentLine;

    public function __construct()
    {
        // e.g. @param <hint> <$var>
        $paramTag = '(?P<tag>param)\s+(?P<hint>[^$]+?)\s+(?P<var>&?\$[^\s]+)';
        // e.g. @return <hint>
        $otherTags = '(?P<tag2>return|throws|var|type)\s+(?P<hint2>[^\s]+?)';
        // optional <desc>
        $desc = '(?:\s+(?P<desc>.*)|\s*)';

        $this->regex = '/^ {5}\* @(?:'.$paramTag.'|'.$otherTags.')'.$desc.'$/';
        $this->regexCommentLine = '/^ {5}\*(?:\s+(?P<desc>.+))(?<!\*\/)$/';
    }

    /**
     * {@inheritdoc}
     */
    public function fix(\SplFileInfo $file, Tokens $tokens)
    {
        foreach ($tokens as $index => $token) {
            if ($token->isGivenKind(T_DOC_COMMENT)) {
                $tokens[$index]->setContent($this->fixDocBlock($token->getContent()));
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'All items of the @param, @throws, @return, @var, and @type phpdoc tags must be aligned vertically.';
    }

    /**
     * {@inheritdoc}
     */
    public function getPriority()
    {
        /*
         * Should be run after all other docblock fixers. This because they
         * modify other annotations to change their type and or separation
         * which totally change the behavior of this fixer. It's important that
         * annotations are of the correct type, and are grouped correctly
         * before running this fixer.
         */
        return -10;
    }

    /**
     * Fix a given docblock.
     *
     * @param string $content
     *
     * @return string
     */
    private function fixDocBlock($content)
    {
        $lines = Utils::splitLines($content);

        $l = count($lines);

        for ($i = 0; $i < $l; ++$i) {
            $items = array();

            if ($matches = $this->getMatches($lines[$i])) {
                $current = $i;
                $items[] = $matches;

                while ($matches = $this->getMatches($lines[++$i], true)) {
                    $items[] = $matches;
                }

                // compute the max length of the tag, hint and variables
                $tagMax = 0;
                $hintMax = 0;
                $varMax = 0;

                foreach ($items as $item) {
                    if (null === $item['tag']) {
                        continue;
                    }

                    $tagMax = max($tagMax, strlen($item['tag']));
                    $hintMax = max($hintMax, strlen($item['hint']));
                    $varMax  = max($varMax, strlen($item['var']));
                }

                $currTag = null;

                // update
                foreach ($items as $j => $item) {
                    if (null === $item['tag']) {
                        if ($item['desc'][0] === '@') {
                            $lines[$current + $j] = '     * '.$item['desc']."\n";
                            continue;
                        }
                        $line =
                            '     *  '
                            .str_repeat(' ', ($tagMax + $hintMax + $varMax + ('param' === $currTag ? 3 : 2)))
                            .$item['desc']."\n";

                        $lines[$current + $j] = $line;

                        continue;
                    }

                    $currTag = $item['tag'];

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
                                ? str_repeat(' ', $varMax - strlen($item['var']) + 1).$item['desc']."\n"
                                : "\n"
                            )
                        ;
                    } elseif (!empty($item['desc'])) {
                        $line .= str_repeat(' ', $hintMax - strlen($item['hint']) + 1).$item['desc']."\n";
                    } else {
                        $line .= "\n";
                    }

                    $lines[$current + $j] = $line;
                }
            }
        }

        return implode($lines);
    }

    /**
     * Get all matches.
     *
     * @param string $line
     * @param bool   $matchCommentOnly
     *
     * @return string[]|null
     */
    private function getMatches($line, $matchCommentOnly = false)
    {
        if (preg_match($this->regex, $line, $matches)) {
            if (!empty($matches['tag2'])) {
                $matches['tag'] = $matches['tag2'];
                $matches['hint'] = $matches['hint2'];
            }

            return $matches;
        }

        if ($matchCommentOnly && preg_match($this->regexCommentLine, $line, $matches)) {
            $matches['tag'] = null;
            $matches['var'] = '';
            $matches['hint'] = '';

            return $matches;
        }
    }
}
