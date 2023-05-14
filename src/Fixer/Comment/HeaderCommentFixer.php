<?php

declare(strict_types=1);

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
use PhpCsFixer\ConfigurationException\InvalidFixerConfigurationException;
use PhpCsFixer\Fixer\ConfigurableFixerInterface;
use PhpCsFixer\Fixer\WhitespacesAwareFixerInterface;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolver;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolverInterface;
use PhpCsFixer\FixerConfiguration\FixerOptionBuilder;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Preg;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use Symfony\Component\OptionsResolver\Options;

/**
 * @author Antonio J. García Lagar <aj@garcialagar.es>
 */
final class HeaderCommentFixer extends AbstractFixer implements ConfigurableFixerInterface, WhitespacesAwareFixerInterface
{
    /**
     * @internal
     */
    public const HEADER_PHPDOC = 'PHPDoc';

    /**
     * @internal
     */
    public const HEADER_COMMENT = 'comment';

    public function getDefinition(): FixerDefinitionInterface
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
                        'comment_type' => 'PHPDoc',
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
                        'comment_type' => 'comment',
                        'location' => 'after_declare_strict',
                    ]
                ),
                new CodeSample(
                    '<?php
declare(strict_types=1);

/*
 * Comment is not wanted here.
 */

namespace A\B;

echo 1;
',
                    [
                        'header' => '',
                    ]
                ),
            ]
        );
    }

    public function isCandidate(Tokens $tokens): bool
    {
        return $tokens->isMonolithicPhp();
    }

    /**
     * {@inheritdoc}
     *
     * Must run before SingleLineCommentStyleFixer.
     * Must run after DeclareStrictTypesFixer, NoBlankLinesAfterPhpdocFixer.
     */
    public function getPriority(): int
    {
        // When this fixer is configured with ["separate" => "bottom", "comment_type" => "PHPDoc"]
        // and the target file has no namespace or declare() construct,
        // the fixed header comment gets trimmed by NoBlankLinesAfterPhpdocFixer if we run before it.
        return -30;
    }

    protected function applyFix(\SplFileInfo $file, Tokens $tokens): void
    {
        $location = $this->configuration['location'];
        $locationIndices = [];

        foreach (['after_open', 'after_declare_strict'] as $possibleLocation) {
            $locationIndex = $this->findHeaderCommentInsertionIndex($tokens, $possibleLocation);

            if (!isset($locationIndices[$locationIndex]) || $possibleLocation === $location) {
                $locationIndices[$locationIndex] = $possibleLocation;
            }
        }

        foreach ($locationIndices as $possibleLocation) {
            // figure out where the comment should be placed
            $headerNewIndex = $this->findHeaderCommentInsertionIndex($tokens, $possibleLocation);

            // check if there is already a comment
            $headerCurrentIndex = $this->findHeaderCommentCurrentIndex($tokens, $headerNewIndex - 1);

            if (null === $headerCurrentIndex) {
                if ('' === $this->configuration['header'] || $possibleLocation !== $location) {
                    continue;
                }

                $this->insertHeader($tokens, $headerNewIndex);

                continue;
            }

            $sameComment = $this->getHeaderAsComment() === $tokens[$headerCurrentIndex]->getContent();
            $expectedLocation = $possibleLocation === $location;

            if (!$sameComment || !$expectedLocation) {
                if ($expectedLocation ^ $sameComment) {
                    $this->removeHeader($tokens, $headerCurrentIndex);
                }

                if ('' === $this->configuration['header']) {
                    continue;
                }

                if ($possibleLocation === $location) {
                    $this->insertHeader($tokens, $headerNewIndex);
                }

                continue;
            }

            $this->fixWhiteSpaceAroundHeader($tokens, $headerCurrentIndex);
        }
    }

    protected function createConfigurationDefinition(): FixerConfigurationResolverInterface
    {
        $fixerName = $this->getName();

        return new FixerConfigurationResolver([
            (new FixerOptionBuilder('header', 'Proper header content.'))
                ->setAllowedTypes(['string'])
                ->setNormalizer(static function (Options $options, string $value) use ($fixerName): string {
                    if ('' === trim($value)) {
                        return '';
                    }

                    if (str_contains($value, '*/')) {
                        throw new InvalidFixerConfigurationException($fixerName, 'Cannot use \'*/\' in header.');
                    }

                    return $value;
                })
                ->getOption(),
            (new FixerOptionBuilder('comment_type', 'Comment syntax type.'))
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
     */
    private function getHeaderAsComment(): string
    {
        $lineEnding = $this->whitespacesConfig->getLineEnding();
        $comment = (self::HEADER_COMMENT === $this->configuration['comment_type'] ? '/*' : '/**').$lineEnding;
        $lines = explode("\n", str_replace("\r", '', $this->configuration['header']));

        foreach ($lines as $line) {
            $comment .= rtrim(' * '.$line).$lineEnding;
        }

        return $comment.' */';
    }

    private function findHeaderCommentCurrentIndex(Tokens $tokens, int $headerNewIndex): ?int
    {
        $index = $tokens->getNextNonWhitespace($headerNewIndex);

        if (null === $index || !$tokens[$index]->isComment()) {
            return null;
        }

        $next = $index + 1;

        if (!isset($tokens[$next]) || \in_array($this->configuration['separate'], ['top', 'none'], true) || !$tokens[$index]->isGivenKind(T_DOC_COMMENT)) {
            return $index;
        }

        if ($tokens[$next]->isWhitespace()) {
            if (!Preg::match('/^\h*\R\h*$/D', $tokens[$next]->getContent())) {
                return $index;
            }

            ++$next;
        }

        if (!isset($tokens[$next]) || !$tokens[$next]->isClassy() && !$tokens[$next]->isGivenKind(T_FUNCTION)) {
            return $index;
        }

        return $this->getHeaderAsComment() === $tokens[$index]->getContent() ? $index : null;
    }

    /**
     * Find the index where the header comment must be inserted.
     */
    private function findHeaderCommentInsertionIndex(Tokens $tokens, string $location): int
    {
        $openTagIndex = $tokens[0]->isGivenKind(T_OPEN_TAG) ? 0 : $tokens->getNextTokenOfKind(0, [[T_OPEN_TAG]]);

        if (null === $openTagIndex) {
            return 1;
        }

        if ('after_open' === $location) {
            return $openTagIndex + 1;
        }

        $index = $tokens->getNextMeaningfulToken($openTagIndex);

        if (null === $index) {
            return $openTagIndex + 1; // file without meaningful tokens but an open tag, comment should always be placed directly after the open tag
        }

        if (!$tokens[$index]->isGivenKind(T_DECLARE)) {
            return $openTagIndex + 1;
        }

        $next = $tokens->getNextMeaningfulToken($index);

        if (null === $next || !$tokens[$next]->equals('(')) {
            return $openTagIndex + 1;
        }

        $next = $tokens->getNextMeaningfulToken($next);

        if (null === $next || !$tokens[$next]->equals([T_STRING, 'strict_types'], false)) {
            return $openTagIndex + 1;
        }

        $next = $tokens->getNextMeaningfulToken($next);

        if (null === $next || !$tokens[$next]->equals('=')) {
            return $openTagIndex + 1;
        }

        $next = $tokens->getNextMeaningfulToken($next);

        if (null === $next || !$tokens[$next]->isGivenKind(T_LNUMBER)) {
            return $openTagIndex + 1;
        }

        $next = $tokens->getNextMeaningfulToken($next);

        if (null === $next || !$tokens[$next]->equals(')')) {
            return $openTagIndex + 1;
        }

        $next = $tokens->getNextMeaningfulToken($next);

        if (null === $next || !$tokens[$next]->equals(';')) { // don't insert after close tag
            return $openTagIndex + 1;
        }

        return $next + 1;
    }

    private function fixWhiteSpaceAroundHeader(Tokens $tokens, int $headerIndex): void
    {
        $lineEnding = $this->whitespacesConfig->getLineEnding();

        // fix lines after header comment
        if (
            ('both' === $this->configuration['separate'] || 'bottom' === $this->configuration['separate'])
            && null !== $tokens->getNextMeaningfulToken($headerIndex)
        ) {
            $expectedLineCount = 2;
        } else {
            $expectedLineCount = 1;
        }

        if ($headerIndex === \count($tokens) - 1) {
            $tokens->insertAt($headerIndex + 1, new Token([T_WHITESPACE, str_repeat($lineEnding, $expectedLineCount)]));
        } else {
            $lineBreakCount = $this->getLineBreakCount($tokens, $headerIndex, 1);

            if ($lineBreakCount < $expectedLineCount) {
                $missing = str_repeat($lineEnding, $expectedLineCount - $lineBreakCount);

                if ($tokens[$headerIndex + 1]->isWhitespace()) {
                    $tokens[$headerIndex + 1] = new Token([T_WHITESPACE, $missing.$tokens[$headerIndex + 1]->getContent()]);
                } else {
                    $tokens->insertAt($headerIndex + 1, new Token([T_WHITESPACE, $missing]));
                }
            } elseif ($lineBreakCount > $expectedLineCount && $tokens[$headerIndex + 1]->isWhitespace()) {
                $newLinesToRemove = $lineBreakCount - $expectedLineCount;
                $tokens[$headerIndex + 1] = new Token([
                    T_WHITESPACE,
                    Preg::replace("/^\\R{{$newLinesToRemove}}/", '', $tokens[$headerIndex + 1]->getContent()),
                ]);
            }
        }

        // fix lines before header comment
        $expectedLineCount = 'both' === $this->configuration['separate'] || 'top' === $this->configuration['separate'] ? 2 : 1;
        $prev = $tokens->getPrevNonWhitespace($headerIndex);

        $regex = '/\h$/';

        if ($tokens[$prev]->isGivenKind(T_OPEN_TAG) && Preg::match($regex, $tokens[$prev]->getContent())) {
            $tokens[$prev] = new Token([T_OPEN_TAG, Preg::replace($regex, $lineEnding, $tokens[$prev]->getContent())]);
        }

        $lineBreakCount = $this->getLineBreakCount($tokens, $headerIndex, -1);

        if ($lineBreakCount < $expectedLineCount) {
            // because of the way the insert index was determined for header comment there cannot be an empty token here
            $tokens->insertAt($headerIndex, new Token([T_WHITESPACE, str_repeat($lineEnding, $expectedLineCount - $lineBreakCount)]));
        }
    }

    private function getLineBreakCount(Tokens $tokens, int $index, int $direction): int
    {
        $whitespace = '';

        for ($index += $direction; isset($tokens[$index]); $index += $direction) {
            $token = $tokens[$index];

            if ($token->isWhitespace()) {
                $whitespace .= $token->getContent();

                continue;
            }

            if (-1 === $direction && $token->isGivenKind(T_OPEN_TAG)) {
                $whitespace .= $token->getContent();
            }

            if ('' !== $token->getContent()) {
                break;
            }
        }

        return substr_count($whitespace, "\n");
    }

    private function removeHeader(Tokens $tokens, int $index): void
    {
        $prevIndex = $index - 1;
        $prevToken = $tokens[$prevIndex];
        $newlineRemoved = false;

        if ($prevToken->isWhitespace()) {
            $content = $prevToken->getContent();

            if (Preg::match('/\R/', $content)) {
                $newlineRemoved = true;
            }

            $content = Preg::replace('/\R?\h*$/', '', $content);

            $tokens->ensureWhitespaceAtIndex($prevIndex, 0, $content);
        }

        $nextIndex = $index + 1;
        $nextToken = $tokens[$nextIndex] ?? null;

        if (!$newlineRemoved && null !== $nextToken && $nextToken->isWhitespace()) {
            $content = Preg::replace('/^\R/', '', $nextToken->getContent());

            $tokens->ensureWhitespaceAtIndex($nextIndex, 0, $content);
        }

        $tokens->clearTokenAndMergeSurroundingWhitespace($index);
    }

    private function insertHeader(Tokens $tokens, int $index): void
    {
        $tokens->insertAt($index, new Token([self::HEADER_COMMENT === $this->configuration['comment_type'] ? T_COMMENT : T_DOC_COMMENT, $this->getHeaderAsComment()]));
        $this->fixWhiteSpaceAroundHeader($tokens, $index);
    }
}
