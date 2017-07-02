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

namespace PhpCsFixer\Tests\Fixer\Phpdoc;

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @author Graham Campbell <graham@alt-three.com>
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 * @author SpacePossum
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\Phpdoc\PhpdocNoAliasTagFixer
 */
final class PhpdocNoAliasTagFixerTest extends AbstractFixerTestCase
{
    public function testInvalidConfigCase1()
    {
        $this->setExpectedExceptionRegExp(
            'PhpCsFixer\ConfigurationException\InvalidFixerConfigurationException',
            '#^\[phpdoc_no_alias_tag\] Invalid configuration: Tag to replace must be a string\.$#'
        );

        $this->fixer->configure(array('replacements' => array(1 => 'abc')));
    }

    public function testInvalidConfigCase2()
    {
        $this->setExpectedExceptionRegExp(
            'PhpCsFixer\ConfigurationException\InvalidFixerConfigurationException',
            '#^\[phpdoc_no_alias_tag\] Invalid configuration: Tag to replace to from "a" must be a string\.$#'
        );

        $this->fixer->configure(array('replacements' => array('a' => null)));
    }

    public function testInvalidConfigCase3()
    {
        $this->setExpectedExceptionRegExp(
            'PhpCsFixer\ConfigurationException\InvalidFixerConfigurationException',
            '#^\[phpdoc_no_alias_tag\] Invalid configuration: Tag "see" cannot be replaced by invalid tag "link\*\/"\.$#'
        );

        $this->fixer->configure(array('replacements' => array('see' => 'link*/')));
    }

    public function testInvalidConfigCase4()
    {
        $this->setExpectedExceptionRegExp(
            'PhpCsFixer\ConfigurationException\InvalidFixerConfigurationException',
            '#^\[phpdoc_no_alias_tag\] Invalid configuration: Cannot change tag "link" to tag "see", as the tag "see" is configured to be replaced to "link"\.$#'
        );

        $this->fixer->configure(array('replacements' => array(
            'link' => 'see',
            'a' => 'b',
            'see' => 'link',
        )));
    }

    public function testInvalidConfigCase5()
    {
        $this->setExpectedExceptionRegExp(
            'PhpCsFixer\ConfigurationException\InvalidFixerConfigurationException',
            '#^\[phpdoc_no_alias_tag\] Invalid configuration: Cannot change tag "b" to tag "see", as the tag "see" is configured to be replaced to "link"\.$#'
        );

        $this->fixer->configure(array('replacements' => array(
            'b' => 'see',
            'see' => 'link',
            'link' => 'b',
        )));
    }

    public function testInvalidConfigCase6()
    {
        $this->setExpectedExceptionRegExp(
            'PhpCsFixer\ConfigurationException\InvalidFixerConfigurationException',
            '#^\[phpdoc_no_alias_tag\] Invalid configuration: Cannot change tag "see" to tag "link", as the tag "link" is configured to be replaced to "b"\.$#'
        );

        $this->fixer->configure(array('replacements' => array(
            'see' => 'link',
            'link' => 'b',
        )));
    }

    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @group legacy
     * @dataProvider providePropertyCases
     * @expectedDeprecation Passing "replacements" at the root of the configuration is deprecated and will not be supported in 3.0, use "replacements" => array(...) option instead.
     */
    public function testLegacyPropertyFix($expected, $input = null)
    {
        $this->fixer->configure(array(
            'property-read' => 'property',
            'property-write' => 'property',
        ));

        $this->doTest($expected, $input);
    }

    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider providePropertyCases
     */
    public function testPropertyFix($expected, $input = null)
    {
        $this->fixer->configure(array('replacements' => array(
            'property-read' => 'property',
            'property-write' => 'property',
        )));

        $this->doTest($expected, $input);
    }

    public function providePropertyCases()
    {
        return array(
            array(
                '<?php
    /**
     *
     */',
            ),
            array(
                '<?php
    /**
     * @property string $foo
     */',
                '<?php
    /**
     * @property-read string $foo
     */',
            ),
            array(
                '<?php /** @property mixed $bar */',
                '<?php /** @property-write mixed $bar */',
            ),
        );
    }

    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideTypeToVarCases
     */
    public function testTypeToVarFix($expected, $input = null)
    {
        $this->fixer->configure(array('replacements' => array(
            'type' => 'var',
        )));

        $this->doTest($expected, $input);
    }

    public function provideTypeToVarCases()
    {
        return array(
            array(
                '<?php
    /**
     *
     */',
            ),
            array(
                '<?php
    /**
     * @var string Hello!
     */',
                '<?php
    /**
     * @type string Hello!
     */',
            ),
            array(
                '<?php /** @var string Hello! */',
                '<?php /** @type string Hello! */',
            ),
            array(
                '<?php
    /**
     * Initializes this class with the given options.
     *
     * @param array $options {
     *     @var bool   $required Whether this element is required
     *     @var string $label    The display name for this element
     * }
     */',
                '<?php
    /**
     * Initializes this class with the given options.
     *
     * @param array $options {
     *     @type bool   $required Whether this element is required
     *     @type string $label    The display name for this element
     * }
     */',
            ),
        );
    }

    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideVarToTypeCases
     */
    public function testVarToTypeFix($expected, $input = null)
    {
        $this->fixer->configure(array('replacements' => array(
            'var' => 'type',
        )));

        $this->doTest($expected, $input);
    }

    public function provideVarToTypeCases()
    {
        return array(
            array(
                '<?php
    /**
     *
     */',
            ),
            array(
                '<?php
    /**
     * @type string Hello!
     */',
                '<?php
    /**
     * @var string Hello!
     */',
            ),
            array(
                '<?php /** @type string Hello! */',
                '<?php /** @var string Hello! */',
            ),
            array(
                '<?php
    /**
     * Initializes this class with the given options.
     *
     * @param array $options {
     *     @type bool   $required Whether this element is required
     *     @type string $label    The display name for this element
     * }
     */',
                '<?php
    /**
     * Initializes this class with the given options.
     *
     * @param array $options {
     *     @var bool   $required Whether this element is required
     *     @var string $label    The display name for this element
     * }
     */',
            ),
        );
    }

    public function testLinkToSee()
    {
        $this->fixer->configure(array('replacements' => array(
            'link' => 'see',
        )));

        $this->doTest(
            '<?php /** @see  https://github.com/php-fig/fig-standards/blob/master/proposed/phpdoc.md#710-link-deprecated */',
            '<?php /** @link  https://github.com/php-fig/fig-standards/blob/master/proposed/phpdoc.md#710-link-deprecated */'
        );
    }
}
