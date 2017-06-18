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

namespace PhpCsFixer\Fixer\Comment;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\ConfigurationException\RequiredFixerConfigurationException;
use PhpCsFixer\Fixer\ConfigurationDefinitionFixerInterface;
use PhpCsFixer\Fixer\WhitespacesAwareFixerInterface;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolver;
use PhpCsFixer\FixerConfiguration\FixerOptionBuilder;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use Symfony\Component\OptionsResolver\Options;

/**
 * @author Antonio J. García Lagar <aj@garcialagar.es>
 * @author SpacePossum
 */
final class HeaderCommentFixer extends AbstractFixer implements ConfigurationDefinitionFixerInterface, WhitespacesAwareFixerInterface
{
    const HEADER_PHPDOC = 'PHPDoc';
    const HEADER_COMMENT = 'comment';

    /** @deprecated will be removed in 3.0 */
    const HEADER_LOCATION_AFTER_OPEN = 1;
    /** @deprecated will be removed in 3.0 */
    const HEADER_LOCATION_AFTER_DECLARE_STRICT = 2;

    /** @deprecated will be removed in 3.0 */
    const HEADER_LINE_SEPARATION_BOTH = 1;
    /** @deprecated will be removed in 3.0 */
    const HEADER_LINE_SEPARATION_TOP = 2;
    /** @deprecated will be removed in 3.0 */
    const HEADER_LINE_SEPARATION_BOTTOM = 3;
    /** @deprecated will be removed in 3.0 */
    const HEADER_LINE_SEPARATION_NONE = 4;

