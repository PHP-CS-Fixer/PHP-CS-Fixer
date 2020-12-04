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

/**
 * @author Michele Locati <michele@locati.it>
 */
final class EchoTagSyntaxFixer extends AbstractFixer implements ConfigurationDefinitionFixerInterface
{
    /** @internal */
    const OPTION_FORMAT = 'format';

    /** @internal */
    const OPTION_SHORTEN_SIMPLE_STATEMENTS_ONLY = 'shorten_simple_statements_only';

    /** @internal */
    const OPTION_LONG_FUNCTION = 'long_function';

    /** @internal */
    const FORMAT_SHORT = 'short';

    /** @internal */
    const FORMAT_LONG = 'long';

    /** @internal */
    const LONG_FUNCTION_ECHO = 'echo';

    /** @internal */
    const LONG_FUNCTION_PRINT = 'print';

    /** @internal */
    const SUPPORTED_FORMAT_OPTIONS = [
        self::FORMAT_LONG,
        self::FORMAT_SHORT,
    ];

    /** @internal */
    const SUPPORTED_LONGFUNCTION_OPTIONS = [
        self::LONG_FUNCTION_ECHO,
        self::LONG_FUNCTION_PRINT,
    ];

    /**
     * {@inheritdoc}
     */
    public function getDefinition()
    {
        $sample = <<<'EOT'
<?=1?>
<?php print '2' . '3'; ?>
<?php /* comment */ echo '2' . '3'; ?>
<?php print '2' . '3'; someFunction(); ?>

EOT
        ;

        return new FixerDefinition(
            'Replaces short-echo `<?=` with long format `<?php echo`/`<?php print` syntax, or vice-versa.',
            [
                new CodeSample($sample),
                new CodeSample($sample, [self::OPTION_FORMAT => self::FORMAT_LONG]),
                new CodeSample($sample, [self::OPTION_FORMAT => self::FORMAT_LONG, self::OPTION_LONG_FUNCTION => self::LONG_FUNCTION_PRINT]),
                new CodeSample($sample, [self::OPTION_FORMAT => self::FORMAT_SHORT]),
                new CodeSample($sample, [self::OPTION_FORMAT => self::FORMAT_SHORT, self::OPTION_SHORTEN_SIMPLE_STATEMENTS_ONLY => false]),
            ],
            null
        );
    }

    /**
     * {@inheritdoc}
     *
     * Must run before NoMixedEchoPrintFixer.
     */
    public function getPriority()
    {
        return 0;
    }

    /**
     * {@inheritdoc}
     */
    public function isCandidate(Tokens $tokens)
    {
        if (self::FORMAT_SHORT === $this->configuration[self::OPTION_FORMAT]) {
            return $tokens->isAnyTokenKindsFound([T_ECHO, T_PRINT]);
        }

        return $tokens->isTokenKindFound(T_OPEN_TAG_WITH_ECHO);
    }

    /**
     * {@inheritdoc}
     */
    protected function createConfigurationDefinition()
    {
        return new FixerConfigurationResolver([
            (new FixerOptionBuilder(self::OPTION_FORMAT, 'The desired language construct.'))
                ->setAllowedValues(self::SUPPORTED_FORMAT_OPTIONS)
                ->setDefault(self::FORMAT_LONG)
                ->getOption(),
            (new FixerOptionBuilder(self::OPTION_LONG_FUNCTION, 'The function to be used to expand the short echo tags'))
                ->setAllowedValues(self::SUPPORTED_LONGFUNCTION_OPTIONS)
                ->setDefault(self::LONG_FUNCTION_ECHO)
                ->getOption(),
            (new FixerOptionBuilder(self::OPTION_SHORTEN_SIMPLE_STATEMENTS_ONLY, 'Render short-echo tags only in case of simple code'))
                ->setAllowedTypes(['bool'])
                ->setDefault(true)
                ->getOption(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    protected function applyFix(\SplFileInfo $file, Tokens $tokens)
    {
        if (self::FORMAT_SHORT === $this->configuration[self::OPTION_FORMAT]) {
            $this->longToShort($tokens);
        } else {
            $this->shortToLong($tokens);
        }
    }

    private function longToShort(Tokens $tokens)
    {
        $skipWhenComplexCode = $this->configuration[self::OPTION_SHORTEN_SIMPLE_STATEMENTS_ONLY];
        $count = $tokens->count();

        for ($index = 0; $index < $count; ++$index) {
            if (!$tokens[$index]->isGivenKind(T_OPEN_TAG)) {
                continue;
            }

            $nextMeaningful = $tokens->getNextMeaningfulToken($index);

            if (null === $nextMeaningful) {
                return;
            }

            if (!$tokens[$nextMeaningful]->isGivenKind([T_ECHO, T_PRINT])) {
                $index = $nextMeaningful;

                continue;
            }

            if ($skipWhenComplexCode && $this->isComplexCode($tokens, $nextMeaningful + 1)) {
                $index = $nextMeaningful;

                continue;
            }

            $newTokens = $this->buildLongToShortTokens($tokens, $index, $nextMeaningful);
            $tokens->overrideRange($index, $nextMeaningful, $newTokens);
            $count = $tokens->count();
        }
    }

    private function shortToLong(Tokens $tokens)
    {
        if (self::LONG_FUNCTION_PRINT === $this->configuration[self::OPTION_LONG_FUNCTION]) {
            $echoToken = [T_PRINT, 'print'];
        } else {
            $echoToken = [T_ECHO, 'echo'];
        }

        $index = -1;

        while (true) {
            $index = $tokens->getNextTokenOfKind($index, [[T_OPEN_TAG_WITH_ECHO]]);

            if (null === $index) {
                return;
            }

            $replace = [new Token([T_OPEN_TAG, '<?php ']), new Token($echoToken)];

            if (!$tokens[$index + 1]->isWhitespace()) {
                $replace[] = new Token([T_WHITESPACE, ' ']);
            }

            $tokens->overrideRange($index, $index, $replace);
            ++$index;
        }
    }

    /**
     * Check if $tokens, starting at $index, contains "complex code", that is, the content
     * of the echo tag contains more than a simple "echo something".
     *
     * This is done by a very quick test: if the tag contains non-whitespace tokens after
     * a semicolon, we consider it as "complex".
     *
     * @param int $index
     *
     * @return bool
     *
     * @example `<?php echo 1 ?>` is false (not complex)
     * @example `<?php echo 'hello' . 'world'; ?>` is false (not "complex")
     * @example `<?php echo 2; $set = 3 ?>` is true ("complex")
     */
    private function isComplexCode(Tokens $tokens, $index)
    {
        $semicolonFound = false;

        for ($count = $tokens->count(); $index < $count; ++$index) {
            $token = $tokens[$index];

            if ($token->isGivenKind(T_CLOSE_TAG)) {
                return false;
            }

            if (';' === $token->getContent()) {
                $semicolonFound = true;
            } elseif ($semicolonFound && !$token->isWhitespace()) {
                return true;
            }
        }

        return false;
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
}
