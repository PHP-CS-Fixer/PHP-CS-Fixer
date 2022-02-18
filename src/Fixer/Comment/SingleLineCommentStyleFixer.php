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
use PhpCsFixer\Fixer\ConfigurableFixerInterface;
use PhpCsFixer\FixerConfiguration\AllowedValueSubset;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolver;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolverInterface;
use PhpCsFixer\FixerConfiguration\FixerOptionBuilder;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Preg;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @author Filippo Tessarotto <zoeslam@gmail.com>
 */
final class SingleLineCommentStyleFixer extends AbstractFixer implements ConfigurableFixerInterface
{
    /**
     * @var bool
     */
    private $asteriskEnabled;

    /**
     * @var bool
     */
    private $hashEnabled;

    /**
     * {@inheritdoc}
     */
    public function configure(array $configuration): void
    {
        parent::configure($configuration);

        $this->asteriskEnabled = \in_array('asterisk', $this->configuration['comment_types'], true);
        $this->hashEnabled = \in_array('hash', $this->configuration['comment_types'], true);
    }

    /**
     * {@inheritdoc}
     */
    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(
            'Single-line comments and multi-line comments with only one line of actual content should use the `//` syntax.',
            [
                new CodeSample(
                    '<?php
/* asterisk comment */
$a = 1;

# hash comment
$b = 2;

/*
 * multi-line
 * comment
 */
$c = 3;
'
                ),
                new CodeSample(
                    '<?php
/* first comment */
$a = 1;

/*
 * second comment
 */
$b = 2;

/*
 * third
 * comment
 */
$c = 3;
',
                    ['comment_types' => ['asterisk']]
                ),
                new CodeSample(
                    "<?php # comment\n",
                    ['comment_types' => ['hash']]
                ),
            ]
        );
    }

    /**
     * {@inheritdoc}
     *
     * Must run after HeaderCommentFixer, NoUselessReturnFixer, PhpdocToCommentFixer.
     */
    public function getPriority(): int
    {
        return -31;
    }

    /**
     * {@inheritdoc}
     */
    public function isCandidate(Tokens $tokens): bool
    {
        return $tokens->isTokenKindFound(T_COMMENT);
    }

    /**
     * {@inheritdoc}
     */
    protected function applyFix(\SplFileInfo $file, Tokens $tokens): void
    {
        foreach ($tokens as $index => $token) {
            if (!$token->isGivenKind(T_COMMENT)) {
                continue;
            }

            $content = $token->getContent();
            $commentContent = substr($content, 2, -2) ?: '';

            if ($this->hashEnabled && str_starts_with($content, '#')) {
                if (isset($content[1]) && '[' === $content[1]) {
                    continue; // This might be an attribute on PHP8, do not change
                }

                $tokens[$index] = new Token([$token->getId(), '//'.substr($content, 1)]);

                continue;
            }

            if (
                !$this->asteriskEnabled
                || str_contains($commentContent, '?>')
                || !str_starts_with($content, '/*')
                || 1 === Preg::match('/[^\s\*].*\R.*[^\s\*]/s', $commentContent)
            ) {
                continue;
            }

            $nextTokenIndex = $index + 1;
            if (isset($tokens[$nextTokenIndex])) {
                $nextToken = $tokens[$nextTokenIndex];
                if (!$nextToken->isWhitespace() || 1 !== Preg::match('/\R/', $nextToken->getContent())) {
                    continue;
                }

                $tokens[$nextTokenIndex] = new Token([$nextToken->getId(), ltrim($nextToken->getContent(), " \t")]);
            }

            $content = '//';
            if (1 === Preg::match('/[^\s\*]/', $commentContent)) {
                $content = '// '.Preg::replace('/[\s\*]*([^\s\*](?:.+[^\s\*])?)[\s\*]*/', '\1', $commentContent);
            }
            $tokens[$index] = new Token([$token->getId(), $content]);
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function createConfigurationDefinition(): FixerConfigurationResolverInterface
    {
        return new FixerConfigurationResolver([
            (new FixerOptionBuilder('comment_types', 'List of comment types to fix'))
                ->setAllowedTypes(['array'])
                ->setAllowedValues([new AllowedValueSubset(['asterisk', 'hash'])])
                ->setDefault(['asterisk', 'hash'])
                ->getOption(),
        ]);
    }
}
