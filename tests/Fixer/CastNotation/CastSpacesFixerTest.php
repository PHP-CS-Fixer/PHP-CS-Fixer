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

namespace PhpCsFixer\Tests\Fixer\CastNotation;

use PhpCsFixer\Test\AbstractFixerTestCase;

/**
 * @internal
 */
final class CastSpacesFixerTest extends AbstractFixerTestCase
{
    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider testFixCastsProvider
     */
    public function testFixCasts($expected, $input = null)
    {
        $this->doTest($expected, $input);
    }

    public function testFixCastsProvider()
    {
        return array(
            array(
                '<?php echo "( int ) $foo";',
            ),
            array(
                '<?php $bar = (int) $foo;',
                '<?php $bar = ( int)$foo;',
            ),
            array(
                '<?php $bar = (int) $foo;',
                '<?php $bar = (	int)$foo;',
            ),
            array(
                '<?php $bar = (int) $foo;',
                '<?php $bar = (int)	$foo;',
            ),
            array(
                '<?php $bar = (string) (int) $foo;',
                '<?php $bar = ( string )( int )$foo;',
            ),
            array(
                '<?php $bar = (string) (int) $foo;',
                '<?php $bar = (string)(int)$foo;',
            ),
            array(
                '<?php $bar = (string) (int) $foo;',
                '<?php $bar = ( string   )    (   int )$foo;',
            ),
            array(
                '<?php $bar = (string) $foo;',
                '<?php $bar = ( string )   $foo;',
            ),
            array(
                '<?php $bar = (float) Foo::bar();',
                '<?php $bar = (float )Foo::bar();',
            ),
            array(
                '<?php $bar = Foo::baz((float) Foo::bar());',
                '<?php $bar = Foo::baz((float )Foo::bar());',
            ),
            array(
                '<?php $bar = $query["params"] = (array) $query["params"];',
                '<?php $bar = $query["params"] = (array)$query["params"];',
            ),
            array(
                "<?php \$bar = (int)\n \$foo;",
            ),
            array(
                "<?php \$bar = (int)\r\n \$foo;",
            ),
        );
    }
}
