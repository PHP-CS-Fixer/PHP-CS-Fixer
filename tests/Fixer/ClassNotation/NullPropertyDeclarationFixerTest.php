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

namespace PhpCsFixer\Tests\Fixer\ClassNotation;

use PhpCsFixer\Test\AbstractFixerTestCase;

/**
 * @author ntzm
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\ClassNotation\NullPropertyDeclarationFixer
 */
final class NullPropertyDeclarationFixerTest extends AbstractFixerTestCase
{
    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideCases
     */
    public function testFix($expected, $input = null)
    {
        $this->doTest($expected, $input);
    }

    public function provideCases()
    {
        return [
            [
                '<?php class Foo { public $bar; }',
                '<?php class Foo { public $bar = null; }',
            ],
            [
                '<?php class Foo { protected $bar; }',
                '<?php class Foo { protected $bar = null; }',
            ],
            [
                '<?php class Foo { private $bar; }',
                '<?php class Foo { private $bar = null; }',
            ],
            [
                '<?php class Foo { var $bar; }',
                '<?php class Foo { var $bar = null; }',
            ],
            [
                '<?php class Foo { public $bar; }',
                '<?php class Foo { public $bar = NULL; }',
            ],
            [
                '<?php class Foo { public $bar; }',
                '<?php class Foo { public $bar = nuLL; }',
            ],
            [
                '<?php class Foo { public $bar; protected $baz; }',
                '<?php class Foo { public $bar = null; protected $baz = null; }',
            ],
            [
                '<?php class Foo { public $bar = \'null\'; }',
            ],
            [
                '<?php class Foo { public function bar() { return null; } }',
            ],
        ];
    }
}
