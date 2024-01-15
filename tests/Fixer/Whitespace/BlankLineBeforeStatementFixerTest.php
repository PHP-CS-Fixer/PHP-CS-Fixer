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

use PhpCsFixer\ConfigurationException\InvalidFixerConfigurationException;
use PhpCsFixer\Tests\Test\AbstractFixerTestCase;
use PhpCsFixer\WhitespacesFixerConfig;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 * @author Andreas Möller <am@localheinz.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\Whitespace\BlankLineBeforeStatementFixer
 */
final class BlankLineBeforeStatementFixerTest extends AbstractFixerTestCase
{
    /**
     * @dataProvider provideConfigureRejectsInvalidControlStatementCases
     *
     * @param mixed $controlStatement
     */
    public function testConfigureRejectsInvalidControlStatement($controlStatement): void
    {
        $this->expectException(InvalidFixerConfigurationException::class);

        $this->fixer->configure([
            'statements' => [$controlStatement],
        ]);
    }

    public static function provideConfigureRejectsInvalidControlStatementCases(): iterable
    {
        yield 'null' => [null];

        yield 'false' => [false];

        yield 'true' => [true];

        yield 'int' => [0];

        yield 'float' => [3.14];

        yield 'array' => [[]];

        yield 'object' => [new \stdClass()];

        yield 'unknown' => ['foo'];
    }

