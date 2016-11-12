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
 */
final class NoMultilineWhitespaceBeforeSemicolonsFixerTest extends AbstractFixerTestCase
{
    /**
     * @dataProvider provideCases
     */
    public function testFix($expected, $input = null)
    {
        $this->doTest($expected, $input);
    }

    public function provideCases()
    {
        return array(
            array(
                '<?php
                    $foo->bar() // test
;',
                '<?php
                    $foo->bar() // test
                    ;',
            ),
            array(
                "<?php echo(1) // test\n;",
            ),
            array(
                '<?php
                    $foo->bar() # test
;',
                '<?php
                    $foo->bar() # test


                ;',
            ),
            array(
                "<?php\n;",
            ),
            array(
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
            ),
            array(
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
            ),
            array(
                '<?php echo "$this->foo(\'with param containing ;\') ;" ;',
            ),
            array(
                '<?php $this->foo();',
            ),
            array(
                '<?php $this->foo() ;',
            ),
            array(
                '<?php $this->foo(\'with param containing ;\') ;',
            ),
            array(
                '<?php $this->foo(\'with param containing ) ; \') ;',
            ),
            array(
                '<?php $this->foo("with param containing ) ; ")  ; ?>',
            ),
            array(
                '<?php $this->foo("with semicolon in string) ; "); ?>',
            ),
            array(
                '<?php
$this
    ->example();',
                '<?php
$this
    ->example()

    ;',
            ),
        );
    }

    /**
     * @dataProvider provideMessyWhitespacesCases
     */
    public function testMessyWhitespaces($expected, $input = null)
    {
        $this->fixer->setWhitespacesConfig(new WhitespacesFixerConfig("\t", "\r\n"));

        $this->doTest($expected, $input);
    }

    public function provideMessyWhitespacesCases()
    {
        return array(
            array(
                "<?php echo(1) // test\r\n;",
            ),
        );
    }
}
