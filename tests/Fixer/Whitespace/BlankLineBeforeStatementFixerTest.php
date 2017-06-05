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

use PhpCsFixer\ConfigurationException\InvalidFixerConfigurationException;
use PhpCsFixer\Tests\Test\AbstractFixerTestCase;
use PhpCsFixer\WhitespacesFixerConfig;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 * @author Andreas Möller <am@localheinz.com>
 * @author SpacePossum
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\Whitespace\BlankLineBeforeStatementFixerTest
 */
final class BlankLineBeforeStatementFixerTest extends AbstractFixerTestCase
{
    /**
     * @dataProvider providerInvalidControlStatement
     *
     * @param mixed $controlStatement
     */
    public function testConfigureRejectsInvalidControlStatement($controlStatement)
    {
        $this->setExpectedException(InvalidFixerConfigurationException::class);

        $this->fixer->configure([
            'statements' => [$controlStatement],
        ]);
    }

    /**
     * @return array
     */
    public function providerInvalidControlStatement()
    {
        return [
            'null' => [null],
            'false' => [false],
            'true' => [true],
            'int' => [0],
            'float' => [3.14],
            'array' => [[]],
            'object' => [new \stdClass()],
            'unknown' => ['foo'],
        ];
    }

    /**
     * @dataProvider providerFixWithReturn
     *
     * @param string      $expected
     * @param null|string $input
     */
    public function testFixWithDefaultConfiguration($expected, $input = null)
    {
        $this->doTest($expected, $input);
    }

    /**
     * @dataProvider providerFixWithBreak
     *
     * @param string      $expected
     * @param null|string $input
     */
    public function testFixWithBreak($expected, $input = null)
    {
        $this->fixer->configure([
            'statements' => ['break'],
        ]);

        $this->doTest($expected, $input);
    }

    /**
     * @return array
     */
    public function providerFixWithBreak()
    {
        return [
            [
                '<?php
switch ($a) {
    case 42:
        break;
}',
            ],
            [
                '<?php
switch ($a) {
    case 42:
        $foo = $bar;

        break;
}',
                '<?php
switch ($a) {
    case 42:
        $foo = $bar;
        break;
}',
            ],
            [
                '<?php
while (true) {
    if ($foo === $bar) {
        break;
    }
}',
            ],
            [
                '<?php
while (true) {
    if ($foo === $bar) {
        break 1;
    }
}',
            ],
            [
                '<?php
while (true) {
    if ($foo === $bar) {
        echo $baz;

        break;
    }
}',
                '<?php
while (true) {
    if ($foo === $bar) {
        echo $baz;
        break;
    }
}',
            ],
            [
                '<?php
while (true) {
    if ($foo === $bar) {
        echo $baz;

        break 1;
    }
}',
                '<?php
while (true) {
    if ($foo === $bar) {
        echo $baz;
        break 1;
    }
}',
            ],
        ];
    }

    /**
     * @dataProvider providerFixWithContinue
     *
     * @param string      $expected
     * @param null|string $input
     */
    public function testFixWithContinue($expected, $input = null)
    {
        $this->fixer->configure([
            'statements' => ['continue'],
        ]);

        $this->doTest($expected, $input);
    }

