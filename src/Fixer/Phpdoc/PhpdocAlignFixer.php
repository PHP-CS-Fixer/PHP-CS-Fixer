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
use PhpCsFixer\Fixer\ConfigurationDefinitionFixerInterface;
use PhpCsFixer\Fixer\WhitespacesAwareFixerInterface;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolver;
use PhpCsFixer\FixerConfiguration\FixerOptionBuilder;
use PhpCsFixer\FixerConfiguration\FixerOptionValidatorGenerator;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
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
final class PhpdocAlignFixer extends AbstractFixer implements ConfigurationDefinitionFixerInterface, WhitespacesAwareFixerInterface
{
    private $regex;
    private $regexCommentLine;

    private static $alignableParts = [
        'hint',
        'var',
        'desc',
    ];

    private static $alignableTags = [
        'param',
        'property',
        'return',
        'throws',
        'type',
        'var',
    ];

    private static $tagsWithName = [
        'param',
        'property',
    ];

    /**
     * {@inheritdoc}
     */
    public function configure(array $configuration = null)
    {
        parent::configure($configuration);

        $tagsWithNameToAlign = array_intersect($this->configuration['tags'], self::$tagsWithName);
        $tagsWithoutNameToAlign = array_diff($this->configuration['tags'], $tagsWithNameToAlign);

        $indent = '(?P<indent>(?: {2}|\t)*)';
        // e.g. @param <hint> <$var>
        $tagsWithName = '(?P<tag>'.implode('|', $tagsWithNameToAlign).')\s+(?P<hint>[^$]+?)\s+(?P<var>(?:&|\.{3})?\$[^\s]+)';
        // e.g. @return <hint>
        $tagsWithoutName = '(?P<tag2>'.implode('|', $tagsWithoutNameToAlign).')\s+(?P<hint2>[^\s]+?)';
        // optional <desc>
        $desc = '(?:\s+(?P<desc>\V*))';

        $this->regex = '/^'.$indent.' \* @(?:'.$tagsWithName.'|'.$tagsWithoutName.')'.$desc.'\s*$/u';
        $this->regexCommentLine = '/^'.$indent.' \*(?! @)(?:\s+(?P<desc>\V+))(?<!\*\/)$/u';
    }

    /**
     * {@inheritdoc}
     */
    public function getDefinition()
    {
        return new FixerDefinition(
            'All items of the given phpdoc tags must be aligned vertically.',
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
        return -11;
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
            if ($token->isGivenKind(T_DOC_COMMENT)) {
                $tokens[$index] = new Token([T_DOC_COMMENT, $this->fixDocBlock($token->getContent())]);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function createConfigurationDefinition()
    {
        $generator = new FixerOptionValidatorGenerator();

        $tags = new FixerOptionBuilder('tags', 'The tags that should be aligned.');
        $tags
            ->setAllowedTypes(['array'])
            ->setAllowedValues([
                $generator->allowedValueIsSubsetOf(self::$alignableTags),
            ])
            // By default, all tags apart from @property will be aligned for backwards compatibility
            ->setDefault([
                'param',
                'return',
                'throws',
                'type',
                'var',
            ])
        ;

        $separatorSpaces = new FixerOptionBuilder('separatorSpaces', 'Separator spaces between parts.');
        $separatorSpaces
            ->setAllowedTypes(['int'])
            ->setAllowedValues(range(0, 8))
            ->setDefault(1)
        ;

        $parts = new FixerOptionBuilder('parts', 'Parts should be aligned.');
        $parts
            ->setAllowedTypes(['array'])
            ->setAllowedValues([
                $generator->allowedValueIsSubsetOf(self::$alignableParts),
            ])
            ->setDefault(self::$alignableParts)
        ;

        return new FixerConfigurationResolver([$tags->getOption(), $separatorSpaces->getOption(), $parts->getOption()]);
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

        $l = count($lines);

        $desiredTagLength = strlen('param');

        for ($i = 0; $i < $l; ++$i) {
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

                if (!$matches) {
                    break;
                }

                $items[] = $matches;
            }

            // compute the max length of the tag, hint and variables
            $maxLengths = [
                'tag' => 0,
                'hint' => 0,
                'var' => 0,
            ];

            foreach ($items as $item) {
                if (null === $item['tag']) {
                    continue;
                }

                $maxLengths['tag'] = max($maxLengths['tag'], strlen($item['tag']));
                $maxLengths['hint'] = max($maxLengths['hint'], strlen($item['hint']));
                $maxLengths['var'] = max($maxLengths['var'], strlen($item['var']));
            }

            // relative aligned start positions
            $alignedPositions = [
                'hint' => $maxLengths['tag'] + $this->configuration['separatorSpaces'],
                'var' => $maxLengths['tag'] + $maxLengths['hint'] + 2 * $this->configuration['separatorSpaces'],
                'desc' => $maxLengths['tag'] + $maxLengths['hint'] + $maxLengths['var'] + ($maxLengths['var'] ? 3 : 2) * $this->configuration['separatorSpaces'],
            ];

            $currTag = null;

            // update
            foreach ($items as $j => $item) {
                // start with indent
                $line = $item['indent'];

                // add *
                $line .= ' * ';

                // multiline
                if (null === $item['tag']) {
                    if ($item['desc'][0] !== '@') {
                        // vertical align desc
                        if (in_array('desc', $this->configuration['parts'], true)) {
                            $line .= str_repeat(' ', $alignedPositions[in_array($currTag, self::$tagsWithName, true) ? 'desc' : 'var'] - (strlen($line) - strlen($item['indent'].' * @')));
                        }
                    }

                    // add desc
                    $line .= $item['desc'];

                    // add line ending
                    $line .= $lineEnding;

                    // add to lines
                    $lines[$current + $j] = $line;

                    continue;
                }

                $currTag = $item['tag'];

                // add @
                $line .= '@';

                // add tag
                $line .= $item['tag'];

                // vertical align hint
                if (in_array('hint', $this->configuration['parts'], true)) {
                    $line .= str_repeat(' ', $alignedPositions['hint'] - (strlen($line) - strlen($item['indent'].' * @')));
                }

                // add hint
                $line .= $item['hint'];

                // has var
                if (!empty($item['var'])) {
                    // vertical align var
                    if (in_array('var', $this->configuration['parts'], true)) {
                        $line .= str_repeat(' ', $alignedPositions['var'] - (strlen($line) - strlen($item['indent'].' * @')));
                    }

                    // add var
                    $line .= $item['var'];
                }

                // has desc
                if (!empty($item['desc'])) {
                    // vertical align desc
                    if (in_array('desc', $this->configuration['parts'], true)) {
                        $line .= str_repeat(' ', $alignedPositions[in_array($item['tag'], self::$tagsWithName, true) ? 'desc' : 'var'] - (strlen($line) - strlen($item['indent'].' * @')));
                    }

                    // add desc
                    $line .= $item['desc'];
                }

                // add line ending
                $line .= $lineEnding;

                // add to lines
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
        if (preg_match($this->regex, $line, $matches)) {
            if (!empty($matches['tag2'])) {
                $matches['tag'] = $matches['tag2'];
                $matches['hint'] = $matches['hint2'];
                $matches['var'] = '';
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
