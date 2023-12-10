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

namespace PhpCsFixer\Tests\Fixer\Phpdoc;

use PhpCsFixer\ConfigurationException\InvalidFixerConfigurationException;
use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @author Graham Campbell <hello@gjcampbell.co.uk>
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\Phpdoc\PhpdocNoAliasTagFixer
 */
final class PhpdocNoAliasTagFixerTest extends AbstractFixerTestCase
{
    public function testInvalidConfigCase1(): void
    {
        $this->expectException(InvalidFixerConfigurationException::class);
        $this->expectExceptionMessageMatches('#^\[phpdoc_no_alias_tag\] Invalid configuration: Tag to replace must be a string\.$#');

        $this->fixer->configure(['replacements' => [1 => 'abc']]);
    }

    public function testInvalidConfigCase2(): void
    {
        $this->expectException(InvalidFixerConfigurationException::class);
        $this->expectExceptionMessageMatches('#^\[phpdoc_no_alias_tag\] Invalid configuration: Tag to replace to from "a" must be a string\.$#');

        $this->fixer->configure(['replacements' => ['a' => null]]);
    }

    public function testInvalidConfigCase3(): void
    {
        $this->expectException(InvalidFixerConfigurationException::class);
        $this->expectExceptionMessageMatches('#^\[phpdoc_no_alias_tag\] Invalid configuration: Tag "see" cannot be replaced by invalid tag "link\*\/"\.$#');

        $this->fixer->configure(['replacements' => ['see' => 'link*/']]);
    }

    public function testInvalidConfigCase4(): void
    {
        $this->expectException(InvalidFixerConfigurationException::class);
        $this->expectExceptionMessageMatches('#^\[phpdoc_no_alias_tag\] Invalid configuration: Cannot change tag "link" to tag "see", as the tag "see" is configured to be replaced to "link"\.$#');

        $this->fixer->configure(['replacements' => [
            'link' => 'see',
            'a' => 'b',
            'see' => 'link',
        ]]);
    }

    public function testInvalidConfigCase5(): void
    {
        $this->expectException(InvalidFixerConfigurationException::class);
        $this->expectExceptionMessageMatches('#^\[phpdoc_no_alias_tag\] Invalid configuration: Cannot change tag "b" to tag "see", as the tag "see" is configured to be replaced to "link"\.$#');

        $this->fixer->configure(['replacements' => [
            'b' => 'see',
            'see' => 'link',
            'link' => 'b',
        ]]);
    }

    public function testInvalidConfigCase6(): void
    {
        $this->expectException(InvalidFixerConfigurationException::class);
        $this->expectExceptionMessageMatches('#^\[phpdoc_no_alias_tag\] Invalid configuration: Cannot change tag "see" to tag "link", as the tag "link" is configured to be replaced to "b"\.$#');

        $this->fixer->configure(['replacements' => [
            'see' => 'link',
            'link' => 'b',
        ]]);
    }

    /**
     * @dataProvider providePropertyFixCases
     */
    public function testPropertyFix(string $expected, ?string $input = null): void
    {
        $this->fixer->configure(['replacements' => [
            'property-read' => 'property',
            'property-write' => 'property',
        ]]);

        $this->doTest($expected, $input);
    }

    public static function providePropertyFixCases(): iterable
    {
        yield [
            '<?php
    /**
     *
     */',
        ];

        yield [
            '<?php
    /**
     * @property string $foo
     */',
            '<?php
    /**
     * @property-read string $foo
     */',
        ];

        yield [
            '<?php /** @property mixed $bar */',
            '<?php /** @property-write mixed $bar */',
        ];
    }

    /**
     * @dataProvider provideTypeToVarFixCases
     */
    public function testTypeToVarFix(string $expected, ?string $input = null): void
    {
        $this->fixer->configure(['replacements' => [
            'type' => 'var',
        ]]);

        $this->doTest($expected, $input);
    }

    public static function provideTypeToVarFixCases(): iterable
    {
        yield [
            '<?php
    /**
     *
     */',
        ];

        yield [
            '<?php
    /**
     * @var string Hello!
     */',
            '<?php
    /**
     * @type string Hello!
     */',
        ];

        yield [
            '<?php /** @var string Hello! */',
            '<?php /** @type string Hello! */',
        ];

        yield [
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
        ];
    }

    /**
     * @dataProvider provideVarToTypeFixCases
     */
    public function testVarToTypeFix(string $expected, ?string $input = null): void
    {
        $this->fixer->configure(['replacements' => [
            'var' => 'type',
        ]]);

        $this->doTest($expected, $input);
    }

    public static function provideVarToTypeFixCases(): iterable
    {
        yield [
            '<?php
    /**
     *
     */',
        ];

        yield [
            '<?php
    /**
     * @type string Hello!
     */',
            '<?php
    /**
     * @var string Hello!
     */',
        ];

        yield [
            '<?php /** @type string Hello! */',
            '<?php /** @var string Hello! */',
        ];

        yield [
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
        ];
    }

    public function testLinkToSee(): void
    {
        $this->fixer->configure(['replacements' => [
            'link' => 'see',
        ]]);

        $this->doTest(
            '<?php /** @see  https://github.com/php-fig/fig-standards/blob/master/proposed/phpdoc.md#710-link-deprecated */',
            '<?php /** @link  https://github.com/php-fig/fig-standards/blob/master/proposed/phpdoc.md#710-link-deprecated */'
        );
    }

    /**
     * @dataProvider provideDefaultConfigCases
     */
    public function testDefaultConfig(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    public static function provideDefaultConfigCases(): iterable
    {
        yield [
            '<?php /** @see  https://github.com/php-fig/fig-standards/blob/master/proposed/phpdoc.md#710-link-deprecated */',
            '<?php /** @link  https://github.com/php-fig/fig-standards/blob/master/proposed/phpdoc.md#710-link-deprecated */',
        ];

        yield [
            '<?php /** @property mixed $bar */',
            '<?php /** @property-write mixed $bar */',
        ];

        yield [
            '<?php /** @property mixed $bar */',
            '<?php /** @property-read mixed $bar */',
        ];

        yield [
            '<?php /** @var string Hello! */',
            '<?php /** @type string Hello! */',
        ];
    }
}
