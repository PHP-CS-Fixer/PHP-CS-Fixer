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
use PhpCsFixer\WhitespacesFixerConfig;

/**
 * @internal
 */
final class DeclareStrictTypesFixerTest extends AbstractFixerTestCase
{
    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideFixCases
     * @requires PHP 7.0
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
declare(ticks=1);
//


namespace A\B\C;
class A {
}',
                '<?php
declare(ticks=1);
//
declare(strict_types=1);

namespace A\B\C;
class A {
}',
            ),
            array(
                '<?php DECLARE/* A b C*/(strict_types=1);
//abc',
                '<?php DECLARE/* A b C*/(strict_types=1);      //abc',
            ),
            array(
                '<?php declare/* A b C*/(strict_types=1);',
            ),
            array(
                '<?php declare(strict_types=1);
/**/ /**/       ?>Test',
                '<?php /**/ /**/ deClarE  (STRICT_TYPES=1)    ?>Test',
            ),
            array(
                '<?php declare(strict_types=1);
',
                '<?php            DECLARE  (    strict_types=1   )   ;',
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
     * @param string $input
     *
     * @dataProvider provideDoNotFixCases
     */
    public function testDoNotFix($input)
    {
        $this->doTest($input);
    }

    public function provideDoNotFixCases()
    {
        return array(
            array('  <?php echo 123;'), // first statement must be a open tag
            array('<?= 123;'), // first token open with echo is not fixed
        );
    }

    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideMessyWhitespacesCases
     * @requires PHP 7.0
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
                "<?php declare(strict_types=1);\r\nphpinfo();",
                "<?php\r\n\tphpinfo();",
            ),
            array(
                "<?php declare(strict_types=1);\r\nphpinfo();",
                "<?php\nphpinfo();",
            ),
        );
    }
}
