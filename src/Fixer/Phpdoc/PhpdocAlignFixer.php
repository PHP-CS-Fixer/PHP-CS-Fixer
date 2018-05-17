<?php

/*
 * This file is part of PHP CS Fixer.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *     Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace PhpCsFixer\Fixer\Phpdoc;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\Fixer\ConfigurableFixerInterface;
use PhpCsFixer\Fixer\WhitespacesAwareFixerInterface;
use PhpCsFixer\FixerConfiguration\AllowedValueSubset;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolver;
use PhpCsFixer\FixerConfiguration\FixerOptionBuilder;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\Preg;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use PhpCsFixer\Utils;

/**
 * @author Fabien Potencier <fabien@symfony.com>
 * @author Jordi Boggiano <j.boggiano@seld.be>
 * @author Sebastiaan Stok <s.stok@rollerscapes.net>
 * @author Graham Campbell <graham@alt-three.com>
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
final class PhpdocAlignFixer extends AbstractFixer implements ConfigurableFixerInterface, WhitespacesAwareFixerInterface
{
    private $regex;
    private $regexCommentLine;

    private static $alignableTags = [
        'param',
        'property',
        'return',
        'throws',
        'type',
        'var',
        'method',
    ];

    private static $tagsWithName = [
        'param',
        'property',
    ];

    private static $tagsWithMethodSignature = [
        'method',
    ];

    /**
     * {@inheritdoc}
     */
    public function configure(array $configuration)
    {
        parent::configure($configuration);

        $tagsWithNameToAlign = array_intersect($this->configuration['tags'], self::$tagsWithName);
        $tagsWithMethodSignatureToAlign = array_intersect($this->configuration['tags'], self::$tagsWithMethodSignature);
        $tagsWithoutNameToAlign = array_diff($this->configuration['tags'], $tagsWithNameToAlign, $tagsWithMethodSignatureToAlign);
        $types = [];

        $indent = '(?P<indent>(?: {2}|\t)*)';
        // e.g. @param <hint> <$var>
        if (!empty($tagsWithNameToAlign)) {
            $types[] = '(?P<tag>'.implode('|', $tagsWithNameToAlign).')\s+(?P<hint>[^$]+?)\s+(?P<var>(?:&|\.{3})?\$[^\s]+)';
        }
        // e.g. @return <hint>
        if (!empty($tagsWithoutNameToAlign)) {
            $types[] = '(?P<tag2>'.implode('|', $tagsWithoutNameToAlign).')\s+(?P<hint2>[^\s]+?)';
        }
        // e.g. @method <hint> <signature>
        if (!empty($tagsWithMethodSignatureToAlign)) {
            $types[] = '(?P<tag3>'.implode('|', $tagsWithMethodSignatureToAlign).')(\s+(?P<hint3>[^\s(]+)|)\s+(?P<signature>.+\))';
        }
        // optional <desc>
        $desc = '(?:\s+(?P<desc>\V*))';

        $this->regex = '/^'.$indent.' \* @(?:'.implode('|', $types).')'.$desc.'\s*$/u';
        $this->regexCommentLine = '/^'.$indent.' \*(?! @)(?:\s+(?P<desc>\V+))(?<!\*\/)\r?$/u';
    }

    /**
     * {@inheritdoc}
     */
    public function getDefinition()
    {
        return new FixerDefinition(
            'All items of the given PHPDoc tags must be aligned vertically.',
            [new CodeSample('<?php
/**
 * @param  EngineInterface $templating
 * @param string      $format
 * @param  int  $code       an HTTP response status code
 * @param    bool         $debug
 * @param  mixed    &$reference     a parameter passed by reference
 */
')]
        );
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
        return -21;
    }

    /**
     * {@inheritdoc}
     */
    public function isCandidate(Tokens $tokens)
    {
        return $tokens->isTokenKindFound(T_DOC_COMMENT);
    }

    /**
     * {@inheritdoc}
     */
    protected function applyFix(\SplFileInfo $file, Tokens $tokens)
    {
        foreach ($tokens as $index => $token) {
            if (!$token->isGivenKind(T_DOC_COMMENT)) {
                continue;
            }

            $content = $token->getContent();
            $newContent = $this->fixDocBlock($content);
            if ($newContent !== $content) {
                $tokens[$index] = new Token([T_DOC_COMMENT, $newContent]);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function createConfigurationDefinition()
    {
        $tags = new FixerOptionBuilder('tags', 'The tags that should be aligned.');
        $tags
            ->setAllowedTypes(['array'])
            ->setAllowedValues([new AllowedValueSubset(self::$alignableTags)])
            ->setDefault([
                'method',
                'param',
                'property',
                'return',
                'throws',
                'type',
                'var',
            ])
        ;

        return new FixerConfigurationResolver([$tags->getOption()]);
    }

    /**
     * @param string $content
     *
     * @return string
     */
    private function fixDocBlock($content)
    {
        $lineEnding = $this->whitespacesConfig->getLineEnding();
        $lines = Utils::splitLines($content);

        for ($i = 0, $l = count($lines); $i < $l; ++$i) {
            $items = [];
            $matches = $this->getMatches($lines[$i]);

            if (null === $matches) {
                continue;
            }

            $current = $i;
            $items[] = $matches;

            while (true) {
                if (!isset($lines[++$i])) {
                    break 2;
                }

                $matches = $this->getMatches($lines[$i], true);
                if (null === $matches) {
                    break;
                }

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
                $varMax = max($varMax, strlen($item['var']));
            }

            $currTag = null;

            // update
            foreach ($items as $j => $item) {
                if (null === $item['tag']) {
                    if ('@' === $item['desc'][0]) {
                        $lines[$current + $j] = $item['indent'].' * '.$item['desc'].$lineEnding;

                        continue;
                    }

                    $extraIndent = 2;

                    if (in_array($currTag, self::$tagsWithName, true) || in_array($currTag, self::$tagsWithMethodSignature, true)) {
                        $extraIndent = 3;
                    }

                    $line =
                        $item['indent']
                        .' *  '
                        .str_repeat(' ', $tagMax + $hintMax + $varMax + $extraIndent)
                        .$item['desc']
                        .$lineEnding;

                    $lines[$current + $j] = $line;

                    continue;
                }

                $currTag = $item['tag'];

                $line =
                    $item['indent']
                    .' * @'
                    .$item['tag']
                    .str_repeat(' ', $tagMax - strlen($item['tag']) + 1)
                    .$item['hint']
                ;

                if (!empty($item['var'])) {
                    $line .=
                        str_repeat(' ', ($hintMax ?: -1) - strlen($item['hint']) + 1)
                        .$item['var']
                        .(
                            !empty($item['desc'])
                            ? str_repeat(' ', $varMax - strlen($item['var']) + 1).$item['desc'].$lineEnding
                            : $lineEnding
                        )
                    ;
                } elseif (!empty($item['desc'])) {
                    $line .= str_repeat(' ', $hintMax - strlen($item['hint']) + 1).$item['desc'].$lineEnding;
                } else {
                    $line .= $lineEnding;
                }

                $lines[$current + $j] = $line;
            }
        }

        return implode($lines);
    }

    /**
     * @param string $line
     * @param bool   $matchCommentOnly
     *
     * @return null|string[]
     */
    private function getMatches($line, $matchCommentOnly = false)
    {
        if (Preg::match($this->regex, $line, $matches)) {
            if (!empty($matches['tag2'])) {
                $matches['tag'] = $matches['tag2'];
                $matches['hint'] = $matches['hint2'];
                $matches['var'] = '';
            }

            if (!empty($matches['tag3'])) {
                $matches['tag'] = $matches['tag3'];
                $matches['hint'] = $matches['hint3'];
                $matches['var'] = $matches['signature'];
            }

            return $matches;
        }

        if ($matchCommentOnly && Preg::match($this->regexCommentLine, $line, $matches)) {
            $matches['tag'] = null;
            $matches['var'] = '';
            $matches['hint'] = '';

            return $matches;
        }
    }
}
