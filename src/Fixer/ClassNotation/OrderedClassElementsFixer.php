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

namespace PhpCsFixer\Fixer\ClassNotation;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\Fixer\ConfigurableFixerInterface;
use PhpCsFixer\FixerConfiguration\AllowedValueSubset;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolver;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolverInterface;
use PhpCsFixer\FixerConfiguration\FixerOptionBuilder;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @author Gregor Harlan <gharlan@web.de>
 */
final class OrderedClassElementsFixer extends AbstractFixer implements ConfigurableFixerInterface
{
    /** @internal */
    public const SORT_ALPHA = 'alpha';

    /** @internal */
    public const SORT_NONE = 'none';

    private const SUPPORTED_SORT_ALGORITHMS = [
        self::SORT_NONE,
        self::SORT_ALPHA,
    ];

    /**
     * @var array<string, null|list<string>> Array containing all class element base types (keys) and their parent types (values)
     */
    private static array $typeHierarchy = [
        'use_trait' => null,
        'public' => null,
        'protected' => null,
        'private' => null,
        'case' => ['public'],
        'constant' => null,
        'constant_public' => ['constant', 'public'],
        'constant_protected' => ['constant', 'protected'],
        'constant_private' => ['constant', 'private'],
        'property' => null,
        'property_static' => ['property'],
        'property_public' => ['property', 'public'],
        'property_protected' => ['property', 'protected'],
        'property_private' => ['property', 'private'],
        'property_public_readonly' => ['property_readonly', 'property_public'],
        'property_protected_readonly' => ['property_readonly', 'property_protected'],
        'property_private_readonly' => ['property_readonly', 'property_private'],
        'property_public_static' => ['property_static', 'property_public'],
        'property_protected_static' => ['property_static', 'property_protected'],
        'property_private_static' => ['property_static', 'property_private'],
        'method' => null,
        'method_abstract' => ['method'],
        'method_static' => ['method'],
        'method_public' => ['method', 'public'],
        'method_protected' => ['method', 'protected'],
        'method_private' => ['method', 'private'],
        'method_public_abstract' => ['method_abstract', 'method_public'],
        'method_protected_abstract' => ['method_abstract', 'method_protected'],
        'method_private_abstract' => ['method_abstract', 'method_private'],
        'method_public_abstract_static' => ['method_abstract', 'method_static', 'method_public'],
        'method_protected_abstract_static' => ['method_abstract', 'method_static', 'method_protected'],
        'method_private_abstract_static' => ['method_abstract', 'method_static', 'method_private'],
        'method_public_static' => ['method_static', 'method_public'],
        'method_protected_static' => ['method_static', 'method_protected'],
        'method_private_static' => ['method_static', 'method_private'],
    ];

    /**
     * @var array<string, null> Array containing special method types
     */
    private static array $specialTypes = [
        'construct' => null,
        'destruct' => null,
        'magic' => null,
        'phpunit' => null,
    ];

    /**
     * @var array<string, int> Resolved configuration array (type => position)
     */
    private array $typePosition;

    public function configure(array $configuration): void
    {
        parent::configure($configuration);

        $this->typePosition = [];
        $pos = 0;

        foreach ($this->configuration['order'] as $type) {
            $this->typePosition[$type] = $pos++;
        }

        foreach (self::$typeHierarchy as $type => $parents) {
            if (isset($this->typePosition[$type])) {
                continue;
            }

            if (!$parents) {
                $this->typePosition[$type] = null;

                continue;
            }

            foreach ($parents as $parent) {
                if (isset($this->typePosition[$parent])) {
                    $this->typePosition[$type] = $this->typePosition[$parent];

                    continue 2;
                }
            }

            $this->typePosition[$type] = null;
        }

        $lastPosition = \count($this->configuration['order']);

        foreach ($this->typePosition as &$pos) {
            if (null === $pos) {
                $pos = $lastPosition;
            }

            $pos *= 10; // last digit is used by phpunit method ordering
        }
    }

