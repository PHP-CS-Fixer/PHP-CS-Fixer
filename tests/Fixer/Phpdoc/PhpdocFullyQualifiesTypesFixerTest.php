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
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\Phpdoc\PhpdocFullyQualifiesTypesFixer
 */
final class PhpdocFullyQualifiesTypesFixerTest extends AbstractFixerTestCase
{
    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideTestFixMethods
     */
    public function testFixMethods($expected, $input = null)
    {
        $this->doTest($expected, $input);
    }

    public function provideTestFixMethods()
    {
        return [
            'Docblock' => [
                '<?php

use Foo\Bar;

/**
 * @param Bar $foo
 */
function foo($foo) {}',
                '<?php

use Foo\Bar;

/**
 * @param \Foo\Bar $foo
 */
function foo($foo) {}',
            ],
        ];
    }
}
