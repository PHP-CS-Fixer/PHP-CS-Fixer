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

namespace PhpCsFixer\Tests\Fixer\Casing;

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @author siad007
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\Casing\NativeClassCasingFixer
 */
final class NativeClassCasingFixerTest extends AbstractFixerTestCase
{
    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideFixCases
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
                    $stdclass = new \stdClass();
                    $stdclass = new stdClass();
                ',
                '<?php
                    $stdclass = new \STDCLASS();
                    $stdclass = new STDCLASS();
                ',
            ],
            [
                '<?php
                
                namespace Foo;
                
                class exception extends \Exception
                {
                }
                ',
                '<?php
                
                namespace Foo;
                
                class exception extends \EXCEPTION
                {
                }
                ',
            ],
            [
                '<?php
                
                echo \Exception::class;
                echo Exception::class;
                ',
                '<?php
                
                echo \ExCePTion::class;
                echo ExCePTion::class;
                ',
            ],
            [
                '<?php
                
                $a::exception();
                ',
            ],
            [
                '<?php
                
                $a->exception();
                ',
            ],
            [
                '<?php
                
                function exception() {};
                ',
            ],
            [
                '<?php
                
                echo "This is an " . "exception";
                ',
            ],
            [
                '<?php
                
                namespace Foo;

                trait stdclass
                {
                }
            
                use Foo\stdclass as exception;
            
                class test
                {
                    use exception;
                }
                ',
            ],
        ];
    }
}
