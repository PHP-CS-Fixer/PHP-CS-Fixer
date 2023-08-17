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

namespace PhpCsFixer\Fixer\Phpdoc;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\Fixer\ConfigurableFixerInterface;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolver;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolverInterface;
use PhpCsFixer\FixerConfiguration\FixerOptionBuilder;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Preg;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

final class PhpdocInlineTagNormalizerFixer extends AbstractFixer implements ConfigurableFixerInterface
{
    public function isCandidate(Tokens $tokens): bool
    {
        return $tokens->isTokenKindFound(T_DOC_COMMENT);
    }

    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(
            'Fixes PHPDoc inline tags.',
            [
                new CodeSample(
                    "<?php\n/**\n * @{TUTORIAL}\n * {{ @link }}\n * @inheritDoc\n */\n"
                ),
                new CodeSample(
                    "<?php\n/**\n * @{TUTORIAL}\n * {{ @link }}\n * @inheritDoc\n */\n",
                    ['tags' => ['TUTORIAL']]
                ),
            ]
        );
    }

    /**
     * {@inheritdoc}
     *
     * Must run before PhpdocAlignFixer.
     * Must run after AlignMultilineCommentFixer, CommentToPhpdocFixer, PhpdocIndentFixer, PhpdocScalarFixer, PhpdocToCommentFixer, PhpdocTypesFixer.
     */
    public function getPriority(): int
    {
        return 0;
    }

    protected function applyFix(\SplFileInfo $file, Tokens $tokens): void
    {
        if (0 === \count($this->configuration['tags'])) {
            return;
        }

        foreach ($tokens as $index => $token) {
            if (!$token->isGivenKind(T_DOC_COMMENT)) {
                continue;
            }

            // Move `@` inside tag, for example @{tag} -> {@tag}, replace multiple curly brackets,
            // remove spaces between '{' and '@', remove white space between end
            // of text and closing bracket and between the tag and inline comment.
            $content = Preg::replaceCallback(
                sprintf(
                    '#(?:@{+|{+\h*@)\h*(%s)\b([^}]*)(?:}+)#i',
                    implode('|', array_map(static fn (string $tag): string => preg_quote($tag, '/'), $this->configuration['tags']))
                ),
                static function (array $matches): string {
                    $doc = trim($matches[2]);

                    if ('' === $doc) {
                        return '{@'.$matches[1].'}';
                    }

                    return '{@'.$matches[1].' '.$doc.'}';
                },
                $token->getContent()
            );

            $tokens[$index] = new Token([T_DOC_COMMENT, $content]);
        }
    }

    protected function createConfigurationDefinition(): FixerConfigurationResolverInterface
    {
        return new FixerConfigurationResolver([
            (new FixerOptionBuilder('tags', 'The list of tags to normalize.'))
                ->setAllowedTypes(['array'])
                ->setDefault(['example', 'id', 'internal', 'inheritdoc', 'inheritdocs', 'link', 'source', 'toc', 'tutorial'])
                ->getOption(),
        ]);
    }
}
