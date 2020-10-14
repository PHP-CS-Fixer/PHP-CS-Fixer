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

namespace PhpCsFixer\Tests\Fixer\ControlStructure;

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @author SpacePossum
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\ControlStructure\NoUnusedCapturingCatchFixer
 * @requires PHP 8.0
 */
final class NoUnusedCapturingCatchFixerTest extends AbstractFixerTestCase
{
    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideFixCases
     */
    public function testFix($expected, $input = null)
    {
        $this->doTest($expected, $input);
    }

    public function provideFixCases()
    {
        $negativeCases = [
            'var simple 1' => 'echo $e->getMessage();',
            'var simple 2' => 'throw $e;',
            'var simple 3' => '$this->logger->error(\'Foo\', [\'exception\' => $e]);',
            'var as argument 1' => 'foo($e);',
            'var as import 1' => '$a = function() use ($e) { echo $e->getMessage(); }; $a();',
            'compact 1' => 'return compact(\'e\');',
            'compact 2' => 'return \compact(\'e\');',
            'eval' => 'eval(foo());',
            'include' => 'include(foo());',
            'include_once' => 'include(foo());',
            'require' => 'include(foo());',
            'require_once' => 'include(foo());',
            '${$X}' => '${$e};',
            '$$X' => '$$e;',
            'interpolation 1' => 'echo "foo $e";',
            'interpolation 2' => 'echo "foo {$e}";',
            'interpolation 3' => 'echo "foo ${e}";',
            'heredoc' => '
echo
<<<"TEST"
Foo $e
TEST;
            ',
        ];

        $template =
            '<?php
try {
    foo();
} catch (\Exception $e) {
    %s
}
';

        foreach ($negativeCases as $index => $negativeCase) {
            yield 'negative case '.$index => [
                sprintf($template, $negativeCase),
            ];
        }

        yield 'simple' => [
            '<?php
try {
    foo();
} catch (Exception) {
    // ignore exception
}
',
            '<?php
try {
    foo();
} catch (Exception $e) {
    // ignore exception
}
',
        ];

        yield 'comments and strings' => [
            '<?php
try {
    foo();
} catch (\Exception\A\B) {
    // echo $e;
    /** $e->foo(); */
    $a = \'$e\';
    echo "\$e";
    echo <<<\'TEST\'
    $e
TEST;
    echo <<<"TEST"
        \$e
    TEST;
    echo <<<TEST
        \$e
    TEST;
?>
$e
<?php
}
',
            '<?php
try {
    foo();
} catch (\Exception\A\B $e) {
    // echo $e;
    /** $e->foo(); */
    $a = \'$e\';
    echo "\$e";
    echo <<<\'TEST\'
    $e
TEST;
    echo <<<"TEST"
        \$e
    TEST;
    echo <<<TEST
        \$e
    TEST;
?>
$e
<?php
}
',
        ];

        yield 'type alternation' => [
            '<?php
try {
    foo();
} catch (\Exception\A\A|\Exception) {
    // ignore exception
}
',
            '<?php
try {
    foo();
} catch (\Exception\A\A|\Exception $e) {
    // ignore exception
}
',
        ];

        yield 'multiple fixes' => [
            '<?php
try {
    foo();
} catch (Exception\A\A|Exception) {
    // ignore exception
}
try {
    foo();
} catch (Exception\A\A|Exception) {
    // ignore exception
}
try {
    foo();
} catch (Exception\A\A|Exception) {
    // ignore exception
}
try {
    foo();
} catch (Exception\A\A|Exception) {
    // ignore exception
}
',
            '<?php
try {
    foo();
} catch (Exception\A\A|Exception $e) {
    // ignore exception
}
try {
    foo();
} catch (Exception\A\A|Exception $e) {
    // ignore exception
}
try {
    foo();
} catch (Exception\A\A|Exception $e) {
    // ignore exception
}
try {
    foo();
} catch (Exception\A\A|Exception $e) {
    // ignore exception
}
',
        ];

        yield 'multi line comment' => [
            '<?php

try {

} catch (\Exception
 #foo
) {

}
',
            '<?php

try {

} catch (\Exception
 #foo
$e) {

}
',
        ];

        yield 'negative case: super global' => [
            '<?php
try {
    foo();
} catch (Exception $_COOKIE) {
    // ignore exception
}
',
        ];
    }
}
