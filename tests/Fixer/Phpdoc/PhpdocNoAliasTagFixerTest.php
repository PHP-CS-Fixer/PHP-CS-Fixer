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

use PhpCsFixer\Test\AbstractFixerTestCase;

/**
 * @author Graham Campbell <graham@alt-three.com>
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 * @author SpacePossum
 *
 * @internal
 */
final class PhpdocNoAliasTagFixerTest extends AbstractFixerTestCase
{
    /**
     * @expectedException \PhpCsFixer\ConfigurationException\InvalidFixerConfigurationException
     * @expectedExceptionMessageRegExp #^\[phpdoc_no_alias_tag\] Tag to replace must be a string.$#
     */
    public function testInvalidConfigCase1()
    {
        $this->getFixer()->configure(array(1 => 'abc'));
    }

    /**
     * @expectedException \PhpCsFixer\ConfigurationException\InvalidFixerConfigurationException
     * @expectedExceptionMessageRegExp #^\[phpdoc_no_alias_tag\] Tag to replace to from "a" must be a string.$#
     */
    public function testInvalidConfigCase2()
    {
        $this->getFixer()->configure(array('a' => null));
    }

    /**
     * @expectedException \PhpCsFixer\ConfigurationException\InvalidFixerConfigurationException
     * @expectedExceptionMessageRegExp #^\[phpdoc_no_alias_tag\] Tag "see" cannot be replaced by invalid tag "link\*\/".$#
     */
    public function testInvalidConfigCase3()
    {
        $this->getFixer()->configure(array('see' => 'link*/'));
    }

    /**
     * @expectedException \PhpCsFixer\ConfigurationException\InvalidFixerConfigurationException
     * @expectedExceptionMessageRegExp #^\[phpdoc_no_alias_tag\] Cannot change tag "link" to tag "see", as the tag is set configured to be replaced to "link".$#
     */
    public function testInvalidConfigCase4()
    {
        $config = array(
            'see' => 'link',
            'a' => 'b',
            'link' => 'see',
        );
        $this->getFixer()->configure($config);
    }

    /**
     * @expectedException \PhpCsFixer\ConfigurationException\InvalidFixerConfigurationException
     * @expectedExceptionMessageRegExp #^\[phpdoc_no_alias_tag\] Cannot change tag "b" to tag "see", as the tag is set configured to be replaced to "link".$#
     */
    public function testInvalidConfigCase5()
    {
        $config = array(
            'see' => 'link',
            'link' => 'b',
            'b' => 'see',
        );
        $this->getFixer()->configure($config);
    }

    /**
     * @dataProvider providePropertyCases
     */
    public function testPropertyFix($expected, $input = null)
    {
        $this->getFixer()->configure(array(
            'property-read' => 'property',
            'property-write' => 'property',
        ));
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
     * @dataProvider provideTypeToVarCases
     */
    public function testTypeToVarFix($expected, $input = null)
    {
        $this->getFixer()->configure(array(
            'type' => 'var',
        ));
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
     * @dataProvider provideVarToTypeCases
     */
    public function testVarToTypeFix($expected, $input = null)
    {
        $this->getFixer()->configure(array(
            'var' => 'type',
        ));
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
        $this->getFixer()->configure(array(
            'link' => 'see',
        ));
        $this->doTest(
            '<?php /** @see  https://github.com/php-fig/fig-standards/blob/master/proposed/phpdoc.md#710-link-deprecated */',
            '<?php /** @link  https://github.com/php-fig/fig-standards/blob/master/proposed/phpdoc.md#710-link-deprecated */'
        );
    }
}