    /**
     * @return array
     */
    public function providerFixWithContinue()
    {
        return [
            [
                '<?php
while (true) {
    continue;
}',
            ],
            [
                '<?php
while (true) {
    continue 1;
}',
            ],
            [
                '<?php
while (true) {
    while (true) {
        continue 2;
    }
}',
            ],
            [
                '<?php
while (true) {
    $foo = true;

    continue;
}',
                '<?php
while (true) {
    $foo = true;
    continue;
}',
            ],
            [
                '<?php
while (true) {
    $foo = true;

    continue 1;
}',
                '<?php
while (true) {
    $foo = true;
    continue 1;
}',
            ],
            [
                '<?php
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
}',
                '<?php
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
}',
            ],
        ];
    }

    /**
     * @dataProvider providerFixWithDeclare
     *
     * @param string      $expected
     * @param null|string $input
     */
    public function testFixWithDeclare($expected, $input = null)
    {
        $this->fixer->configure([
            'statements' => ['declare'],
        ]);

        $this->doTest($expected, $input);
    }

    /**
     * @return array
     */
    public function providerFixWithDeclare()
    {
        return [
            [
                '<?php
declare(ticks=1);',
            ],
            [
                '<?php
$foo = "bar";
do {
} while (true);
$foo = "bar";

declare(ticks=1);',
                '<?php
$foo = "bar";
do {
} while (true);
$foo = "bar";
declare(ticks=1);',
            ],
        ];
    }

    /**
     * @dataProvider providerFixWithDo
     *
     * @param string      $expected
     * @param null|string $input
     */
    public function testFixWithDo($expected, $input = null)
    {
        $this->fixer->configure([
            'statements' => ['do'],
        ]);

        $this->doTest($expected, $input);
    }

    /**
     * @return array
     */
    public function providerFixWithDo()
    {
        return [
            [
                '<?php
do {
} while (true);',
            ],
            [
                '<?php
$foo = "bar";

do {
} while (true);',
                '<?php
$foo = "bar";
do {
} while (true);',
            ],
        ];
    }

    /**
     * @dataProvider providerFixWithFor
     *
     * @param string      $expected
     * @param null|string $input
     */
    public function testFixWithFor($expected, $input = null)
    {
        $this->fixer->configure([
            'statements' => ['for'],
        ]);

        $this->doTest($expected, $input);
    }

    public function providerFixWithFor()
    {
        return [
            [
                '<?php
                    echo 1;

                    for(;;){break;}
                ',
                '<?php
                    echo 1;
                    for(;;){break;}
                ',
            ],
        ];
    }

    /**
     * @dataProvider providerFixWithIf
     *
     * @param string      $expected
     * @param null|string $input
     */
    public function testFixWithIf($expected, $input = null)
    {
        $this->fixer->configure([
            'statements' => ['if'],
        ]);

        $this->doTest($expected, $input);
    }

    /**
     * @dataProvider providerFixWithForEach
     *
     * @param string      $expected
     * @param null|string $input
     */
    public function testFixWithForEach($expected, $input = null)
    {
        $this->fixer->configure([
            'statements' => ['foreach'],
        ]);

        $this->doTest($expected, $input);
    }

    public function providerFixWithForEach()
    {
        return [
            [
                '<?php
                    echo 1;

                    foreach($a as $b){break;}
                ',
                '<?php
                    echo 1;
                    foreach($a as $b){break;}
                ',
            ],
        ];
    }

    /**
     * @return array
     */
    public function providerFixWithIf()
    {
        return [
            [
                '<?php
if (true) {
    echo $bar;
}',
            ],
            [
                '<?php
$foo = $bar;

if (true) {
    echo $bar;
}',
                '<?php
$foo = $bar;
if (true) {
    echo $bar;
}',
            ],
            [
                '<?php
// foo
if ($foo) { }',
            ],
        ];
    }

    /**
     * @dataProvider providerFixWithInclude
     *
     * @param string      $expected
     * @param null|string $input
     */
    public function testFixWithInclude($expected, $input = null)
    {
        $this->fixer->configure([
            'statements' => ['include'],
        ]);

        $this->doTest($expected, $input);
    }

    /**
     * @return array
     */
    public function providerFixWithInclude()
    {
        return [
            [
                '<?php
include "foo.php";',
            ],
            [
                '<?php
$foo = $bar;

include "foo.php";',
                '<?php
$foo = $bar;
include "foo.php";',
            ],
        ];
    }

    /**
     * @dataProvider providerFixWithIncludeOnce
     *
     * @param string      $expected
     * @param null|string $input
     */
    public function testFixWithIncludeOnce($expected, $input = null)
    {
        $this->fixer->configure([
            'statements' => ['include_once'],
        ]);

        $this->doTest($expected, $input);
    }

    /**
     * @return array
     */
    public function providerFixWithIncludeOnce()
    {
        return [
            [
                '<?php
include_once "foo.php";',
            ],
            [
                '<?php
$foo = $bar;

include_once "foo.php";',
                '<?php
$foo = $bar;
include_once "foo.php";',
            ],
        ];
    }

    /**
     * @dataProvider providerFixWithRequire
     *
     * @param string      $expected
     * @param null|string $input
     */
    public function testFixWithRequire($expected, $input = null)
    {
        $this->fixer->configure([
            'statements' => ['require'],
        ]);

        $this->doTest($expected, $input);
    }

    /**
     * @return array
     */
    public function providerFixWithRequire()
    {
        return [
            [
                '<?php
require "foo.php";',
            ],
            [
                '<?php
$foo = $bar;

require "foo.php";',
                '<?php
$foo = $bar;
require "foo.php";',
            ],
        ];
    }

    /**
     * @dataProvider providerFixWithRequireOnce
     *
     * @param string      $expected
     * @param null|string $input
     */
    public function testFixWithRequireOnce($expected, $input = null)
    {
        $this->fixer->configure([
            'statements' => ['require_once'],
        ]);

        $this->doTest($expected, $input);
    }

    /**
     * @return array
     */
    public function providerFixWithRequireOnce()
    {
        return [
            [
                '<?php
require_once "foo.php";',
            ],
            [
                '<?php
$foo = $bar;

require_once "foo.php";',
                '<?php
$foo = $bar;
require_once "foo.php";',
            ],
        ];
    }

    /**
     * @dataProvider providerFixWithReturn
     *
     * @param string      $expected
     * @param null|string $input
     */
    public function testFixWithReturn($expected, $input = null)
    {
        $this->fixer->configure([
            'statements' => ['return'],
        ]);

        $this->doTest($expected, $input);
    }

    /**
     * @return array
     */
    public function providerFixWithReturn()
    {
        return [
            [
                '
$a = $a;
return $a;',
            ],
            [
                '<?php
$a = $a;

return $a;',
                '<?php
$a = $a; return $a;',
            ],
            [
                '<?php
$b = $b;

return $b;',
                '<?php
$b = $b;return $b;',
            ],
            [
                '<?php
$c = $c;

return $c;',
                '<?php
$c = $c;
return $c;',
            ],
            [
                '<?php
$d = $d;

return $d;',
                '<?php
$d = $d;
return $d;',
            ],
            [
                '<?php
if (true) {
    return 1;
}',
            ],
            [
                '<?php
if (true)
    return 1;',
            ],
            [
                '<?php
if (true) {
    return 1;
} else {
    return 2;
}',
            ],
            [
                '<?php
if (true)
    return 1;
else
    return 2;',
            ],
            [
                '<?php
if (true) {
    return 1;
} elseif (false) {
    return 2;
}',
            ],
            [
                '<?php
if (true)
    return 1;
elseif (false)
    return 2;',
            ],
            [
                '<?php
throw new Exception("return true;");',
            ],
            [
                '<?php
function foo()
{
    // comment
    return "foo";
}',
            ],
            [
                '<?php
function foo()
{
    // comment

    return "bar";
}',
            ],
        ];
    }

    /**
     * @dataProvider providerFixWithReturnAndMessyWhitespaces
     *
     * @param string      $expected
     * @param null|string $input
     */
    public function testFixWithReturnAndMessyWhitespaces($expected, $input = null)
    {
        $this->fixer->setWhitespacesConfig(new WhitespacesFixerConfig("\t", "\r\n"));

        $this->doTest($expected, $input);
    }

    /**
     * @return array
     */
    public function providerFixWithReturnAndMessyWhitespaces()
    {
        return [
            [
                "<?php\r\n\$a = \$a;\r\n\r\nreturn \$a;",
                "<?php\r\n\$a = \$a; return \$a;",
            ],
            [
                "<?php\r\n\$b = \$b;\r\n\r\nreturn \$b;",
                "<?php\r\n\$b = \$b;return \$b;",
            ],
            [
                "<?php\r\n\$c = \$c;\r\n\r\nreturn \$c;",
                "<?php\r\n\$c = \$c;\r\nreturn \$c;",
            ],
        ];
    }

    /**
     * @dataProvider providerFixWithSwitch
     *
     * @param string      $expected
     * @param null|string $input
     */
    public function testFixWithSwitch($expected, $input = null)
    {
        $this->fixer->configure([
            'statements' => ['switch'],
        ]);

        $this->doTest($expected, $input);
    }

    /**
     * @return array
     */
    public function providerFixWithSwitch()
    {
        return [
            [
                '<?php
switch ($a) {
    case 42:
        break;
}',
            ],
            [
                '<?php
$foo = $bar;

switch ($foo) {
    case $bar:
        break;
}',
                '<?php
$foo = $bar;
switch ($foo) {
    case $bar:
        break;
}',
            ],
        ];
    }

    /**
     * @dataProvider providerFixWithThrow
     *
     * @param string      $expected
     * @param null|string $input
     */
    public function testFixWithThrow($expected, $input = null)
    {
        $this->fixer->configure([
            'statements' => ['throw'],
        ]);

        $this->doTest($expected, $input);
    }

    /**
     * @return array
     */
    public function providerFixWithThrow()
    {
        return [
            [
                '<?php
if (false) {
    throw new \Exception("Something unexpected happened");
}',
            ],
            [
                '<?php
if (false) {
    $log->error("No");

    throw new \Exception("Something unexpected happened");
}',
                '<?php
if (false) {
    $log->error("No");
    throw new \Exception("Something unexpected happened");
}',
            ],
        ];
    }

    /**
     * @dataProvider providerFixWithTry
     *
     * @param string      $expected
     * @param null|string $input
     */
    public function testFixWithTry($expected, $input = null)
    {
        $this->fixer->configure([
            'statements' => ['try'],
        ]);

        $this->doTest($expected, $input);
    }

    /**
     * @return array
     */
    public function providerFixWithTry()
    {
        return [
            [
                '<?php
try {
    $transaction->commit();
} catch (\Exception $exception) {
    $transaction->rollback();
}',
            ],
            [
                '<?php
$foo = $bar;

try {
    $transaction->commit();
} catch (\Exception $exception) {
    $transaction->rollback();
}',
                '<?php
$foo = $bar;
try {
    $transaction->commit();
} catch (\Exception $exception) {
    $transaction->rollback();
}',
            ],
        ];
    }

    /**
     * @dataProvider providerFixWithWhile
     *
     * @param string      $expected
     * @param null|string $input
     */
    public function testFixWithWhile($expected, $input = null)
    {
        $this->fixer->configure([
            'statements' => ['while'],
        ]);

        $this->doTest($expected, $input);
    }

    /**
     * @return array
     */
    public function providerFixWithWhile()
    {
        return [
            [
                '<?php
while (true) {
    $worker->work();
}',
            ],
            [
                '<?php
$foo = $bar;

while (true) {
    $worker->work();
}',
                '<?php
$foo = $bar;
while (true) {
    $worker->work();
}',
            ],
            [
                '<?php
$foo = $bar;

do {
    echo 1;

    while($a());
    $worker->work();
} while (true);',
                '<?php
$foo = $bar;

do {
    echo 1;
    while($a());
    $worker->work();
} while (true);',
            ],
        ];
    }

    /**
     * @dataProvider provideFixWithMultipleConfigStatements
     *
     * @param string[]    $statements
     * @param string      $expected
     * @param null|string $input
     */
    public function testFixWithMultipleConfigStatements(array $statements, $expected, $input = null)
    {
        $this->fixer->configure(['statements' => $statements]);

        $this->doTest($expected, $input);
    }

    public function provideFixWithMultipleConfigStatements()
    {
        $allStatements = [
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

        return [
            [
                $allStatements,
                '<?php
                    while($a) {
                        if ($c) {
                            switch ($d) {
                                case $e:
                                    continue 2;
                                case 4:
                                    break;
                                case 5:
                                    return 1;
                            }
                        }
                    }
                ',
            ],
            [
                ['break', 'throw'],
                '<?php
do {
    echo 0;
    if ($a) {
        echo 1;

        break;
    }
    echo 2;

    throw $f;
} while(true);',
                '<?php
do {
    echo 0;
    if ($a) {
        echo 1;
        break;
    }
    echo 2;
    throw $f;
} while(true);',
            ],
        ];
    }
}