    /**
     * @dataProvider provideFixWithReturnCases
     */
    public function testFixWithDefaultConfiguration(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    /**
     * @dataProvider provideFixWithBreakCases
     */
    public function testFixWithBreak(string $expected, ?string $input = null): void
    {
        $this->fixer->configure([
            'statements' => ['break'],
        ]);

        $this->doTest($expected, $input);
    }

    public static function provideFixWithBreakCases(): iterable
    {
        yield [
            <<<'EOD'
                <?php
                switch ($a) {
                    case 42:
                        break;
                }
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                switch ($a) {
                    case 42:
                        $foo = $bar;

                        break;
                }
                EOD,
            <<<'EOD'
                <?php
                switch ($a) {
                    case 42:
                        $foo = $bar;
                        break;
                }
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                while (true) {
                    if ($foo === $bar) {
                        break;
                    }
                }
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                while (true) {
                    if ($foo === $bar) {
                        break 1;
                    }
                }
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                while (true) {
                    if ($foo === $bar) {
                        echo $baz;

                        break;
                    }
                }
                EOD,
            <<<'EOD'
                <?php
                while (true) {
                    if ($foo === $bar) {
                        echo $baz;
                        break;
                    }
                }
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                while (true) {
                    if ($foo === $bar) {
                        echo $baz;

                        break 1;
                    }
                }
                EOD,
            <<<'EOD'
                <?php
                while (true) {
                    if ($foo === $bar) {
                        echo $baz;
                        break 1;
                    }
                }
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                while (true) {
                    if ($foo === $bar) {
                        /** X */
                        break 1;
                    }
                }
                EOD,
        ];
    }

    /**
     * @dataProvider provideFixWithCaseCases
     */
    public function testFixWithCase(string $expected, ?string $input = null): void
    {
        $this->fixer->configure([
            'statements' => ['case'],
        ]);

        $this->doTest($expected, $input);
    }

    public static function provideFixWithCaseCases(): iterable
    {
        yield [
            <<<'EOD'
                <?php
                switch ($a) {
                    case 1:
                        return 1;

                    case 2;
                        return 2;

                    case 3:
                        return 3;
                }
                EOD,
            <<<'EOD'
                <?php
                switch ($a) {
                    case 1:
                        return 1;
                    case 2;
                        return 2;
                    case 3:
                        return 3;
                }
                EOD,
        ];
    }

    /**
     * @dataProvider provideFixWithContinueCases
     */
    public function testFixWithContinue(string $expected, ?string $input = null): void
    {
        $this->fixer->configure([
            'statements' => ['continue'],
        ]);

        $this->doTest($expected, $input);
    }

    public static function provideFixWithContinueCases(): iterable
    {
        yield [
            <<<'EOD'
                <?php
                while (true) {
                    continue;
                }
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                while (true) {
                    continue 1;
                }
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                while (true) {
                    while (true) {
                        continue 2;
                    }
                }
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                while (true) {
                    $foo = true;

                    continue;
                }
                EOD,
            <<<'EOD'
                <?php
                while (true) {
                    $foo = true;
                    continue;
                }
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                while (true) {
                    $foo = true;

                    continue 1;
                }
                EOD,
            <<<'EOD'
                <?php
                while (true) {
                    $foo = true;
                    continue 1;
                }
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                while (true) {
                    while (true) {
                        switch($a) {
                            case 1:
                                echo 1;

                                continue;
                        }
                        $foo = true;

                        continue 2;
                    }
                }
                EOD,
            <<<'EOD'
                <?php
                while (true) {
                    while (true) {
                        switch($a) {
                            case 1:
                                echo 1;
                                continue;
                        }
                        $foo = true;
                        continue 2;
                    }
                }
                EOD,
        ];
    }

    /**
     * @dataProvider provideFixWithDeclareCases
     */
    public function testFixWithDeclare(string $expected, ?string $input = null): void
    {
        $this->fixer->configure([
            'statements' => ['declare'],
        ]);

        $this->doTest($expected, $input);
    }

    public static function provideFixWithDeclareCases(): iterable
    {
        yield [
            <<<'EOD'
                <?php
                declare(ticks=1);
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                $foo = "bar";
                do {
                } while (true);
                $foo = "bar";

                declare(ticks=1);
                EOD,
            <<<'EOD'
                <?php
                $foo = "bar";
                do {
                } while (true);
                $foo = "bar";
                declare(ticks=1);
                EOD,
        ];
    }

    /**
     * @dataProvider provideFixWithDefaultCases
     */
    public function testFixWithDefault(string $expected, ?string $input = null): void
    {
        $this->fixer->configure([
            'statements' => ['default'],
        ]);

        $this->doTest($expected, $input);
    }

    public static function provideFixWithDefaultCases(): iterable
    {
        yield [
            <<<'EOD'
                <?php
                switch ($a) {
                    case 1:
                        return 1;

                    default:
                        return 2;
                }

                switch ($a1) {
                    default:
                        return 22;
                }
                EOD,
            <<<'EOD'
                <?php
                switch ($a) {
                    case 1:
                        return 1;
                    default:
                        return 2;
                }

                switch ($a1) {
                    default:
                        return 22;
                }
                EOD,
        ];
    }

    /**
     * @dataProvider provideFixWithDoCases
     */
    public function testFixWithDo(string $expected, ?string $input = null): void
    {
        $this->fixer->configure([
            'statements' => ['do'],
        ]);

        $this->doTest($expected, $input);
    }

    public static function provideFixWithDoCases(): iterable
    {
        yield [
            <<<'EOD'
                <?php
                do {
                } while (true);
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                $foo = "bar";

                do {
                } while (true);
                EOD,
            <<<'EOD'
                <?php
                $foo = "bar";
                do {
                } while (true);
                EOD,
        ];
    }

    /**
     * @dataProvider provideFixWithExitCases
     */
    public function testFixWithExit(string $expected, ?string $input = null): void
    {
        $this->fixer->configure([
            'statements' => ['exit'],
        ]);

        $this->doTest($expected, $input);
    }

    public static function provideFixWithExitCases(): iterable
    {
        yield [
            <<<'EOD'
                <?php
                if ($foo === $bar) {
                    exit();
                }
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                if ($foo === $bar) {
                    echo $baz;

                    exit();
                }
                EOD,
            <<<'EOD'
                <?php
                if ($foo === $bar) {
                    echo $baz;
                    exit();
                }
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                if ($foo === $bar) {
                    echo $baz;

                    exit();
                }
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                mysqli_connect() or exit();
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                if ($foo === $bar) {
                    $bar = 9001;
                    mysqli_connect() or exit();
                }
                EOD,
        ];
    }

    /**
     * @dataProvider provideFixWithForCases
     */
    public function testFixWithFor(string $expected, ?string $input = null): void
    {
        $this->fixer->configure([
            'statements' => ['for'],
        ]);

        $this->doTest($expected, $input);
    }

    public static function provideFixWithForCases(): iterable
    {
        yield [
            <<<'EOD'
                <?php
                                    echo 1;

                                    for(;;){break;}
                EOD."\n                ",
            <<<'EOD'
                <?php
                                    echo 1;
                                    for(;;){break;}
                EOD."\n                ",
        ];
    }

    /**
     * @dataProvider provideFixWithGotoCases
     */
    public function testFixWithGoto(string $expected, ?string $input = null): void
    {
        $this->fixer->configure([
            'statements' => ['goto'],
        ]);

        $this->doTest($expected, $input);
    }

    public static function provideFixWithGotoCases(): iterable
    {
        yield [
            <<<'EOD'
                <?php
                a:

                if ($foo === $bar) {
                    goto a;
                }
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                a:

                if ($foo === $bar) {
                    echo $baz;

                    goto a;
                }
                EOD,
            <<<'EOD'
                <?php
                a:

                if ($foo === $bar) {
                    echo $baz;
                    goto a;
                }
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                a:

                if ($foo === $bar) {
                    echo $baz;

                    goto a;
                }
                EOD,
        ];
    }

    /**
     * @dataProvider provideFixWithIfCases
     */
    public function testFixWithIf(string $expected, ?string $input = null): void
    {
        $this->fixer->configure([
            'statements' => ['if'],
        ]);

        $this->doTest($expected, $input);
    }

    public static function provideFixWithIfCases(): iterable
    {
        yield [
            <<<'EOD'
                <?php if (true) {
                    echo $bar;
                }
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                if (true) {
                    echo $bar;
                }
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                $foo = $bar;

                if (true) {
                    echo $bar;
                }
                EOD,
            <<<'EOD'
                <?php
                $foo = $bar;
                if (true) {
                    echo $bar;
                }
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                // foo
                if ($foo) { }
                EOD,
        ];
    }

    /**
     * @dataProvider provideFixWithForEachCases
     */
    public function testFixWithForEach(string $expected, ?string $input = null): void
    {
        $this->fixer->configure([
            'statements' => ['foreach'],
        ]);

        $this->doTest($expected, $input);
    }

    public static function provideFixWithForEachCases(): iterable
    {
        yield [
            <<<'EOD'
                <?php
                                    echo 1;

                                    foreach($a as $b){break;}
                EOD."\n                ",
            <<<'EOD'
                <?php
                                    echo 1;
                                    foreach($a as $b){break;}
                EOD."\n                ",
        ];
    }

    /**
     * @dataProvider provideFixWithIncludeCases
     */
    public function testFixWithInclude(string $expected, ?string $input = null): void
    {
        $this->fixer->configure([
            'statements' => ['include'],
        ]);

        $this->doTest($expected, $input);
    }

    public static function provideFixWithIncludeCases(): iterable
    {
        yield [
            <<<'EOD'
                <?php
                include "foo.php";
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                $foo = $bar;

                include "foo.php";
                EOD,
            <<<'EOD'
                <?php
                $foo = $bar;
                include "foo.php";
                EOD,
        ];
    }

    /**
     * @dataProvider provideFixWithIncludeOnceCases
     */
    public function testFixWithIncludeOnce(string $expected, ?string $input = null): void
    {
        $this->fixer->configure([
            'statements' => ['include_once'],
        ]);

        $this->doTest($expected, $input);
    }

    public static function provideFixWithIncludeOnceCases(): iterable
    {
        yield [
            <<<'EOD'
                <?php
                include_once "foo.php";
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                $foo = $bar;

                include_once "foo.php";
                EOD,
            <<<'EOD'
                <?php
                $foo = $bar;
                include_once "foo.php";
                EOD,
        ];
    }

    /**
     * @dataProvider provideFixWithRequireCases
     */
    public function testFixWithRequire(string $expected, ?string $input = null): void
    {
        $this->fixer->configure([
            'statements' => ['require'],
        ]);

        $this->doTest($expected, $input);
    }

    public static function provideFixWithRequireCases(): iterable
    {
        yield [
            <<<'EOD'
                <?php
                require "foo.php";
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                $foo = $bar;

                require "foo.php";
                EOD,
            <<<'EOD'
                <?php
                $foo = $bar;
                require "foo.php";
                EOD,
        ];
    }

    /**
     * @dataProvider provideFixWithRequireOnceCases
     */
    public function testFixWithRequireOnce(string $expected, ?string $input = null): void
    {
        $this->fixer->configure([
            'statements' => ['require_once'],
        ]);

        $this->doTest($expected, $input);
    }

    public static function provideFixWithRequireOnceCases(): iterable
    {
        yield [
            <<<'EOD'
                <?php
                require_once "foo.php";
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                $foo = $bar;

                require_once "foo.php";
                EOD,
            <<<'EOD'
                <?php
                $foo = $bar;
                require_once "foo.php";
                EOD,
        ];
    }

    /**
     * @dataProvider provideFixWithReturnCases
     */
    public function testFixWithReturn(string $expected, ?string $input = null): void
    {
        $this->fixer->configure([
            'statements' => ['return'],
        ]);

        $this->doTest($expected, $input);
    }

    public static function provideFixWithReturnCases(): iterable
    {
        yield [
            <<<'EOD'
                <?php
                if ($a) { /* 1 */ /* 2 */ /* 3 */ // something about $a
                    return $b;
                }

                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                if ($a) { // something about $a
                    return $b;
                }

                EOD,
        ];

        yield [
            <<<'EOD'

                $a = $a;
                return $a;
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                $a = $a;

                return $a;
                EOD,
            <<<'EOD'
                <?php
                $a = $a; return $a;
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                $b = $b;

                return $b;
                EOD,
            <<<'EOD'
                <?php
                $b = $b;return $b;
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                $c = $c;

                return $c;
                EOD,
            <<<'EOD'
                <?php
                $c = $c;
                return $c;
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                $d = $d;

                return $d;
                EOD,
            <<<'EOD'
                <?php
                $d = $d;
                return $d;
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                if (true) {
                    return 1;
                }
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                if (true)
                    return 1;
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                if (true) {
                    return 1;
                } else {
                    return 2;
                }
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                if (true)
                    return 1;
                else
                    return 2;
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                if (true) {
                    return 1;
                } elseif (false) {
                    return 2;
                }
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                if (true)
                    return 1;
                elseif (false)
                    return 2;
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                throw new Exception("return true; //.");
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                function foo()
                {
                    // comment
                    return "foo";
                }
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                function foo()
                {
                    // comment

                    return "bar";
                }
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php

                function foo()
                {
                    switch ($foo) {
                        case 2: // comment
                            return 1;
                    }
                }
                EOD,
        ];

        yield 'do not fix when there is empty line between statement and preceding comment' => [
            <<<'EOD'
                <?php function foo()
                                {
                                    bar();
                                    // comment

                                    return 42;
                                }
                EOD,
        ];

        yield 'do not fix when there is empty line between preceding comments' => [
            <<<'EOD'
                <?php function foo()
                                {
                                    bar();
                                    // comment1
                                    // comment2

                                    // comment3
                                    return 42;
                                }
                EOD,
        ];
    }

    /**
     * @dataProvider provideFixWithReturnAndMessyWhitespacesCases
     */
    public function testFixWithReturnAndMessyWhitespaces(string $expected, ?string $input = null): void
    {
        $this->fixer->setWhitespacesConfig(new WhitespacesFixerConfig("\t", "\r\n"));

        $this->doTest($expected, $input);
    }

    public static function provideFixWithReturnAndMessyWhitespacesCases(): iterable
    {
        yield [
            "<?php\r\n\$a = \$a;\r\n\r\nreturn \$a;",
            "<?php\r\n\$a = \$a; return \$a;",
        ];

        yield [
            "<?php\r\n\$b = \$b;\r\n\r\nreturn \$b;",
            "<?php\r\n\$b = \$b;return \$b;",
        ];

        yield [
            "<?php\r\n\$c = \$c;\r\n\r\nreturn \$c;",
            "<?php\r\n\$c = \$c;\r\nreturn \$c;",
        ];
    }

    /**
     * @dataProvider provideFixWithSwitchCases
     */
    public function testFixWithSwitch(string $expected, ?string $input = null): void
    {
        $this->fixer->configure([
            'statements' => ['switch'],
        ]);

        $this->doTest($expected, $input);
    }

    public static function provideFixWithSwitchCases(): iterable
    {
        yield [
            <<<'EOD'
                <?php
                switch ($a) {
                    case 42:
                        break;
                }
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                $foo = $bar;

                switch ($foo) {
                    case $bar:
                        break;
                }
                EOD,
            <<<'EOD'
                <?php
                $foo = $bar;
                switch ($foo) {
                    case $bar:
                        break;
                }
                EOD,
        ];
    }

    /**
     * @dataProvider provideFixWithThrowCases
     */
    public function testFixWithThrow(string $expected, ?string $input = null): void
    {
        $this->fixer->configure([
            'statements' => ['throw'],
        ]);

        $this->doTest($expected, $input);
    }

    public static function provideFixWithThrowCases(): iterable
    {
        yield [
            <<<'EOD'
                <?php
                if (false) {
                    throw new \Exception("Something unexpected happened.");
                }
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                if (false) {
                    $log->error("No");

                    throw new \Exception("Something unexpected happened.");
                }
                EOD,
            <<<'EOD'
                <?php
                if (false) {
                    $log->error("No");
                    throw new \Exception("Something unexpected happened.");
                }
                EOD,
        ];
    }

    /**
     * @dataProvider provideFixWithTryCases
     */
    public function testFixWithTry(string $expected, ?string $input = null): void
    {
        $this->fixer->configure([
            'statements' => ['try'],
        ]);

        $this->doTest($expected, $input);
    }

    public static function provideFixWithTryCases(): iterable
    {
        yield [
            <<<'EOD'
                <?php
                try {
                    $transaction->commit();
                } catch (\Exception $exception) {
                    $transaction->rollback();
                }
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                $foo = $bar;

                try {
                    $transaction->commit();
                } catch (\Exception $exception) {
                    $transaction->rollback();
                }
                EOD,
            <<<'EOD'
                <?php
                $foo = $bar;
                try {
                    $transaction->commit();
                } catch (\Exception $exception) {
                    $transaction->rollback();
                }
                EOD,
        ];
    }

    /**
     * @dataProvider provideFixWithWhileCases
     */
    public function testFixWithWhile(string $expected, ?string $input = null): void
    {
        $this->fixer->configure([
            'statements' => ['while'],
        ]);

        $this->doTest($expected, $input);
    }

    public static function provideFixWithWhileCases(): iterable
    {
        yield [
            <<<'EOD'
                <?php
                while (true) {
                    $worker->work();
                }
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                $foo = $bar;

                while (true) {
                    $worker->work();
                }
                EOD,
            <<<'EOD'
                <?php
                $foo = $bar;
                while (true) {
                    $worker->work();
                }
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                $foo = $bar;

                do {
                    echo 1;

                    while($a());
                    $worker->work();
                } while (true);
                EOD,
            <<<'EOD'
                <?php
                $foo = $bar;

                do {
                    echo 1;
                    while($a());
                    $worker->work();
                } while (true);
                EOD,
        ];
    }

    /**
     * @dataProvider provideFixWithYieldCases
     */
    public function testFixWithYield(string $expected, ?string $input = null): void
    {
        $this->fixer->configure([
            'statements' => ['yield'],
        ]);

        $this->doTest($expected, $input);
    }

    /**
     * @yield array
     */
    public static function provideFixWithYieldCases(): iterable
    {
        yield [
            <<<'EOD'
                <?php
                function foo() {
                yield $a; /* a *//* b */     /* c */       /* d *//* e *//* etc */
                EOD."\n   ".<<<'EOD'

                yield $b;
                }
                EOD,
            <<<'EOD'
                <?php
                function foo() {
                yield $a; /* a *//* b */     /* c */       /* d *//* e *//* etc */
                EOD.'   '.<<<'EOD'

                yield $b;
                }
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                function foo() {
                    yield $a; // test

                    yield $b; // test /* A */

                    yield $c;

                    yield $d;

                yield $e;#

                yield $f;

                    /* @var int $g */
                    yield $g;

                /* @var int $h */
                yield $i;

                yield $j;
                }
                EOD,
            <<<'EOD'
                <?php
                function foo() {
                    yield $a; // test
                    yield $b; // test /* A */
                    yield $c;
                    yield $d;yield $e;#
                yield $f;
                    /* @var int $g */
                    yield $g;
                /* @var int $h */
                yield $i;
                yield $j;
                }
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                function foo() {
                    yield $a;
                }
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                function foo() {
                    yield $a;

                    yield $b;
                }
                EOD,
            <<<'EOD'
                <?php
                function foo() {
                    yield $a;
                    yield $b;
                }
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                function foo() {
                    yield 'b' => $a;

                    yield "a" => $b;
                }
                EOD,
            <<<'EOD'
                <?php
                function foo() {
                    yield 'b' => $a;
                    yield "a" => $b;
                }
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                function foo() {
                    $a = $a;

                    yield $a;
                }
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                function foo() {
                    $a = $a;

                    yield $a;
                }
                EOD,
            <<<'EOD'
                <?php
                function foo() {
                    $a = $a;
                    yield $a;
                }
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php function foo() {
                                    // yield 1
                                    yield 1;

                                    // yield 2
                                    yield 2;
                                }
                EOD,
            <<<'EOD'
                <?php function foo() {
                                    // yield 1
                                    yield 1;
                                    // yield 2
                                    yield 2;
                                }
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php function foo() {
                                    yield 1;

                                    // yield 2
                                    // or maybe yield 3
                                    // better compromise
                                    yield 2.5;
                                }
                EOD,
            <<<'EOD'
                <?php function foo() {
                                    yield 1;
                                    // yield 2
                                    // or maybe yield 3
                                    // better compromise
                                    yield 2.5;
                                }
                EOD,
        ];
    }

    /**
     * @dataProvider provideFixWithYieldFromCases
     */
    public function testFixWithYieldFrom(string $expected, ?string $input = null): void
    {
        $this->fixer->configure([
            'statements' => ['yield_from'],
        ]);

        $this->doTest($expected, $input);
    }

    /**
     * @yield array
     */
    public static function provideFixWithYieldFromCases(): iterable
    {
        yield [
            <<<'EOD'
                <?php
                function foo() {
                    yield from $a;
                }
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                function foo() {
                    yield from $a;

                    yield from $b;
                }
                EOD,
            <<<'EOD'
                <?php
                function foo() {
                    yield from $a;
                    yield from $b;
                }
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                function foo() {
                    $a = $a;

                    yield from $a;

                    yield $a;
                    yield $b;
                }
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                function foo() {
                    $a = $a;

                    yield from $a;
                }
                EOD,
            <<<'EOD'
                <?php
                function foo() {
                    $a = $a;
                    yield from $a;
                }
                EOD,
        ];
    }

    /**
     * @dataProvider provideFixWithMultipleConfigStatementsCases
     *
     * @param string[] $statements
     */
    public function testFixWithMultipleConfigStatements(array $statements, string $expected, ?string $input = null): void
    {
        $this->fixer->configure(['statements' => $statements]);
        $this->doTest($expected, $input);
    }

    public static function provideFixWithMultipleConfigStatementsCases(): iterable
    {
        $statementsWithoutCaseOrDefault = [
            'break',
            'continue',
            'declare',
            'do',
            'for',
            'foreach',
            'if',
            'include',
            'include_once',
            'require',
            'require_once',
            'return',
            'switch',
            'throw',
            'try',
            'while',
        ];

        $allStatements = [...$statementsWithoutCaseOrDefault, 'case', 'default'];

        yield [
            $statementsWithoutCaseOrDefault,
            <<<'EOD'
                <?php
                                    while($a) {
                                        if ($c) {
                                            switch ($d) {
                                                case $e:
                                                    continue 2;
                                                case 4:
                                                    break;
                                                case 5:
                                                    return 1;
                                                default:
                                                    return 0;
                                            }
                                        }
                                    }
                EOD."\n                ",
        ];

        yield [
            $allStatements,
            <<<'EOD'
                <?php
                                    while($a) {
                                        if ($c) {
                                            switch ($d) {
                                                case $e:
                                                    continue 2;

                                                case 4:
                                                    break;

                                                case 5:
                                                    return 1;

                                                default:
                                                    return 0;
                                            }
                                        }
                                    }
                EOD."\n                ",
        ];

        yield [
            ['break', 'throw'],
            <<<'EOD'
                <?php
                do {
                    echo 0;
                    if ($a) {
                        echo 1;

                        break;
                    }
                    echo 2;

                    throw $f;
                } while(true);
                EOD,
            <<<'EOD'
                <?php
                do {
                    echo 0;
                    if ($a) {
                        echo 1;
                        break;
                    }
                    echo 2;
                    throw $f;
                } while(true);
                EOD,
        ];
    }

    /**
     * @dataProvider provideFix80Cases
     *
     * @requires PHP 8.0
     */
    public function testFix80(string $expected, ?string $input = null): void
    {
        $this->fixer->configure([
            'statements' => ['default'],
        ]);

        $this->doTest($expected, $input);
    }

    public static function provideFix80Cases(): iterable
    {
        yield 'match' => [
            <<<'EOD'
                <?php
                                match ($foo) {
                                    1 => "a",
                                    default => "b"
                                };

                                match ($foo) {
                                    1 => "a1",


                                    default => "b2"
                                };
                EOD."\n            ",
        ];
    }

    /**
     * @dataProvider provideFix81Cases
     *
     * @requires PHP 8.1
     */
    public function testFix81(string $expected, ?string $input = null): void
    {
        $this->fixer->configure([
            'statements' => ['case'],
        ]);

        $this->doTest($expected, $input);
    }

    public static function provideFix81Cases(): iterable
    {
        yield 'enum' => [
            <<<'EOD'
                <?php
                enum Suit {
                    case Hearts;
                    case Diamonds;
                    case Clubs;


                    case Spades;
                }

                enum UserStatus: string {
                    case Pending = "P";
                    case Active = "A";

                    public function label(): string {
                        switch ($a) {
                            case 1:
                                return 1;

                            case 2:
                                return 2; // do fix
                        }

                        return "label";
                    }
                }

                EOD,
            <<<'EOD'
                <?php
                enum Suit {
                    case Hearts;
                    case Diamonds;
                    case Clubs;


                    case Spades;
                }

                enum UserStatus: string {
                    case Pending = "P";
                    case Active = "A";

                    public function label(): string {
                        switch ($a) {
                            case 1:
                                return 1;
                            case 2:
                                return 2; // do fix
                        }

                        return "label";
                    }
                }

                EOD,
        ];
    }

    /**
     * @dataProvider provideFixWithDocCommentCases
     */
    public function testFixWithDocComment(string $expected, string $input = null): void
    {
        $this->fixer->configure([
            'statements' => ['phpdoc'],
        ]);

        $this->doTest($expected, $input);
    }

    public static function provideFixWithDocCommentCases(): iterable
    {
        yield [
            <<<'EOD'
                <?php
                /** @var int $foo */
                $foo = 123;

                /** @var float $bar */
                $bar = 45.6;

                /** @var string */
                $baz = "789";

                EOD,
            <<<'EOD'
                <?php
                /** @var int $foo */
                $foo = 123;
                /** @var float $bar */
                $bar = 45.6;
                /** @var string */
                $baz = "789";

                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                /* header */

                /**
                 * Class description
                 */
                class Foo {
                    /** test */
                    public function bar() {}
                }

                EOD,
        ];
    }
}