    /**
     * {@inheritdoc}
     */
    public function getDefinition()
    {
        return new FixerDefinition(
            'Add, replace or remove header comment.',
            [
                new CodeSample(
                    '<?php
declare(strict_types=1);

namespace A\B;

echo 1;
',
                    [
                        'header' => 'Made with love.',
                    ]
                ),
                new CodeSample(
                    '<?php
declare(strict_types=1);

namespace A\B;

echo 1;
',
                    [
                        'header' => 'Made with love.',
                        'commentType' => 'PHPDoc',
                        'location' => 'after_open',
                        'separate' => 'bottom',
                    ]
                ),
                new CodeSample(
                    '<?php
declare(strict_types=1);

namespace A\B;

echo 1;
',
                    [
                        'header' => 'Made with love.',
                        'commentType' => 'comment',
                        'location' => 'after_declare_strict',
                    ]
                ),
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function isCandidate(Tokens $tokens)
    {
        return $tokens[0]->isGivenKind(T_OPEN_TAG) && $tokens->isMonolithicPhp();
    }

    /**
     * {@inheritdoc}
     */
    protected function applyFix(\SplFileInfo $file, Tokens $tokens)
    {
        if (null === $this->configuration['header']) {
            throw new RequiredFixerConfigurationException($this->getName(), 'Configuration is required.');
        }

        // figure out where the comment should be placed
        $headerNewIndex = $this->findHeaderCommentInsertionIndex($tokens);

        // check if there is already a comment
        $headerCurrentIndex = $this->findHeaderCommentCurrentIndex($tokens, $headerNewIndex - 1);

        if (null === $headerCurrentIndex) {
            if ('' === $this->configuration['header']) {
                return; // header not found and none should be set, return
            }

            $this->insertHeader($tokens, $headerNewIndex);
        } elseif ($this->getHeaderAsComment() !== $tokens[$headerCurrentIndex]->getContent()) {
            $tokens->clearTokenAndMergeSurroundingWhitespace($headerCurrentIndex);
            if ('' === $this->configuration['header']) {
                return; // header found and cleared, none should be set, return
            }

            $this->insertHeader($tokens, $headerNewIndex);
        } else {
            $headerNewIndex = $headerCurrentIndex;
        }

        $this->fixWhiteSpaceAroundHeader($tokens, $headerNewIndex);
    }

    /**
     * {@inheritdoc}
     */
    protected function createConfigurationDefinition()
    {
        return new FixerConfigurationResolver([
            (new FixerOptionBuilder('header', 'Proper header content.'))
                ->setAllowedTypes(['string'])
                ->setNormalizer(function (Options $options, $value) {
                    if ('' === trim($value)) {
                        return '';
                    }

                    return $value;
                })
                ->getOption(),
            (new FixerOptionBuilder('commentType', 'Comment syntax type.'))
                ->setAllowedValues([self::HEADER_PHPDOC, self::HEADER_COMMENT])
                ->setDefault(self::HEADER_COMMENT)
                ->getOption(),
            (new FixerOptionBuilder('location', 'The location of the inserted header.'))
                ->setAllowedValues(['after_open', 'after_declare_strict'])
                ->setDefault('after_declare_strict')
                ->getOption(),
            (new FixerOptionBuilder('separate', 'Whether the header should be separated from the file content with a new line.'))
                ->setAllowedValues(['both', 'top', 'bottom', 'none'])
                ->setDefault('both')
                ->getOption(),
        ]);
    }

    /**
     * Enclose the given text in a comment block.
     *
     * @return string
     */
    private function getHeaderAsComment()
    {
        $lineEnding = $this->whitespacesConfig->getLineEnding();

        $comment = (self::HEADER_COMMENT === $this->configuration['commentType'] ? '/*' : '/**').$lineEnding;
        $lines = explode("\n", str_replace("\r", '', $this->configuration['header']));

        foreach ($lines as $line) {
            $comment .= rtrim(' * '.$line).$lineEnding;
        }

        return $comment.' */';
    }

    /**
     * @param Tokens $tokens
     * @param int    $headerNewIndex
     *
     * @return null|int
     */
    private function findHeaderCommentCurrentIndex(Tokens $tokens, $headerNewIndex)
    {
        $index = $tokens->getNextNonWhitespace($headerNewIndex);

        return null === $index || !$tokens[$index]->isComment() ? null : $index;
    }

    /**
     * Find the index where the header comment must be inserted.
     *
     * @param Tokens $tokens
     *
     * @return int
     */
    private function findHeaderCommentInsertionIndex(Tokens $tokens)
    {
        if ('after_open' === $this->configuration['location']) {
            return 1;
        }

        $index = $tokens->getNextMeaningfulToken(0);
        if (null === $index) {
            // file without meaningful tokens but an open tag, comment should always be placed directly after the open tag
            return 1;
        }

        if (!$tokens[$index]->isGivenKind(T_DECLARE)) {
            return 1;
        }

        $next = $tokens->getNextMeaningfulToken($index);
        if (null === $next || !$tokens[$next]->equals('(')) {
            return 1;
        }

        $next = $tokens->getNextMeaningfulToken($next);
        if (null === $next || !$tokens[$next]->equals([T_STRING, 'strict_types'], false)) {
            return 1;
        }

        $next = $tokens->getNextMeaningfulToken($next);
        if (null === $next || !$tokens[$next]->equals('=')) {
            return 1;
        }

        $next = $tokens->getNextMeaningfulToken($next);
        if (null === $next || !$tokens[$next]->isGivenKind(T_LNUMBER)) {
            return 1;
        }

        $next = $tokens->getNextMeaningfulToken($next);
        if (null === $next || !$tokens[$next]->equals(')')) {
            return 1;
        }

        $next = $tokens->getNextMeaningfulToken($next);
        if (null === $next || !$tokens[$next]->equals(';')) { // don't insert after close tag
            return 1;
        }

        return $next + 1;
    }

    /**
     * @param Tokens $tokens
     * @param int    $headerIndex
     */
    private function fixWhiteSpaceAroundHeader(Tokens $tokens, $headerIndex)
    {
        $lineEnding = $this->whitespacesConfig->getLineEnding();

        // fix lines after header comment
        $expectedLineCount = 'both' === $this->configuration['separate'] || 'bottom' === $this->configuration['separate'] ? 2 : 1;
        if ($headerIndex === count($tokens) - 1) {
            $tokens->insertAt($headerIndex + 1, new Token([T_WHITESPACE, str_repeat($lineEnding, $expectedLineCount)]));
        } else {
            $afterCommentIndex = $tokens->getNextNonWhitespace($headerIndex);
            $lineBreakCount = $this->getLineBreakCount($tokens, $headerIndex + 1, null === $afterCommentIndex ? count($tokens) : $afterCommentIndex);
            if ($lineBreakCount < $expectedLineCount) {
                $missing = str_repeat($lineEnding, $expectedLineCount - $lineBreakCount);
                if ($tokens[$headerIndex + 1]->isWhitespace()) {
                    $tokens[$headerIndex + 1] = new Token([T_WHITESPACE, $missing.$tokens[$headerIndex + 1]->getContent()]);
                } else {
                    $tokens->insertAt($headerIndex + 1, new Token([T_WHITESPACE, $missing]));
                }
            } elseif ($lineBreakCount > 2) {
                // remove extra line endings
                if ($tokens[$headerIndex + 1]->isWhitespace()) {
                    $tokens[$headerIndex + 1] = new Token([T_WHITESPACE, $lineEnding.$lineEnding]);
                }
            }
        }

        // fix lines before header comment
        $expectedLineCount = 'both' === $this->configuration['separate'] || 'top' === $this->configuration['separate'] ? 2 : 1;
        $prev = $tokens->getPrevNonWhitespace($headerIndex);

        $regex = '/[\t ]$/';
        if ($tokens[$prev]->isGivenKind(T_OPEN_TAG) && preg_match($regex, $tokens[$prev]->getContent())) {
            $tokens[$prev] = new Token([T_OPEN_TAG, preg_replace($regex, $lineEnding, $tokens[$prev]->getContent())]);
        }

        $lineBreakCount = $this->getLineBreakCount($tokens, $prev, $headerIndex);
        if ($lineBreakCount < $expectedLineCount) {
            // because of the way the insert index was determined for header comment there cannot be an empty token here
            $tokens->insertAt($headerIndex, new Token([T_WHITESPACE, str_repeat($lineEnding, $expectedLineCount - $lineBreakCount)]));
        }
    }

    /**
     * @param Tokens $tokens
     * @param int    $indexStart
     * @param int    $indexEnd
     *
     * @return int
     */
    private function getLineBreakCount(Tokens $tokens, $indexStart, $indexEnd)
    {
        $lineCount = 0;
        for ($i = $indexStart; $i < $indexEnd; ++$i) {
            $lineCount += substr_count($tokens[$i]->getContent(), "\n");
        }

        return $lineCount;
    }

    /**
     * @param Tokens $tokens
     * @param int    $index
     */
    private function insertHeader(Tokens $tokens, $index)
    {
        $tokens->insertAt($index, new Token([self::HEADER_COMMENT === $this->configuration['commentType'] ? T_COMMENT : T_DOC_COMMENT, $this->getHeaderAsComment()]));
    }
}
