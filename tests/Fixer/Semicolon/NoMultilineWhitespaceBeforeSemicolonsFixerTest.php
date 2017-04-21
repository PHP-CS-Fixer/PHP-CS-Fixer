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

namespace PhpCsFixer\Tests\Fixer\Semicolon;

use PhpCsFixer\Test\AbstractFixerTestCase;
use PhpCsFixer\WhitespacesFixerConfig;

/**
 * @author John Kelly <wablam@gmail.com>
 * @author Graham Campbell <graham@alt-three.com>
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\Semicolon\NoMultilineWhitespaceBeforeSemicolonsFixer
 */
final class NoMultilineWhitespaceBeforeSemicolonsFixerTest extends AbstractFixerTestCase
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
                '<?php
                    $foo->bar() // test
;',
                '<?php
                    $foo->bar() // test
                    ;',
            ],
            [
                "<?php echo(1) // test\n;",
            ],
            [
                '<?php
                    $foo->bar() # test
;',
                '<?php
                    $foo->bar() # test


                ;',
            ],
            [
                "<?php\n;",
            ],
            [
                '<?php
$this
    ->setName(\'readme1\')
    ->setDescription(\'Generates the README\');
',
                '<?php
$this
    ->setName(\'readme1\')
    ->setDescription(\'Generates the README\')
;
',
            ],
            [
                '<?php
$this
    ->setName(\'readme2\')
    ->setDescription(\'Generates the README\');
',
                '<?php
$this
    ->setName(\'readme2\')
    ->setDescription(\'Generates the README\')
    ;
',
            ],
            [
                '<?php echo "$this->foo(\'with param containing ;\') ;" ;',
            ],
            [
                '<?php $this->foo();',
            ],
            [
                '<?php $this->foo() ;',
            ],
            [
                '<?php $this->foo(\'with param containing ;\') ;',
            ],
            [
                '<?php $this->foo(\'with param containing ) ; \') ;',
            ],
            [
                '<?php $this->foo("with param containing ) ; ")  ; ?>',
            ],
            [
                '<?php $this->foo("with semicolon in string) ; "); ?>',
            ],
            [
                '<?php
$this
    ->example();',
                '<?php
$this
    ->example()

    ;',
            ],
        ];
    }

    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideMessyWhitespacesCases
     */
    public function testMessyWhitespaces($expected, $input = null)
    {
        $this->fixer->setWhitespacesConfig(new WhitespacesFixerConfig("\t", "\r\n"));

        $this->doTest($expected, $input);
    }

    public function provideMessyWhitespacesCases()
    {
        return [
            [
                "<?php echo(1) // test\r\n;",
            ],
        ];
    }
}
