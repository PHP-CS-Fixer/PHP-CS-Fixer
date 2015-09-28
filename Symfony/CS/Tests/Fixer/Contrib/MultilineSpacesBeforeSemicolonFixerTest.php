<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Tests\Fixer\Contrib;

use Symfony\CS\Tests\Fixer\AbstractFixerTestBase;

/**
 * @author John Kelly <wablam@gmail.com>
 * @author Graham Campbell <graham@mineuk.com>
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 */
final class MultilineSpacesBeforeSemicolonFixerTest extends AbstractFixerTestBase
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
}