    public function isCandidate(Tokens $tokens): bool
    {
        return $tokens->isAnyTokenKindsFound(Token::getClassyTokenKinds());
    }

    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(
            'Orders the elements of classes/interfaces/traits/enums.',
            [
                new CodeSample(
                    '<?php
final class Example
{
    use BarTrait;
    use BazTrait;
    const C1 = 1;
    const C2 = 2;
    protected static $protStatProp;
    public static $pubStatProp1;
    public $pubProp1;
    protected $protProp;
    var $pubProp2;
    private static $privStatProp;
    private $privProp;
    public static $pubStatProp2;
    public $pubProp3;
    protected function __construct() {}
    private static function privStatFunc() {}
    public function pubFunc1() {}
    public function __toString() {}
    protected function protFunc() {}
    function pubFunc2() {}
    public static function pubStatFunc1() {}
    public function pubFunc3() {}
    static function pubStatFunc2() {}
    private function privFunc() {}
    public static function pubStatFunc3() {}
    protected static function protStatFunc() {}
    public function __destruct() {}
}
'
                ),
                new CodeSample(
                    '<?php
class Example
{
    public function A(){}
    private function B(){}
}
',
                    ['order' => ['method_private', 'method_public']]
                ),
                new CodeSample(
                    '<?php
class Example
{
    public function D(){}
    public function B(){}
    public function A(){}
    public function C(){}
}
',
                    ['order' => ['method_public'], 'sort_algorithm' => self::SORT_ALPHA]
                ),
            ]
        );
    }

    /**
     * {@inheritdoc}
     *
     * Must run before ClassAttributesSeparationFixer, NoBlankLinesAfterClassOpeningFixer, SpaceAfterSemicolonFixer.
     * Must run after NoPhp4ConstructorFixer, ProtectedToPrivateFixer.
     */
    public function getPriority(): int
    {
        return 65;
    }

    protected function applyFix(\SplFileInfo $file, Tokens $tokens): void
    {
        for ($i = 1, $count = $tokens->count(); $i < $count; ++$i) {
            if (!$tokens[$i]->isClassy()) {
                continue;
            }

            $i = $tokens->getNextTokenOfKind($i, ['{']);
            $elements = $this->getElements($tokens, $i);

            if (0 === \count($elements)) {
                continue;
            }

            $sorted = $this->sortElements($elements);
            $endIndex = $elements[\count($elements) - 1]['end'];

            if ($sorted !== $elements) {
                $this->sortTokens($tokens, $i, $endIndex, $sorted);
            }

            $i = $endIndex;
        }
    }

    protected function createConfigurationDefinition(): FixerConfigurationResolverInterface
    {
        return new FixerConfigurationResolver([
            (new FixerOptionBuilder('order', 'List of strings defining order of elements.'))
                ->setAllowedTypes(['array'])
                ->setAllowedValues([new AllowedValueSubset(array_keys(array_merge(self::$typeHierarchy, self::$specialTypes)))])
                ->setDefault([
                    'use_trait',
                    'case',
                    'constant_public',
                    'constant_protected',
                    'constant_private',
                    'property_public',
                    'property_protected',
                    'property_private',
                    'construct',
                    'destruct',
                    'magic',
                    'phpunit',
                    'method_public',
                    'method_protected',
                    'method_private',
                ])
                ->getOption(),
            (new FixerOptionBuilder('sort_algorithm', 'How multiple occurrences of same type statements should be sorted.'))
                ->setAllowedValues(self::SUPPORTED_SORT_ALGORITHMS)
                ->setDefault(self::SORT_NONE)
                ->getOption(),
        ]);
    }

    /**
     * @return list<array{
     *     start: int,
     *     visibility: string,
     *     abstract: bool,
     *     static: bool,
     *     readonly: bool,
     *     type: string,
     *     name: string,
     *     end: int,
     * }>
     */
    private function getElements(Tokens $tokens, int $startIndex): array
    {
        static $elementTokenKinds = [CT::T_USE_TRAIT, T_CASE, T_CONST, T_VARIABLE, T_FUNCTION];

        ++$startIndex;
        $elements = [];

        while (true) {
            $element = [
                'start' => $startIndex,
                'visibility' => 'public',
                'abstract' => false,
                'static' => false,
                'readonly' => false,
            ];

            for ($i = $startIndex;; ++$i) {
                $token = $tokens[$i];

                // class end
                if ($token->equals('}')) {
                    return $elements;
                }

                if ($token->isGivenKind(T_ABSTRACT)) {
                    $element['abstract'] = true;

                    continue;
                }

                if ($token->isGivenKind(T_STATIC)) {
                    $element['static'] = true;

                    continue;
                }

                if (\defined('T_READONLY') && $token->isGivenKind(T_READONLY)) { // @TODO: drop condition when PHP 8.1+ is required
                    $element['readonly'] = true;
                }

                if ($token->isGivenKind([T_PROTECTED, T_PRIVATE])) {
                    $element['visibility'] = strtolower($token->getContent());

                    continue;
                }

                if (!$token->isGivenKind($elementTokenKinds)) {
                    continue;
                }

                $type = $this->detectElementType($tokens, $i);

                if (\is_array($type)) {
                    $element['type'] = $type[0];
                    $element['name'] = $type[1];
                } else {
                    $element['type'] = $type;
                }

                if ('property' === $element['type']) {
                    $element['name'] = $tokens[$i]->getContent();
                } elseif (\in_array($element['type'], ['use_trait', 'case', 'constant', 'method', 'magic', 'construct', 'destruct'], true)) {
                    $element['name'] = $tokens[$tokens->getNextMeaningfulToken($i)]->getContent();
                }

                $element['end'] = $this->findElementEnd($tokens, $i);

                break;
            }

            $elements[] = $element;
            $startIndex = $element['end'] + 1;
        }
    }

    /**
     * @return array<string>|string type or array of type and name
     */
    private function detectElementType(Tokens $tokens, int $index)
    {
        $token = $tokens[$index];

        if ($token->isGivenKind(CT::T_USE_TRAIT)) {
            return 'use_trait';
        }

        if ($token->isGivenKind(T_CASE)) {
            return 'case';
        }

        if ($token->isGivenKind(T_CONST)) {
            return 'constant';
        }

        if ($token->isGivenKind(T_VARIABLE)) {
            return 'property';
        }

        $nameToken = $tokens[$tokens->getNextMeaningfulToken($index)];

        if ($nameToken->equals([T_STRING, '__construct'], false)) {
            return 'construct';
        }

        if ($nameToken->equals([T_STRING, '__destruct'], false)) {
            return 'destruct';
        }

        if (
            $nameToken->equalsAny([
                [T_STRING, 'setUpBeforeClass'],
                [T_STRING, 'doSetUpBeforeClass'],
                [T_STRING, 'tearDownAfterClass'],
                [T_STRING, 'doTearDownAfterClass'],
                [T_STRING, 'setUp'],
                [T_STRING, 'doSetUp'],
                [T_STRING, 'assertPreConditions'],
                [T_STRING, 'assertPostConditions'],
                [T_STRING, 'tearDown'],
                [T_STRING, 'doTearDown'],
            ], false)
        ) {
            return ['phpunit', strtolower($nameToken->getContent())];
        }

        return str_starts_with($nameToken->getContent(), '__') ? 'magic' : 'method';
    }

    private function findElementEnd(Tokens $tokens, int $index): int
    {
        $index = $tokens->getNextTokenOfKind($index, ['{', ';']);

        if ($tokens[$index]->equals('{')) {
            $index = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_CURLY_BRACE, $index);
        }

        for (++$index; $tokens[$index]->isWhitespace(" \t") || $tokens[$index]->isComment(); ++$index);

        --$index;

        return $tokens[$index]->isWhitespace() ? $index - 1 : $index;
    }

    /**
     * @return list<array{
     *     start: int,
     *     visibility: string,
     *     abstract: bool,
     *     static: bool,
     *     readonly: bool,
     *     type: string,
     *     name: string,
     *     end: int,
     *     position: int,
     * }>
     */
    private function sortElements(array $elements): array
    {
        static $phpunitPositions = [
            'setupbeforeclass' => 1,
            'dosetupbeforeclass' => 2,
            'teardownafterclass' => 3,
            'doteardownafterclass' => 4,
            'setup' => 5,
            'dosetup' => 6,
            'assertpreconditions' => 7,
            'assertpostconditions' => 8,
            'teardown' => 9,
            'doteardown' => 10,
        ];

        foreach ($elements as &$element) {
            $type = $element['type'];

            if (\array_key_exists($type, self::$specialTypes)) {
                if (isset($this->typePosition[$type])) {
                    $element['position'] = $this->typePosition[$type];

                    if ('phpunit' === $type) {
                        $element['position'] += $phpunitPositions[$element['name']];
                    }

                    continue;
                }

                $type = 'method';
            }

            if (\in_array($type, ['constant', 'property', 'method'], true)) {
                $type .= '_'.$element['visibility'];

                if ($element['abstract']) {
                    $type .= '_abstract';
                }

                if ($element['static']) {
                    $type .= '_static';
                }

                if ($element['readonly']) {
                    $type .= '_readonly';
                }
            }

            $element['position'] = $this->typePosition[$type];
        }

        unset($element);

        usort($elements, function (array $a, array $b): int {
            if ($a['position'] === $b['position']) {
                return $this->sortGroupElements($a, $b);
            }

            return $a['position'] <=> $b['position'];
        });

        return $elements;
    }

    /**
     * @param array{
     *     start: int,
     *     visibility: string,
     *     abstract: bool,
     *     static: bool,
     *     readonly: bool,
     *     type: string,
     *     name: string,
     *     end: int,
     *     position: int,
     * } $a
     * @param array{
     *     start: int,
     *     visibility: string,
     *     abstract: bool,
     *     static: bool,
     *     readonly: bool,
     *     type: string,
     *     name: string,
     *     end: int,
     *     position: int,
     * } $b
     */
    private function sortGroupElements(array $a, array $b): int
    {
        $selectedSortAlgorithm = $this->configuration['sort_algorithm'];

        if (self::SORT_ALPHA === $selectedSortAlgorithm) {
            return strcasecmp($a['name'], $b['name']);
        }

        return $a['start'] <=> $b['start'];
    }

    /**
     * @param list<array{
     *     start: int,
     *     visibility: string,
     *     abstract: bool,
     *     static: bool,
     *     readonly: bool,
     *     type: string,
     *     name: string,
     *     end: int,
     *     position: int,
     * }> $elements
     */
    private function sortTokens(Tokens $tokens, int $startIndex, int $endIndex, array $elements): void
    {
        $replaceTokens = [];

        foreach ($elements as $element) {
            for ($i = $element['start']; $i <= $element['end']; ++$i) {
                $replaceTokens[] = clone $tokens[$i];
            }
        }

        $tokens->overrideRange($startIndex + 1, $endIndex, $replaceTokens);
    }
}
