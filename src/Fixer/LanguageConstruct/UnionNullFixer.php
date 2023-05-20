<?php

namespace PhpCsFixer\Fixer\LanguageConstruct;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\Fixer\ConfigurableFixerInterface;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolver;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolverInterface;
use PhpCsFixer\FixerConfiguration\FixerOptionBuilder;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Tokens;

class UnionNullFixer extends AbstractFixer implements ConfigurableFixerInterface
{
    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(
            'Replaces ? or null with the corresponding union or nullable type.',
            [
                new CodeSample(
                    "<?php\nfunction sample(string|null \$str = null)\n{}\n"
                ),
                new CodeSample(
                    "<?php\nfunction sample(?string \$str = null)\n{}\n",
                    ['use_nullable_type_declaration' => true]
                ),
                new CodeSample(
                    "<?php\nclass Foo {\n  private string|null \$str = null;\n}\n"
                ),
                new CodeSample(
                    "<?php\nclass Foo {\n  private ?string \$str = null;\n}\n",
                    ['use_nullable_type_declaration' => true]
                )
            ],
            'Rule is applied only in a PHP 8.0+ environment.'
        );
    }

    /**
     * {@inheritdoc}
     *
     * Must run after NullableTypeDeclarationForDefaultNullValueFixer.
     */
    public function getPriority(): int
    {
        return 2;
    }

    protected function createConfigurationDefinition(): FixerConfigurationResolverInterface
    {
        return new FixerConfigurationResolver([
            (new FixerOptionBuilder('use_nullable_type_declaration', 'Whether to add or remove `?` before type declarations for parameters with a default `null` value.'))
                ->setAllowedTypes(['bool'])
                ->setDefault(true)
                ->getOption(),
        ]);
    }

    public function isCandidate(Tokens $tokens): bool
    {
        return $tokens->isTokenKindFound(T_VARIABLE) && $tokens->isAnyTokenKindsFound([T_FUNCTION, T_FN]);
    }

    protected function applyFix(\SplFileInfo $file, Tokens $tokens): void
    {
        // TODO: Implement applyFix() method.
    }
}
