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

namespace PhpCsFixer\Tests\Fixer\Whitespace;

use PhpCsFixer\Test\AbstractFixerTestCase;
use PhpCsFixer\WhitespacesFixerConfig;

/**
 * @author Vladimir Boliev <voff.web@gmail.com>
 *
 * @internal
 */
final class MethodChainingIndentationFixerTest extends AbstractFixerTestCase
{
    /**
     * @dataProvider provideFixCases
     *
     * @param string      $expected
     * @param null|string $input
     */
    public function testFix($expected, $input = null)
    {
        $this->doTest($expected, $input);
    }

    public function provideFixCases()
    {
        return array(
            array(
                '<?php

    $user->setEmail(\'voff.web@gmail.com\')
        ->setPassword(\'233434\')
        ->setEmailConfirmed(false)
        ->setEmailConfirmationCode(\'123456\')
        ->setHashsalt(\'1234\')
        ->setTncAccepted(true);
',
                '<?php

    $user->setEmail(\'voff.web@gmail.com\')

     ->setPassword(\'233434\')
        ->setEmailConfirmed(false)
->setEmailConfirmationCode(\'123456\')

                ->setHashsalt(\'1234\')
  ->setTncAccepted(true);
',
            ),
            array(
                '<?php
$foo
    ->bar1() // comment
    ->bar2() /*
comment
*/
    ->bar3()
    // comment
    ->bar4()
    ->bar5()
                                /** buahaha */
    ->bar6();',
                '<?php
$foo
         ->bar1() // comment
      ->bar2() /*
comment
*/
  ->bar3()
    // comment
        ->bar4()
->bar5()
                                /** buahaha */    ->bar6();',
            ),
            array(
            '<?php
$foo
    ->bar1()
    ->bar2();',
            '<?php
$foo
->bar1()
->bar2();',
            ),
            array(
                '<?php $foo
    ->bar();',
                '<?php $foo
->bar();'
            )
        );
    }

    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideWindowsWhitespaces
     */
    public function testWindowsWhitespaces($expected, $input = null)
    {
        $this->fixer->setWhitespacesConfig(new WhitespacesFixerConfig("\t", "\r\n"));
        $this->doTest($expected, $input);
    }

    public function provideWindowsWhitespaces()
    {
        return array(
            array(
                "<?php\r\n\$user->setEmail('voff.web@gmail.com')\r\n\t->setPassword('233434')\r\n\t->setEmailConfirmed(false)\r\n\t->setEmailConfirmationCode('123456')\r\n\t->setHashsalt('1234')\r\n\t->setTncAccepted(true);",
                "<?php\r\n\$user->setEmail('voff.web@gmail.com')\r\n\r\n     ->setPassword('233434')\r\n\t\t\t->setEmailConfirmed(false)\r\n\t\t      ->setEmailConfirmationCode('123456')\r\n->setHashsalt('1234')\r\n\t\t->setTncAccepted(true);",
            ),
        );
    }
}
