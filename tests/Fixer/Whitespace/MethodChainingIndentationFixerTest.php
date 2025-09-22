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
 * @internal
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 *
 * @extends AbstractFixerTestCase<\PhpCsFixer\Fixer\Whitespace\MethodChainingIndentationFixer>
 *
 * @covers \PhpCsFixer\Fixer\Whitespace\MethodChainingIndentationFixer
 *
 * @author Vladimir Boliev <voff.web@gmail.com>
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

    /**
     * @return iterable<int, array{0: string, 1?: string}>
     */
    public static function provideFixCases(): iterable
    {
        yield [
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
        ];

        yield [
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
        ];

        yield [
            '<?php
$foo
    ->bar1()
    ->bar2();',
            '<?php
$foo
->bar1()
->bar2();',
        ];

        yield [
            '<?php $foo
    ->bar();',
            '<?php $foo
->bar();',
        ];

        yield [
            '<?php $foo->bar()->baz()
    ->qux();',
            '<?php $foo->bar()->baz()
->qux();',
        ];

        yield [
            '<?php
someCodeHereAndMultipleBreaks();



$foo
    ->bar1()
    ->bar2();',
        ];

        yield [
            '<?php
        if (null !== $files) {
            return $files;
        }

        $finder = Finder::create()
            ->files()
        ;',
        ];

        yield [
            '<?php
        $finder = Finder::create()
            ->files()
        ;',
        ];

        yield [
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
        ];

        yield [
            '<?php
        return foo()
            ->bar (
                new foo()
            )
            ->bar();
            ',
        ];

        yield [
            '<?php
        return new Foo("param", [
            (new Bar("param1", "param2"))
                ->Foo([
                    (new Bar())->foo(),
                ])
            ]);
                ',
        ];

        yield [
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
        ];

        yield [
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
        ];

        yield [
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
        ];

        yield [
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
        ];

        yield [
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
        ];

        yield [
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
        ];

        yield [
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
        ];

        yield [
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
        ];

        yield [
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
        ];

        yield [
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
        ];

        yield [
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
        ];

        yield [
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
        ];

        yield [
            '<?php return $foo
->bar;',
        ];

        yield [
            '<?php return $foo
->bar;

    if (foo()) {
        echo 123;
    }
',
        ];

        yield [
            '<?php return $foo
->bar?>

<?php
if (foo()) {
    echo 123;
}
',
        ];

        yield [
            '<?php return [$foo
->bar,
1,
2,
abc(),
];
',
        ];

        yield [
            '<?php
$obj
    ->foo()
    ->bar;
',
            '<?php
$obj
    ->foo()
->bar;
',
        ];

        yield [
            '<?php
return $obj
    ->foo()
    ->bar
    ->baz();
',
            '<?php
return $obj
 ->foo()
    ->bar
  ->baz();
',
        ];

        yield [
            '<?php
foo()
    ->bar()
    ->baz;

        $obj
            ->foo(\'123\', 456)
            ->bar(\'789\')
            ->baz;
',
            '<?php
foo()
->bar()
->baz;

        $obj
    ->foo(\'123\', 456)
->bar(\'789\')
->baz;
',
        ];
    }

    /**
     * @dataProvider provideWithWhitespacesConfigCases
     */
    public function testWithWhitespacesConfig(string $expected, ?string $input = null): void
    {
        $this->fixer->setWhitespacesConfig(new WhitespacesFixerConfig("\t", "\r\n"));
        $this->doTest($expected, $input);
    }

    /**
     * @return iterable<int, array{string, string}>
     */
    public static function provideWithWhitespacesConfigCases(): iterable
    {
        yield [
            "<?php\r\n\$user->setEmail('voff.web@gmail.com')\r\n\t->setPassword('233434')\r\n\t->setEmailConfirmed(false)\r\n\t->setEmailConfirmationCode('123456')\r\n\t->setHashsalt('1234')\r\n\t->setTncAccepted(true);",
            "<?php\r\n\$user->setEmail('voff.web@gmail.com')\r\n\r\n     ->setPassword('233434')\r\n\t\t\t->setEmailConfirmed(false)\r\n\t\t      ->setEmailConfirmationCode('123456')\r\n->setHashsalt('1234')\r\n\t\t->setTncAccepted(true);",
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
