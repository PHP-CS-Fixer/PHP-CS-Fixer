<?php

declare(strict_types=1);

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
     */
    public function testFix(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    public static function provideFixCases(): array
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
            [
                '<?php

    $user->setEmail("voff.web@gmail.com", )
        ->setPassword("233434" ,)
        ->setEmailConfirmed(false , )
        ->setEmailConfirmationCode("123456",    );
',
                '<?php

    $user->setEmail("voff.web@gmail.com", )

     ->setPassword("233434" ,)
        ->setEmailConfirmed(false , )
->setEmailConfirmationCode("123456",    );
',
            ],
            [
                '<?php

                $obj = (new Foo)
                    ->setBar((new Bar)
                        ->baz());
',
                '<?php

                $obj = (new Foo)
        ->setBar((new Bar)
                            ->baz());
',
            ],
            [
                '<?php

                $obj
                    ->foo("bar", function ($baz) {
                                    return $baz
                                        ->on("table1", "table2");
                                })
                    ->where("a", "b");
',
                '<?php

                $obj
        ->foo("bar", function ($baz) {
                        return $baz
                                    ->on("table1", "table2");
                    })
                ->where("a", "b");
',
            ],
            [
                '<?php

                $obj
                    ->foo("baz", fn ($bar) => $bar
                        ->baz("foobar"))
                    ->baz();
',
                '<?php

                $obj
                                        ->foo("baz", fn ($bar) => $bar
        ->baz("foobar"))
                                ->baz();
',
            ],
            [
                '<?php

                $obj
                    ->foo("baz", fn (string $bar) => otherFunc($bar)
                        ->baz("foobar"))
                    ->baz();
',
                '<?php

                $obj
                                        ->foo("baz", fn (string $bar) => otherFunc($bar)
                            ->baz("foobar"))
                                ->baz();
',
            ],
            [
                '<?php

                $obj
                    ->foo("baz", fn (SomeClass $bar) => $bar
                        ->baz("foobar"))
                    ->baz();
',
                '<?php

                $obj
                                        ->foo("baz", fn (SomeClass $bar) => $bar
        ->baz("foobar"))
                                ->baz();
',
            ],
            [
                '<?php

                $obj
                    ->foo("baz", fn (?AnotherClass $bar) => $bar
                        ->baz("foobar"))
                    ->baz();
',
                '<?php

                $obj
                                        ->foo("baz", fn (?AnotherClass $bar) => $bar
        ->baz("foobar"))
                                ->baz();
',
            ],
            [
                '<?php

                $obj
        /*buahaha*/
                    ->foo("baz", fn ($bar) => $bar
                        ->baz/*buahaha*/("foobar"))
                    ->/**buahaha*/baz();
',
                '<?php

                $obj
        /*buahaha*/                                ->foo("baz", fn ($bar) => $bar
        ->baz/*buahaha*/("foobar"))
                                ->/**buahaha*/baz();
',
            ],
            [
                '<?php

                $obj
                    ->      foo("baz", fn ($bar) => $bar
                        ->baz              ("foobar"))
                    ->       baz  ();
',
                '<?php

                $obj
                                        ->      foo("baz", fn ($bar) => $bar
        ->baz              ("foobar"))
                                ->       baz  ();
',
            ],
            [
                '<?php

    $user->setEmail("voff.web@gmail.com", )
        ->setPassword("233434" ,)
        ->setEmailConfirmed(false , )
        ->setEmailConfirmationCode("123456",    );
',
                '<?php

    $user->setEmail("voff.web@gmail.com", )

     ->setPassword("233434" ,)
        ->setEmailConfirmed(false , )
->setEmailConfirmationCode("123456",    );
',
            ],
            [
                '<?php return $foo
->bar;',
            ],
            [
                '<?php return $foo
->bar;

    if (foo()) {
        echo 123;
    }
',
            ],
            [
                '<?php return $foo
->bar?>

<?php
if (foo()) {
    echo 123;
}
',
            ],
            [
                '<?php return [$foo
->bar,
1,
2,
abc(),
];
',
            ],
        ];
    }

    /**
     * @dataProvider provideWindowsWhitespacesCases
     */
    public function testWindowsWhitespaces(string $expected, ?string $input = null): void
    {
        $this->fixer->setWhitespacesConfig(new WhitespacesFixerConfig("\t", "\r\n"));
        $this->doTest($expected, $input);
    }

    public static function provideWindowsWhitespacesCases(): array
    {
        return [
            [
                "<?php\r\n\$user->setEmail('voff.web@gmail.com')\r\n\t->setPassword('233434')\r\n\t->setEmailConfirmed(false)\r\n\t->setEmailConfirmationCode('123456')\r\n\t->setHashsalt('1234')\r\n\t->setTncAccepted(true);",
                "<?php\r\n\$user->setEmail('voff.web@gmail.com')\r\n\r\n     ->setPassword('233434')\r\n\t\t\t->setEmailConfirmed(false)\r\n\t\t      ->setEmailConfirmationCode('123456')\r\n->setHashsalt('1234')\r\n\t\t->setTncAccepted(true);",
            ],
        ];
    }

    /**
     * @requires PHP 8.0
     */
    public function testFix80(): void
    {
        $this->doTest(
            '<?php

    $user?->setEmail("voff.web@gmail.com")
        ?->setPassword("233434")
        ?->setEmailConfirmed(false)
        ?->setEmailConfirmationCode("123456");
',
            '<?php

    $user?->setEmail("voff.web@gmail.com")

     ?->setPassword("233434")
        ?->setEmailConfirmed(false)
?->setEmailConfirmationCode("123456");
'
        );
    }
}
