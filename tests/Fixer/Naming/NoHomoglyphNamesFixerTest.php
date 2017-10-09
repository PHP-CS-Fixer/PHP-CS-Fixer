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

namespace PhpCsFixer\Tests\Fixer\Naming;

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @author Fred Cox <mcfedr@gmail.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\Naming\NoHomoglyphNamesFixer
 */
final class NoHomoglyphNamesFixerTest extends AbstractFixerTestCase
{
    /**
     * @dataProvider provideFixCases
     *
     * @param mixed      $expected
     * @param null|mixed $input
     */
    public function testFix($expected, $input = null)
    {
        $this->doTest($expected, $input);
    }

    public function provideFixCases()
    {
        return [
            ['<?php $øøøøa = 1;'],
            ['<?php $name = "This should not be changed";'],
            ['<?php $name = "Это не меняется";'],
            ['<?php $name = \'Это не меняется\';'],
            ['<?php // This should not be chаnged'],
            ['<?php /* This should not be chаnged */'],
            [
                '<?php $name = \'wrong\';',
                '<?php $nаmе = \'wrong\';', // 'а' in name is a cyrillic letter
            ],
            [
                '<?php $a->name = \'wrong\';',
                '<?php $a->nаmе = \'wrong\';',
            ],
            [
                '<?php class A { private $name; }',
                '<?php class A { private $nаmе; }',
            ],
            [
                '<?php class Broken {}',
                '<?php class Вroken {}', // 'В' in Broken is a cyrillic letter
            ],
            [
                '<?php interface Broken {}',
                '<?php interface Вroken {}',
            ],
            [
                '<?php trait Broken {}',
                '<?php trait Вroken {}',
            ],
            [
                '<?php $a = new Broken();',
                '<?php $a = new Вroken();',
            ],
            [
                '<?php class A extends Broken {}',
                '<?php class A extends Вroken {}',
            ],
            [
                '<?php class A implements Broken {}',
                '<?php class A implements Вroken {}',
            ],
            [
                '<?php class A { use Broken; }',
                '<?php class A { use Вroken; }',
            ],
            [
                '<?php echo Broken::class;',
                '<?php echo Вroken::class;',
            ],
            [
                '<?php function name() {}',
                '<?php function nаmе() {}',
            ],
            [
                '<?php name();',
                '<?php nаmе();',
            ],
            [
                '<?php $first_name = "a";',
                '<?php $first＿name = "a";', // Weird underscore symbol
            ],
        ];
    }
}
