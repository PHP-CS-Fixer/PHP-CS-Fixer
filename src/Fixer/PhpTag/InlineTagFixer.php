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

namespace PhpCsFixer\Fixer\PhpTag;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\Fixer\ConfigurationDefinitionFixerInterface;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolver;
use PhpCsFixer\FixerConfiguration\FixerOptionBuilder;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use SplFileInfo;

/**
 * @author Michele Locati <michele@locati.it>
 */
final class InlineTagFixer extends AbstractFixer implements ConfigurationDefinitionFixerInterface
{
    /**
     * @internal
     */
    const OPTION_SPACEBEFORE = 'space_before';

    /**
     * @internal
     */
    const OPTION_SPACEAFTER = 'space_after';

    /**
     * @internal
     */
    const OPTION_SEMICOLON = 'semicolon';

    /**
     * @internal
     */
    const SPACE_MINIMUM = 'minimum';

    /**
     * @internal
     */
    const SPACE_ONE = 'one';

    /**
     * @internal
     */
    const SPACE_KEEP = 'keep';

    /**
     * @internal
     */
    const SUPPORTED_SPACEBEFORE_OPTIONS = [
        self::SPACE_MINIMUM,
        self::SPACE_ONE,
        self::SPACE_KEEP,
    ];

    /**
     * @internal
     */
    const SUPPORTED_SPACEAFTER_OPTIONS = [
        self::SPACE_MINIMUM,
        self::SPACE_ONE,
        self::SPACE_KEEP,
    ];

    /**
     * {@inheritdoc}
     */
    public function getDefinition()
    {
        $sample = <<<'EOT'
<?=1?> <?= 1 ?> <?=  1  ?>
<?=2;?> <?= 2; ?> <?=  2;  ?>
<?=3  ;?> <?= 3  ;?> <?=3  ;?> <?=  3  ;  ?>
<?php   echo 4  ;  ?>

EOT
        ;

        return new FixerDefinition(
            'Changes spaces and semicolons in inline PHP tags.',
            [
                new CodeSample($sample),
                new CodeSample($sample, [self::OPTION_SPACEBEFORE => self::SPACE_KEEP]),
                new CodeSample($sample, [self::OPTION_SPACEBEFORE => self::SPACE_MINIMUM]),
                new CodeSample($sample, [self::OPTION_SPACEAFTER => self::SPACE_KEEP]),
                new CodeSample($sample, [self::OPTION_SPACEAFTER => self::SPACE_MINIMUM]),
                new CodeSample($sample, [self::OPTION_SEMICOLON => null]),
                new CodeSample($sample, [self::OPTION_SEMICOLON => true]),
            ],
            null
        );
    }

    /**
     * {@inheritdoc}
     */
    public function isCandidate(Tokens $tokens)
    {
        if (self::SPACE_KEEP === $this->configuration[self::OPTION_SPACEBEFORE] && self::SPACE_KEEP === $this->configuration[self::OPTION_SPACEAFTER] && null === $this->configuration[self::OPTION_SEMICOLON]) {
            return false;
        }

        return null !== $this->findApplicableRange($tokens);
    }

