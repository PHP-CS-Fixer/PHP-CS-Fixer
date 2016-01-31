<?php

/*
 * This file is part of the PHP CS Fixer.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *     Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Tests\Fixer\Symfony;

use Symfony\CS\Tests\Fixer\AbstractFixerTestBase;

/**
 * @author John Kelly <wablam@gmail.com>
 * @author Graham Campbell <graham@mineuk.com>
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
class SpacesBeforeSemicolonFixerTest extends AbstractFixerTestBase
{
    /**
     * @dataProvider provideCases
     */
    public function testFix($expected, $input = null)
    {
        $this->makeTest($expected, $input);
    }

    public function provideCases()
    {
        return array(
            array(
                '<?php for ($uu = 0; ; ++$uu) {}',
                '<?php for ($uu = 0    ;    ; ++$uu) {}',
            ),
            array(
                '<?php
$this
    ->setName(\'readme1\')
    ->setDescription(\'Generates the README content, based on the fix command help\')
;',
            ),
            array(
                '<?php
$this
    ->setName(\'readme2\')
    ->setDescription(\'Generates the README content, based on the fix command help\')
    ;',
            ),
            array(
                '<?php echo "$this->foo(\'with param containing ;\') ;";',
                '<?php echo "$this->foo(\'with param containing ;\') ;" ;',
            ),
            array(
                '<?php $this->foo();',
                '<?php $this->foo() ;',
            ),
            array(
                '<?php $this->foo(\'with param containing ;\');',
                '<?php $this->foo(\'with param containing ;\') ;',
            ),
            array(
                '<?php $this->foo(\'with param containing ) ; \');',
                '<?php $this->foo(\'with param containing ) ; \') ;',
            ),
            array(
                '<?php $this->foo("with param containing ) ; ");',
                '<?php $this->foo("with param containing ) ; ")  ;',
            ),
            array(
                '<?php
    $foo
        ->bar(1)
        ->baz(2)
    ;',
            ),
            array(
                '<?php
    $foo
        ->bar(1)
        //->baz(2)
    ;',
            ),
            array(
                '<?php $this->foo();',
            ),
            array(
                '<?php $this->foo("with semicolon in string) ; ");',
            ),
        );
    }
}
