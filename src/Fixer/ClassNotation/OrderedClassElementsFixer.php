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

namespace PhpCsFixer\Fixer\ClassNotation;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\ConfigurationException\InvalidFixerConfigurationException;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @author Gregor Harlan <gharlan@web.de>
 */
final class OrderedClassElementsFixer extends AbstractFixer
{
    /**
     * @var array Array containing all class element base types (keys) and their parent types (values)
     */
    private static $typeHierarchy = array(
        'use_trait' => null,
        'public' => null,
        'protected' => null,
        'private' => null,
        'constant' => null,
        'constant_public' => array('constant', 'public'),
        'constant_protected' => array('constant', 'protected'),
        'constant_private' => array('constant', 'private'),
        'property' => null,
        'property_static' => array('property'),
        'property_public' => array('property', 'public'),
        'property_protected' => array('property', 'protected'),
        'property_private' => array('property', 'private'),
        'property_public_static' => array('property_static', 'property_public'),
        'property_protected_static' => array('property_static', 'property_protected'),
        'property_private_static' => array('property_static', 'property_private'),
        'method' => null,
        'method_static' => array('method'),
        'method_public' => array('method', 'public'),
        'method_protected' => array('method', 'protected'),
        'method_private' => array('method', 'private'),
        'method_public_static' => array('method_static', 'method_public'),
        'method_protected_static' => array('method_static', 'method_protected'),
        'method_private_static' => array('method_static', 'method_private'),
    );

    /**
     * @var array Array containing special method types
     */
    private static $specialTypes = array(
        'construct' => null,
        'destruct' => null,
        'magic' => null,
        'phpunit' => null,
    );

    /**
     * @var string[] Default order/configuration
     */
    private static $defaultOrder = array(
        'use_trait',
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
    );

    /**
     * @var array Resolved configuration array (type => position)
     */
    private $typePosition;

