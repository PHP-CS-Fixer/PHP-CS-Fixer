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

namespace PhpCsFixer\Tests\Fixer\Strict;

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @author VeeWee <toonverwerft@gmail.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\Strict\StrictMethodsFixer
 */
final class StrictMethodsFixerTest extends AbstractFixerTestCase
{
    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideFixCases
     * @requires PHP 7.0
     */
    public function testFix($expected, $input = null)
    {
        $this->doTest($expected, $input);
    }

    public function provideFixCases()
    {
        return [
            [
                '<?php
namespace A\B\C;
class A {

    /**
     * @var string
     */
    private $someprop;

    /**
     * @param int $invalidDocType
     * @param int|bool $multipleDocTypes
     * @return null|string
     */
    public function a(int $replaceMe, string $invalidDocType, $multipleDocTypes)
    {
    }

    public function b($noDocBlocks, string $withType)
    {
    }

    /**
     * @param string $doesNotExist
     * @param mixed $mixed
     */
    public function c(string $doesExist, $mixed): string
    {
    }
}',
                '<?php
namespace A\B\C;
class A {

    /**
     * @var string
     */
    private $someprop;

    /**
     * @param int $replaceMe
     * @param int $invalidDocType
     * @param int|bool $multipleDocTypes
     * @return null|string
     */
    public function a($replaceMe, string $invalidDocType, $multipleDocTypes)
    {
    }

    public function b($noDocBlocks, string $withType)
    {
    }

    /**
     * @param string $doesNotExist
     * @param $doesExist
     * @param mixed $mixed
     * @return string
     */
    public function c(string $doesExist, $mixed)
    {
    }
}',
            ],
            [
                '<?php

function x(SomeClass ...$classes): SomeCollection
{
}',
                '<?php
/**
 * @param SomeClass $classes
 * @return SomeCollection
 */
function x(SomeClass ...$classes)
{
}',
            ],
            [
                '<?php

function x(int $a=((1*(2+3))/(5+6)), string $b = (\'\' . (\'\'))): self
{
}',
                '<?php
/**
 * @param int $a
 * @param string $b
 * @return self
 */
function x($a=((1*(2+3))/(5+6)), string $b = (\'\' . (\'\')))
{
}',
            ],
            [
                '<?php
namespace SomeNamespace;

function x(SomeClass $x, \SomeNameSpace\SomeClass $y): SomeClass
{
}',
                '<?php
namespace SomeNamespace;

/**
 * @param \SomeNamespace\SomeClass $x;
 * @param SomeClass $y
 * @return \SomeNamespace\SomeClass
 */
function x(SomeClass $x, \SomeNameSpace\SomeClass $y): SomeClass
{
}
',
            ],
        ];
    }
}
