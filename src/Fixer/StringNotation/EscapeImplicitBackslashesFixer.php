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

namespace PhpCsFixer\Fixer\StringNotation;

use PhpCsFixer\AbstractProxyFixer;
use PhpCsFixer\Fixer\ConfigurableFixerInterface;
use PhpCsFixer\Fixer\DeprecatedFixerInterface;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolver;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolverInterface;
use PhpCsFixer\FixerConfiguration\FixerOptionBuilder;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;

/**
 * @author Filippo Tessarotto <zoeslam@gmail.com>
 * @author Michael Vorisek <https://github.com/mvorisek>
 *
 * @deprecated Use `string_implicit_backslashes` with config: ['single_quoted' => 'ignore', 'double_quoted' => 'escape', 'heredoc' => 'escape'] (default)
 */
final class EscapeImplicitBackslashesFixer extends AbstractProxyFixer implements ConfigurableFixerInterface, DeprecatedFixerInterface
{
    public function getSuccessorsNames(): array
    {
        return array_keys($this->proxyFixers);
    }

    public function getDefinition(): FixerDefinitionInterface
    {
        $codeSample = <<<'EOF'
            <?php

            $singleQuoted = 'String with \" and My\Prefix\\';

            $doubleQuoted = "Interpret my \n but not my \a";

            $hereDoc = <<<HEREDOC
            Interpret my \100 but not my \999
            HEREDOC;

            EOF;

        return new FixerDefinition(
            'Escape implicit backslashes in strings and heredocs to ease the understanding of which are special chars interpreted by PHP and which not.',
            [
                new CodeSample($codeSample),
                new CodeSample(
                    $codeSample,
                    ['single_quoted' => true]
                ),
                new CodeSample(
                    $codeSample,
                    ['double_quoted' => false]
                ),
                new CodeSample(
                    $codeSample,
                    ['heredoc_syntax' => false]
                ),
            ],
            'In PHP double-quoted strings and heredocs some chars like `n`, `$` or `u` have special meanings if preceded by a backslash '
            .'(and some are special only if followed by other special chars), while a backslash preceding other chars are interpreted like a plain '
            .'backslash. The precise list of those special chars is hard to remember and to identify quickly: this fixer escapes backslashes '
            ."that do not start a special interpretation with the char after them.\n"
            .'It is possible to fix also single-quoted strings: in this case there is no special chars apart from single-quote and backslash '
            .'itself, so the fixer simply ensure that all backslashes are escaped. Both single and double backslashes are allowed in single-quoted '
            .'strings, so the purpose in this context is mainly to have a uniformed way to have them written all over the codebase.'
        );
    }

    /**
     * {@inheritdoc}
     *
     * Must run before HeredocToNowdocFixer, SingleQuoteFixer.
     * Must run after BacktickToShellExecFixer, MultilineStringToHeredocFixer.
     */
    public function getPriority(): int
    {
        return parent::getPriority();
    }

    public function configure(array $configuration): void
    {
        parent::configure($configuration);

        /** @var StringImplicitBackslashesFixer */
        $stringImplicitBackslashesFixer = $this->proxyFixers['string_implicit_backslashes'];

        $stringImplicitBackslashesFixer->configure([
            'single_quoted' => true === $this->configuration['single_quoted'] ? 'escape' : 'ignore',
            'double_quoted' => true === $this->configuration['double_quoted'] ? 'escape' : 'ignore',
            'heredoc' => true === $this->configuration['heredoc_syntax'] ? 'escape' : 'ignore',
        ]);
    }

    protected function createProxyFixers(): array
    {
        $stringImplicitBackslashesFixer = new StringImplicitBackslashesFixer();
        $stringImplicitBackslashesFixer->configure([
            'single_quoted' => 'ignore',
            'double_quoted' => 'escape',
            'heredoc' => 'escape',
        ]);

        return [
            $stringImplicitBackslashesFixer,
        ];
    }

    protected function createConfigurationDefinition(): FixerConfigurationResolverInterface
    {
        return new FixerConfigurationResolver([
            (new FixerOptionBuilder('single_quoted', 'Whether to fix single-quoted strings.'))
                ->setAllowedTypes(['bool'])
                ->setDefault(false)
                ->getOption(),
            (new FixerOptionBuilder('double_quoted', 'Whether to fix double-quoted strings.'))
                ->setAllowedTypes(['bool'])
                ->setDefault(true)
                ->getOption(),
            (new FixerOptionBuilder('heredoc_syntax', 'Whether to fix heredoc syntax.'))
                ->setAllowedTypes(['bool'])
                ->setDefault(true)
                ->getOption(),
        ]);
    }
}
