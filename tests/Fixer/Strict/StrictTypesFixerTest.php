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

use PhpCsFixer\Test\AbstractFixerTestCase;

/**
 * @internal
 */
final class StrictTypesFixerTest extends AbstractFixerTestCase
{
    /**
     * @dataProvider provideFixCases
     */
    public function testFix($expected, $input = null)
    {
        $this->doTest($expected, $input);
    }

    public function provideFixCases()
    {
        return array(
            array(
                '<?php declare(strict_types=1);
',
                '<?php            declare(strict_types=1);',
            ),
            array(
                '<?php declare(strict_types=1);
/**/
                ',
                '<?php
                /**/
                declare(strict_types=1);',
            ),
            array(
                '<?php declare(strict_types=1);
/**/ /**/ /**/
                 /* abc */ ',
                '<?php
                /**/ /**/ /**/
                declare /* abc */ (strict_types=1);',
            ),
            array(
                '<?php declare(strict_types=1);
                phpinfo();',
                '<?php

                phpinfo();',
            ),
            array(
                '<?php declare(strict_types=1);
/**
 * Foo
 */
phpinfo();',
                '<?php

/**
 * Foo
 */
phpinfo();',
            ),
            array(
                '<?php declare(strict_types=1);
/*
 * Foo
 */

phpinfo();',
                '<?php

/*
 * Foo
 */

phpinfo();',
            ),
            array(
                '<?php declare(strict_types=1);
phpinfo();',
                '<?php phpinfo();',
            ),
            array(
                '<?php declare(strict_types=1);
$a = 1;',
                '<?php declare(strict_types=1);  $a = 1;',
            ),
            array(
                '<?php /**/ /**/ declare(strict_types=1)?>Test',
                '<?php /**/ /**/ deClarE(STRICT_TYPES=1)?>Test',
            ),
            array(
                '<?php declare(strict_types=1);
$a = 456;
',
                '<?php
$a = 456;
',
            ),
            array(
                '<?php declare(strict_types=1);
/**/',
                '<?php /**/',
            ),
        );
    }

    /**
     * @dataProvider provideDoNotFixCases
     */
    public function testDoNotFix($input)
    {
        $this->doTest($input);
    }

    public function provideDoNotFixCases()
    {
        return array(
            array(''),                  // leave empty file empty
            array('  <?php echo 123;'),
            array('<?= 123;'),
        );
    }
}
