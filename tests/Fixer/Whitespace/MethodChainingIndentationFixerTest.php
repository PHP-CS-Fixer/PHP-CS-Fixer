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

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;
use PhpCsFixer\WhitespacesFixerConfig;

/**
 * @author Vladimir Boliev <voff.web@gmail.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\Whitespace\MethodChainingIndentationFixer
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
        return [
            [
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
            ],
            [
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
    ->bar6()
                                /** buahaha */
    ->bar7();',
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
                                /** buahaha */    ->bar6()
                                /** buahaha */->bar7();',
            ],
            [
                '<?php
$foo
    ->bar1()
    ->bar2();',
                '<?php
$foo
->bar1()
->bar2();',
            ],
            [
                '<?php $foo
    ->bar();',
                '<?php $foo
->bar();',
            ],
            [
                '<?php $foo->bar()->baz()
    ->qux();',
                '<?php $foo->bar()->baz()
->qux();',
            ],
            [
                '<?php
someCodeHereAndMultipleBreaks();



$foo
    ->bar1()
    ->bar2();',
            ],
            [
                '<?php
        if (null !== $files) {
            return $files;
        }

        $finder = Finder::create()
            ->files()
        ;',
            ],
            [
                '<?php
        $finder = Finder::create()
            ->files()
        ;',
            ],
            [
                '<?php
        $replacements = $replacements
            ->setAllowedTypes([\'array\'])
            ->setNormalizer(function (Options $options, $value) use ($toTypes, $default) {
                return $normalizedValue;
            })
            ->setDefault($default)
            ->setWhitespacesConfig(
                new WhitespacesFixerConfig($config[\'indent\'], $config[\'lineEnding\'])
            )
            ;',
            ],
            [
                '<?php
        return foo()
            ->bar (
                new foo()
            )
            ->bar();
            ',
            ],
            [
                '<?php
        return new Foo("param", [
            (new Bar("param1", "param2"))
                ->Foo([
                    (new Bar())->foo(),
                ])
            ]);
                ',
            ],
            [
                '<?php
(new Foo(
    \'argument on line 1\',
    \'argument on line 2\'
))
    ->foo()
    ->bar()
;',
                '<?php
(new Foo(
    \'argument on line 1\',
    \'argument on line 2\'
))
  ->foo()
->bar()
;',
            ],
            [
                '<div>
    <?php $object
        ->method()
        ->method();
    ?>
</div>

<?= $object
    ->method()
    ->method();
?>',
                '<div>
    <?php $object
        ->method()
    ->method();
    ?>
</div>

<?= $object
    ->method()
        ->method();
?>',
            ],
            [
                '<?php

    $user->setFoo(1)
        ->setBar([
                1 => 1,
                ])
        ->setBaz(true)
        ->setX(array(
    2 => 2,
))
        ->setY();
',
                '<?php

    $user->setFoo(1)
            ->setBar([
                1 => 1,
                ])
  ->setBaz(true)
->setX(array(
    2 => 2,
))
                    ->setY();
',
            ],
        ];
    }

    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideWindowsWhitespacesCases
     */
    public function testWindowsWhitespaces($expected, $input = null)
    {
        $this->fixer->setWhitespacesConfig(new WhitespacesFixerConfig("\t", "\r\n"));
        $this->doTest($expected, $input);
    }

    public function provideWindowsWhitespacesCases()
    {
        return [
            [
                "<?php\r\n\$user->setEmail('voff.web@gmail.com')\r\n\t->setPassword('233434')\r\n\t->setEmailConfirmed(false)\r\n\t->setEmailConfirmationCode('123456')\r\n\t->setHashsalt('1234')\r\n\t->setTncAccepted(true);",
                "<?php\r\n\$user->setEmail('voff.web@gmail.com')\r\n\r\n     ->setPassword('233434')\r\n\t\t\t->setEmailConfirmed(false)\r\n\t\t      ->setEmailConfirmationCode('123456')\r\n->setHashsalt('1234')\r\n\t\t->setTncAccepted(true);",
            ],
        ];
    }
}
