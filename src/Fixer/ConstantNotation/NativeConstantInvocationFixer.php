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

namespace PhpCsFixer\Fixer\ConstantNotation;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\Fixer\ConfigurationDefinitionFixerInterface;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolver;
use PhpCsFixer\FixerConfiguration\FixerOptionBuilder;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\Tokenizer\Analyzer\NamespaceUsesAnalyzer;
use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;

/**
 * @author Filippo Tessarotto <zoeslam@gmail.com>
 */
final class NativeConstantInvocationFixer extends AbstractFixer implements ConfigurationDefinitionFixerInterface
{
    /**
     * @var array<string, true>
     */
    private $constantsToEscape = [];

    /**
     * @var array<string, true>
     */
    private $caseInsensitiveConstantsToEscape = [];

    /**
     * {@inheritdoc}
     */
    public function getDefinition()
    {
        return new FixerDefinition(
            'Add leading `\` before constant invocation of internal constant to speed up resolving. Constant name match is case-sensitive, except for `null`, `false` and `true`.',
            [
                new CodeSample('<?php var_dump(PHP_VERSION, M_PI, MY_CUSTOM_PI);'.PHP_EOL),
                new CodeSample(
                    '<?php var_dump(PHP_VERSION, M_PI, MY_CUSTOM_PI);'.PHP_EOL,
                    [
                        'include' => [
                            'MY_CUSTOM_PI',
                        ],
                    ]
                ),
                new CodeSample(
                    '<?php var_dump(PHP_VERSION, M_PI, MY_CUSTOM_PI);'.PHP_EOL,
                    [
                        'fix_built_in' => false,
                        'include' => [
                            'MY_CUSTOM_PI',
                        ],
                    ]
                ),
                new CodeSample(
                    '<?php var_dump(PHP_VERSION, M_PI, MY_CUSTOM_PI);'.PHP_EOL,
                    [
                        'exclude' => [
                            'M_PI',
                        ],
                    ]
                ),
            ],
            null,
            'Risky when any of the constants are namespaced or overridden.'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function isCandidate(Tokens $tokens)
    {
        return $tokens->isTokenKindFound(T_STRING);
    }

    /**
     * {@inheritdoc}
     */
    public function isRisky()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function configure(array $configuration = null)
    {
        parent::configure($configuration);

        $uniqueConfiguredExclude = \array_unique($this->configuration['exclude']);

        // Case sensitive constants handling
        $constantsToEscape = \array_values($this->configuration['include']);
        if (true === $this->configuration['fix_built_in']) {
            $getDefinedConstants = \get_defined_constants(true);
            unset($getDefinedConstants['user']);
            foreach ($getDefinedConstants as $constants) {
                $constantsToEscape = \array_merge($constantsToEscape, \array_keys($constants));
            }
        }
        $constantsToEscape = \array_diff(
            \array_unique($constantsToEscape),
            $uniqueConfiguredExclude
        );

        // Case insensitive constants handling
        static $caseInsensitiveConstants = ['null', 'false', 'true'];
        $caseInsensitiveConstantsToEscape = [];
        foreach ($constantsToEscape as $constantIndex => $constant) {
            $loweredConstant = \strtolower($constant);
            if (\in_array($loweredConstant, $caseInsensitiveConstants, true)) {
                $caseInsensitiveConstantsToEscape[] = $loweredConstant;
                unset($constantsToEscape[$constantIndex]);
            }
        }

        $caseInsensitiveConstantsToEscape = \array_diff(
            \array_unique($caseInsensitiveConstantsToEscape),
            \array_map('strtolower', $uniqueConfiguredExclude)
        );

        // Store the cache
        $this->constantsToEscape = \array_fill_keys($constantsToEscape, true);
        \ksort($this->constantsToEscape);

        $this->caseInsensitiveConstantsToEscape = \array_fill_keys($caseInsensitiveConstantsToEscape, true);
        \ksort($this->caseInsensitiveConstantsToEscape);
    }

    /**
     * {@inheritdoc}
     */
    protected function applyFix(\SplFileInfo $file, Tokens $tokens)
    {
        $useDeclarations = (new NamespaceUsesAnalyzer())->getDeclarationsFromTokens($tokens);
        $useConstantDeclarations = [];
        foreach ($useDeclarations as $use) {
            if ($use->isConstant()) {
                $useConstantDeclarations[$use->getShortName()] = true;
            }
        }

        $indexes = [];
        foreach ($tokens as $index => $token) {
            $tokenContent = $token->getContent();

            // test if we are at a constant call
            if (!$token->isGivenKind(T_STRING)) {
                continue;
            }

            $prevIndex = $tokens->getPrevMeaningfulToken($index);
            if ($tokens[$prevIndex]->isGivenKind(T_AS)) {
                continue;
            }

            $nextIndex = $tokens->getNextMeaningfulToken($index);
            if ($tokens[$nextIndex]->equals('(')) {
                continue;
            }

            $constantNamePrefix = $tokens->getPrevMeaningfulToken($index);
            if ($tokens[$constantNamePrefix]->isGivenKind([CT::T_USE_TRAIT, T_CLASS, T_CONST, T_DOUBLE_COLON, T_EXTENDS, T_FUNCTION, T_IMPLEMENTS, T_INTERFACE, T_NAMESPACE, T_NEW, T_NS_SEPARATOR, T_OBJECT_OPERATOR, T_TRAIT, T_USE])) {
                continue;
            }

            if (!isset($this->constantsToEscape[$tokenContent]) && !isset($this->caseInsensitiveConstantsToEscape[strtolower($tokenContent)])) {
                continue;
            }

            if (isset($useConstantDeclarations[$tokenContent])) {
                continue;
            }

            $indexes[] = $index;
        }

        $indexes = \array_reverse($indexes);
        foreach ($indexes as $index) {
            $tokens->insertAt($index, new Token([T_NS_SEPARATOR, '\\']));
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function createConfigurationDefinition()
    {
        $constantChecker = static function ($value) {
            foreach ($value as $constantName) {
                if (!\is_string($constantName) || '' === \trim($constantName) || \trim($constantName) !== $constantName) {
                    throw new InvalidOptionsException(\sprintf(
                        'Each element must be a non-empty, trimmed string, got "%s" instead.',
                        \is_object($constantName) ? \get_class($constantName) : \gettype($constantName)
                    ));
                }
            }

            return true;
        };

        return new FixerConfigurationResolver([
            (new FixerOptionBuilder('fix_built_in', 'Whether to fix constants returned by `get_defined_constants`. User constants are not accounted in this list and must be specified in the include one.'))
                ->setAllowedTypes(['bool'])
                ->setDefault(true)
                ->getOption(),
            (new FixerOptionBuilder('include', 'List of additional constants to fix.'))
                ->setAllowedTypes(['array'])
                ->setAllowedValues([$constantChecker])
                ->setDefault([])
                ->getOption(),
            (new FixerOptionBuilder('exclude', 'List of constants to ignore.'))
                ->setAllowedTypes(['array'])
                ->setAllowedValues([$constantChecker])
                ->setDefault(['null', 'false', 'true'])
                ->getOption(),
        ]);
    }
}
