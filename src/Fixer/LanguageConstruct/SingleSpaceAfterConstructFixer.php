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

namespace PhpCsFixer\Fixer\LanguageConstruct;

use PhpCsFixer\AbstractProxyFixer;
use PhpCsFixer\Fixer\ConfigurableFixerInterface;
use PhpCsFixer\Fixer\DeprecatedFixerInterface;
use PhpCsFixer\FixerConfiguration\AllowedValueSubset;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolver;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolverInterface;
use PhpCsFixer\FixerConfiguration\FixerOptionBuilder;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\CT;

/**
 * @author Andreas Möller <am@localheinz.com>
 *
 * @deprecated
 */
final class SingleSpaceAfterConstructFixer extends AbstractProxyFixer implements ConfigurableFixerInterface, DeprecatedFixerInterface
{
    /**
     * @var array<string, null|int>
     */
    private static array $tokenMap = [
        'abstract' => T_ABSTRACT,
        'as' => T_AS,
        'attribute' => CT::T_ATTRIBUTE_CLOSE,
        'break' => T_BREAK,
        'case' => T_CASE,
        'catch' => T_CATCH,
        'class' => T_CLASS,
        'clone' => T_CLONE,
        'comment' => T_COMMENT,
        'const' => T_CONST,
        'const_import' => CT::T_CONST_IMPORT,
        'continue' => T_CONTINUE,
        'do' => T_DO,
        'echo' => T_ECHO,
        'else' => T_ELSE,
        'elseif' => T_ELSEIF,
        'enum' => null,
        'extends' => T_EXTENDS,
        'final' => T_FINAL,
        'finally' => T_FINALLY,
        'for' => T_FOR,
        'foreach' => T_FOREACH,
        'function' => T_FUNCTION,
        'function_import' => CT::T_FUNCTION_IMPORT,
        'global' => T_GLOBAL,
        'goto' => T_GOTO,
        'if' => T_IF,
        'implements' => T_IMPLEMENTS,
        'include' => T_INCLUDE,
        'include_once' => T_INCLUDE_ONCE,
        'instanceof' => T_INSTANCEOF,
        'insteadof' => T_INSTEADOF,
        'interface' => T_INTERFACE,
        'match' => null,
        'named_argument' => CT::T_NAMED_ARGUMENT_COLON,
        'namespace' => T_NAMESPACE,
        'new' => T_NEW,
        'open_tag_with_echo' => T_OPEN_TAG_WITH_ECHO,
        'php_doc' => T_DOC_COMMENT,
        'php_open' => T_OPEN_TAG,
        'print' => T_PRINT,
        'private' => T_PRIVATE,
        'protected' => T_PROTECTED,
        'public' => T_PUBLIC,
        'readonly' => null,
        'require' => T_REQUIRE,
        'require_once' => T_REQUIRE_ONCE,
        'return' => T_RETURN,
        'static' => T_STATIC,
        'switch' => T_SWITCH,
        'throw' => T_THROW,
        'trait' => T_TRAIT,
        'try' => T_TRY,
        'type_colon' => CT::T_TYPE_COLON,
        'use' => T_USE,
        'use_lambda' => CT::T_USE_LAMBDA,
        'use_trait' => CT::T_USE_TRAIT,
        'var' => T_VAR,
        'while' => T_WHILE,
        'yield' => T_YIELD,
        'yield_from' => T_YIELD_FROM,
    ];

    private SingleSpaceAroundConstructFixer $singleSpaceAroundConstructFixer;

    /**
     * {@inheritdoc}
     */
    public function __construct()
    {
        $this->singleSpaceAroundConstructFixer = new SingleSpaceAroundConstructFixer();

        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    public function getSuccessorsNames(): array
    {
        return array_keys($this->proxyFixers);
    }

    /**
     * {@inheritdoc}
     */
    public function configure(array $configuration): void
    {
        parent::configure($configuration);

        $this->singleSpaceAroundConstructFixer->configure([
            'constructs_contain_a_single_space' => [
                'yield_from',
            ],
            'constructs_preceded_by_a_single_space' => [],
            'constructs_followed_by_a_single_space' => $this->configuration['constructs'],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(
            'Ensures a single space after language constructs.',
            [
                new CodeSample(
                    '<?php

throw  new  \Exception();
'
                ),
                new CodeSample(
                    '<?php

echo  "Hello!";
',
                    [
                        'constructs' => [
                            'echo',
                        ],
                    ]
                ),
                new CodeSample(
                    '<?php

yield  from  baz();
',
                    [
                        'constructs' => [
                            'yield_from',
                        ],
                    ]
                ),
            ]
        );
    }

    /**
     * {@inheritdoc}
     *
     * Must run before BracesFixer, FunctionDeclarationFixer.
     * Must run after ModernizeStrposFixer.
     */
    public function getPriority(): int
    {
        return parent::getPriority();
    }

    /**
     * {@inheritdoc}
     */
    protected function createProxyFixers(): array
    {
        return [$this->singleSpaceAroundConstructFixer];
    }

    protected function createConfigurationDefinition(): FixerConfigurationResolverInterface
    {
        $defaults = self::$tokenMap;
        $tokens = array_keys($defaults);

        unset($defaults['type_colon']);

        return new FixerConfigurationResolver([
            (new FixerOptionBuilder('constructs', 'List of constructs which must be followed by a single space.'))
                ->setAllowedTypes(['array'])
                ->setAllowedValues([new AllowedValueSubset($tokens)])
                ->setDefault(array_keys($defaults))
                ->getOption(),
        ]);
    }
}