    /**
     * {@inheritdoc}
     */
    protected function createConfigurationDefinition()
    {
        return new FixerConfigurationResolver([
            (new FixerOptionBuilder(self::OPTION_SPACEBEFORE, 'The desired number of spaces at the beginning of the tag.'))
                ->setAllowedValues(self::SUPPORTED_SPACEBEFORE_OPTIONS)
                ->setDefault(self::SPACE_ONE)
                ->getOption(),
            (new FixerOptionBuilder(self::OPTION_SPACEAFTER, 'The desired number of spaces at the end of the tag.'))
                ->setAllowedValues(self::SUPPORTED_SPACEAFTER_OPTIONS)
                ->setDefault(self::SPACE_ONE)
                ->getOption(),
            (new FixerOptionBuilder(self::OPTION_SEMICOLON, 'Whether there should be a semi-colon at the end.'))
                ->setAllowedTypes(['bool', 'null'])
                ->setDefault(false)
                ->getOption(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    protected function applyFix(SplFileInfo $file, Tokens $tokens)
    {
        $index = 0;
        for (;;) {
            $range = $this->findApplicableRange($tokens, $index);
            if (null === $range) {
                return;
            }
            $newRange = $this->fixRange($range);
            $tokens->overrideRange($index, $index + \count($range) - 1, $newRange);
            $index += \count($newRange);
        }
    }

    /**
     * Builds the list of tokens that replace a long echo sequence.
     *
     * @param int $openTagIndex
     * @param int $echoTagIndex
     *
     * @return Token[]
     */
    private function buildLongToShortTokens(Tokens $tokens, $openTagIndex, $echoTagIndex)
    {
        $result = [new Token([T_OPEN_TAG_WITH_ECHO, '<?='])];

        $start = $tokens->getNextNonWhitespace($openTagIndex);

        if ($start === $echoTagIndex) {
            // No non-whitespace tokens between $openTagIndex and $echoTagIndex
            return $result;
        }
        // Find the last non-whitespace index before $echoTagIndex
        $end = $echoTagIndex - 1;
        while ($tokens[$end]->isWhitespace()) {
            --$end;
        }
        // Copy the non-whitespace tokens between $openTagIndex and $echoTagIndex
        for ($index = $start; $index <= $end; ++$index) {
            $result[] = clone $tokens[$index];
        }

        return $result;
    }

    /**
     * @param mixed $start
     *
     * @return null|Token[]
     */
    private function findApplicableRange(Tokens $tokens, &$start = 0)
    {
        $maxIndex = $tokens->count() - 1;
        for (; $start < $maxIndex; ++$start) {
            $token = $tokens[$start];
            // @var Token $startToken
            if (!$token->isGivenKind([T_OPEN_TAG, T_OPEN_TAG_WITH_ECHO])) {
                continue;
            }
            if (false !== strpos($token->getContent(), "\n")) {
                continue;
            }
            $result = [$token];
            for ($nextIndex = $start + 1; $nextIndex <= $maxIndex; ++$nextIndex) {
                $token = $tokens[$nextIndex];
                if ($token->isGivenKind(T_CLOSE_TAG)) {
                    $result[] = $token;

                    return $result;
                }
                if (false !== strpos($token->getContent(), "\n")) {
                    break;
                }
                $result[] = $token;
            }
            $start = $nextIndex;
        }

        return null;
    }

    /**
     * @param Token[] $tokens
     *
     * @return Token[]
     */
    private function fixRange(array $tokens)
    {
        $start = $this->fixStart($tokens);
        $end = $this->fixEnd($tokens);

        return array_merge($start, $tokens, $end);
    }

    /**
     * @param Token[] $tokens
     *
     * @return Token[]
     */
    private function fixStart(array &$tokens)
    {
        $token = array_shift($tokens);
        $spaceBefore = $this->configuration[self::OPTION_SPACEBEFORE];
        if (self::SPACE_KEEP === $spaceBefore) {
            return [$token];
        }
        // Let's remove the extra whitespaces
        while ($tokens[0]->isWhitespace()) {
            array_shift($tokens);
        }
        if (T_OPEN_TAG_WITH_ECHO !== $token->getId()) {
            return [new Token([$token->getId(), rtrim($token->getContent()).' '])];
        }
        $result = [new Token([T_OPEN_TAG_WITH_ECHO, '<?='])];
        if (self::SPACE_ONE === $spaceBefore) {
            $result[] = new Token([T_WHITESPACE, ' ']);
        }

        return $result;
    }

    /**
     * @param Token[] $tokens
     *
     * @return Token[]
     */
    private function fixEnd(array &$tokens)
    {
        $spaceAfter = $this->configuration[self::OPTION_SPACEAFTER];
        $semicolon = $this->configuration[self::OPTION_SEMICOLON];
        if (self::SPACE_KEEP === $spaceAfter && null === $semicolon) {
            return [];
        }
        $token = array_pop($tokens);
        if (self::SPACE_KEEP === $spaceAfter) {
            $closing = [$token];
        } else {
            $closing = [new Token([$token->getId(), ltrim($token->getContent())])];
            if (self::SPACE_ONE === $spaceAfter) {
                array_unshift($closing, new Token([T_WHITESPACE, ' ']));
            }
        }
        $spacesAfterSemicolons = $this->popWhitespacesAndSemicolons($tokens, false, true);
        $semicolons = $this->popWhitespacesAndSemicolons($tokens, true, false);
        $spacesBeforeSemicolons = $this->popWhitespacesAndSemicolons($tokens, false, true);
        if ([] === $tokens && self::SPACE_KEEP === $this->configuration[self::OPTION_SPACEBEFORE]) {
            $tokens = $spacesBeforeSemicolons;
            $spacesBeforeSemicolons = [];
        }
        if (self::SPACE_KEEP !== $spaceAfter) {
            $spacesBeforeSemicolons = [];
            $spacesAfterSemicolons = [];
        }
        if (false === $semicolon) {
            $semicolons = [];
        } elseif (true === $semicolon) {
            $semicolons = $this->needsSemicolonAfter($tokens) ? [new Token(';')] : [];
        }

        return array_merge($spacesBeforeSemicolons, $semicolons, $spacesAfterSemicolons, $closing);
    }

    /**
     * @param Token[] $tokens
     * @param bool    $semicolons
     * @param bool    $whitespaces
     *
     * @return Token[]
     */
    private function popWhitespacesAndSemicolons(array &$tokens, $semicolons, $whitespaces)
    {
        $result = [];
        for (;;) {
            $token = array_pop($tokens);
            if (null === $token) {
                break;
            }
            if ($semicolons && ';' === $token->getContent() || $whitespaces && $token->isWhitespace()) {
                array_unshift($result, $token);

                continue;
            }
            $tokens[] = $token;

            break;
        }

        return $result;
    }

    /**
     * @param Token[] $tokens
     *
     * @return bool
     */
    private function needsSemicolonAfter(array $tokens)
    {
        $count = \count($tokens);
        if (0 === $count) {
            return false;
        }
        $token = $tokens[$count - 1];
        if ('}' === $token->getContent()) {
            return false;
        }

        return true;
    }
}