    /**
     * {@inheritdoc}
     */
    public function configure(array $configuration = null)
    {
        if (null === $configuration) {
            $configuration = self::$defaultOrder;
        }

        $this->typePosition = array();
        $pos = 0;
        foreach ($configuration as $type) {
            if (!array_key_exists($type, self::$typeHierarchy) && !array_key_exists($type, self::$specialTypes)) {
                throw new InvalidFixerConfigurationException($this->getName(), sprintf('Unknown class element type "%s".', $type));
            }

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

        $lastPosition = count($configuration);
        foreach ($this->typePosition as &$pos) {
            if (null === $pos) {
                $pos = $lastPosition;
            }
            // last digit is used by phpunit method ordering
            $pos *= 10;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function isCandidate(Tokens $tokens)
    {
        return $tokens->isAnyTokenKindsFound(Token::getClassyTokenKinds());
    }

    /**
     * {@inheritdoc}
     */
    public function fix(\SplFileInfo $file, Tokens $tokens)
    {
        for ($i = 1, $count = $tokens->count(); $i < $count; ++$i) {
            if (!$tokens[$i]->isClassy()) {
                continue;
            }

            $i = $tokens->getNextTokenOfKind($i, array('{'));
            $elements = $this->getElements($tokens, $i);

            if (!$elements) {
                continue;
            }

            $sorted = $this->sortElements($elements);
            $endIndex = $elements[count($elements) - 1]['end'];

            if ($sorted !== $elements) {
                $this->sortTokens($tokens, $i, $endIndex, $sorted);
            }

            $i = $endIndex;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'Orders the elements of classes/interfaces/traits.';
    }

    /**
     * {@inheritdoc}
     */
    public function getPriority()
    {
        // must run before MethodSeparationFixer, NoBlankLinesAfterClassOpeningFixer and SpaceAfterSemicolonFixer
        return 65;
    }

    /**
     * @param Tokens $tokens
     * @param int    $startIndex
     *
     * @return array[]
     */
    private function getElements(Tokens $tokens, $startIndex)
    {
        static $elementTokenKinds = array(CT_USE_TRAIT, T_CONST, T_VARIABLE, T_FUNCTION);

        ++$startIndex;
        $elements = array();

        while (true) {
            $element = array(
                'start' => $startIndex,
                'visibility' => 'public',
                'static' => false,
            );

            for ($i = $startIndex; ; ++$i) {
                $token = $tokens[$i];

                // class end
                if ($token->equals('}')) {
                    return $elements;
                }

                if ($token->isGivenKind(T_STATIC)) {
                    $element['static'] = true;
                    continue;
                }

                if ($token->isGivenKind(array(T_PROTECTED, T_PRIVATE))) {
                    $element['visibility'] = strtolower($token->getContent());
                    continue;
                }

                if (!$token->isGivenKind($elementTokenKinds)) {
                    continue;
                }

                $type = $this->detectElementType($tokens, $i);
                if (is_array($type)) {
                    $element['type'] = $type[0];
                    $element['name'] = $type[1];
                } else {
                    $element['type'] = $type;
                }

                $element['end'] = $this->findElementEnd($tokens, $i);
                break;
            }

            $elements[] = $element;
            $startIndex = $element['end'] + 1;
        }
    }

    /**
     * @param Tokens $tokens
     * @param int    $index
     *
     * @return string|array type or array of type and name
     */
    private function detectElementType(Tokens $tokens, $index)
    {
        $token = $tokens[$index];

        if ($token->isGivenKind(CT_USE_TRAIT)) {
            return 'use_trait';
        }

        if ($token->isGivenKind(T_CONST)) {
            return 'constant';
        }

        if ($token->isGivenKind(T_VARIABLE)) {
            return 'property';
        }

        $nameToken = $tokens[$tokens->getNextMeaningfulToken($index)];

        if ($nameToken->equals(array(T_STRING, '__construct'), false)) {
            return 'construct';
        }

        if ($nameToken->equals(array(T_STRING, '__destruct'), false)) {
            return 'destruct';
        }

        if (
            $nameToken->equalsAny(array(
                array(T_STRING, 'setUpBeforeClass'),
                array(T_STRING, 'tearDownAfterClass'),
                array(T_STRING, 'setUp'),
                array(T_STRING, 'tearDown'),
            ), false)
        ) {
            return array('phpunit', strtolower($nameToken->getContent()));
        }

        if ('__' === substr($nameToken->getContent(), 0, 2)) {
            return 'magic';
        }

        return 'method';
    }

    /**
     * @param Tokens $tokens
     * @param int    $index
     *
     * @return int
     */
    private function findElementEnd(Tokens $tokens, $index)
    {
        $index = $tokens->getNextTokenOfKind($index, array('{', ';'));

        if ($tokens[$index]->equals('{')) {
            $index = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_CURLY_BRACE, $index);
        }

        for (++$index; $tokens[$index]->isWhitespace(" \t") || $tokens[$index]->isComment(); ++$index);

        --$index;

        return $tokens[$index]->isWhitespace() ? $index - 1 : $index;
    }

    /**
     * @param array[] $elements
     *
     * @return array[]
     */
    private function sortElements(array $elements)
    {
        static $phpunitPositions = array(
            'setupbeforeclass' => 1,
            'teardownafterclass' => 2,
            'setup' => 3,
            'teardown' => 4,
        );

        foreach ($elements as &$element) {
            $type = $element['type'];

            if (array_key_exists($type, self::$specialTypes)) {
                if (isset($this->typePosition[$type])) {
                    $element['position'] = $this->typePosition[$type];
                    if ('phpunit' === $type) {
                        $element['position'] += $phpunitPositions[$element['name']];
                    }
                    continue;
                }

                $type = 'method';
            }

            if (in_array($type, array('constant', 'property', 'method'), true)) {
                $type .= '_'.$element['visibility'];
                if ($element['static']) {
                    $type .= '_static';
                }
            }

            $element['position'] = $this->typePosition[$type];
        }

        usort($elements, function (array $a, array $b) {
            if ($a['position'] === $b['position']) {
                // same group, preserve current order
                return $a['start'] > $b['start'] ? 1 : -1;
            }

            return $a['position'] > $b['position'] ? 1 : -1;
        });

        return $elements;
    }

    /**
     * @param Tokens  $tokens
     * @param int     $startIndex
     * @param int     $endIndex
     * @param array[] $elements
     */
    private function sortTokens(Tokens $tokens, $startIndex, $endIndex, array $elements)
    {
        $replaceTokens = array();

        foreach ($elements as $element) {
            for ($i = $element['start']; $i <= $element['end']; ++$i) {
                $replaceTokens[] = clone $tokens[$i];
            }
        }

        $tokens->overrideRange($startIndex + 1, $endIndex, $replaceTokens);
    }
}
