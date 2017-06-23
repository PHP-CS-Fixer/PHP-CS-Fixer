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
use PhpCsFixer\Fixer\ConfigurationDefinitionFixerInterface;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolver;
use PhpCsFixer\FixerConfiguration\FixerOptionBuilder;
use PhpCsFixer\FixerConfiguration\FixerOptionValidatorGenerator;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @author Filippo Tessarotto <zoeslam@gmail.com>
 */
final class SingleLineCommentStyleFixer extends AbstractFixer implements ConfigurationDefinitionFixerInterface
{
    private $starEnabled;
    private $hashEnabled;

    /**
     * {@inheritdoc}
     */
    public function configure(array $configuration = null)
    {
        parent::configure($configuration);

        $this->starEnabled = in_array('asterisk', $this->configuration['comment_types'], true);
        $this->hashEnabled = in_array('hash', $this->configuration['comment_types'], true);
    }

    /**
     * {@inheritdoc}
     */
    public function getDefinition()
    {
        return new FixerDefinition(
            'Single-line comments and multi-line comments with only one line of actual content should use the `//` syntax.',
            [
                new CodeSample(
                    "<?php\n/* first comment */\n\$a = 1;\n/*\n * second comment\n */\n\$b = 2;\n/*\n * third\n * comment\n */\n\$c = 3;",
                    ['comment_types' => ['asterisk']]
                ),
                new CodeSample(
                    '<?php # comment',
                    ['comment_types' => ['hash']]
                ),
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function isCandidate(Tokens $tokens)
    {
        return $tokens->isTokenKindFound(T_COMMENT);
    }

    /**
     * {@inheritdoc}
     */
    protected function applyFix(\SplFileInfo $file, Tokens $tokens)
    {
        foreach ($tokens as $index => $token) {
            if (!$token->isGivenKind(T_COMMENT)) {
                continue;
            }
            $content = $token->getContent();
            $commentContent = substr($content, 2, -2);
            if ($this->hashEnabled && '#' === $content[0]) {
                $tokens[$index] = new Token([$token->getId(), '//'.substr($content, 1)]);
                continue;
            }
            if (!$this->starEnabled || '/*' !== substr($content, 0, 2) || preg_match('/[^\s\*].*\R.*[^\s\*]/s', $commentContent)) {
                continue;
            }
            $nextTokenIndex = $index + 1;
            if (isset($tokens[$nextTokenIndex])) {
                $nextToken = $tokens[$nextTokenIndex];
                if (!$nextToken->isWhitespace() || !preg_match('/\R/', $nextToken->getContent())) {
                    continue;
                }

                $tokens[$nextTokenIndex] = new Token([$nextToken->getId(), ltrim($nextToken->getContent(), " \t")]);
            }

            $content = '//';
            if (preg_match('/[^\s\*]/', $commentContent)) {
                $content = '// '.preg_replace('/[\s\*]*([^\s\*](.+[^\s\*])?)[\s\*]*/', '\1', $commentContent);
            }
            $tokens[$index] = new Token([$token->getId(), $content]);
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function createConfigurationDefinition()
    {
        return new FixerConfigurationResolver([
            (new FixerOptionBuilder('comment_types', 'List of comment types to fix'))
                ->setAllowedTypes(['array'])
                ->setAllowedValues([
                    (new FixerOptionValidatorGenerator())->allowedValueIsSubsetOf(['asterisk', 'hash']),
                ])
                ->setDefault(['asterisk', 'hash'])
                ->getOption(),
        ]);
    }
}
